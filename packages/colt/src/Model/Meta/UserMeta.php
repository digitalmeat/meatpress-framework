<?php

declare(strict_types=1);

namespace Meatpress\Colt\Model\Meta;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Meatpress\Colt\Model\User;

/**
 * Class UserMeta
 */
class UserMeta extends Meta
{
    /**
     * @var string
     */
    protected $table = 'usermeta';

    /**
     * @var string
     */
    protected $primaryKey = 'umeta_id';

    /**
     * @var array
     */
    protected $fillable = ['meta_key', 'meta_value', 'user_id'];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
