<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\Eloquent;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;
use MarekSkopal\ORMBenchmark\BenchmarkInterface;
use MarekSkopal\ORMBenchmark\Eloquent\Model\User;
use MarekSkopal\ORMBenchmark\Utils\BenchmarkTime;

class EloquentBenchmark implements BenchmarkInterface
{
    private Connection $connection;

    public function __construct()
    {
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => __DIR__ . '/../../database.sqlite',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $this->connection = $capsule->getConnection();
    }

    public function selectOneRow(): float
    {
        return BenchmarkTime::measure(function (): void {
            $user = User::with('address')->find(1);
            $city = $user?->address?->city;
        });
    }

    public function selectOneRowThousandTimes(): float
    {
        return BenchmarkTime::measure(function (): void {
            for ($i = 0; $i < 1000; $i++) {
                $user = User::with('address')->find(1);
                $city = $user?->address?->city;
            }
        });
    }

    public function selectAllRows(): float
    {
        return BenchmarkTime::measure(function (): void {
            $users = User::with('address')->get();
            foreach ($users as $user) {
                $city = $user->address?->city;
            }
        });
    }

    public function updateOneRow(): float
    {
        $user = User::find(1);
        assert($user instanceof User);

        return BenchmarkTime::measure(function () use ($user): void {
            $user->first_name = 'Updated';
            $user->save();
        });
    }

    public function updateOneRowThousandTimes(): float
    {
        $user = User::find(1);
        assert($user instanceof User);

        return BenchmarkTime::measure(function () use ($user): void {
            for ($i = 0; $i < 1000; $i++) {
                $user->first_name = 'Updated' . $i;
                $user->save();
            }
        });
    }

    public function insertOneRow(): float
    {
        return BenchmarkTime::measure(function (): void {
            $user = new User([
                'created_at' => date('Y-m-d H:i:s'),
                'first_name' => 'John',
                'middle_name' => 'Doe',
                'last_name' => 'Smith',
                'email' => 'john.dow@example.com',
                'is_active' => 1,
                'address_id' => 1,
            ]);
            $user->save();
        });
    }

    public function insertOneRowThousandTimes(): float
    {
        return BenchmarkTime::measure(function (): void {
            for ($i = 0; $i < 1000; $i++) {
                $user = new User([
                    'created_at' => date('Y-m-d H:i:s'),
                    'first_name' => 'John',
                    'middle_name' => 'Doe',
                    'last_name' => 'Smith',
                    'email' => 'john.dow@example.com',
                    'is_active' => 1,
                    'address_id' => 1,
                ]);
                $user->save();
            }
        });
    }

    public function insertOneThousandRows(): float
    {
        return BenchmarkTime::measure(function (): void {
            $this->connection->beginTransaction();

            for ($i = 0; $i < 1000; $i++) {
                $user = new User([
                    'created_at' => date('Y-m-d H:i:s'),
                    'first_name' => 'John' . $i,
                    'middle_name' => 'Doe' . $i,
                    'last_name' => 'Smith' . $i,
                    'email' => 'john.dow@example.com' . $i,
                    'is_active' => 1,
                    'address_id' => 1,
                ]);
                $user->save();
            }

            $this->connection->commit();
        });
    }
}
