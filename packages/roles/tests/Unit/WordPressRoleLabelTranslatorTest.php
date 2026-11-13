<?php

declare(strict_types=1);

use Brain\Monkey\Functions;
use Meatpress\Role\Application\Services\CapabilityOwnerReader;
use Meatpress\Role\Application\Services\RoleDefinitionBuilder;
use Meatpress\Role\Application\Services\RoleRegistry;
use Meatpress\Role\Domain\Services\PostTypeCapabilityMap;
use Meatpress\Role\Infrastructure\Adapters\WordPressRoleLabelTranslator;
use Meatpress\Role\Tests\Fixtures\Usher;

beforeEach(function (): void {
    $registry = new RoleRegistry(new CapabilityOwnerReader, new PostTypeCapabilityMap);
    $registry->addRole((new RoleDefinitionBuilder(new CapabilityOwnerReader))->build(Usher::class));

    $this->translator = new WordPressRoleLabelTranslator($registry);
    Functions\when('translate')->alias(fn (string $text, string $domain): string => $domain === 'events' ? 'Placeur' : $text);
});

it("translates a declared label with the role's text domain", function (): void {
    expect($this->translator->translate('Usher', 'Usher', 'User role'))->toBe('Placeur');
});

it('keeps a translation WordPress already found, and other texts', function (): void {
    expect($this->translator->translate('Huissier', 'Usher', 'User role'))->toBe('Huissier')
        ->and($this->translator->translate('Usher', 'Usher', 'noun'))->toBe('Usher')
        ->and($this->translator->translate('Editor', 'Editor', 'User role'))->toBe('Editor');
});
