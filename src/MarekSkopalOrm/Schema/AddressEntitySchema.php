<?php

declare(strict_types=1);

namespace MarekSkopal\ORMBenchmark\MarekSkopalOrm\Schema;

use MarekSkopal\ORM\Schema\ColumnSchema;
use MarekSkopal\ORM\Schema\EntitySchema;
use MarekSkopal\ORM\Schema\Enum\PropertyTypeEnum;
use MarekSkopal\ORMBenchmark\MarekSkopalOrm\Entity\Address;
use MarekSkopal\ORMBenchmark\MarekSkopalOrm\Repository\AddressRepository;

class AddressEntitySchema
{
    public static function create(): EntitySchema
    {
        return new EntitySchema(
            entityClass: Address::class,
            repositoryClass: AddressRepository::class,
            table: 'addresses',
            columns: [
                'id' => new ColumnSchema(
                    propertyName: 'id',
                    propertyType: PropertyTypeEnum::Int,
                    columnName: 'id',
                    columnType: 'int',
                    isPrimary: true,
                    isAutoIncrement: true,
                ),
                'street' => new ColumnSchema(
                    propertyName: 'street',
                    propertyType: PropertyTypeEnum::String,
                    columnName: 'street',
                    columnType: 'varchar',
                ),
                'number' => new ColumnSchema(
                    propertyName: 'number',
                    propertyType: PropertyTypeEnum::Int,
                    columnName: 'number',
                    columnType: 'int',
                ),
                'city' => new ColumnSchema(
                    propertyName: 'city',
                    propertyType: PropertyTypeEnum::String,
                    columnName: 'city',
                    columnType: 'varchar',
                ),
                'country' => new ColumnSchema(
                    propertyName: 'country',
                    propertyType: PropertyTypeEnum::String,
                    columnName: 'country',
                    columnType: 'varchar',
                ),
            ],
        );
    }
}
