<?php

declare(strict_types=1);

use Meatpress\VersionCheck\Domain\Contracts\VersionCheckerInterface;
use Meatpress\VersionCheck\Domain\Services\VersionComparator;
use Meatpress\VersionCheck\UI\Http\SiteHealthCheck;

describe('SiteHealthCheck', function (): void {
    it('adds Meatpress section to debug information', function (): void {
        $checker = Mockery::mock(VersionCheckerInterface::class);
        $checker->shouldReceive('getCurrentVersion')->andReturn('13.3.0');
        $checker->shouldReceive('getLatestVersion')->andReturn('13.3.0');

        $health = new SiteHealthCheck(new VersionComparator($checker));
        $info = $health->addDebugInfo([]);

        expect($info)->toHaveKey('meatpress');
        expect($info['meatpress']['label'])->toBe('Meatpress');
        expect($info['meatpress']['fields']['version']['value'])->toBe('13.3.0');
        expect($info['meatpress']['fields']['latest_version']['value'])->toBe('13.3.0');
        expect($info['meatpress']['fields']['up_to_date']['value'])->toBe('Yes');
    });

    it('shows not up to date when update available', function (): void {
        $checker = Mockery::mock(VersionCheckerInterface::class);
        $checker->shouldReceive('getCurrentVersion')->andReturn('13.2.0');
        $checker->shouldReceive('getLatestVersion')->andReturn('13.3.0');

        $health = new SiteHealthCheck(new VersionComparator($checker));
        $info = $health->addDebugInfo([]);

        expect($info['meatpress']['fields']['up_to_date']['value'])->toBe('No');
    });

    it('adds version test to site status tests', function (): void {
        $checker = Mockery::mock(VersionCheckerInterface::class);

        $health = new SiteHealthCheck(new VersionComparator($checker));
        $tests = $health->addTests([]);

        expect($tests['direct'])->toHaveKey('meatpress_update');
        expect($tests['direct']['meatpress_update']['test'])->toBeCallable();
    });

    it('returns good status when up to date', function (): void {
        $checker = Mockery::mock(VersionCheckerInterface::class);
        $checker->shouldReceive('getCurrentVersion')->andReturn('13.3.0');
        $checker->shouldReceive('getLatestVersion')->andReturn('13.3.0');

        $health = new SiteHealthCheck(new VersionComparator($checker));
        $result = $health->testVersionStatus();

        expect($result['status'])->toBe('good');
        expect($result['badge']['color'])->toBe('blue');
    });

    it('returns recommended status when update available', function (): void {
        $checker = Mockery::mock(VersionCheckerInterface::class);
        $checker->shouldReceive('getCurrentVersion')->andReturn('13.2.0');
        $checker->shouldReceive('getLatestVersion')->andReturn('13.3.0');

        $health = new SiteHealthCheck(new VersionComparator($checker));
        $result = $health->testVersionStatus();

        expect($result['status'])->toBe('recommended');
        expect($result['badge']['color'])->toBe('orange');
        expect($result['label'])->toContain('13.3.0');
    });

    it('returns good status for a development build', function (): void {
        $checker = Mockery::mock(VersionCheckerInterface::class);
        $checker->shouldReceive('getCurrentVersion')->andReturn('dev-develop');
        $checker->shouldReceive('getLatestVersion')->andReturn('13.4.4');

        $health = new SiteHealthCheck(new VersionComparator($checker));
        $result = $health->testVersionStatus();

        expect($result['status'])->toBe('good');
        expect($result['label'])->not->toContain('13.4.4');
    });

    it('returns recommended status when version cannot be determined', function (): void {
        $checker = Mockery::mock(VersionCheckerInterface::class);
        $checker->shouldReceive('getCurrentVersion')->andReturn(null);
        $checker->shouldReceive('getLatestVersion')->andReturn(null);

        $health = new SiteHealthCheck(new VersionComparator($checker));
        $result = $health->testVersionStatus();

        expect($result['status'])->toBe('recommended');
    });
});
