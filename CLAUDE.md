# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Install dependencies
composer install

# Run the benchmark (parameter is number of pre-seeded user rows)
./bin/console benchmark 100000

# Static analysis
vendor/bin/phpstan analyse
```

## Architecture

This is a PHP 8.4 CLI tool that benchmarks ORM libraries against each other using a SQLite database (`database.sqlite` in project root, created at runtime).

**Entry point:** `bin/console` bootstraps a Symfony Console `Application` with a single command: `ORMBenchmarkCommand`.

**Flow:**
1. `ORMBenchmarkCommand` seeds the SQLite database via raw PDO using `src/Database/schema.sql` (two tables: `users` and `addresses`)
2. It instantiates each benchmark class and calls all methods defined in `BenchmarkInterface`
3. Results (milliseconds) are printed as console tables

**Adding a new ORM:**
- Create a namespace directory under `src/` (e.g. `src/MyOrm/`)
- Implement `BenchmarkInterface` with all 6 benchmark methods: `selectOneRow`, `selectOneRowThousandTimes`, `selectAllRows`, `insertOneRow`, `insertOneRowThousandTimes`, `insertOneThousandRows`
- Register the class in the `$benchmarkClasses` array in `ORMBenchmarkCommand::execute()`

**Key files:**
- `src/BenchmarkInterface.php` — contract all benchmark implementations must satisfy
- `src/Utils/BenchmarkTime.php` — measures execution time in milliseconds using `hrtime`
- `src/CycleOrm/CycleOrmBenchmark.php` — Cycle ORM implementation (uses annotated entities, rebuilds schema on every `init()` call)
- `src/MarekSkopalOrm/MarekSkopalOrmBenchmark.php` — MarekSkopal ORM implementation
- `src/Database/schema.sql` — SQLite DDL, dropped and recreated on every benchmark run

Each benchmark's `init()` method creates a fresh ORM instance (no state shared between benchmark methods).
