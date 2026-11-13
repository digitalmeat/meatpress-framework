<?php

declare(strict_types=1);

use Meatpress\WordPress\AppUrl;

describe('normalize', function (): void {
    it('adds http:// to a URL without a scheme', function (string $given, string $expected): void {
        expect(AppUrl::normalize($given))->toBe($expected);
    })->with([
        'a host' => ['meatpress.test', 'http://meatpress.test'],
        'a host and a port' => ['localhost:8888', 'http://localhost:8888'],
        'a host with a path' => ['meatpress.test/site', 'http://meatpress.test/site'],
        'a protocol-relative URL' => ['//meatpress.test', 'http://meatpress.test'],
        'spaces around' => ['  meatpress.test ', 'http://meatpress.test'],
    ]);

    it('leaves a URL that has a scheme alone', function (string $url): void {
        expect(AppUrl::normalize($url))->toBe($url);
    })->with(['http://meatpress.test', 'https://meatpress.test', 'https://meatpress.test/', 'HTTPS://meatpress.test']);

    it('leaves an empty value alone', function (): void {
        expect(AppUrl::normalize(''))->toBe('');
    });
});

describe('host', function (): void {
    it('finds the host with and without a scheme', function (string $url): void {
        expect(AppUrl::host($url))->toBe('meatpress.test');
    })->with(['meatpress.test', 'http://meatpress.test', 'https://meatpress.test:8443/cms', 'meatpress.test:8000']);

    it('has no host for an empty value', function (): void {
        expect(AppUrl::host(''))->toBeNull();
    });
});
