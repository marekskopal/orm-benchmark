<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\CycleOrm\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use MarekSkopal\ORMBenchmark\CycleOrm\Repository\UserRepository;

#[Entity(repository: UserRepository::class)]
class User
{
    public function __construct(
        #[Column(type: 'primary')]
        private int $id,
        #[Column(type: 'string')]
        private string $firstName,
        #[Column(type: 'string')]
        private string $lastName,
        #[Column(type: 'string')]
        private string $email,
        #[Column(type: 'boolean')]
        private bool $isActive,
    ) {
    }
}
