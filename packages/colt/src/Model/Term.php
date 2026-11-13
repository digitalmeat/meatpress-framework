<?php

declare(strict_types=1);

namespace Meatpress\Colt\Model;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Meatpress\Colt\Concerns\MetaFields;
use Meatpress\Colt\Model;
use Meatpress\Colt\Model\Collection\MetaCollection;

/**
 * Class Term.
 *
 *
 * @property-read MetaCollection $meta
 */
class Term extends Model
{
    use MetaFields;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'terms';

    /**
     * @var string
     */
    protected $primaryKey = 'term_id';

    /**
     * @return HasOne
     */
    public function taxonomy()
    {
        return $this->hasOne(Taxonomy::class, 'term_id');
    }
}
