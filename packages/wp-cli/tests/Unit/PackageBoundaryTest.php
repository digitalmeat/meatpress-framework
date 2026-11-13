<?php

declare(strict_types=1);

/**
 * meatpress/wp-cli runs without Laravel's application: it uses the attributes,
 * discovery, PSR logging and WP-CLI, nothing else. The Laravel service provider
 * and the meatpress:make:wp-cli generator live in the framework.
 */
arch('wp-cli stays free of the Laravel application')
    ->expect('Meatpress\WpCli')
    ->toOnlyUse([
        'Meatpress\WpCli',
        'Meatpress\Attributes',
        'Meatpress\Discovery',
        'Psr\Log',
        'Spatie\StructureDiscoverer',
    ])
    ->ignoring(['WP_CLI', 'WP_CLI_Command']);
