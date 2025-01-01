<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\MarekSkopalOrm\Entity;

use DateTimeImmutable;
use MarekSkopal\ORM\Attribute\Column;
use MarekSkopal\ORM\Attribute\Entity;
use MarekSkopal\ORM\Attribute\ManyToOne;
use MarekSkopal\ORM\Enum\Type;

#[Entity]
final class User
{
    #[Column(type: Type::Int, primary: true, autoIncrement: true)]
    public int $id;

    public function __construct(
        #[Column(type: Type::DateTime)]
        public DateTimeImmutable $createdAt,
        #[Column(type: Type::String, size: 255)]
        public string $firstName,
        #[Column(type: Type::String, size: 255, nullable: true)]
        public ?string $middleName,
        #[Column(type: Type::String, size: 255)]
        public string $lastName,
        #[Column(type: Type::String, size: 255)]
        public string $email,
        #[Column(type: Type::Boolean)]
        public bool $isActive,
        #[ManyToOne(entityClass: Address::class)]
        public Address $address,
    ) {
    }
}
