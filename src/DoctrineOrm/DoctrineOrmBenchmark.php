<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\DoctrineOrm;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use MarekSkopal\ORMBenchmark\BenchmarkInterface;
use MarekSkopal\ORMBenchmark\DoctrineOrm\Entity\Address;
use MarekSkopal\ORMBenchmark\DoctrineOrm\Entity\User;
use MarekSkopal\ORMBenchmark\Utils\BenchmarkTime;

class DoctrineOrmBenchmark implements BenchmarkInterface
{
    private Configuration $config;
    private Connection $connection;

    public function __construct()
    {
        $this->config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '/Entity'],
            isDevMode: false,
        );
        $this->config->enableNativeLazyObjects(true);

        $this->connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../../database.sqlite',
        ]);

        // Warm up metadata cache so the first benchmark does not pay for attribute parsing
        $em = $this->createEntityManager();
        $em->getClassMetadata(User::class);
        $em->getClassMetadata(Address::class);
    }

    private function createEntityManager(): EntityManager
    {
        return new EntityManager($this->connection, $this->config);
    }

    public function selectOneRow(): float
    {
        $em = $this->createEntityManager();

        return BenchmarkTime::measure(function () use ($em): void {
            $user = $em->find(User::class, 1);
            $city = $user?->address->city;
        });
    }

    public function selectOneRowThousandTimes(): float
    {
        $em = $this->createEntityManager();

        return BenchmarkTime::measure(function () use ($em): void {
            for ($i = 0; $i < 1000; $i++) {
                $em->clear();
                $user = $em->find(User::class, 1);
                $city = $user?->address->city;
            }
        });
    }

    public function selectAllRows(): float
    {
        $em = $this->createEntityManager();

        return BenchmarkTime::measure(function () use ($em): void {
            /** @var list<User> $users */
            $users = $em->getRepository(User::class)->findAll();
            foreach ($users as $user) {
                $city = $user->address->city;
            }
        });
    }

    public function updateOneRow(): float
    {
        $em = $this->createEntityManager();
        $user = $em->find(User::class, 1);
        assert($user instanceof User);

        return BenchmarkTime::measure(function () use ($em, $user): void {
            $user->firstName = 'Updated';
            $em->flush();
        });
    }

    public function updateOneRowThousandTimes(): float
    {
        $em = $this->createEntityManager();
        $user = $em->find(User::class, 1);
        assert($user instanceof User);

        return BenchmarkTime::measure(function () use ($em, $user): void {
            for ($i = 0; $i < 1000; $i++) {
                $user->firstName = 'Updated' . $i;
                $em->flush();
            }
        });
    }

    public function insertOneRow(): float
    {
        $em = $this->createEntityManager();
        $address = $em->find(Address::class, 1);
        assert($address instanceof Address);

        return BenchmarkTime::measure(function () use ($em, $address): void {
            $user = new User();
            $user->createdAt = new DateTimeImmutable();
            $user->firstName = 'John';
            $user->middleName = 'Doe';
            $user->lastName = 'Smith';
            $user->email = 'john.dow@example.com';
            $user->isActive = true;
            $user->address = $address;

            $em->persist($user);
            $em->flush();
        });
    }

    public function insertOneRowThousandTimes(): float
    {
        $em = $this->createEntityManager();
        $address = $em->find(Address::class, 1);
        assert($address instanceof Address);

        return BenchmarkTime::measure(function () use ($em, $address): void {
            for ($i = 0; $i < 1000; $i++) {
                $user = new User();
                $user->createdAt = new DateTimeImmutable();
                $user->firstName = 'John';
                $user->middleName = 'Doe';
                $user->lastName = 'Smith';
                $user->email = 'john.dow@example.com';
                $user->isActive = true;
                $user->address = $address;

                $em->persist($user);
                $em->flush();
                $em->detach($user);
            }
        });
    }

    public function insertOneThousandRows(): float
    {
        $em = $this->createEntityManager();
        $address = $em->find(Address::class, 1);
        assert($address instanceof Address);

        return BenchmarkTime::measure(function () use ($em, $address): void {
            for ($i = 0; $i < 1000; $i++) {
                $user = new User();
                $user->createdAt = new DateTimeImmutable();
                $user->firstName = 'John' . $i;
                $user->middleName = 'Doe' . $i;
                $user->lastName = 'Smith' . $i;
                $user->email = 'john.dow@example.com' . $i;
                $user->isActive = true;
                $user->address = $address;

                $em->persist($user);
            }

            $em->flush();
        });
    }
}
