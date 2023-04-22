# Repository Storage Service

[Storage](https://github.com/tobento-ch/service-storage) [repository](https://github.com/tobento-ch/service-repository) implementation.

## Table of Contents

- [Getting started](#getting-started)
    - [Requirements](#requirements)
    - [Highlights](#highlights)
- [Documentation](#documentation)
    - [Storage Repository](#storage-repository)
    - [Storage Read Repository](#storage-read-repository)
    - [Storage Write Repository](#storage-write-repository)
    - [Where Parameters](#where-parameters)
    - [Storage Entity Factory](#storage-entity-factory)
    - [Repository With Columns](#repository-with-columns)
    - [Columns Common Methods](#columns-common-methods)
        - [Type](#type)
        - [Read](#read)
        - [Write](#write)
        - [Storable](#storable)
    - [Columns](#columns)
        - [Boolean](#boolean)
        - [Datetime](#datetime)
        - [Float](#float)
        - [Id](#id)
        - [Integer](#integer)
        - [Json](#json)
        - [Text](#text)
        - [Translatable](#translatable)
    - [Translations](#translations)
        - [Where Parameters Translations](#where-parameters-translations)
        - [Write Translations](#write-translations)
    - [Migration](#migration)
- [Credits](#credits)
___

# Getting started

Add the latest version of the repository storage service project running this command.

```
composer require tobento/service-repository-storage
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design

# Documentation

Check out the [Storage Service - Storages](https://github.com/tobento-ch/service-storage#storages) for its available storages.

Check out the [Repository Service](https://github.com/tobento-ch/service-repository) for its documentation.

## Storage Repository

To create a storage repository simply extend the ```StorageRepository::class```:

You may also check out the [Repository With Columns](#repository-with-columns) section to create the repository using columns.

```php
use Tobento\Service\Repository\RepositoryInterface;
use Tobento\Service\Repository\ReadRepositoryInterface;
use Tobento\Service\Repository\WriteRepositoryInterface;
use Tobento\Service\Repository\Storage\StorageRepository;
use Tobento\Service\Repository\Storage\StorageEntityFactoryInterface;
use Tobento\Service\Storage\InMemoryStorage;
use Tobento\Service\Storage\Tables\Tables;

class ProductRepository extends StorageRepository
{
    //
}

$repository = new ProductRepository(
    storage: new  InMemoryStorage(
        items: [],
        tables: (new Tables())->add('products', ['id', 'sku', 'price'], 'id')
    ),
    table: 'products', // specify which storage table should be used.
    entityFactory: null, // null|StorageEntityFactoryInterface
);

var_dump($repository instanceof RepositoryInterface);
// bool(true)

var_dump($repository instanceof ReadRepositoryInterface);
// bool(true)

var_dump($repository instanceof WriteRepositoryInterface);
// bool(true)
```

By default, the read and write methods will return the following types depending on the method called:

* ```Tobento\Service\Storage\ItemInterface::class``` [Storage - ItemInterface](https://github.com/tobento-ch/service-storage#item-interface)
* ```Tobento\Service\Storage\ItemsInterface::class``` [Storage - ItemsInterface](https://github.com/tobento-ch/service-storage#items-interface)
* ```null```

You may specify a custom [Storage Entity Factory](#storage-entity-factory) to return custom entities by the ```entityFactory``` parameter.

## Storage Read Repository

To create a storage read repository simply extend the ```StorageReadRepository::class```:

You may also check out the [Repository With Columns](#repository-with-columns) section to create the repository using columns.

```php
use Tobento\Service\Repository\ReadRepositoryInterface;
use Tobento\Service\Repository\Storage\StorageReadRepository;
use Tobento\Service\Repository\Storage\StorageEntityFactoryInterface;
use Tobento\Service\Storage\InMemoryStorage;
use Tobento\Service\Storage\Tables\Tables;

class ProductReadRepository extends StorageReadRepository
{
    // adding custom find methods
}

$repository = new ProductReadRepository(
    storage: new  InMemoryStorage(
        items: [
            'products' => [
                1 => ['id' => 1, 'sku' => 'paper', 'price' => 1.2],
                2 => ['id' => 2, 'sku' => 'pen', 'price' => 1.8],
                3 => ['id' => 3, 'sku' => 'pencil', 'price' => 1.5],
            ],
        ],
        tables: (new Tables())->add('products', ['id', 'sku', 'price'], 'id')
    ),
    table: 'products',
    entityFactory: null, // null|StorageEntityFactoryInterface
);

var_dump($repository instanceof ReadRepositoryInterface);
// bool(true)
```

By default, the find methods will return the following types depending on the method called:

* ```Tobento\Service\Storage\ItemInterface::class``` [Storage - ItemInterface](https://github.com/tobento-ch/service-storage#item-interface)
* ```Tobento\Service\Storage\ItemsInterface::class``` [Storage - ItemsInterface](https://github.com/tobento-ch/service-storage#items-interface)
* ```null```

You may specify a custom [Storage Entity Factory](#storage-entity-factory) to return custom entities by the ```entityFactory``` parameter.

**findById**

```php
use Tobento\Service\Storage\ItemInterface;

$entity = $repository->findById(id: 2);

var_dump($entity instanceof ItemInterface);
// bool(true)

var_dump($entity->get('sku'));
// string(3) "pen"

$entity = $repository->findById(id: 5);

var_dump($entity);
// NULL
```

You may check out the [Storage Service - ItemInterface](https://github.com/tobento-ch/service-storage#item-interface) for its documentation.

**findByIds**

```php
use Tobento\Service\Storage\ItemsInterface;
use Tobento\Service\Storage\ItemInterface;

$entities = $repository->findByIds(1, 2, 8);

var_dump($entities instanceof ItemsInterface);
// bool(true)

var_dump($entities->count());
// int(2)

foreach($entities as $entity) {
    var_dump($entity instanceof ItemInterface);
    // bool(true)
}
```

You may check out the [Storage Service - ItemsInterface](https://github.com/tobento-ch/service-storage#items-interface) for its documentation.

You may check out the [Storage Service - ItemInterface](https://github.com/tobento-ch/service-storage#item-interface) for its documentation.

**findOne**

```php
use Tobento\Service\Storage\ItemInterface;

$entity = $repository->findOne(where: [
    'sku' => 'pen',
]);

var_dump($entity instanceof ItemInterface);
// bool(true)

var_dump($entity->get('sku'));
// string(3) "pen"

$entity = $repository->findOne(where: [
    'sku' => 'foo',
]);

var_dump($entity);
// NULL
```

You may check out the [Where Parameters](#where-parameters) for its supported parameters.

You may check out the [Storage Service - ItemInterface](https://github.com/tobento-ch/service-storage#item-interface) for its documentation.

**findAll**

```php
use Tobento\Service\Storage\ItemsInterface;
use Tobento\Service\Storage\ItemInterface;

$entities = $repository->findAll(where: [
    'price' => ['>' => 1.3],
]);

var_dump($entities instanceof ItemsInterface);
// bool(true)

var_dump($entities->count());
// int(2)

foreach($entities as $entity) {
    var_dump($entity instanceof ItemInterface);
    // bool(true)
}

$entities = $repository->findAll(
    where: [
        'price' => [
            '>' => 1.3,
            '<' => 1.6,
        ],
        'sku' => ['like' => 'pe%'],
    ],
    orderBy: [
        'sku' => 'DESC', // or 'ASC'
    ],
    limit: 20, // (number)
    // limit: [20, 5], // [20(number), 5(offset)]
);

var_dump($entities->count());
// int(1)
```

You may check out the [Where Parameters](#where-parameters) for its supported parameters.

You may check out the [Storage Service - ItemsInterface](https://github.com/tobento-ch/service-storage#items-interface) for its documentation.

You may check out the [Storage Service - ItemInterface](https://github.com/tobento-ch/service-storage#item-interface) for its documentation.

## Storage Write Repository

To create a storage write repository simply extend the ```StorageWriteRepository::class```:

You may also check out the [Repository With Columns](#repository-with-columns) section to create the repository using columns.

```php
use Tobento\Service\Repository\WriteRepositoryInterface;
use Tobento\Service\Repository\Storage\StorageWriteRepository;
use Tobento\Service\Repository\Storage\StorageEntityFactoryInterface;
use Tobento\Service\Storage\InMemoryStorage;
use Tobento\Service\Storage\Tables\Tables;

class ProductWriteRepository extends StorageWriteRepository
{
    // you may add custom write methods
}

$repository = new ProductWriteRepository(
    storage: new  InMemoryStorage(
        items: [
            'products' => [
                1 => ['id' => 1, 'sku' => 'paper', 'price' => 1.2],
                2 => ['id' => 2, 'sku' => 'pen', 'price' => 1.8],
                3 => ['id' => 3, 'sku' => 'pencil', 'price' => 1.5],
            ],
        ],
        tables: (new Tables())->add('products', ['id', 'sku', 'price'], 'id')
    ),
    table: 'products',
    entityFactory: null, // null|StorageEntityFactoryInterface
);

var_dump($repository instanceof WriteRepositoryInterface);
// bool(true)
```

By default, the methods will return the following types depending on the method called:

* ```Tobento\Service\Storage\ItemInterface::class``` [Storage - ItemInterface](https://github.com/tobento-ch/service-storage#item-interface)
* ```Tobento\Service\Storage\ItemsInterface::class``` [Storage - ItemsInterface](https://github.com/tobento-ch/service-storage#items-interface)
* ```null```

You may specify a custom [Storage Entity Factory](#storage-entity-factory) to return custom entities by the ```entityFactory``` parameter.

**create**

```php
use Tobento\Service\Storage\ItemInterface;

$createdEntity = $repository->create(attributes: [
    'sku' => 'scissors',
]);

var_dump($createdEntity instanceof ItemInterface);
// bool(true)

var_dump($createdEntity->all());
// array(2) { ["sku"]=> string(8) "scissors" ["id"]=> int(4) }
```

You may check out the [Storage Service - ItemInterface](https://github.com/tobento-ch/service-storage#item-interface) for its documentation.

**updateById**

```php
use Tobento\Service\Storage\ItemInterface;
use Tobento\Service\Repository\RepositoryUpdateException;

$updatedEntity = $repository->updateById(
    id: 2,
    attributes: [
        'price' => 2.5,
    ]
);

var_dump($updatedEntity instanceof ItemInterface);
// bool(true)

var_dump($updatedEntity->all());
// array(2) { ["sku"]=> string(8) "scissors" ["id"]=> int(4) }
```

This method will throw a ```RepositoryUpdateException::class``` exception if the storage table has no primary key specified or the entity to update does not exist.

You may check out the [Storage Service - ItemInterface](https://github.com/tobento-ch/service-storage#item-interface) for its documentation.

**update**

```php
use Tobento\Service\Storage\ItemsInterface;
use Tobento\Service\Storage\ItemInterface;

$updatedEntities = $repository->update(
    where: [
        'id' => ['>' => 1],
    ],
    attributes: [
        'price' => 2.5,
    ],
);

var_dump($updatedEntities instanceof ItemsInterface);
// bool(true)

var_dump($updatedEntities->count());
// int(2)

foreach($updatedEntities as $entity) {
    var_dump($entity instanceof ItemInterface);
    // bool(true)
}
```

You may check out the [Where Parameters](#where-parameters) for its supported parameters.

You may check out the [Storage Service - ItemsInterface](https://github.com/tobento-ch/service-storage#items-interface) for its documentation.

You may check out the [Storage Service - ItemInterface](https://github.com/tobento-ch/service-storage#item-interface) for its documentation.

**deleteById**

```php
use Tobento\Service\Storage\ItemInterface;
use Tobento\Service\Repository\RepositoryDeleteException;

$deletedEntity = $repository->deleteById(id: 2);

var_dump($deletedEntity instanceof ItemInterface);
// bool(true)

var_dump($deletedEntity->all());
// array(3) { ["id"]=> int(2) ["sku"]=> string(3) "pen" ["price"]=> float(1.8) }
```

This method will throw a ```RepositoryDeleteException::class``` exception if the storage table has no primary key specified or the entity to delete does not exist.

You may check out the [Storage Service - ItemInterface](https://github.com/tobento-ch/service-storage#item-interface) for its documentation.

**delete**

```php
use Tobento\Service\Storage\ItemsInterface;
use Tobento\Service\Storage\ItemInterface;

$deletedEntities = $repository->delete(where: [
    'id' => ['>' => 1],
]);

var_dump($deletedEntities instanceof ItemsInterface);
// bool(true)

var_dump($deletedEntities->count());
// int(2)

foreach($deletedEntities as $entity) {
    var_dump($entity instanceof ItemInterface);
    // bool(true)
}
```

You may check out the [Where Parameters](#where-parameters) for its supported parameters.

You may check out the [Storage Service - ItemsInterface](https://github.com/tobento-ch/service-storage#items-interface) for its documentation.

You may check out the [Storage Service - ItemInterface](https://github.com/tobento-ch/service-storage#item-interface) for its documentation.

## Where Parameters

The following where clauses are supported (for all read/write methods with where parameter):

```php
$entities = $repository->findAll(where: [
    'sku' => 'pen',
    // is equal to:
    'sku' => ['=' => 'pen'],
    
    'sku' => ['!=' => 'pen'],
    
    'sku' => ['null'],
    'sku' => ['not null'],

    'price' => ['>' => 1.5],
    'price' => ['<' => 1.5],
    'price' => ['>=' => 1.5],
    'price' => ['<=' => 1.5],
    'price' => ['<>' => 1.5],
    'price' => ['<=>' => 1.5],
    'price' => ['between' => [2, 5]],
    'price' => ['not between' => [2, 5]],
    'id' => ['in' => [2,5,6]],
    'id' => ['not in' => [2,5,6]],    
    
    // Finds any values that (not) start with "a"
    'title' => ['like' => 'a%'],
    'title' => ['not like' => 'a%'],
    
    // Finds any values that (not) end with "a"
    'title' => ['like' => '%a'],
    'title' => ['not like' => '%a'],
    
    // Finds any values that have (not) "a" in any position
    'title' => ['like' => '%a%'],
    'title' => ['not like' => '%a%'],
    
    // Json specific:
    'options->color' => 'blue',
    
    'options->colors' => ['contains' => 'blue'],
    'options->colors' => ['contains' => ['blue']],
    
    'options->color' => ['contains key'],
]);
```

## Storage Entity Factory

You may create a custom entity factory to return custom entities by the [Storage Repository](#storage-repository), [Storage Read Repository](#storage-read-repository) or [Storage Write Repository](#storage-write-repository).

To create a custom entity factory simply extend the ```EntityFactory::class``` and adjust the ```createEntityFromArray``` method:

```php
use Tobento\Service\Repository\Storage\EntityFactory;
use Tobento\Service\Repository\Storage\StorageEntityFactoryInterface;
use Tobento\Service\Repository\EntityFactoryInterface;
use Tobento\Service\Storage\ItemInterface;

class ProductFactory extends EntityFactory
{
    public function createEntityFromArray(array $attributes): Product
    {
        // Process the columns reading:
        $attributes = $this->columns->processReading($attributes);
        
        // Create entity:
        return new Product(
            id: $attributes['id'] ?? 0,
            sku: $attributes['sku'] ?? '',
        );
    }
}

class Product
{
    public function __construct(
        public readonly int $id,
        public readonly string $sku,
    ) {}
}

$productFactory = new ProductFactory();
$productFactory->setColumns([]); // will be set by the storage

var_dump($productFactory instanceof StorageEntityFactoryInterface);
// bool(true)

var_dump($productFactory instanceof EntityFactoryInterface);
// bool(true)

$product = $productFactory->createEntityFromArray([
    'id' => 1,
    'sku' => 'pen',
]);

var_dump($product);
// object(Product)#4 (2) { ["id"]=> int(1) ["sku"]=> string(3) "pen" }
```

## Repository With Columns

Creating a storage repository with columns has the following advantages:

* casts values to primitive types on reading and writing
* specify a reader and writer to handle casting
* create database migration based on columns

```php
use Tobento\Service\Repository\Storage\StorageRepository;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Storage\InMemoryStorage;

class ProductRepository extends StorageRepository
{
    //
}

$repository = new ProductRepository(
    storage: new  InMemoryStorage(items: []),
    table: 'products',
    
    // specify the columns:
    columns: [
        Column\Id::new(),
        Column\Text::new('sku'),
        Column\Text::new('title')
            ->read(fn (string $value, array $attributes): string => ucfirst($value))
            ->write(fn (string $value, array $attributes): string => ucfirst($value)),
        Column\Bool::new('active'),
    ],
);
```

You may prefer to specify the columns on its class instead by using the ```configureColumns``` method:

```php
use Tobento\Service\Repository\Storage\StorageRepository;
use Tobento\Service\Repository\Storage\Column\ColumnsInterface;
use Tobento\Service\Repository\Storage\Column\ColumnInterface;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Storage\InMemoryStorage;

class ProductRepository extends StorageRepository
{
    /**
     * Returns the configured columns.
     *
     * @return iterable<ColumnInterface>|ColumnsInterface
     */
    protected function configureColumns(): iterable|ColumnsInterface
    {
        return [
            Column\Id::new(),
            Column\Text::new('sku'),
            Column\Text::new('title')
                ->read(fn (string $value, array $attributes): string => ucfirst($value))
                ->write(fn (string $value, array $attributes): string => ucfirst($value)),
            Column\Bool::new('active'),
        ];
    }
}

$repository = new ProductRepository(
    storage: new  InMemoryStorage(items: []),
    table: 'products',
);
```

## Columns Common Methods

### Type

The parameters set on the ```type``` method are used for [Migration](#migration) purpose only.

```php
use Tobento\Service\Repository\Storage\Column\Text;
use Tobento\Service\Repository\Storage\Column\Int;

$column = Text::new(name: 'name')
    ->type(length: 150, nullable: false, default: 'foo', parameters: ['charset' => 'utf8mb4']);
    
$column = Int::new(name: 'name')
    ->type(
        length: 20,
        unsigned: true,
        index: ['name' => 'index_name', 'column' => 'name', 'unique' => true, 'primary' => true],
    );
    
$column = Float::new(name: 'name', type: 'decimal')
    ->type(precision: 10, scale: 0);
```

Check out the [Service Database - Column Factory](https://github.com/tobento-ch/service-database#column-factory) ```createColumnFromArray``` method for more detail.

Check out the [Service Database - Index Factory](https://github.com/tobento-ch/service-database#index-factory) ```createIndexFromArray``` method for more detail.

### Read

You may use the read method to specify a reader (callable). The reader will automatically be called by the repository when attempting to retrieve the value.

```php
use Tobento\Service\Repository\Storage\Column\Text;

$column = Text::new(name: 'name')
    ->read(fn (string $value, array $attributes): string => ucfirst($value));
```

The value is casted to its column type before being passed to the reader!

### Write

You may use the write method to specify a writer (callable). The writer will automatically be called by the repository when attempting to write the value.

```php
use Tobento\Service\Repository\Storage\Column\Text;

$column = Text::new(name: 'name')
    ->write(fn (string $value, array $attributes): string => ucfirst($value));
```

The value is casted to its column type before being passed to the writer!

### Storable

```php
use Tobento\Service\Repository\Storage\Column\Text;

$column = Text::new(name: 'name')
    ->storable(false);
```

## Columns

### Boolean

```php
use Tobento\Service\Repository\Storage\Column\Boolean;

$column = Boolean::new(name: 'active');

$column = Boolean::new(name: 'active')
    ->type(default: true);
```

### Datetime

```php
use Tobento\Service\Repository\Storage\Column\Datetime;

$column = Datetime::new(name: 'created_at');

// with datetime type (default):
$column = Datetime::new(name: 'created_at', type: 'datetime')
    ->type(nullable: true);

// with date type:
$column = Datetime::new(name: 'created_at', type: 'date')
    ->type(nullable: true);

// with time type:
$column = Datetime::new(name: 'created_time', type: 'time')
    ->type(nullable: true);

// with timestamp type:
$column = Datetime::new(name: 'created_ts', type: 'timestamp')
    ->type(nullable: true);
```

**read**

You may use the read method to cast your value. Without specifying a read method, the value will be casted to a string only.

```php
use Tobento\Service\Repository\Storage\Column\Datetime;
use Tobento\Service\Dater\DateFormatter;
use DateTimeImmutable;

$column = Datetime::new(name: 'created_at');

$read = fn (mixed $value, array $attributes, DateFormatter $df)
    : DateTimeImmutable => $df->toDateTime(value: $value);

$column = Datetime::new(name: 'created_at')->read($read);
```

Check out the [Dater Service - DateFormatter](https://github.com/tobento-ch/service-dater#date-formatter) for more detail.

**write**

You may use the write method to cast your value. Without specifying a write method, the value will be casted to the following formats:

* datetime type: ```Y-m-d H:i:s```
* date type: ```Y-m-d```
* time type: ```H:i:s```
* timestamp type: timestamp string

```php
use Tobento\Service\Repository\Storage\Column\Datetime;
use Tobento\Service\Dater\DateFormatter;

$column = Datetime::new(name: 'created_at');

$write = fn (mixed $value, array $attributes, DateFormatter $df)
    : string => $df->format(value: $value, format: 'H:i:s');

$column = Datetime::new(name: 'created_at')->read($read);
```

Check out the [Dater Service - DateFormatter](https://github.com/tobento-ch/service-dater#date-formatter) for more detail.

### Float

```php
use Tobento\Service\Repository\Storage\Column\FloatCol;

$column = FloatCol::new(name: 'name');

// with float type (default):
$column = FloatCol::new(name: 'name', type: 'float')
    ->type(nullable: false, default: 0.5);

// with double type:
$column = FloatCol::new(name: 'name', type: 'double')
    ->type(nullable: false, default: 0.5);

// with decimal type:
$column = FloatCol::new(name: 'name', type: 'decimal')
    ->type(nullable: false, default: 0.5, precision: 10, scale: 0);
```

### Id

```php
use Tobento\Service\Repository\Storage\Column\Id;

$column = Id::new();

// with name (default is id):
$column = Id::new(name: 'some_id');

// with bigPrimary type (default):
$column = Id::new(type: 'bigPrimary')
    ->type(
        length: 18,
        unsigned: true,
        index: ['name' => 'index_name', 'primary' => true],
    );

// with primary:
$column = Id::new(type: 'primary')
    ->type(
        length: 5,
        unsigned: true,
        index: ['name' => 'index_name', 'primary' => true],
    );
```

### Integer

```php
use Tobento\Service\Repository\Storage\Column\Integer;

$column = Integer::new(name: 'name');

// with int type (default):
$column = Integer::new(name: 'name', type: 'int')
    ->type(length: 11, unsigned: true, nullable: false, default: 0);

// with tinyInt type:
$column = Integer::new(name: 'name', type: 'tinyInt')
    ->type(length: 5, unsigned: true, nullable: false, default: 0);

// with bigInt type:
$column = Integer::new(name: 'name', type: 'bigInt')
    ->type(length: 200, unsigned: true, nullable: false, default: 0);
```

### Json

```php
use Tobento\Service\Repository\Storage\Column\Json;

$column = Json::new(name: 'name');

$column = Integer::new(name: 'name')
    ->type(nullable: false, default: ['foo', 'bar']);
```

### Text

```php
use Tobento\Service\Repository\Storage\Column\Text;

$column = Text::new(name: 'sku');

// with string type (default):
$column = Text::new(name: 'sku', type: 'string')
    ->type(length: 100, nullable: false, default: '');

// with char type:
$column = Text::new(name: 'locale', type: 'char')
    ->type(length: 5, nullable: false, default: 'en');

// with text type:
$column = Text::new(name: 'desc', type: 'text')
    ->type(nullable: false, default: 'lorem ipsum');
```

### Translatable

```php
use Tobento\Service\Repository\Storage\Column\Translatable;

$column = Translatable::new(name: 'name');

// with string subtype (default):
$column = Translatable::new(name: 'name', subtype: 'string')
    ->type(nullable: false)
    ->read(fn (string $value, array $attributes, string $locale): string => strtoupper($value))
    ->write(fn (string $value, array $attributes, string $locale): string => strtoupper($value));
    
// with array subtype:
$column = Translatable::new(name: 'name', subtype: 'array')
    ->type(nullable: false)
    ->read(fn (array $value, array $attributes, string $locale): array => $value)
    ->write(fn (array $value, array $attributes, string $locale): array => $value);
```

**Read Attribute**

After reading, a ```StringTranslations::class``` or ```ArrayTranslations::class``` is being created depending on its column subtype ```string``` or ```array```.

```php
use Tobento\Service\Repository\Storage\Attribute\StringTranslations;

$repository->locale('en');
$repository->locales('en', 'de', 'fr');
$repository->localeFallbacks(['de' => 'en']);

$entity = $repository->findById(id: 2);

var_dump($entity->get('title') instanceof StringTranslations);
// bool(true)

// The title on the current locale set on the repository:
$title = (string)$entity->get('title');

// or:
$title = $entity->get('title')->get();

// specific locale:
$title = $entity->get('title')->get(locale: 'de');

// specific locale with default value
// if fallback locale value does not exist:
$title = $entity->get('title')->get(locale: 'fr', default: 'title');

// check if translation exists:
var_dump($entity->get('title')->has(locale: 'de'));
// bool(true)

// returns all translations:
var_dump($entity->get('title')->all();
// array(2) {["en"]=> string(5) "Title" ["de"]=> string(5) "Titel"}
```

```php
use Tobento\Service\Repository\Storage\Attribute\ArrayTranslations;

$repository->locale('en');
$repository->locales('en', 'de', 'fr');
$repository->localeFallbacks(['de' => 'en']);

$entity = $repository->findById(id: 2);

var_dump($entity->get('meta') instanceof ArrayTranslations);
// bool(true)

// The meta on the current locale set on the repository:
$meta = $entity->get('meta')->get();
// array(1) {["color"]=> string(3) "red"}

// specific locale:
$meta = $entity->get('meta')->get(locale: 'de');
// array(1) {["color"]=> string(3) "rot"}

// specific locale with default value
// if fallback locale value does not exist:
$meta = $entity->get('meta')->get(locale: 'fr', default: ['color' => 'rot']);
// array(1) {["color"]=> string(3) "red"}

// check if translation exists:
var_dump($entity->get('title')->has(locale: 'de'));
// bool(true)

// returns all translations:
var_dump($entity->get('meta')->all();
// array(2) {["en"]=> array(1) {["color"]=> string(3) "red"} ["de"]=> array(1) {["color"]=> string(3) "rot"}}

// The meta color on the current locale set on the repository:
$color = $entity->get('meta')->get(key: 'color');
// string(3) "red"

// specific locale:
$color = $entity->get('meta')->get(locale: 'de', key: 'color');
// string(3) "rot"

// specific locale with default value
// if fallback locale value does not exist:
$color = $entity->get('meta')->get(locale: 'fr', key: 'color', default: ['color' => 'rot']);
// string(3) "red"

// check if translation exists:
var_dump($entity->get('title')->has(locale: 'de', key: 'color'));
// bool(true)
```

## Translations

Confiure the locales for the repository:

```php
// current locale:
$repository->locale('en');

// only the defined locales are used:
$repository->locales('en', 'de', 'fr');

// fallbacks:
$repository->localeFallbacks(['de' => 'en']);
```

### Where Parameters Translations

Where clauses for translation columns (for all read/write methods with where parameter):

```php
$entities = $repository->findAll(where: [

    // query current locale set on the repository:
    'title' => ['like' => 'pe%'],
    
    // query specific locale using json syntax:
    'title->de' => ['like' => 'pe%'],
    
    // Array translations:
    // query current locale set on the repository:
    'options->color' => 'red', // same as: options->en->color
    
    // query specific locale using json syntax:
    'options->de->color' => 'red',
]);
```

### Write Translations

**create**

```php
$createEntity = $repository->create([
    'title' => [
        'en' => 'Title',
        'de' => 'Titel',
    ],
]);
```

**update**

```php
// updates all:
$updatedEntity = $repository->updateById(2, [
    'title' => [
        'en' => 'Title',
        'de' => 'Titel',
    ],
]);

// updates specific locale using json syntax:
$updatedEntity = $repository->updateById(2, [
    'title->de' => 'Title',
]);

// Array translations:
// updates specific locale using json syntax:
$updatedEntity = $repository->updateById(2, [
    'options->de->color' => 'red',
]);
```

## Migration

If you have set up your [Repository With Columns](#repository-with-columns), you might use the migration ```RepositoryAction::class``` and ```RepositoryDeleteAction::class``` to create your database migration from the columns defined.

First, you will need to install:

* ```composer require tobento/service-database-storage```
* ```composer require tobento/service-migration```

**Example of migration class**

```php
use Tobento\Service\Repository\Storage\Migration\RepositoryAction;
use Tobento\Service\Repository\Storage\Migration\RepositoryDeleteAction;
use Tobento\Service\Migration\MigrationInterface;
use Tobento\Service\Migration\ActionsInterface;
use Tobento\Service\Migration\Actions;

class UserMigration implements MigrationInterface
{
    public function __construct(
        protected UserRepository $userRepository,
    ) {}
    
    /**
     * Return a description of the migration.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Users migration';
    }
    
    /**
     * Return the actions to be processed on install.
     *
     * @return ActionsInterface
     */
    public function install(): ActionsInterface
    {
        return new Actions(
            new RepositoryAction(
                repository: $this->userRepository,
                description: 'User migration',
                
                // you might set items to be migrated
                items: [
                    ['email' => 'demo@example.com'],
                ],
            ),
        );
    }

    /**
     * Return the actions to be processed on uninstall.
     *
     * @return ActionsInterface
     */
    public function uninstall(): ActionsInterface
    {
        return new Actions(
            new RepositoryDeleteAction(
                repository: $this->userRepository,
                description: 'User migration',
            ),
        );
    }
}
```

You may check out the [Migration Service](https://github.com/tobento-ch/service-migration) for more detail.

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)