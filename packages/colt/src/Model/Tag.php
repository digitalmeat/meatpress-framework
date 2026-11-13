<?php

declare(strict_types=1);

namespace Meatpress\Colt\Model;

/**
 * Tag class.
 */
class Tag extends Taxonomy
{
    /**
     * @var string
     */
    protected $taxonomy = 'post_tag';
}
