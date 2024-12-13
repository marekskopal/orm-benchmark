<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\CycleOrm;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Config\SQLite\FileConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;
use Cycle\Database\DatabaseManager;
use Cycle\ORM\EntityManager;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\ForeignKeys;
use Cycle\Schema\Generator\GenerateModifiers;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderModifiers;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use DateTimeImmutable;
use MarekSkopal\ORMBenchmark\BenchmarkInterface;
use MarekSkopal\ORMBenchmark\CycleOrm\Entity\Address;
use MarekSkopal\ORMBenchmark\CycleOrm\Entity\User;
use MarekSkopal\ORMBenchmark\Utils\BenchmarkTime;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

class CycleOrmBenchmark implements BenchmarkInterface
{
    private DatabaseManager $dbal;

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
            foreach ($userRepository->findAll() as $user) {
                $address = $user->address->city;
            }
        });
    }

    public function insertOneRow(): float
    {
        $orm = $this->init();
        $addressRepository = $orm->getRepository(Address::class);

        $manager = new EntityManager($orm);

        $address = $addressRepository->findOne(['id' => 1]);
        assert($address instanceof Address);

        return BenchmarkTime::measure(function () use ($manager, $address): void {
            $user = new User(
                createdAt: new DateTimeImmutable(),
                firstName: 'John',
                middleName: 'Doe',
                lastName: 'Smith',
                email: 'john.dow@example.com',
                isActive: true,
                address: $address,
            );

            $manager->persist($user);
            $manager->run();
        });
    }

    public function insertOneRowThousandTimes(): float
    {
        $orm = $this->init();
        $addressRepository = $orm->getRepository(Address::class);

        $manager = new EntityManager($orm);

        $address = $addressRepository->findOne(['id' => 1]);
        assert($address instanceof Address);

        return BenchmarkTime::measure(function () use ($manager, $address): void {
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

                $manager->persist($user);
                $manager->run();
            }
        });
    }

    public function insertOneThousandRows(): float
    {
        $orm = $this->init();
        $addressRepository = $orm->getRepository(Address::class);

        $manager = new EntityManager($orm);

        $address = $addressRepository->findOne(['id' => 1]);
        assert($address instanceof Address);

        return BenchmarkTime::measure(function () use ($manager, $address): void {
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

                $manager->persist($user);
                $manager->run();
            }
        });
    }

    private function init(): ORM
    {
        if (!isset($this->dbal)) {
            $this->dbal = new DatabaseManager(
                new DatabaseConfig([
                    'default' => 'default',
                    'databases' => [
                        'default' => [
                            'connection' => 'sqlite',
                        ],
                    ],
                    'connections' => [
                        'sqlite' => new SQLiteDriverConfig(
                            connection: new FileConnectionConfig(
                                __DIR__ . '/../../database.sqlite',
                            ),
                        ),
                    ],
                ]),
            );
        }

        $registry = new Registry($this->dbal);

        $classLocator = (new Tokenizer(new TokenizerConfig([
            'directories' => [
                __DIR__ . '/Entity',
            ],
        ])))->classLocator();

        $schema = (new Compiler())->compile($registry, [
            // Reconfigure table schemas (deletes columns if necessary)
            new ResetTables(),
            // Recognize embeddable entities
            new Embeddings(new TokenizerEmbeddingLocator($classLocator)),
            // Identify attributed entities
            new Entities(new TokenizerEntityLocator($classLocator)),
            // Setup Single Table or Joined Table Inheritance
            new TableInheritance(),
            // Integrate table #[Column] attributes
            new MergeColumns(),
            // Define entity relationships
            new GenerateRelations(),
            // Apply schema modifications
            new GenerateModifiers(),
            // Ensure entity schemas adhere to conventions
            new ValidateEntities(),
            // Create table schemas
            new RenderTables(),
            // Establish keys and indexes for relationships
            new RenderRelations(),
            // Implement schema modifications
            new RenderModifiers(),
            // Define foreign key constraints
            new ForeignKeys(),
            // Merge table index attributes
            new MergeIndexes(),
            // Typecast non-string columns
            new GenerateTypecast(),
        ]);

        return new ORM(new Factory($this->dbal), new Schema($schema));
    }
}
