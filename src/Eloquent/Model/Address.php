<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\Eloquent\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $street
 * @property int $number
 * @property string $city
 * @property string $country
 */
class Address extends Model
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var string|null
     */
    protected $table = 'addresses';

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var bool
     */
    public $timestamps = false;
}
