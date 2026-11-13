<?php

declare(strict_types=1);

namespace Meatpress\Doctor\Domain\Contracts;

use Meatpress\Doctor\Domain\Enums\RunContext;
use Meatpress\Doctor\Domain\Models\CheckResult;

/**
 * One thing `meatpress:doctor` and Site Health look at.
 *
 * A check earns its place by naming a failure that has happened and stays silent
 * — the site renders, the command exits 0 — and by saying how to fix it. It
 * reads; it never writes, and never requests the site over HTTP.
 */
interface CheckInterface
{
    /** Stable identifier, kebab-case: the key of the JSON output and of the Site Health test. */
    public function id(): string;

    /** What is checked, for a person. */
    public function label(): string;

    /**
     * Where the check can reach a verdict it can stand behind.
     *
     * @return list<RunContext>
     */
    public function runsIn(): array;

    public function run(RunContext $context): CheckResult;
}
