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

| ORM            | selectOneRow | selectOneRowThousandTimes | selectAllRows |
| -------------- | -----------: | ------------------------: | ------------: |
| MarekSkopalORM | 3.044583     | 19.0925                   | 848.438833    |
| CycleORM       | 5.46425      | 34.584292                 | 1864.360875   |
| DoctrineORM    | 18.903125    | 77.563792                 | 1523.257625   |
| Eloquent       | 18.160625    | 227.729416                | 1269.457625   |
| Propel         | 9.638125     | 2.802667                  | 937.7775      |
| RedBeanPHP     | 1.490666     | 12.522959                 | 1493.386792   |

Insert in milliseconds:

| ORM            | insertOneRow | insertOneRowThousandTimes | insertOneThousandRows |
| -------------- | -----------: | ------------------------: | --------------------: |
| MarekSkopalORM | 0.894792     | 371.145958                | 456.226334            |
| CycleORM       | 2.661042     | 414.902334                | 481.423667            |
| DoctrineORM    | 3.363875     | 4576.803708               | 4678.412584           |
| Eloquent       | 3.898042     | 468.036667                | 488.999166            |
| Propel         | 0.953958     | 408.288458                | 435.116792            |
| RedBeanPHP     | 1.201084     | 402.387625                | 444.830458            |




## Contributing
If you want to contribute, feel free to submit a pull request.
