<?php

declare(strict_types=1);

// meatpress/media runs with or without Laravel: nothing in it may reach Illuminate or the framework.
arch('the package does not use Laravel')
    ->expect('Meatpress\Media')
    ->not->toUse(['Illuminate', 'Laravel', 'Meatpress\Support', 'Meatpress\Models', 'Meatpress\Doctor']);

arch('the domain stays free of WordPress and of the storage library')
    ->expect('Meatpress\Media\Domain')
    ->not->toUse(['League\Flysystem', 'Meatpress\Media\Infrastructure']);
