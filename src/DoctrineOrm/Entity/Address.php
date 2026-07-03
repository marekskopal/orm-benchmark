<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\DoctrineOrm\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'addresses')]
class Address
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    public int $id;

    #[Column(type: 'string', length: 255)]
    public string $street;

    #[Column(type: 'integer')]
    public int $number;

    #[Column(type: 'string', length: 255)]
    public string $city;

    #[Column(type: 'string', length: 255)]
    public string $country;
}
