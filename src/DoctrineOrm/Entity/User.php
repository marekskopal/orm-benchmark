<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\DoctrineOrm\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'users')]
class User
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    public int $id;

    #[Column(name: 'created_at', type: 'datetime_immutable')]
    public DateTimeImmutable $createdAt;

    #[Column(name: 'first_name', type: 'string', length: 255)]
    public string $firstName;

    #[Column(name: 'middle_name', type: 'string', length: 255, nullable: true)]
    public ?string $middleName;

    #[Column(name: 'last_name', type: 'string', length: 255)]
    public string $lastName;

    #[Column(type: 'string', length: 255)]
    public string $email;

    #[Column(name: 'is_active', type: 'boolean')]
    public bool $isActive;

    #[ManyToOne(targetEntity: Address::class)]
    #[JoinColumn(name: 'address_id', referencedColumnName: 'id')]
    public Address $address;
}
