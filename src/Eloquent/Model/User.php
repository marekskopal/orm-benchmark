<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\Eloquent\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Address|null $address
 */
class User extends Model
{
    protected $table = 'users';

    public $timestamps = false;

    protected $fillable = [
        'created_at',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'is_active',
        'address_id',
    ];

    /** @return BelongsTo<Address, $this> */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
