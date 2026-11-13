<?php

declare(strict_types=1);

// meatpress/meta runs with or without Laravel: nothing in it may reach Illuminate or the framework.
arch('the package does not use Laravel')
    ->expect('Meatpress\Meta')
    ->not->toUse(['Illuminate', 'Laravel', 'Meatpress\Support', 'Meatpress\Models', 'Meatpress\Doctor']);

arch('the domain stays free of WordPress, Hook and Discovery')
    ->expect('Meatpress\Meta\Domain')
    ->not->toUse(['Meatpress\Hook', 'Meatpress\Discovery', 'WP_Error', 'WP_REST_Request']);
