<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\MarekSkopalOrm;

use MarekSkopal\ORM\Database\SqliteDatabase;
use MarekSkopal\ORM\ORM;
use MarekSkopal\ORM\Repository\RepositoryInterface;
use MarekSkopal\ORM\Schema\Builder\SchemaBuilder;
use MarekSkopal\ORMBenchmark\BenchmarkInterface;
use MarekSkopal\ORMBenchmark\MarekSkopalOrm\Entity\User;
use MarekSkopal\ORMBenchmark\Utils\BenchmarkTime;

final class MarekSkopalOrmBenchmark implements BenchmarkInterface
{
    public function selectOneRow(): float
    {
        $userRepository = $this->init();

        return BenchmarkTime::measure(function () use ($userRepository): void {
            $user = $userRepository->findOne(['id' => 1]);
            $address = $user?->address->city;
        });
    }

    public function selectOneRowThousandTimes(): float
    {
        $userRepository = $this->init();

        return BenchmarkTime::measure(function () use ($userRepository): void {
            for ($i = 0; $i < 1000; $i++) {
                $user = $userRepository->findOne(['id' => 1]);
                $address = $user?->address->city;
            }
        });
    }

    public function selectAllRows(): float
    {
        $userRepository = $this->init();

        return BenchmarkTime::measure(function () use ($userRepository): void {
            foreach (iterator_to_array($userRepository->findAll()) as $user) {
                $address = $user->address->city;
            }
        });
    }

    /** @return RepositoryInterface<User> */
    private function init(): RepositoryInterface
    {
        $database = new SqliteDatabase(__DIR__ . '/../../database.sqlite');

        $schema = new SchemaBuilder()
            ->addEntityPath(__DIR__ . '/Entity')
            ->build();

        $orm = new ORM($database, $schema);

        return $orm->getRepository(User::class);
    }
}
