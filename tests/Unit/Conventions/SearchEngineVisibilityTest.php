<?php

declare(strict_types=1);

namespace Meatpress\Conventions\Hooks {
    // Namespaced stand-in for the WordPress function, resolved before the global one.
    function wp_get_environment_type(): string
    {
        return $GLOBALS['meatpress_test_environment'] ?? 'production';
    }
}

namespace Tests\Unit\Conventions {
    use Meatpress\Conventions\Hooks\SearchEngineVisibility;
    use PHPUnit\Framework\Attributes\DataProvider;
    use PHPUnit\Framework\TestCase;

    final class SearchEngineVisibilityTest extends TestCase
    {
        protected function tearDown(): void
        {
            unset($GLOBALS['meatpress_test_environment']);
        }

        public function test_production_is_left_untouched(): void
        {
            $GLOBALS['meatpress_test_environment'] = 'production';

            $robots = (new SearchEngineVisibility)->filterRobots(['max-image-preview' => 'large']);

            $this->assertSame(['max-image-preview' => 'large'], $robots);
        }

        /**
         * @return array<string, array{string}>
         */
        public static function nonProductionEnvironments(): array
        {
            return [
                'local' => ['local'],
                'development' => ['development'],
                'staging' => ['staging'],
            ];
        }

        #[DataProvider('nonProductionEnvironments')]
        public function test_other_environments_are_not_indexed(string $environment): void
        {
            $GLOBALS['meatpress_test_environment'] = $environment;

            $robots = (new SearchEngineVisibility)->filterRobots(['max-image-preview' => 'large']);

            $this->assertTrue($robots['noindex']);
            $this->assertTrue($robots['nofollow']);
            $this->assertSame('large', $robots['max-image-preview']);
        }
    }
}
