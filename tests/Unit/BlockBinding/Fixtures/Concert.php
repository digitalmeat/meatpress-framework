<?php

declare(strict_types=1);

namespace Tests\Unit\BlockBinding\Fixtures;

use Carbon\CarbonImmutable;
use Meatpress\Attributes\Meta;
use Meatpress\Attributes\PostType;

#[PostType('concert')]
class Concert
{
    #[Meta(showInRest: true)]
    public ?CarbonImmutable $startsAt = null;

    #[Meta(showInRest: true)]
    public int $capacity = 0;

    #[Meta(showInRest: true)]
    public float $price = 0.0;

    #[Meta(showInRest: true)]
    public bool $soldOut = false;

    #[Meta(showInRest: true)]
    public ConcertStatus $status = ConcertStatus::Announced;

    #[Meta(showInRest: true, media: true)]
    public ?int $coverImageId = null;

    /** @var list<string> */
    #[Meta(showInRest: true)]
    public array $genres = [];

    #[Meta]
    public string $backstageCode = '';

    #[Meta(key: '_promoter_fee', showInRest: true, capability: 'manage_options')]
    public int $promoterFee = 0;
}
