<?php

declare(strict_types=1);

namespace Meatpress\Meta\Tests\Fixtures;

use Meatpress\Colt\Model\Post;

class EventPostModel extends Post
{
    protected $postType = 'fixture_event';
}
