<?php

declare(strict_types=1);

namespace Meatpress\Colt\Model;

use Meatpress\Colt\Concerns\SafeUnserialize;
use Meatpress\Colt\Model;

/**
 * Option class.
 *
 *
 * @property string $option_name
 * @property string $option_value
 * @property-read mixed $value
 */
class Option extends Model
{
    use SafeUnserialize;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'options';

    /**
     * @var string
     */
    protected $primaryKey = 'option_id';

    /**
     * @var array
     */
    protected $fillable = [
        'option_name',
        'option_value',
        'autoload',
    ];

    /**
     * @var array
     */
    protected $appends = ['value'];

    /**
     * @return mixed
     */
    public function getValueAttribute()
    {
        return $this->maybeUnserialize($this->option_value);
    }

    /**
     * @param  string  $key
     * @param  mixed  $value
     * @return Option
     */
    public static function add($key, $value)
    {
        return static::create([
            'option_name' => $key,
            'option_value' => is_array($value) ? serialize($value) : $value,
        ]);
    }

    /**
     * @param  string  $name
     * @return mixed
     */
    public static function get($name)
    {
        if ($option = self::where('option_name', $name)->first()) {
            return $option->value;
        }

        return null;
    }

    /**
     * @return array
     *
     * @deprecated
     */
    public static function getAll()
    {
        return static::asArray();
    }

    /**
     * @param  array  $keys
     * @return array
     */
    public static function asArray($keys = [])
    {
        $query = static::query();

        if (! empty($keys)) {
            $query->whereIn('option_name', $keys);
        }

        return $query->get()
            ->pluck('value', 'option_name')
            ->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [$this->option_name => $this->value];
    }
}
