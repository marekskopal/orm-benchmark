<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark;

use MarekSkopal\ORMBenchmark\CycleOrm\CycleOrmBenchmark;
use MarekSkopal\ORMBenchmark\MarekSkopalOrm\MarekSkopalOrmBenchmark;
use Nette\Utils\Random;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use const E_ALL;
use const E_DEPRECATED;

final class ORMBenchmarkCommand extends Command
{
    private const string ARGUMENT_ROWS = 'rows';
    private const int ARGUMENT_ROWS_DEFAULT = 100000;

    /**
     * @var array<class-string, array{
     *     name: string,
     *     selectOneRow: float,
     *     selectOneRowThousandTimes: float,
     *     selectAllRows: float,
     * }> $results
     */
    private array $results = [];

    protected function configure(): void
    {
        $this
            ->setName('benchmark')
            ->setDescription('Run ORM benchmark');

        $this->addArgument(self::ARGUMENT_ROWS, null, 'Number of rows', self::ARGUMENT_ROWS_DEFAULT);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        error_reporting(E_ALL ^ E_DEPRECATED);

        $output->writeln('Initializing database...');

        /** @var string $argumentRows */
        $argumentRows = $input->getArgument(self::ARGUMENT_ROWS);
        $this->initDb((int) $argumentRows);

        $output->writeln('Running benchmark...');

        $benchmarkClasses = [
            'MarekSkopalORM' => MarekSkopalOrmBenchmark::class,
            'CycleORM' => CycleOrmBenchmark::class,
        ];

        foreach ($benchmarkClasses as $benchmarkClassName => $benchmarkClass) {
            $benchmark = new $benchmarkClass();
            $results = [
                'name' => $benchmarkClassName,
                'selectOneRow' => $benchmark->selectOneRow(),
                'selectOneRowThousandTimes' => $benchmark->selectOneRowThousandTimes(),
                'selectAllRows' => $benchmark->selectAllRows(),
            ];

            $this->results[$benchmarkClass] = $results;
        }

        $output->writeln('Results:');

        $table = new Table($output);
        $table
            ->setHeaders(['ORM', 'selectOneRow', 'selectOneRowThousandTimes', 'selectAllRows'])
            ->setRows($this->results);
        $table->render();

        return Command::SUCCESS;
    }

    private function initDb(int $userRows): void
    {
        $pdo = new PDO('sqlite:' . __DIR__ . '/../database.sqlite');
        $schema = file_get_contents(__DIR__ . '/Database/schema.sql');
        if ($schema === false) {
            throw new \RuntimeException('Cannot read schema.sql file');
        }

        $pdo->exec($schema);

        $stmt = $pdo->prepare('INSERT INTO addresses (id, street, number, city, country) VALUES (:id, :street, :number, :city, :country)');
        $stmt->execute([
            'id' => 1,
            'street' => 'Main Street',
            'number' => '123',
            'city' => 'New York',
            'country' => 'USA',
        ]);

        for ($i = 0; $i < $userRows; $i++) {
            $stmt = $pdo->prepare(
                'INSERT INTO users (id, created_at, first_name, middle_name, last_name, email, is_active, address_id) VALUES (:id, :created_at, :first_name, :middle_name, :last_name, :email, :is_active, :address_id)',
            );
            $stmt->execute([
                'id' => $i + 1,
                'created_at' => date('Y-m-d H:i:s'),
                'first_name' => Random::generate(),
                'middle_name' => null,
                'last_name' => Random::generate(),
                'email' => Random::generate(5) . '@' . Random::generate(5) . '.com',
                'is_active' => 1,
                'address_id' => 1,
            ]);
        }
    }
}
