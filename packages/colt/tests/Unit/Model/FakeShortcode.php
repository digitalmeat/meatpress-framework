<?php

declare(strict_types=1);

namespace Meatpress\Colt\Tests\Unit\Model;

use Meatpress\Colt\Shortcode;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

class FakeShortcode implements Shortcode
{
    public function render(ShortcodeInterface $shortcode): string
    {
        return sprintf(
            'html-for-shortcode-%s-%s',
            $shortcode->getName(),
            $shortcode->getParameter('one')
        );
    }
}
