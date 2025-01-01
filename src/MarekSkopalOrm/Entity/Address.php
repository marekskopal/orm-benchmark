<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\MarekSkopalOrm\Entity;

use MarekSkopal\ORM\Attribute\Column;
use MarekSkopal\ORM\Attribute\Entity;
use MarekSkopal\ORM\Enum\Type;

#[Entity]
final class Address
{
    #[Column(type: Type::Int, primary: true, autoIncrement: true)]
    public int $id;

    public function __construct(
        #[Column(type: Type::String, size: 255)]
        public string $street,
        #[Column(type: Type::Int)]
        public int $number,
        #[Column(type: Type::String, size: 255)]
        public string $city,
        #[Column(type: Type::String, size: 255)]
        public string $country,
    ) {
    }
}
