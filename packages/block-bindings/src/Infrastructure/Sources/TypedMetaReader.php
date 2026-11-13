<?php

declare(strict_types=1);

namespace Meatpress\BlockBinding\Infrastructure\Sources;

use Closure;
use Meatpress\Meta\Application\Services\MetaAccessor;
use Meatpress\Meta\Application\Services\MetaSchemaRepository;
use Meatpress\Meta\Domain\Enums\MetaObjectType;
use Meatpress\Meta\Domain\Models\MetaDefinition;

/**
 * Reads one declared meta of an object for the Meatpress binding sources. A key
 * no `#[Meta]` declares on the object, or one the source may not show, reads
 * as nothing.
 */
final readonly class TypedMetaReader
{
    public function __construct(
        private MetaSchemaRepository $schemas,
        private MetaAccessor $accessor,
    ) {}

    /**
     * @param  Closure(MetaDefinition): bool  $readable  Whether the source may show the meta
     * @return array{0: MetaDefinition, 1: mixed}|null The definition and the typed value
     */
    public function read(MetaObjectType $objectType, ?string $subtype, int $objectId, string $key, Closure $readable): ?array
    {
        foreach ($this->schemas->forObject($objectType, $subtype) as $schema) {
            $definition = $schema->find($key);

            if (! $definition instanceof MetaDefinition) {
                continue;
            }

            if (! $readable($definition)) {
                return null;
            }

            return [$definition, $this->accessor->record($schema, $objectId)->get($definition->property)];
        }

        return null;
    }

    /**
     * The rule of `core/post-meta`: a meta exposed in REST, under a key that is not protected.
     */
    public static function exposedInRest(MetaDefinition $definition): bool
    {
        return $definition->showInRest && ! $definition->isProtected();
    }
}
