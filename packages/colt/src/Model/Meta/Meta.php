<?php

declare(strict_types=1);

namespace Meatpress\Colt\Model\Meta;

use Meatpress\Colt\Concerns\SafeUnserialize;
use Meatpress\Colt\Model;
use Meatpress\Colt\Model\Collection\MetaCollection;

/**
 * Class Meta
 *
 *
 * @property string $meta_key
 * @property mixed $meta_value
 */
abstract class Meta extends Model
{
    use SafeUnserialize;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = 'meta_id';

    /**
     * @var array
     */
    protected $appends = ['value'];

    /**
     * @return mixed
     */
    public function getValueAttribute()
    {
        return $this->maybeUnserialize($this->meta_value);
    }

    /**
     * @return MetaCollection
     */
    public function newCollection(array $models = [])
    {
        return new MetaCollection($models);
    }
}
