<?php

declare(strict_types=1);

use Meatpress\Harness\Pest\Architecture;

/*
 * Architecture rules of the Meatpress harness (ARCH-*, CONV-*).
 *
 * The rules follow the profile in .harness.json and the layers found under
 * the PSR-4 roots: nothing to maintain here. List them with
 * `vendor/bin/meatpress-qa rules`.
 */

Architecture::for(dirname(__DIR__))->register();
