# ORM Benchmark

Benchmark for ORM libraries.

Main goal is to compare performance of different ORM libraries specially in comparision with my own ORM library.

## Comparated Libraries
* [MarekSkopal ORM](https://github.com/marekskopal/orm)
* [Cycle ORM](https://cycle-orm.dev/)

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

Select in milliseconds:

| ORM            | selectOneRow | selectOneRowThousandTimes | selectAllRows |
| -------------- | ------------ |-------------------------- | ------------- |
| MarekSkopalORM | 1.553708     | 15.247417                 | 554.422375    |
| CycleORM       | 5.68475      | 24.939208                 | 1559.673667   |

Insert in milliseconds:

| ORM            | insertOneRow | insertOneRowThousandTimes | insertOneThousandRows |
| -------------- | ------------ | ------------------------- | --------------------- |
| MarekSkopalORM | 0.942292     | 422.615833                | 416.305333            |
| CycleORM       | 2.955875     | 549.028083                | 561.206375            |




## Contributing
If you want to contribute, feel free to submit a pull request.
