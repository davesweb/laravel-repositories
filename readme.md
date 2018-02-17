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

Once the package is installed in your project, all that is left is to  
publish the config file and other resources. Simply run
```
php artisan vendor:publish --provider=RepositoryServiceProvider
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
|Option|Default value|Description|
|=|=|
|

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

Configuration options only apply to the Artisan command for generating code.

### FAQ