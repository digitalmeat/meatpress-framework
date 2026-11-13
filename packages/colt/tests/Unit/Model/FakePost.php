<?php

declare(strict_types=1);

namespace Meatpress\Colt\Tests\Unit\Model;

use Meatpress\Colt\Model\Post;

class FakePost extends Post
{
    protected $postType = 'fake_post';
}
