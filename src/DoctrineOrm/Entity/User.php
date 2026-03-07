<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\DoctrineOrm\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    public int $id;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'first_name', type: 'string', length: 255)]
    public string $firstName;

    #[ORM\Column(name: 'middle_name', type: 'string', length: 255, nullable: true)]
    public ?string $middleName;

    #[ORM\Column(name: 'last_name', type: 'string', length: 255)]
    public string $lastName;

    #[ORM\Column(type: 'string', length: 255)]
    public string $email;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    public bool $isActive;

    #[ORM\ManyToOne(targetEntity: Address::class)]
    #[ORM\JoinColumn(name: 'address_id', referencedColumnName: 'id')]
    public Address $address;
}
