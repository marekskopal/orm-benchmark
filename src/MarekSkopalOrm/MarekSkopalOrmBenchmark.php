<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\MarekSkopalOrm;

use DateTimeImmutable;
use MarekSkopal\ORM\Database\SqliteDatabase;
use MarekSkopal\ORM\ORM;
use MarekSkopal\ORM\Schema\Builder\SchemaBuilder;
use MarekSkopal\ORMBenchmark\BenchmarkInterface;
use MarekSkopal\ORMBenchmark\MarekSkopalOrm\Entity\Address;
use MarekSkopal\ORMBenchmark\MarekSkopalOrm\Entity\User;
use MarekSkopal\ORMBenchmark\Utils\BenchmarkTime;

final class MarekSkopalOrmBenchmark implements BenchmarkInterface
{
    public function selectOneRow(): float
    {
        $orm = $this->init();
        $userRepository = $orm->getRepository(User::class);

        return BenchmarkTime::measure(function () use ($userRepository): void {
            $user = $userRepository->findOne(['id' => 1]);
            $address = $user?->address->city;
        });
    }

    public function selectOneRowThousandTimes(): float
    {
        $orm = $this->init();
        $userRepository = $orm->getRepository(User::class);

        return BenchmarkTime::measure(function () use ($userRepository): void {
            for ($i = 0; $i < 1000; $i++) {
                $user = $userRepository->findOne(['id' => 1]);
                $address = $user?->address->city;
            }
        });
    }

    public function selectAllRows(): float
    {
        $orm = $this->init();
        $userRepository = $orm->getRepository(User::class);

        return BenchmarkTime::measure(function () use ($userRepository): void {
            foreach (iterator_to_array($userRepository->findAll()) as $user) {
                $address = $user->address->city;
            }
        });
    }

    public function insertOneRow(): float
    {
        $orm = $this->init();
        $addressRepository = $orm->getRepository(Address::class);
        $userRepository = $orm->getRepository(User::class);

        $address = $addressRepository->findOne(['id' => 1]);
        assert($address instanceof Address);

        return BenchmarkTime::measure(function () use ($userRepository, $address): void {
            $user = new User(
                createdAt: new DateTimeImmutable(),
                firstName: 'John',
                middleName: 'Doe',
                lastName: 'Smith',
                email: 'john.dow@example.com',
                isActive: true,
                address: $address,
            );

            $userRepository->persist($user);
        });
    }

    public function insertOneRowThousandTimes(): float
    {
        $orm = $this->init();
        $addressRepository = $orm->getRepository(Address::class);
        $userRepository = $orm->getRepository(User::class);

        $address = $addressRepository->findOne(['id' => 1]);
        assert($address instanceof Address);

        return BenchmarkTime::measure(function () use ($userRepository, $address): void {
            for ($i = 0; $i < 1000; $i++) {
                $user = new User(
                    createdAt: new DateTimeImmutable(),
                    firstName: 'John',
                    middleName: 'Doe',
                    lastName: 'Smith',
                    email: 'john.dow@example.com',
                    isActive: true,
                    address: $address,
                );

                $userRepository->persist($user);
            }
        });
    }

    public function insertOneThousandRows(): float
    {
        $orm = $this->init();
        $addressRepository = $orm->getRepository(Address::class);
        $userRepository = $orm->getRepository(User::class);

        $address = $addressRepository->findOne(['id' => 1]);
        assert($address instanceof Address);

        return BenchmarkTime::measure(function () use ($userRepository, $address): void {
            for ($i = 0; $i < 1000; $i++) {
                $user = new User(
                    createdAt: new DateTimeImmutable(),
                    firstName: 'John' . $i,
                    middleName: 'Doe' . $i,
                    lastName: 'Smith' . $i,
                    email: 'john.dow@example.com' . $i,
                    isActive: true,
                    address: $address,
                );

                $userRepository->persist($user);
            }
        });
    }

    private function init(): ORM
    {
        $database = new SqliteDatabase(__DIR__ . '/../../database.sqlite');

        $schema = new SchemaBuilder()
            ->addEntityPath(__DIR__ . '/Entity')
            ->build();

        return new ORM($database, $schema);
    }
}
