<?php

declare(strict_types=1);

// meatpress/roles runs with or without Laravel: nothing in it may reach Illuminate or the framework.
arch('the package does not use Laravel')
    ->expect('Meatpress\Role')
    ->not->toUse(['Illuminate', 'Laravel', 'Meatpress\Support', 'Meatpress\Models', 'Meatpress\Doctor']);

arch('the domain stays free of Discovery')
    ->expect('Meatpress\Role\Domain')
    ->not->toUse(['Meatpress\Discovery', 'Psr\Log']);
