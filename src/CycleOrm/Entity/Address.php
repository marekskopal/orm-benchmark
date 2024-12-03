<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\CycleOrm\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity()]
class Address
{
    public function __construct(
        #[Column(type: 'primary')]
        public int $id,
        #[Column(type: 'string')]
        public string $street,
        #[Column(type: 'integer')]
        public int $number,
        #[Column(type: 'string')]
        public string $city,
        #[Column(type: 'string')]
        public string $country,
    ) {
    }
}
