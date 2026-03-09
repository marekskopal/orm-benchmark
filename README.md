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

### Benchmarked operations

| Method | Description |
| --- | --- |
| `selectOneRow` | Fetch a single user by primary key including its related address |
| `selectOneRowThousandTimes` | Repeat the same single-row fetch 1,000 times; ORM identity map / instance pool is cleared before each iteration to force real database queries |
| `selectAllRows` | Fetch all rows from the users table including related addresses |
| `insertOneRow` | Insert one user row and persist it to the database |
| `insertOneRowThousandTimes` | Insert and persist 1,000 user rows one by one; managed-entity sets are kept minimal after each flush to prevent identity-map growth |
| `insertOneThousandRows` | Same as above but with unique field values per row |

### Database

SQLite file database seeded with the configured number of user rows (default 100,000) and one shared address row. The schema is recreated from scratch before each run.

### Timing

PHP `hrtime()` is used for nanosecond-precision wall-clock measurement. Results are reported in milliseconds.

## Results

Benchmark run on macOS with 100,000 pre-seeded rows. All times in milliseconds.

Select in milliseconds:

| ORM            | Version      | selectOneRow | selectOneRowThousandTimes | selectAllRows |
| -------------- | ------------ | -----------: | ------------------------: | ------------: |
| MarekSkopalORM | v1.0.1       | 1.944        | 20.476                    | 974.284       |
| CycleORM       | v2.14.3      | 11.521       | 74.785                    | 1906.815      |
| DoctrineORM    | 3.6.2        | 14.518       | 79.710                    | 1582.312      |
| Eloquent       | v12.53.0     | 19.783       | 146.378                   | 1381.524      |
| Propel         | dev-master   | 10.603       | 32.122                    | 939.237       |
| RedBeanPHP     | v5.7.5       | 1.350        | 11.782                    | 1430.958      |

Insert in milliseconds:

| ORM            | Version      | insertOneRow | insertOneRowThousandTimes | insertOneThousandRows |
| -------------- | ------------ | -----------: | ------------------------: | --------------------: |
| MarekSkopalORM | v1.0.1       | 4.456        | 398.978                   | 357.114               |
| CycleORM       | v2.14.3      | 3.359        | 453.730                   | 458.137               |
| DoctrineORM    | 3.6.2        | 1.618        | 417.364                   | 501.518               |
| Eloquent       | v12.53.0     | 3.170        | 447.192                   | 478.295               |
| Propel         | dev-master   | 1.146        | 394.666                   | 416.278               |
| RedBeanPHP     | v5.7.5       | 2.112        | 357.774                   | 408.059               |




## Contributing
If you want to contribute, feel free to submit a pull request.
