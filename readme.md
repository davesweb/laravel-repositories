[![License](https://poser.pugx.org/davesweb/repositories/license)](https://packagist.org/packages/davesweb/repositories)
[![Latest Stable Version](https://poser.pugx.org/davesweb/repositories/v/stable)](https://packagist.org/packages/davesweb/repositories)
[![Latest Unstable Version](https://poser.pugx.org/davesweb/repositories/v/unstable)](https://packagist.org/packages/davesweb/repositories)
[![composer.lock](https://poser.pugx.org/davesweb/repositories/composerlock)](https://packagist.org/packages/davesweb/repositories)

# Davesweb Repositories

This package aims to provide a simple and elegant implementation for 
the repository package in Laravel. It provides the base framework for 
Repository interfaces and comes with a default Eloquent implementation. 

Once you've  installed the package, you can instantly use the interfaces and 
base implementations for your own repositories. See the section about usage 
for more details.
However, this package also provides an Artisan command that takes away 
the tedious task of writing your interfaces and base classes, by generating 
them for you. See the section about the Artisan command for more information.

### Requirements

- Laravel 5.5 or higher
- PHP 7.0 or higher
- Composer

### Installation 

Install the package via composer:
```
composer require davesweb\repositories
```

Or add the following line to your composer.json file in the require block and run the update command:
```
"davesweb\repositories": "^0.1"
```

```
composer update davesweb/repositories
```

If you use Laravel's auto-discover feature, the service provider is registered automatically. If not, add the following 
service provider to your app: `Davesweb\Repositories\RepositoryServiceProvider::class`.

Once the package is installed in your project, all that is left is to  
publish the config file and other resources. Simply run
```
php artisan vendor:publish
```

### Usage

Let's say you're writing an App in Laravel to keep track of your Lego sets collection. First thing you do is
create a model and a migration for your Set Entity:

```php
<?php

namespace App\Lego;

use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
}
```

Since you are an experienced developer, you don't want to access the database directly via the model from within your
app, but you want to add an abstraction layer. This is where repositories come into play. With this package,
implementing the repository pattern for your models is as easy as defining two classes with minimal content and adding
a line to your AppServiceProvider.

First thing you'll need is an interface for your new repository class. This isn't strictly necessary, but it is better
to program against interfaces. To keep everything clear, we'll add a new folder within our Lego folder: Repositories.
In that folder we'll create our interface class:

```php
<?php

namespace App\Lego\Repositories;

use Davesweb\Repositories\Repository;

interface SetRepository extends Repository
{
}
```

Note that this new interface doesn't have any methods at all. This is because the package does all the work for us,
everything we need for now is already in the parent Repository interface.

Next we'll need an actual implementation of our new interface that we can use. Fortunately, this package also helps
us with that. For now, only one type of implementation is available: Eloquent. To use the Eloquent implementation,
add a new class to the repositories folder:

```php
<?php

namespace App\Lego\Repositories;

use Davesweb\Repositories\Implementations\Eloquent\EloquentRepository;

class EloquentSetRepository extends EloquentRepository implements SetRepository
{
}
```

Notice that again there is no content. Everything we need in this class is defined in the base EloquentRepository,
the package does all the work again.

The only thing left to do is bind our implementation to our interface, so we can use the interface for dependency
injection. For this, add the following line to the register method in your AppServiceProvider:

```php
$this->app->bind(\App\Lego\Repositories\SetRepository::class, function() {
    return new \App\Lego\Repositories\EloquentSetRepository(new \App\Lego\Set());
});
```

Note that we pass along a new instance of our Set model to the EloquentSetRepository. The base EloquentRepository
needs this to communicate with the database.

### Artisan command

The above use case is off course the goal for this package, but you'll be writing a lot off the same code multiple
times just with different names and in different locations. Luckily this package also solves that problem by
providing an Artisan command that generates all the code for you.

Apart from the Repository interface and implementation, the Artisan command can also generate the model and an interface
for that model for you, as well as the migration. Simply run the following command:

```php
php artisan davesweb:make:repository {name}
```

Where name is the name of the repository you want to create, in this case Set.

The command has the following options:

|Option:|Default value:|Description:|
|---|---|---|
|--namespace|*none*|The namespace to use for the new classes and interfaces. This will be merged with the namespace option in the config file. See the config docs for more information about this.|
|--entities|true|Whether or not to also create the model and an interface for the model.|
|--migrations|false|Whether or not to also create the migration file.|
|--concrete|*Options defined in the config file*|A comma separated list of the names of the implementation types to use for your new repository class. The package only provides an Eloquent implementation, but you can add more yourself. See the config docs for more information.|

To re-create the classes and migrations from the usage example, without creating any code manually, use the following
command with the default configuration provided by the package:

```php
php artisan davesweb:make:repository Set --namespace=Lego --migrations=true
```

The only thing you need to do manually is adding the binding of the Model to your ModelInterface and of your Repository 
to your RepositoryInterface in the AppServiceProvider.

### Configuration

Configuration options only apply to the Artisan command for generating code. All options are defined as 

```php
return [
    'generator' => [
        'option' => 'value',
    ]
];
```

For most config values, the following parameters are available:

|Parameter|Value|
|---|---|
|{namespace}|Gets replaced by the --namespace option in the command call|
|{name}|Get replaced by the name provided in the command call|

For all parameters, slashes and backslashes are set to directory separators where needed, so the command works on both 
Windows and *nix machines.

|Option:|Description:|
|---|---|
|entity_interface_path|The location where the entity interfaces are saved.|
|entity_path|The location where the entities are saved.|
|interface_path|The location where the repository interfaces are saved.|
|repository_path|The location where the repository classes are saved.|
|migration_path|The location where the migrations are saved|
|entity_interface_namespace|The namespace used for entity interfaces|
|entity_namespace|The namespace used for entities|
|interface_namespace|The namespace used for repository interfaces|
|repository_namespace|The namespace used for repositories|
|entity_interface_prefix|Prefix for entity interface names|
|entity_interface_suffix|Suffix for entity interface names|
|entity_prefix|Prefix for entity names|
|entity_suffix|Suffix for entity names|
|interface_prefix|Prefix for repository interface names|
|interface_suffix|Suffix for repository interface names|
|repository_prefix|Prefix for repository names|
|repository_suffix|Suffix for repository names|
|available_implementations|An array where the key is the name if the implementation and the value is the full class name of the base class. See the default Eloquent for example|
|stubs|An array with the location of the stub files. The key is the name of the stub, the value the location. The following stub files are needed: 'entity', 'entity_interface', 'interface' and 'repository'|


### FAQ