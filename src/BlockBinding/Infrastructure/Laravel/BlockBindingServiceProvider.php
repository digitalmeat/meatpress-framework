<?php

declare(strict_types=1);

namespace Meatpress\BlockBinding\Infrastructure\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Meatpress\BlockBinding\Application\Services\BindingReferenceInspector;
use Meatpress\BlockBinding\Application\Services\BindingResolver;
use Meatpress\BlockBinding\Application\Services\BindingSourceBuilder;
use Meatpress\BlockBinding\Application\Services\BindingSourceRegistry;
use Meatpress\BlockBinding\Domain\Contracts\ContentVisibilityInterface;
use Meatpress\BlockBinding\Domain\Contracts\ValuePresenterInterface;
use Meatpress\BlockBinding\Infrastructure\Adapters\WordPressBindingEditorScript;
use Meatpress\BlockBinding\Infrastructure\Adapters\WordPressBindingRegistry;
use Meatpress\BlockBinding\Infrastructure\Adapters\WordPressContentVisibility;
use Meatpress\BlockBinding\Infrastructure\Adapters\WordPressValuePresenter;
use Meatpress\BlockBinding\Infrastructure\Laravel\Console\BindingListCommand;
use Meatpress\BlockBinding\Infrastructure\Laravel\Console\MakeBindingCommand;
use Meatpress\BlockBinding\Infrastructure\Services\BindingEditorData;
use Meatpress\BlockBinding\Infrastructure\Services\BindingFormatter;
use Meatpress\BlockBinding\Infrastructure\Services\BlockBindingDiscovery;
use Meatpress\BlockBinding\Infrastructure\Sources\AuthorMetaSource;
use Meatpress\BlockBinding\Infrastructure\Sources\OptionSource;
use Meatpress\BlockBinding\Infrastructure\Sources\PostMetaSource;
use Meatpress\BlockBinding\Infrastructure\Sources\TermMetaSource;
use Meatpress\BlockBinding\Infrastructure\Sources\TypedMetaReader;
use Meatpress\BlockBinding\UI\Http\ResolveBindingsController;
use Meatpress\Doctor\Infrastructure\Providers\DoctorServiceProvider;
use Meatpress\Hook\Domain\Contract\Action;
use Meatpress\Meta\Application\Services\MetaAccessor;
use Meatpress\Meta\Application\Services\MetaSchemaRepository;
use Psr\Log\LoggerInterface;

/**
 * Block Bindings: `#[BlockBinding]` discovery, the sources Meatpress ships
 * (`meatpress/post-meta`, `term-meta`, `author-meta`, `option`), the
 * resolver every source answers through, and their editor side: the fields
 * each source offers and the preview of bound values.
 *
 * Bindings:
 *  - {@see BindingResolver} (singleton: values are kept for the request)
 *  - {@see BlockBindingDiscovery} (singleton, picked up by the discovery engine)
 */
class BlockBindingServiceProvider extends ServiceProvider
{
    /**
     * The sources Meatpress ships, registered whatever the project declares.
     *
     * @var list<class-string>
     */
    public const array SOURCES = [PostMetaSource::class, TermMetaSource::class, AuthorMetaSource::class, OptionSource::class];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/block-bindings.php', 'block-bindings');

        $this->app->singleton(BindingSourceBuilder::class);
        $this->app->singleton(BindingSourceRegistry::class);
        $this->app->singleton(ContentVisibilityInterface::class, WordPressContentVisibility::class);
        $this->app->singleton(ValuePresenterInterface::class, WordPressValuePresenter::class);
        $this->app->singleton(BindingFormatter::class);
        $this->app->singleton(TypedMetaReader::class);
        $this->app->singleton(BindingEditorData::class, fn (Application $app): BindingEditorData => new BindingEditorData(
            $app->make(BindingSourceRegistry::class),
            $app->make(MetaSchemaRepository::class),
            $this->options(),
        ));
        $this->app->singleton(OptionSource::class, fn (): OptionSource => new OptionSource($this->options()));
        $this->app->singleton(WordPressBindingEditorScript::class);
        $this->app->singleton(ResolveBindingsController::class);

        $this->app->singleton(BindingReferenceInspector::class, fn (Application $app): BindingReferenceInspector => new BindingReferenceInspector(
            $app->make(BindingSourceRegistry::class),
            $app->make(MetaSchemaRepository::class),
            $this->options(),
        ));

        $this->app->singleton(BindingResolver::class, fn (Application $app): BindingResolver => new BindingResolver(
            $app,
            $app->make(ContentVisibilityInterface::class),
            $app->make(ValuePresenterInterface::class),
            $app->make(MetaAccessor::class),
            (bool) $app->make('config')->get('app.debug', false),
            $app->make(LoggerInterface::class),
            $app->make(LaravelMethodInvoker::class),
            $app->make(LaravelBindingEvents::class),
            $app->make(LaravelPostFinder::class),
        ));

        $this->app->singleton(WordPressBindingRegistry::class, fn (Application $app): WordPressBindingRegistry => new WordPressBindingRegistry(
            $app->make(Action::class),
            $app->make(BindingResolver::class),
        ));

        $this->app->singleton(BlockBindingDiscovery::class, fn (Application $app): BlockBindingDiscovery => new BlockBindingDiscovery(
            $app->make(BindingSourceBuilder::class),
            $app->make(BindingSourceRegistry::class),
            $app->make(WordPressBindingRegistry::class),
            $app->make(LoggerInterface::class),
        ));

        if ($this->app->runningInConsole()) {
            $this->commands([MakeBindingCommand::class, BindingListCommand::class]);
        }
    }

    public function boot(): void
    {
        // A check of meatpress:doctor and Site Health; tagged on boot, so it comes after the framework's own
        $this->app->tag([BlockBindingsCheck::class], DoctorServiceProvider::CHECKS_TAG);

        $builder = $this->app->make(BindingSourceBuilder::class);
        $sources = $this->app->make(BindingSourceRegistry::class);
        $registry = $this->app->make(WordPressBindingRegistry::class);

        foreach (self::SOURCES as $class) {
            $source = $builder->build($class);
            $sources->add($source);
            $registry->register($source);
        }

        $action = $this->app->make(Action::class);
        $action->add('enqueue_block_editor_assets', fn () => $this->app->make(WordPressBindingEditorScript::class)->enqueue());
        $action->add('rest_api_init', fn () => $this->app->make(ResolveBindingsController::class)->register());
    }

    /**
     * The options a project lists as bindable.
     *
     * @return list<string>
     */
    private function options(): array
    {
        return array_values(array_filter((array) $this->app->make('config')->get('block-bindings.options', []), is_string(...)));
    }
}
