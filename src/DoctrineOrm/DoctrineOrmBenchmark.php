<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\DoctrineOrm;

use DateTimeImmutable;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use MarekSkopal\ORMBenchmark\BenchmarkInterface;
use MarekSkopal\ORMBenchmark\DoctrineOrm\Entity\Address;
use MarekSkopal\ORMBenchmark\DoctrineOrm\Entity\User;
use MarekSkopal\ORMBenchmark\Utils\BenchmarkTime;

class DoctrineOrmBenchmark implements BenchmarkInterface
{
    public function selectOneRow(): float
    {
        $em = $this->init();

        return BenchmarkTime::measure(function () use ($em): void {
            $user = $em->find(User::class, 1);
            $city = $user?->address->city;
        });
    }

    public function selectOneRowThousandTimes(): float
    {
        $em = $this->init();

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
        $em = $this->init();

        return BenchmarkTime::measure(function () use ($em): void {
            /** @var list<User> $users */
            $users = $em->getRepository(User::class)->findAll();
            foreach ($users as $user) {
                $city = $user->address->city;
            }
        });
    }

    public function insertOneRow(): float
    {
        $em = $this->init();
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
        $em = $this->init();
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
            }
        });
    }

    public function insertOneThousandRows(): float
    {
        $em = $this->init();
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
                $em->flush();
            }
        });
    }

    private function init(): EntityManager
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '/Entity'],
            isDevMode: true,
        );
        $config->enableNativeLazyObjects(true);

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../../database.sqlite',
        ]);

        return new EntityManager($connection, $config);
    }
}
