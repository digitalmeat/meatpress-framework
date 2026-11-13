<?php

declare(strict_types=1);

namespace Meatpress\Colt\Model\Meta;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Meatpress\Colt\Model\Post;

/**
 * Class PostMeta
 */
class PostMeta extends Meta
{
    /**
     * @var string
     */
    protected $table = 'postmeta';

    /**
     * @var array
     */
    protected $fillable = ['meta_key', 'meta_value', 'post_id'];

    /**
     * @return BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
