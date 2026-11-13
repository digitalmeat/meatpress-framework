<?php

declare(strict_types=1);

namespace Meatpress\Role\Infrastructure\Services;

use BackedEnum;
use Meatpress\Attributes\CapabilitySet;
use Meatpress\Attributes\ModifyRole;
use Meatpress\Attributes\PostType;
use Meatpress\Attributes\Role;
use Meatpress\Attributes\Taxonomy;
use Meatpress\Discovery\Domain\Contracts\DiscoveryInterface;
use Meatpress\Discovery\Domain\Contracts\DiscoveryLocationInterface;
use Meatpress\Discovery\Domain\Contracts\ReflectionCacheInterface;
use Meatpress\Discovery\Domain\Services\IsDiscovery;
use Meatpress\Role\Application\Services\RoleDefinitionBuilder;
use Meatpress\Role\Application\Services\RoleRegistry;
use Meatpress\Role\Domain\Models\RoleDefinition;
use Meatpress\Role\Infrastructure\Adapters\WordPressRoleInjector;
use Psr\Log\LoggerInterface;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredEnum;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;

/**
 * Discovers `#[Role]`, `#[ModifyRole]` and `#[CapabilitySet]` declarations, and
 * the post types and taxonomies whose capabilities roles can be granted.
 *
 * Each apply() adds what it found to the registry, then asks the injector to
 * refresh: declarations from a plugin or a theme arrive after WordPress has
 * built its roles.
 */
final class RoleDiscovery implements DiscoveryInterface
{
    use IsDiscovery;

    private const array KINDS = [
        Role::class => 'role',
        ModifyRole::class => 'role',
        CapabilitySet::class => 'capabilitySet',
        PostType::class => 'postType',
        Taxonomy::class => 'taxonomy',
    ];

    public function __construct(
        private readonly RoleDefinitionBuilder $builder,
        private readonly RoleRegistry $registry,
        private readonly WordPressRoleInjector $injector,
        private readonly ?LoggerInterface $logger = null,
    ) {}

    public function discover(DiscoveryLocationInterface $location, DiscoveredStructure $structure, ?ReflectionCacheInterface $reflectionCache = null): void
    {
        $isClass = $structure instanceof DiscoveredClass && ! $structure->isAbstract;

        if (! $isClass && ! $structure instanceof DiscoveredEnum) {
            return;
        }

        foreach ($structure->attributes as $attribute) {
            $kind = self::KINDS[$attribute->class] ?? null;

            if ($kind === null || ($kind === 'capabilitySet') === $isClass) {
                continue;
            }

            $this->getItems()->add($location, ['class' => $structure->namespace.'\\'.$structure->name, 'kind' => $kind]);

            return;
        }
    }

    public function apply(): void
    {
        foreach ($this->getItems() as ['class' => $class, 'kind' => $kind]) {
            try {
                match ($kind) {
                    'role' => $this->addRole($class),
                    'capabilitySet' => $this->registry->addCapabilitySet(array_map(
                        static fn (BackedEnum $case): string => (string) $case->value,
                        $class::cases()
                    )),
                    'postType' => $this->registry->addPostType($class),
                    'taxonomy' => $this->registry->addTaxonomy($class),
                };
            } catch (\Throwable $throwable) {
                $this->logger?->error(sprintf('Failed to register the role declaration %s: %s', $class, $throwable->getMessage()), ['exception' => $throwable]);
            }
        }

        $this->injector->refresh();
    }

    public function getIdentifier(): string
    {
        return 'roles';
    }

    /**
     * @param  class-string  $class
     */
    private function addRole(string $class): void
    {
        $declaration = $this->builder->build($class);

        if ($declaration instanceof RoleDefinition) {
            $this->registry->addRole($declaration);
        } else {
            $this->registry->addModification($declaration);
        }
    }
}
