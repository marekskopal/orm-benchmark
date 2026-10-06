# ORM Benchmark

Benchmark for ORM libraries.

Main goal is to compare performance of different ORM libraries specially in comparision with my own ORM library.

## Comparated Libraries
* [MarekSkopal ORM](https://github.com/marekskopal/orm)
* [Cycle ORM](https://cycle-orm.dev/)
* [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html)
* [Eloquent](https://laravel.com/docs/eloquent)
* [Propel](http://propelorm.org/)
* [RedBeanPHP](https://redbeanphp.com/)

## Installation

Clone this repository and install dependencies via Composer:

```bash
composer install
```

## Usage

Run benchmark:

```bash
./bin/console benchmark 100000
```

Parameter `100000` is number of precreated entities to be tested with selects.

## Methodology

### What is measured

Each benchmark method measures **only the ORM operation itself** — no ORM initialization, schema compilation, connection setup, or metadata loading is included in the measured time. All such one-time setup costs happen in the class constructor before any timing begins.

Every benchmark method runs against a freshly created ORM instance (empty identity map) so that results from one method cannot influence the next.

Each method is run **5 times** and results are reported as **median ± standard deviation** to reduce noise from OS scheduling and CPU frequency scaling.

### Benchmarked operations

| Method | Description |
| --- | --- |
| `selectOneRow` | Fetch a single user by primary key including its related address |
| `selectOneRowThousandTimes` | Repeat the same single-row fetch 1,000 times; ORM identity map / instance pool is cleared before each iteration to force real database queries |
| `selectAllRows` | Fetch all rows from the users table including related addresses |
| `updateOneRow` | Fetch a single user, change a field, and persist the update |
| `updateOneRowThousandTimes` | Fetch a user once, then update and persist the same entity 1,000 times |
| `insertOneRow` | Insert one user row and persist it to the database |
| `insertOneRowThousandTimes` | Insert and persist 1,000 user rows one by one (1,000 separate commits) |
| `insertOneThousandRows` | Insert 1,000 user rows in a single transaction / batch flush |

### Database

SQLite file database seeded with the configured number of user rows (default 100,000) and one shared address row. The schema is recreated from scratch before each run. Seeding runs inside a single transaction. Before every run the users table is reset back to the seeded row count, so every ORM and every run operates on a table of identical size regardless of rows inserted by previous benchmarks.

### Timing

PHP `hrtime()` is used for nanosecond-precision wall-clock measurement. Results are reported in milliseconds.

## Results

Environment: PHP 8.5.11, OPcache disabled, Apple M1 Max, macOS, 100,000 pre-seeded rows, 5 runs per method. All times in milliseconds (median ±stddev).

Select:

| ORM            | Version    | selectOneRow  | selectOneRowThousandTimes | selectAllRows      |
| -------------- | ---------- | ------------: | ------------------------: | -----------------: |
| MarekSkopalORM | v2.0.1     | 0.485 ±0.823  | 46.010 ±14.516            | 315.182 ±28.008    |
| CycleORM       | v2.18.0    | 0.601 ±12.406 | 94.692 ±6.150             | 1860.790 ±38.826   |
| DoctrineORM    | 3.6.7      | 0.493 ±5.983  | 98.254 ±6.926             | 1512.988 ±38.525   |
| Eloquent       | v13.18.1   | 0.578 ±5.458  | 206.647 ±5.718            | 1913.039 ±35.081   |
| Propel         | dev-master | 0.029 ±6.253  | 55.358 ±7.163             | 924.211 ±19.002    |
| RedBeanPHP     | v5.7.6     | 0.331 ±0.459  | 12.211 ±0.464             | 1403.375 ±26.907   |

Update:

| ORM            | Version    | updateOneRow  | updateOneRowThousandTimes |
| -------------- | ---------- | ------------: | ------------------------: |
| MarekSkopalORM | v2.0.1     | 0.948 ±0.346  | 430.259 ±37.667           |
| CycleORM       | v2.18.0    | 1.034 ±1.241  | 519.575 ±29.820           |
| DoctrineORM    | 3.6.7      | 0.758 ±0.310  | 457.690 ±61.076           |
| Eloquent       | v13.18.1   | 0.835 ±0.823  | 562.197 ±45.415           |
| Propel         | dev-master | 1.216 ±1.449  | 444.081 ±55.317           |
| RedBeanPHP     | v5.7.6     | 0.969 ±0.116  | 400.444 ±42.330           |

Insert:

| ORM            | Version    | insertOneRow  | insertOneRowThousandTimes | insertOneThousandRows |
| -------------- | ---------- | ------------: | ------------------------: | --------------------: |
| MarekSkopalORM | v2.0.1     | 0.648 ±0.525  | 571.937 ±127.706          | 10.390 ±0.520         |
| CycleORM       | v2.18.0    | 0.803 ±0.364  | 606.824 ±34.141           | 48.884 ±9.216         |
| DoctrineORM    | 3.6.7      | 0.608 ±0.868  | 560.181 ±62.450           | 47.315 ±31.201        |
| Eloquent       | v13.18.1   | 0.609 ±0.153  | 580.564 ±58.763           | 89.862 ±2.490         |
| Propel         | dev-master | 0.588 ±0.059  | 629.992 ±48.685           | 20.237 ±2.119         |
| RedBeanPHP     | v5.7.6     | 0.507 ±0.242  | 468.164 ±90.433           | 37.399 ±2.827         |




## Contributing
If you want to contribute, feel free to submit a pull request.
