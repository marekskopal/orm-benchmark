<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\CycleOrm\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\RefersTo;
use DateTimeImmutable;
use MarekSkopal\ORMBenchmark\CycleOrm\Repository\UserRepository;

#[Entity(repository: UserRepository::class)]
class User
{
    public function __construct(
        #[Column(type: 'primary')]
        public int $id,
        #[Column(type: 'datetime')]
        public DateTimeImmutable $createdAt,
        #[Column(type: 'string')]
        public string $firstName,
        #[Column(type: 'string', nullable: true)]
        public ?string $middleName,
        #[Column(type: 'string')]
        public string $lastName,
        #[Column(type: 'string')]
        public string $email,
        #[Column(type: 'boolean')]
        public bool $isActive,
        #[RefersTo(target: Address::class)]
        public Address $address,
    ) {
    }
}
