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
use MarekSkopal\ORMBenchmark\BenchmarkInterface;
use MarekSkopal\ORMBenchmark\CycleOrm\Entity\User;
use MarekSkopal\ORMBenchmark\CycleOrm\Repository\UserRepository;
use MarekSkopal\ORMBenchmark\Utils\BenchmarkTime;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

class CycleOrmBenchmark implements BenchmarkInterface
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
            foreach ($userRepository->findAll() as $user) {
                $address = $user->address->city;
            }
        });
    }

    private function init(): UserRepository
    {
        $dbal = new DatabaseManager(
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

        $registry = new Registry($dbal);

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

        $orm = new ORM(new Factory($dbal), new Schema($schema));

        //@phpstan-ignore-next-line
        return $orm->getRepository(User::class);
    }
}
