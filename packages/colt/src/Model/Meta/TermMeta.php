<?php

declare(strict_types=1);

namespace Meatpress\Colt\Model\Meta;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Meatpress\Colt\Model\Term;

/**
 * Class TermMeta
 */
class TermMeta extends Meta
{
    /** @var string */
    protected $table = 'termmeta';

    /** @var string */
    protected $primaryKey = 'meta_id';

    /** @var array */
    protected $fillable = ['meta_key', 'meta_value', 'term_id'];

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
