<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\Utils;

final class BenchmarkStats
{
    /** @param list<float> $values */
    public static function median(array $values): float
    {
        sort($values);
        $count = count($values);
        $mid = (int) floor($count / 2);

        return $count % 2 === 0
            ? ($values[$mid - 1] + $values[$mid]) / 2.0
            : $values[$mid];
    }

    /** @param list<float> $values */
    public static function stddev(array $values): float
    {
        $count = count($values);
        if ($count < 2) {
            return 0.0;
        }

        $mean = array_sum($values) / $count;
        $variance = array_sum(array_map(fn(float $v): float => ($v - $mean) ** 2, $values)) / $count;

        return sqrt($variance);
    }

    /** @param list<float> $values */
    public static function format(array $values): string
    {
        return round(self::median($values), 3) . ' ±' . round(self::stddev($values), 3);
    }
}
