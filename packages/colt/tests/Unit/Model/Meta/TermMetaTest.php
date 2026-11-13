<?php

declare(strict_types=1);

use Meatpress\Colt\Model\Meta\TermMeta;
use Meatpress\Colt\Model\Term;

test('term relation', function () {
    $term_meta = factory(TermMeta::class)->create();

    expect($term_meta->term)->toBeInstanceOf(Term::class);
});
