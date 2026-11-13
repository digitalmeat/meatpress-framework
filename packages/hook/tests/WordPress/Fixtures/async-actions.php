<?php

/**
 * Asynchronous actions declared the way a plain WordPress plugin would, without the framework.
 *
 * Each handler records what it received in the 'meatpress_hook_fixture_runs' option.
 */

declare(strict_types=1);

use Meatpress\Hook\Action;
use Meatpress\Hook\Async\AsyncContext;
use Meatpress\Hook\Async\AsyncPayload;

final class MeatpressHookFixture
{
    public const string RUNS = 'meatpress_hook_fixture_runs';

    /** Default driver of the suite, 'wp-cron' unless a test sets it */
    public const string DRIVER = 'meatpress_hook_fixture_driver';

    /** Failures flaky() still has to throw */
    public const string FAILURES = 'meatpress_hook_fixture_failures';

    /**
     * @param  array<string, mixed>  $data
     */
    public static function record(string $key, array $data = []): void
    {
        wp_cache_delete(self::RUNS, 'options');
        $runs = get_option(self::RUNS, []);
        $runs[] = ['key' => $key, ...$data];
        update_option(self::RUNS, $runs, false);
    }

    public function event(int $postId, WP_Post $post, AsyncContext $context): void
    {
        self::record('event', [
            'postId' => $postId,
            'title' => $post->post_title,
            'userId' => $context->userId,
            'attempt' => $context->attempt,
            'cron' => defined('DOING_CRON') && DOING_CRON,
            'currentUser' => get_current_user_id(),
        ]);
    }

    public function captured(int $postId, WP_Post $post, AsyncContext $context): void
    {
        self::record('captured', [
            'statusThen' => $context->get('statusThen'),
            'statusNow' => $post->post_status,
            'source' => $context->get('source'),
        ]);
    }

    public function user(int $id, AsyncContext $context): void
    {
        self::record('user', [
            'currentUser' => get_current_user_id(),
            'canEditPosts' => current_user_can('edit_posts'),
            'triggeredBy' => $context->userId,
        ]);
    }

    public function counted(int $id): void
    {
        self::record('counted', ['id' => $id]);
    }

    public function large(int $id, AsyncContext $context): void
    {
        self::record('large', ['id' => $id, 'length' => strlen((string) $context->get('report'))]);
    }

    public function flaky(int $id, AsyncContext $context): void
    {
        self::record('flaky', ['attempt' => $context->attempt]);

        wp_cache_delete(self::FAILURES, 'options');
        $failures = (int) get_option(self::FAILURES, 0);

        if ($failures > 0) {
            update_option(self::FAILURES, $failures - 1, false);

            throw new RuntimeException('Temporary failure');
        }
    }

    public function fails(int $id): void
    {
        throw new RuntimeException('Fixture failure');
    }

    public function loop(int $id): void
    {
        self::record('loop', ['id' => $id]);
        do_action('meatpress_fixture_loop', $id);
    }
}

Action::add('meatpress_fixture_event', [MeatpressHookFixture::class, 'event'])->async();
Action::add('meatpress_fixture_delayed', [MeatpressHookFixture::class, 'event'])->async()->delay(3600);
Action::add('meatpress_fixture_fails', [MeatpressHookFixture::class, 'fails'])->async();
Action::add('meatpress_fixture_loop', [MeatpressHookFixture::class, 'loop'])->async();
Action::add('meatpress_fixture_queued', [MeatpressHookFixture::class, 'event'])->async()->onQueue('integrations');
Action::add('meatpress_fixture_large', [MeatpressHookFixture::class, 'large'])->async()
    ->capture(fn (int $id): array => ['report' => str_repeat('x', 9000)]);
Action::add('meatpress_fixture_flaky', [MeatpressHookFixture::class, 'flaky'])->async()->tries(2)->backoff(0);

// Phase 2 options
Action::add('meatpress_fixture_captured', [MeatpressHookFixture::class, 'captured'])->async()
    ->capture(fn (int $postId, WP_Post $post): array => [
        'statusThen' => $post->post_status,
        'source' => isset($_POST['acme_source']) ? sanitize_key(wp_unslash($_POST['acme_source'])) : 'admin',
    ]);
Action::add('meatpress_fixture_when', [MeatpressHookFixture::class, 'counted'])->async()
    ->when(fn (int $postId): bool => ! wp_is_post_revision($postId));
Action::add('meatpress_fixture_unique', [MeatpressHookFixture::class, 'counted'])->async()->unique();
Action::add('meatpress_fixture_user', [MeatpressHookFixture::class, 'user'])->async()->asUser();
Action::add('meatpress_fixture_retried', [MeatpressHookFixture::class, 'flaky'])->async()->tries(2)->backoff(0);

// A closure, signed with a key derived from the WordPress salts
$closureLabel = 'closure';
Action::add('meatpress_fixture_closure', static function (int $id, AsyncContext $context) use ($closureLabel): void {
    MeatpressHookFixture::record($closureLabel, ['id' => $id, 'cron' => defined('DOING_CRON') && DOING_CRON]);
})->async();
unset($closureLabel);

// WP-Cron unless a test chooses another driver; the MEATPRESS_ASYNC_DRIVER constant still comes first
add_filter('meatpress/hook/async_driver', fn (): string => (string) get_option(MeatpressHookFixture::DRIVER, 'wp-cron'));

// Action Scheduler would otherwise start its queue through a loopback request at shutdown
add_filter('action_scheduler_allow_async_request_runner', '__return_false');

add_action('meatpress/async/failed', function (AsyncPayload $payload, Throwable $throwable): void {
    MeatpressHookFixture::record('failed', ['hook' => $payload->hook, 'message' => $throwable->getMessage()]);
}, 10, 2);
