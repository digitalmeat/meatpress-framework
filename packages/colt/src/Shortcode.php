<?php

declare(strict_types=1);

namespace Meatpress\Colt;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

/**
 * Interface Shortcode
 */
interface Shortcode
{
    /**
     * @return string
     */
    public function render(ShortcodeInterface $shortcode);
}
