<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\Utils;

final class BenchmarkTime
{
    public static function measure(callable $callback): float
    {
        $start = hrtime(true);

        $callback();

        return (hrtime(true) - $start) / 1000000;
    }
}
