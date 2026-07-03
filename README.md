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

Environment: PHP 8.5.7, OPcache disabled, Apple M1 Max, macOS, 100,000 pre-seeded rows, 5 runs per method. All times in milliseconds (median ±stddev).

Select:

| ORM            | Version    | selectOneRow  | selectOneRowThousandTimes | selectAllRows      |
| -------------- | ---------- | ------------: | ------------------------: | -----------------: |
| MarekSkopalORM | v1.3.0     | 0.346 ±0.992  | 58.883 ±10.636            | 1027.897 ±100.449  |
| CycleORM       | v2.18.0    | 0.631 ±4.247  | 97.103 ±5.248             | 1938.415 ±38.460   |
| DoctrineORM    | 3.6.7      | 0.500 ±5.520  | 89.040 ±2.116             | 1448.280 ±52.213   |
| Eloquent       | v13.18.1   | 0.655 ±5.560  | 205.971 ±15.053           | 1855.522 ±34.876   |
| Propel         | dev-master | 0.025 ±3.950  | 50.962 ±6.762             | 923.468 ±14.563    |
| RedBeanPHP     | v5.7.6     | 0.254 ±0.448  | 12.042 ±0.209             | 1351.546 ±26.963   |

Update:

| ORM            | Version    | updateOneRow  | updateOneRowThousandTimes |
| -------------- | ---------- | ------------: | ------------------------: |
| MarekSkopalORM | v1.3.0     | 0.782 ±0.343  | 507.631 ±49.095           |
| CycleORM       | v2.18.0    | 1.403 ±1.931  | 574.515 ±50.855           |
| DoctrineORM    | 3.6.7      | 1.118 ±0.208  | 443.035 ±31.362           |
| Eloquent       | v13.18.1   | 1.442 ±0.709  | 514.913 ±21.826           |
| Propel         | dev-master | 1.339 ±1.010  | 409.055 ±72.537           |
| RedBeanPHP     | v5.7.6     | 1.213 ±0.190  | 377.317 ±30.354           |

Insert:

| ORM            | Version    | insertOneRow  | insertOneRowThousandTimes | insertOneThousandRows |
| -------------- | ---------- | ------------: | ------------------------: | --------------------: |
| MarekSkopalORM | v1.3.0     | 0.794 ±0.244  | 608.833 ±71.560           | 19.485 ±0.322         |
| CycleORM       | v2.18.0    | 0.875 ±0.318  | 565.768 ±47.305           | 51.106 ±1.409         |
| DoctrineORM    | 3.6.7      | 0.712 ±0.740  | 513.149 ±24.138           | 47.581 ±24.689        |
| Eloquent       | v13.18.1   | 0.567 ±0.052  | 550.818 ±36.195           | 92.956 ±3.822         |
| Propel         | dev-master | 0.443 ±0.028  | 405.589 ±35.257           | 20.006 ±1.546         |
| RedBeanPHP     | v5.7.6     | 0.603 ±0.726  | 450.988 ±31.956           | 36.267 ±0.760         |




## Contributing
If you want to contribute, feel free to submit a pull request.
