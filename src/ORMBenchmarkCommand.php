<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark;

use MarekSkopal\ORMBenchmark\CycleOrm\CycleOrmBenchmark;
use MarekSkopal\ORMBenchmark\DoctrineOrm\DoctrineOrmBenchmark;
use MarekSkopal\ORMBenchmark\Eloquent\EloquentBenchmark;
use MarekSkopal\ORMBenchmark\MarekSkopalOrm\MarekSkopalOrmBenchmark;
use MarekSkopal\ORMBenchmark\Propel\PropelBenchmark;
use MarekSkopal\ORMBenchmark\RedBeanPhp\RedBeanPhpBenchmark;
use MarekSkopal\ORMBenchmark\Utils\BenchmarkStats;
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
    private const int BENCHMARK_RUNS = 5;

    /**
     * @var array<class-string, array{
     *     name: string,
     *     selectOneRow: list<float>,
     *     selectOneRowThousandTimes: list<float>,
     *     selectAllRows: list<float>,
     * }> $selectResults
     */
    private array $selectResults = [];

    /**
     * @var array<class-string, array{
     *     name: string,
     *     updateOneRow: list<float>,
     *     updateOneRowThousandTimes: list<float>,
     * }> $updateResults
     */
    private array $updateResults = [];

    /**
     * @var array<class-string, array{
     *     name: string,
     *     insertOneRow: list<float>,
     *     insertOneRowThousandTimes: list<float>,
     *     insertOneThousandRows: list<float>,
     * }> $insertResults
     */
    private array $insertResults = [];

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

        $this->printEnvironment($output);

        $output->writeln('Initializing database...');

        /** @var string $argumentRows */
        $argumentRows = $input->getArgument(self::ARGUMENT_ROWS);
        $this->initDb((int) $argumentRows);

        $output->writeln('Running benchmark (' . self::BENCHMARK_RUNS . ' runs each)...');

        $benchmarkClasses = [
            'MarekSkopalORM' => MarekSkopalOrmBenchmark::class,
            'CycleORM' => CycleOrmBenchmark::class,
            'DoctrineORM' => DoctrineOrmBenchmark::class,
            'Eloquent' => EloquentBenchmark::class,
            'Propel' => PropelBenchmark::class,
            'RedBeanPHP' => RedBeanPhpBenchmark::class,
        ];

        foreach ($benchmarkClasses as $benchmarkClassName => $benchmarkClass) {
            $output->writeln('  ' . $benchmarkClassName . '...');

            $benchmark = new $benchmarkClass();

            $selectResults = [
                'name' => $benchmarkClassName,
                'selectOneRow' => [],
                'selectOneRowThousandTimes' => [],
                'selectAllRows' => [],
            ];
            $updateResults = [
                'name' => $benchmarkClassName,
                'updateOneRow' => [],
                'updateOneRowThousandTimes' => [],
            ];
            $insertResults = [
                'name' => $benchmarkClassName,
                'insertOneRow' => [],
                'insertOneRowThousandTimes' => [],
                'insertOneThousandRows' => [],
            ];

            for ($run = 0; $run < self::BENCHMARK_RUNS; $run++) {
                $selectResults['selectOneRow'][] = $benchmark->selectOneRow();
                $selectResults['selectOneRowThousandTimes'][] = $benchmark->selectOneRowThousandTimes();
                $selectResults['selectAllRows'][] = $benchmark->selectAllRows();
                $updateResults['updateOneRow'][] = $benchmark->updateOneRow();
                $updateResults['updateOneRowThousandTimes'][] = $benchmark->updateOneRowThousandTimes();
                $insertResults['insertOneRow'][] = $benchmark->insertOneRow();
                $insertResults['insertOneRowThousandTimes'][] = $benchmark->insertOneRowThousandTimes();
                $insertResults['insertOneThousandRows'][] = $benchmark->insertOneThousandRows();
            }

            $this->selectResults[$benchmarkClass] = $selectResults;
            $this->updateResults[$benchmarkClass] = $updateResults;
            $this->insertResults[$benchmarkClass] = $insertResults;
        }

        $output->writeln('');
        $output->writeln('Results (median ±stddev, ms, ' . self::BENCHMARK_RUNS . ' runs):');

        $table = new Table($output);
        $table
            ->setHeaders(['ORM', 'selectOneRow', 'selectOneRowThousandTimes', 'selectAllRows'])
            ->setRows(array_map(
                fn(array $r): array => [
                    $r['name'],
                    BenchmarkStats::format($r['selectOneRow']),
                    BenchmarkStats::format($r['selectOneRowThousandTimes']),
                    BenchmarkStats::format($r['selectAllRows']),
                ],
                $this->selectResults,
            ));
        $table->render();

        $table = new Table($output);
        $table
            ->setHeaders(['ORM', 'updateOneRow', 'updateOneRowThousandTimes'])
            ->setRows(array_map(
                fn(array $r): array => [
                    $r['name'],
                    BenchmarkStats::format($r['updateOneRow']),
                    BenchmarkStats::format($r['updateOneRowThousandTimes']),
                ],
                $this->updateResults,
            ));
        $table->render();

        $table = new Table($output);
        $table
            ->setHeaders(['ORM', 'insertOneRow', 'insertOneRowThousandTimes', 'insertOneThousandRows'])
            ->setRows(array_map(
                fn(array $r): array => [
                    $r['name'],
                    BenchmarkStats::format($r['insertOneRow']),
                    BenchmarkStats::format($r['insertOneRowThousandTimes']),
                    BenchmarkStats::format($r['insertOneThousandRows']),
                ],
                $this->insertResults,
            ));
        $table->render();

        return Command::SUCCESS;
    }

    private function printEnvironment(OutputInterface $output): void
    {
        $opcache = function_exists('opcache_get_status')
            ? ((opcache_get_status(false)['opcache_enabled'] ?? false) ? 'enabled' : 'disabled')
            : 'n/a';

        $cpu = trim((string) shell_exec('sysctl -n machdep.cpu.brand_string 2>/dev/null'));
        if ($cpu === '') {
            $cpuLine = (string) shell_exec('grep -m1 "model name" /proc/cpuinfo 2>/dev/null');
            $cpu = trim(explode(':', $cpuLine)[1] ?? 'unknown');
        }

        $output->writeln('Environment:');
        $output->writeln('  PHP:     ' . PHP_VERSION);
        $output->writeln('  OPcache: ' . $opcache);
        $output->writeln('  OS:      ' . php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('m'));
        $output->writeln('  CPU:     ' . ($cpu !== '' ? $cpu : 'unknown'));
        $output->writeln('  Runs:    ' . self::BENCHMARK_RUNS . ' per method');
        $output->writeln('');
    }

    private function initDb(int $userRows): void
    {
        $pdo = new PDO('sqlite:' . __DIR__ . '/../database.sqlite');
        $schema = file_get_contents(__DIR__ . '/Database/schema.sql');
        if ($schema === false) {
            throw new \RuntimeException('Cannot read schema.sql file');
        }

        $pdo->exec($schema);

        $pdo->exec('INSERT INTO addresses (id, street, number, city, country) VALUES (1, \'Main Street\', 123, \'New York\', \'USA\')');

        $stmt = $pdo->prepare(
            'INSERT INTO users (id, created_at, first_name, middle_name, last_name, email, is_active, address_id) VALUES (:id, :created_at, :first_name, :middle_name, :last_name, :email, :is_active, :address_id)',
        );

        $pdo->beginTransaction();
        for ($i = 0; $i < $userRows; $i++) {
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
        $pdo->commit();
    }
}
