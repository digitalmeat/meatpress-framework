<?php

declare(strict_types=1);

namespace Meatpress\Models;

use Meatpress\Models\Concerns\HasTypedMeta;

/**
 * Class Comment
 */
class Comment extends \Meatpress\Colt\Model\Comment
{
    use HasTypedMeta;
}
