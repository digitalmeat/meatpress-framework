<?php

declare(strict_types=1);

namespace Tests\Unit\Meta\Fixtures;

use Meatpress\Models\Post;

class EventPostModel extends Post
{
    protected $postType = 'fixture_event';
}
