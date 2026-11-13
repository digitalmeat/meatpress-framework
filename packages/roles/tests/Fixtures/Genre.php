<?php

declare(strict_types=1);

namespace Meatpress\Role\Tests\Fixtures;

use Meatpress\Attributes\Taxonomy;
use Meatpress\Attributes\Taxonomy\Capabilities;

#[Taxonomy('genre')]
#[Capabilities(['manage_terms' => 'manage_genres', 'edit_terms' => 'edit_genres', 'assign_terms' => 'assign_genres', 'other' => 'ignored'])]
class Genre {}
