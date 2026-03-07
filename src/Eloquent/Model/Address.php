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
    protected $table = 'addresses';

    public $timestamps = false;
}
