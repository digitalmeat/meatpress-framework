<?php

declare(strict_types=1);

// meatpress/block-bindings runs with or without Laravel: nothing in it may reach Illuminate or the framework.
arch('the package does not use Laravel')
    ->expect('Meatpress\BlockBinding')
    ->not->toUse(['Illuminate', 'Laravel', 'Meatpress\Support', 'Meatpress\Models', 'Meatpress\Doctor']);

arch('the domain stays free of WordPress adapters and Discovery')
    ->expect('Meatpress\BlockBinding\Domain')
    ->not->toUse(['Meatpress\Discovery', 'Meatpress\Hook', 'Meatpress\BlockBinding\Infrastructure']);
