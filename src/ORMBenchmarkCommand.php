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

final class ORMBenchmarkCommand extends Command
{
    private const int ROWS = 100000;

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
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Initializing database...');
        $this->initDb();

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

    private function initDb(): void
    {
        $pdo = new PDO('sqlite:' . __DIR__ . '/../database.sqlite');
        $schema = file_get_contents(__DIR__ . '/Database/schema.sql');
        if ($schema === false) {
            throw new \RuntimeException('Cannot read schema.sql file');
        }

        $pdo->exec($schema);

        for ($i = 0; $i < self::ROWS; $i++) {
            $stmt = $pdo->prepare(
                'INSERT INTO users (id, first_name, last_name, email, is_active) VALUES (:id, :first_name, :last_name, :email, :is_active)',
            );
            $stmt->execute([
                'id' => $i + 1,
                'first_name' => Random::generate(),
                'last_name' => Random::generate(),
                'email' => Random::generate(5) . '@' . Random::generate(5) . '.com',
                'is_active' => 1,
            ]);
        }
    }
}
