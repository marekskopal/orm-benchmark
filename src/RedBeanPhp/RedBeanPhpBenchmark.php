<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\RedBeanPhp;

use MarekSkopal\ORMBenchmark\BenchmarkInterface;
use MarekSkopal\ORMBenchmark\Utils\BenchmarkTime;
use RedBeanPHP\Facade as R;

class RedBeanPhpBenchmark implements BenchmarkInterface
{
    public function __construct()
    {
        R::setup('sqlite:' . __DIR__ . '/../../database.sqlite');
        R::freeze(true);
    }

    public function selectOneRow(): float
    {
        return BenchmarkTime::measure(function (): void {
            $user = R::load('users', 1);
            $address = R::load('addresses', (int) $user->address_id);
            $city = $address->city;
        });
    }

    public function selectOneRowThousandTimes(): float
    {
        return BenchmarkTime::measure(function (): void {
            for ($i = 0; $i < 1000; $i++) {
                $user = R::load('users', 1);
                $address = R::load('addresses', (int) $user->address_id);
                $city = $address->city;
            }
        });
    }

    public function selectAllRows(): float
    {
        return BenchmarkTime::measure(function (): void {
            $users = R::findAll('users');
            foreach ($users as $user) {
                $address = R::load('addresses', (int) $user->address_id);
                $city = $address->city;
            }
        });
    }

    public function updateOneRow(): float
    {
        $user = R::load('users', 1);

        return BenchmarkTime::measure(function () use ($user): void {
            $user->first_name = 'Updated';
            R::store($user);
        });
    }

    public function updateOneRowThousandTimes(): float
    {
        $user = R::load('users', 1);

        return BenchmarkTime::measure(function () use ($user): void {
            for ($i = 0; $i < 1000; $i++) {
                $user->first_name = 'Updated' . $i;
                R::store($user);
            }
        });
    }

    public function insertOneRow(): float
    {
        return BenchmarkTime::measure(function (): void {
            $user = R::dispense('users');
            $user->created_at = date('Y-m-d H:i:s');
            $user->first_name = 'John';
            $user->middle_name = 'Doe';
            $user->last_name = 'Smith';
            $user->email = 'john.dow@example.com';
            $user->is_active = 1;
            $user->address_id = 1;
            R::store($user);
        });
    }

    public function insertOneRowThousandTimes(): float
    {
        return BenchmarkTime::measure(function (): void {
            for ($i = 0; $i < 1000; $i++) {
                $user = R::dispense('users');
                $user->created_at = date('Y-m-d H:i:s');
                $user->first_name = 'John';
                $user->middle_name = 'Doe';
                $user->last_name = 'Smith';
                $user->email = 'john.dow@example.com';
                $user->is_active = 1;
                $user->address_id = 1;
                R::store($user);
            }
        });
    }

    public function insertOneThousandRows(): float
    {
        return BenchmarkTime::measure(function (): void {
            R::begin();

            for ($i = 0; $i < 1000; $i++) {
                $user = R::dispense('users');
                $user->created_at = date('Y-m-d H:i:s');
                $user->first_name = 'John' . $i;
                $user->middle_name = 'Doe' . $i;
                $user->last_name = 'Smith' . $i;
                $user->email = 'john.dow@example.com' . $i;
                $user->is_active = 1;
                $user->address_id = 1;
                R::store($user);
            }

            R::commit();
        });
    }
}
