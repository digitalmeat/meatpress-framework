<?php

declare(strict_types=1);

use Meatpress\Query\PostQuery;

test('Comment: comment_count', function () {
    $args = PostQuery::select()
        ->commentCount(1)
        ->getArguments();

    expect($args)->toMatchArray([
        'comment_count' => 1,
    ]);
});
