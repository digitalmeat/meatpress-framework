<?php

declare(strict_types=1);

namespace Meatpress\Role\Tests\Fixtures;

use Meatpress\Attributes\Role;

#[Role('usher', label: 'Usher', inherits: 'subscriber', textDomain: 'events')]
final class Usher {}
