<?php

// Relocated from Commands/FilterMakeCommand.php. Content will be copied verbatim and namespace updated.

declare(strict_types=1);

namespace Meatpress\Hook\UI\Console;

use Illuminate\Console\Attributes\Aliases;
use Illuminate\Console\Attributes\Description;

/**
 * Class FilterMakeCommand
 *
 * Command to create a new filter hook class (feature UI layer).
 * Supports generation in different locations (app, theme, plugin) through traits.
 */
#[Description('Create a new filter hook class')]
#[Aliases(['meatpress:make-filter'])]
class FilterMakeCommand extends AttributeMakeCommand
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'meatpress:make:filter';

    /**
     * The type of the attribute.
     *
     * @var string
     */
    protected $type = 'Filter';
}
