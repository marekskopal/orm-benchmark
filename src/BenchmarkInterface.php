<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark;

interface BenchmarkInterface
{
    public function selectOneRow(): float;

    public function selectOneRowThousandTimes(): float;

    public function selectAllRows(): float;
}
