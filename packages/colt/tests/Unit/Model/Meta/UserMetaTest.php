<?php

declare(strict_types=1);

use Meatpress\Colt\Model\Meta\UserMeta;
use Meatpress\Colt\Model\User;

test('user relation', function () {
    $user_meta = factory(UserMeta::class)->create();

    expect($user_meta->user)->toBeInstanceOf(User::class);
});
