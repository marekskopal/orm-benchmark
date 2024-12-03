<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\MarekSkopalOrm;

use MarekSkopal\ORM\Database\SqliteDatabase;
use MarekSkopal\ORM\ORM;
use MarekSkopal\ORM\Repository\RepositoryInterface;
use MarekSkopal\ORM\Schema\Schema;
use MarekSkopal\ORMBenchmark\BenchmarkInterface;
use MarekSkopal\ORMBenchmark\MarekSkopalOrm\Entity\Address;
use MarekSkopal\ORMBenchmark\MarekSkopalOrm\Entity\User;
use MarekSkopal\ORMBenchmark\MarekSkopalOrm\Schema\AddressEntitySchema;
use MarekSkopal\ORMBenchmark\MarekSkopalOrm\Schema\UserEntitySchema;
use MarekSkopal\ORMBenchmark\Utils\BenchmarkTime;

final class MarekSkopalOrmBenchmark implements BenchmarkInterface
{
    public function selectOneRow(): float
    {
        $userRepository = $this->init();

        return BenchmarkTime::measure(function () use ($userRepository): void {
            $user = $userRepository->findOne(['id' => 1]);
            $address = $user->address->city;
        });
    }

    public function selectOneRowThousandTimes(): float
    {
        $userRepository = $this->init();

        return BenchmarkTime::measure(function () use ($userRepository): void {
            for ($i = 0; $i < 1000; $i++) {
                $user = $userRepository->findOne(['id' => 1]);
                $address = $user->address->city;
            }
        });
    }

    public function selectAllRows(): float
    {
        $userRepository = $this->init();

        return BenchmarkTime::measure(function () use ($userRepository): void {
            foreach (iterator_to_array($userRepository->find()) as $user) {
                $address = $user->address->city;
            };
        });
    }

    /** @return RepositoryInterface<User> */
    private function init(): RepositoryInterface
    {
        $database = new SqliteDatabase(__DIR__ . '/../../database.sqlite');

        $orm = new ORM($database, new Schema([
            User::class => UserEntitySchema::create(),
            Address::class => AddressEntitySchema::create(),
        ]));

        return $orm->getRepository(User::class);
    }
}
