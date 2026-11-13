<?php

declare(strict_types=1);

use Meatpress\Query\PostQuery;
use Meatpress\Query\QueryException;

test('Permission: readable', function () {
    $args = PostQuery::select()
        ->userPermission('readable')
        ->getArguments();

    expect($args)->toMatchArray([
        'perm' => 'readable',
    ]);
});

test('Permission: editable', function () {
    $args = PostQuery::select()
        ->userPermission('editable')
        ->getArguments();

    expect($args)->toMatchArray([
        'perm' => 'editable',
    ]);
});

test('Permission: invalid', function () {
    PostQuery::select()->userPermission('invalid');
})->throws(QueryException::class, 'Invalid permission supplied: invalid');
