<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\Propel;

use MarekSkopal\ORMBenchmark\BenchmarkInterface;
use MarekSkopal\ORMBenchmark\Propel\generated\AddressQuery;
use MarekSkopal\ORMBenchmark\Propel\generated\Map\AddressTableMap;
use MarekSkopal\ORMBenchmark\Propel\generated\Map\UserTableMap;
use MarekSkopal\ORMBenchmark\Propel\generated\User;
use MarekSkopal\ORMBenchmark\Propel\generated\UserQuery;
use MarekSkopal\ORMBenchmark\Utils\BenchmarkTime;
use Propel\Runtime\Adapter\Pdo\SqliteAdapter;
use Propel\Runtime\Connection\ConnectionManagerSingle;
use Propel\Runtime\Propel;
use Propel\Runtime\ServiceContainer\StandardServiceContainer;

class PropelBenchmark implements BenchmarkInterface
{
    public function __construct()
    {
        $serviceContainer = Propel::getServiceContainer();
        assert($serviceContainer instanceof StandardServiceContainer);
        $serviceContainer->setAdapterClass('default', SqliteAdapter::class);
        $manager = new ConnectionManagerSingle('default');
        $manager->setConfiguration([
            'dsn' => 'sqlite:' . __DIR__ . '/../../database.sqlite',
            'user' => '',
            'password' => '',
        ]);
        $serviceContainer->setConnectionManager($manager);
        $serviceContainer->initDatabaseMaps([
            'default' => [AddressTableMap::class, UserTableMap::class],
        ]);
    }

    public function selectOneRow(): float
    {
        return BenchmarkTime::measure(function (): void {
            /** @var User|null $user */
            $user = UserQuery::create()->findPk(1);
            $city = $user?->getAddress()?->getCity();
        });
    }

    public function selectOneRowThousandTimes(): float
    {
        return BenchmarkTime::measure(function (): void {
            for ($i = 0; $i < 1000; $i++) {
                UserTableMap::clearInstancePool();
                AddressTableMap::clearInstancePool();
                /** @var User|null $user */
                $user = UserQuery::create()->findPk(1);
                $city = $user?->getAddress()?->getCity();
            }
        });
    }

    public function selectAllRows(): float
    {
        return BenchmarkTime::measure(function (): void {
            /** @var iterable<User> $users */
            $users = UserQuery::create()->find();
            foreach ($users as $user) {
                $city = $user->getAddress()?->getCity();
            }
        });
    }

    public function insertOneRow(): float
    {
        /** @var \MarekSkopal\ORMBenchmark\Propel\generated\Address|null $address */
        $address = AddressQuery::create()->findPk(1);

        return BenchmarkTime::measure(function () use ($address): void {
            $user = new User();
            $user->setCreatedAt(date('Y-m-d H:i:s'));
            $user->setFirstName('John');
            $user->setMiddleName('Doe');
            $user->setLastName('Smith');
            $user->setEmail('john.dow@example.com');
            $user->setIsActive(1);
            $user->setAddress($address);
            $user->save();
        });
    }

    public function insertOneRowThousandTimes(): float
    {
        /** @var \MarekSkopal\ORMBenchmark\Propel\generated\Address|null $address */
        $address = AddressQuery::create()->findPk(1);

        return BenchmarkTime::measure(function () use ($address): void {
            for ($i = 0; $i < 1000; $i++) {
                $user = new User();
                $user->setCreatedAt(date('Y-m-d H:i:s'));
                $user->setFirstName('John');
                $user->setMiddleName('Doe');
                $user->setLastName('Smith');
                $user->setEmail('john.dow@example.com');
                $user->setIsActive(1);
                $user->setAddress($address);
                $user->save();
            }
        });
    }

    public function insertOneThousandRows(): float
    {
        /** @var \MarekSkopal\ORMBenchmark\Propel\generated\Address|null $address */
        $address = AddressQuery::create()->findPk(1);

        return BenchmarkTime::measure(function () use ($address): void {
            for ($i = 0; $i < 1000; $i++) {
                $user = new User();
                $user->setCreatedAt(date('Y-m-d H:i:s'));
                $user->setFirstName('John' . $i);
                $user->setMiddleName('Doe' . $i);
                $user->setLastName('Smith' . $i);
                $user->setEmail('john.dow@example.com' . $i);
                $user->setIsActive(1);
                $user->setAddress($address);
                $user->save();
            }
        });
    }
}
