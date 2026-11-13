<?php

declare(strict_types=1);

namespace Meatpress\Meta\Testing\Fixtures;

use Carbon\CarbonImmutable;
use Meatpress\Attributes\Meta;
use Meatpress\Attributes\SkipDiscovery;
use Meatpress\Attributes\UserMeta;
use Meatpress\Meta\Domain\Enums\Control;
use Meatpress\Meta\Domain\Enums\MetaObjectType;

/**
 * One meta of every type and control, for MetaUiDriverConformance.
 *
 * @internal
 */
#[SkipDiscovery]
#[UserMeta]
final class EveryMetaKind
{
    #[Meta(label: 'Text', group: 'Basics', hints: ['any-driver' => ['width' => 50]])]
    public string $text = '';

    #[Meta(control: Control::Textarea)]
    public string $textarea = '';

    #[Meta(sanitize: 'wp_kses_post')]
    public string $richText = '';

    #[Meta]
    public int $number = 0;

    #[Meta]
    public float $decimal = 0.0;

    #[Meta]
    public bool $toggle = false;

    #[Meta(control: Control::Date)]
    public ?CarbonImmutable $date = null;

    #[Meta]
    public ?CarbonImmutable $dateTime = null;

    #[Meta]
    public MetaObjectType $select = MetaObjectType::Post;

    #[Meta(control: Control::Media)]
    public ?int $media = null;

    #[Meta(control: Control::Url)]
    public ?string $url = null;

    #[Meta(control: Control::Email)]
    public ?string $email = null;

    #[Meta(control: Control::Color)]
    public ?string $color = null;

    /** @var list<string> */
    #[Meta(single: false)]
    public array $rows = [];

    #[Meta(items: 'int')]
    public array $list = [];
}
