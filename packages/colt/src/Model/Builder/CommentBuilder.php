<?php

declare(strict_types=1);

namespace Meatpress\Colt\Model\Builder;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class CommentBuilder
 */
class CommentBuilder extends Builder
{
    /**
     * @return CommentBuilder
     */
    public function approved()
    {
        return $this->where('comment_approved', 1);
    }
}
