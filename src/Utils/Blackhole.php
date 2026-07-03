<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\Utils;

/** Consumes benchmark results so fetched values are observably used (JMH Blackhole pattern). */
final class Blackhole
{
    public static mixed $sink = null;

    public static function consume(mixed $value): void
    {
        self::$sink = $value;
    }
}
