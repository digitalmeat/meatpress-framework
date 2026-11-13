<?php

declare(strict_types=1);

namespace Meatpress\Query\Traits;

use Meatpress\Query\QueryException;
use Meatpress\Query\Traits\Concerns\ValidatesChoices;

trait Permission
{
    use ValidatesChoices;

    protected ?string $perm = null;

    /**
     * @throws QueryException If the value is not 'readable' or 'editable'.
     */
    public function userPermission(string $value): self
    {
        $this->perm = $this->assertOneOf($value, ['readable', 'editable'], 'permission');

        return $this;
    }
}
