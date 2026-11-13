<?php

declare(strict_types=1);

namespace Meatpress\Colt\Model\Meta;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Meatpress\Colt\Model\Comment;

/**
 * Class CommentMeta
 */
class CommentMeta extends Meta
{
    /**
     * @var string
     */
    protected $table = 'commentmeta';

    /**
     * @var array
     */
    protected $fillable = ['meta_key', 'meta_value', 'comment_id'];

    /**
     * @return BelongsTo
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
