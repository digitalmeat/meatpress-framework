<?php

declare(strict_types=1);

use Meatpress\Colt\Model\Comment;
use Meatpress\Colt\Model\Meta\CommentMeta;

test('comment relation', function () {
    $comment = factory(Comment::class)->create();
    $comment_meta = factory(CommentMeta::class)->create(['comment_id' => $comment->comment_ID]);

    expect($comment_meta->comment)->toBeInstanceOf(Comment::class);
});
