<?php

declare(strict_types=1);

namespace Meatpress\Colt\Tests\Unit\Concerns;

use Meatpress\Colt\Concerns\CustomTimestamps;
use Meatpress\Colt\Model\User;

class FakeUser extends User
{
    use CustomTimestamps;

    const CREATED_AT = 'foo_created';

    const UPDATED_AT = 'foo_updated';
}
