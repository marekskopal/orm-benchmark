<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\MarekSkopalOrm\Entity;

use MarekSkopal\ORM\Attribute\Column;
use MarekSkopal\ORM\Attribute\Entity;

#[Entity]
final class Address
{
    public function __construct(
        #[Column(type: 'int', primary: true)]
        public int $id,
        #[Column(type: 'varchar(255)')]
        public string $street,
        #[Column(type: 'int(11)')]
        public int $number,
        #[Column(type: 'varchar(255)')]
        public string $city,
        #[Column(type: 'country(255)')]
        public string $country,
    ) {
    }
}
