<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\DoctrineOrm\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'addresses')]
class Address
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    public int $id;

    #[ORM\Column(type: 'string', length: 255)]
    public string $street;

    #[ORM\Column(type: 'integer')]
    public int $number;

    #[ORM\Column(type: 'string', length: 255)]
    public string $city;

    #[ORM\Column(type: 'string', length: 255)]
    public string $country;
}
