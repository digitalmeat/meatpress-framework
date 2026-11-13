<?php

declare(strict_types=1);

use Meatpress\Colt\Tests\TestCase;
use Meatpress\Colt\Tests\WordPress\TestCase as WordPressTestCase;

/*
|--------------------------------------------------------------------------
| Test Cases
|--------------------------------------------------------------------------
|
| Unit tests run against in-memory SQLite databases. The WordPress suite runs
| against a real WordPress installation (composer test:wordpress).
|
*/

pest()->extend(TestCase::class)->in('Unit');

pest()->extend(WordPressTestCase::class)->in('WordPress');
