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

SQLite file database seeded with the configured number of user rows (default 100,000) and one shared address row. The schema is recreated from scratch before each run. Seeding runs inside a single transaction.

### Timing

PHP `hrtime()` is used for nanosecond-precision wall-clock measurement. Results are reported in milliseconds.

## Results

Environment: PHP 8.5.3, OPcache disabled, Apple M1 Max, macOS, 100,000 pre-seeded rows, 5 runs per method. All times in milliseconds (median ±stddev).

Select:

| ORM            | Version    | selectOneRow  | selectOneRowThousandTimes | selectAllRows      |
| -------------- | ---------- | ------------: | ------------------------: | -----------------: |
| MarekSkopalORM | v1.0.1     | 0.148 ±0.680  | 17.896 ±1.097             | 898.878 ±31.232    |
| CycleORM       | v2.14.3    | 0.442 ±4.589  | 72.620 ±2.006             | 2046.867 ±61.092   |
| DoctrineORM    | 3.6.2      | 0.357 ±5.091  | 80.458 ±3.187             | 1768.364 ±42.275   |
| Eloquent       | v12.53.0   | 0.382 ±5.420  | 150.493 ±6.697            | 1731.280 ±94.030   |
| Propel         | dev-master | 0.024 ±4.467  | 54.494 ±9.662             | 1303.625 ±83.378   |
| RedBeanPHP     | v5.7.5     | 0.238 ±0.516  | 12.324 ±1.024             | 2185.548 ±70.746   |

Update:

| ORM            | Version    | updateOneRow  | updateOneRowThousandTimes |
| -------------- | ---------- | ------------: | ------------------------: |
| MarekSkopalORM | v1.0.1     | 1.012 ±0.284  | 344.804 ±59.430           |
| CycleORM       | v2.14.3    | 1.244 ±1.063  | 380.753 ±9.827            |
| DoctrineORM    | 3.6.2      | 0.778 ±0.117  | 346.972 ±24.206           |
| Eloquent       | v12.53.0   | 0.915 ±0.848  | 445.381 ±86.441           |
| Propel         | dev-master | 0.887 ±0.977  | 353.564 ±22.276           |
| RedBeanPHP     | v5.7.5     | 0.915 ±0.077  | 387.193 ±50.980           |

Insert:

| ORM            | Version    | insertOneRow  | insertOneRowThousandTimes | insertOneThousandRows |
| -------------- | ---------- | ------------: | ------------------------: | --------------------: |
| MarekSkopalORM | v1.0.1     | 0.498 ±0.156  | 467.341 ±52.176           | 12.928 ±0.272         |
| CycleORM       | v2.14.3    | 0.558 ±0.534  | 476.735 ±71.822           | 48.111 ±0.230         |
| DoctrineORM    | 3.6.2      | 0.644 ±0.903  | 483.060 ±40.132           | 45.615 ±1.698         |
| Eloquent       | v12.53.0   | 0.533 ±0.045  | 459.674 ±87.618           | 78.020 ±5.859         |
| Propel         | dev-master | 0.435 ±0.030  | 416.375 ±56.386           | 18.498 ±0.390         |
| RedBeanPHP     | v5.7.5     | 0.494 ±0.764  | 524.459 ±72.586           | 36.314 ±2.041         |




## Contributing
If you want to contribute, feel free to submit a pull request.
