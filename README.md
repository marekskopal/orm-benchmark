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

## Results

Benchmark run with 100,000 pre-seeded rows. All times in milliseconds.

Select in milliseconds:

| ORM            | Version      | selectOneRow | selectOneRowThousandTimes | selectAllRows |
| -------------- | ------------ | -----------: | ------------------------: | ------------: |
| MarekSkopalORM | v1.0.0       | 0.788792     | 18.823917                 | 926.1495      |
| CycleORM       | v2.14.3      | 6.466959     | 32.277833                 | 1847.869375   |
| DoctrineORM    | 3.6.2        | 19.119625    | 76.220916                 | 1514.44       |
| Eloquent       | v12.53.0     | 17.362958    | 208.66975                 | 1267.275583   |
| Propel         | dev-master   | 8.93025      | 2.765291                  | 940.344958    |
| RedBeanPHP     | v5.7.5       | 1.286417     | 11.993833                 | 1462.891916   |

Insert in milliseconds:

| ORM            | Version      | insertOneRow | insertOneRowThousandTimes | insertOneThousandRows |
| -------------- | ------------ | -----------: | ------------------------: | --------------------: |
| MarekSkopalORM | v1.0.0       | 2.954166     | 327.037625                | 385.491417            |
| CycleORM       | v2.14.3      | 3.853875     | 449.497333                | 453.769375            |
| DoctrineORM    | 3.6.2        | 404.938875   | 4596.686042               | 4744.7745             |
| Eloquent       | v12.53.0     | 2.958917     | 406.851                   | 416.847208            |
| Propel         | dev-master   | 1.201125     | 364.065                   | 369.997               |
| RedBeanPHP     | v5.7.5       | 2.137125     | 418.91725                 | 359.562958            |




## Contributing
If you want to contribute, feel free to submit a pull request.
