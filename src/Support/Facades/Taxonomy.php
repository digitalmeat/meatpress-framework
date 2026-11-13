<?php

declare(strict_types=1);

namespace Meatpress\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Meatpress\Taxonomy\Infrastructure\Factories\TaxonomyFactory;

/**
 * @method static \Meatpress\Entity\Taxonomy make(string $slug, string|array $objectType, string $singular = null, string $plural = null)
 * @method static \Meatpress\Entity\Taxonomy showTagcloud()
 * @method static \Meatpress\Entity\Taxonomy setShowTagcloud(bool $showTagcloud)
 * @method static \Meatpress\Entity\Taxonomy showInQuickEdit()
 * @method static \Meatpress\Entity\Taxonomy setShowInQuickEdit(bool $showInQuickEdit)
 * @method static \Meatpress\Entity\Taxonomy showAdminColumn()
 * @method static \Meatpress\Entity\Taxonomy setShowAdminColumn(bool $showAdminColumn)
 * @method static \Meatpress\Entity\Taxonomy setMetaBoxCb(callable|bool|null $metaBoxCb)
 * @method static \Meatpress\Entity\Taxonomy setMetaBoxSanitizeCb(?callable $metaBoxSanitizeCb)
 * @method static \Meatpress\Entity\Taxonomy setUpdateCountCallback(callable $updateCountCallback)
 * @method static \Meatpress\Entity\Taxonomy setDefaultTerm(array|string $defaultTerm)
 * @method static \Meatpress\Entity\Taxonomy sort()
 * @method static \Meatpress\Entity\Taxonomy setSort(?bool $sort)
 * @method static \Meatpress\Entity\Taxonomy setArgs(?array $args)
 * @method static \Meatpress\Entity\Taxonomy checkedOntop()
 * @method static \Meatpress\Entity\Taxonomy setCheckedOntop(bool $checkedOntop)
 * @method static \Meatpress\Entity\Taxonomy exclusive()
 * @method static \Meatpress\Entity\Taxonomy setExclusive(bool $exclusive)
 * @method static \Meatpress\Entity\Taxonomy allowHierarchy()
 * @method static \Meatpress\Entity\Taxonomy setAllowHierarchy(bool $allowHierarchy)
 *
 * @see \Meatpress\Entity\Taxonomy
 */
class Taxonomy extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return TaxonomyFactory::class;
    }
}
