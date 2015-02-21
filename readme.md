[![Build Status](https://travis-ci.org/mobileka/scope-applicator.svg)](https://travis-ci.org/mobileka/scope-applicator)
[![Code Climate](https://codeclimate.com/github/mobileka/scope-applicator.png)](https://codeclimate.com/github/mobileka/scope-applicator)
[![Coverage Status](https://coveralls.io/repos/mobileka/scope-applicator/badge.png?branch=master)](https://coveralls.io/r/mobileka/scope-applicator?branch=master)

ScopeApplicator brings an elegant way to sort and filter data to your Laravel projects.

- [Overview](#overview)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage (with Models)](#usage-with-models)
- [Configuration options](#configuration-options)
    - [Alias](#alias)
    - [Type](#type)
    - [Default](#default)
    - [Allow Empty](#allow-empty)
    - [Keys (experimental)](#keys-experimental)
- [A better usage (with Repositories)](#a-better-usage-with-repositories)
- [Other ways to use this trait](#other-ways-to-use-this-trait)
- [Credits](#credits)
- [Contributing](#contributing)
- [License](#license)

## Overview

ScopeApplicator is an easy and logical way to achieve something like this:

`/posts` – returns a list of all posts

`/posts?recent` – returns only recent posts

`/posts?author_id=5` – returns posts belonging to an author with an `id=5`

`/posts?author_id=5&order_by_title=desc&status=active` – returns only active posts belonging to an author with an `id=5` and sorts them by a title in a descending order

## Requirements

— php >= 5.4

Tested with Laravel 4 and 5 but can be used with other versions or even with other frameworks / PHP projects.

## Installation

`composer require mobileka/scope-applicator 1.0.*`

## Usage (with Models)

> Make sure you are familiar with Laravel's [query scopes](http://laravel.com/docs/eloquent#query-scopes) before you dive in

Let's learn by example. First of all, we'll implement an `author_id` filter for `posts` table.

> Please note that this is going to be a basic example and it's not the most optimal way of doing things ;)

These are steps required to achieve this:

1. Create a basic `PostController` which outputs a list of posts when you hit `/posts` route
2. Create a `userId` scope at the `Post` model (and it has to extend the `Mobileka\ScopeApplicator\Laravel\Model` class)
3. Tell ScopeApplicator that this scope is available and give it an alias
4. Visit `/posts?author_id=1` and enjoy the result

Ok, let's cover these step by step.

— The `PostController`:

```php
<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }
}
```

— The `Post` model:

```php
<?php namespace App\Models;

use Mobileka\ScopeApplicator\Laravel\Model;

class Post extends Model
{
    public function scopeUserId($builder, $param = 0)
    {
        if (!$param) {
            return $builder;
        }
        
        return $builder->where('user_id', '=', $param);
    }
}
```

> Note that it extends `Mobileka\ScopeApplicator\Laravel\Model`

— Now we have to replace `Post::all()` in our controller with `Post::handleScopes()` and tell this mehotd which scopes are available for filtering:

```php
<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Post;

class PostController extends Controller
{
    // an array of available scopes
    public $scopes = [
        'userId'
    ];

    public function index()
    {
        return Post::handleScopes($this->scopes)->get();
    }
}
```

At this moment you can add some dummy data to your `posts` table and make sure that you can filter it by hitting the following route:
`/posts?userId=your_number`

But, as we wanted `author_id` instead of `userId`, let's create an alias for this scope:

```php
<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Post;

class PostController extends Controller
{
    // an array of available scopes
    public $scopes = [
        'userId' => [
            // Here it is!
            'alias' => 'author_id'
        ]
    ];

    public function index()
    {
        return Post::handleScopes($this->scopes)->get();
    }
}
```
— That's it! Now you can visit `/posts?author_id=x` and check the result.

`alias` is only one of the many available scope configuration options. These are described in the next chapter.

## Configuration options

Here's the list of all configuration options available:

#### Alias

Sometimes we don't want our users to see the actual scope name. Alias is a key that maps a URL query parameter to a scope name.

Example:

```php
public $scopes = [
    'orderByTitle' => ['alias' => 'order_by_title']
];
```

`/posts?order_by_title` – the `orderByTitle` scope will be applied

`/posts?orderByTitle` – no scope will be applied

#### Type

This option allows to cast a type of the parameter value before it will passed to a scope.

In the example from [Usage](#usage-with-models) section, we can tell ScopeAplicator to cast the `author_id` value to `int` before this value will be passed to `userId` scope. 

When type is set to `bool` or `boolean`, only `1` and `true` will be converted to `true`. Everything else is considered  to be `false`.

If `type` is set to something different than `bool` or `boolean`, `settype` php function will be called.

Examples:

```php
public $scopes = [
    'userId' => [
        'alias' => 'author_id',
        'type' => 'int'
    ],
    'new' => [
        'type' => 'bool'
    ]
];
```

`/posts?author_id=123sometext555` – the `userId` scope will be applied with integer `123` as an argument

`/posts?new=true` – the `new` scope will be applied with boolean `true` as its argument  

`/posts?new=yes` – the `new` scope will be called with boolean `false` as its argument  

#### Default

When this option is set, a scope will be applied on every single request, even there are no query parameters in URL matching a scope name or alias.

Examples:

```php
public $scopes = [
    'userId' => [
        'alias' => 'author_id',
        'default' => 5
    ]
];
```

`/posts?author_id=1` - the `userId` scope will be applied with `1` as an argument  

`/posts` - the `userId` scope will be applied with `5` as an argument

#### Allow Empty

`allowEmpty` is used when an empty string should be passed to a scope as an argument. This option is set to `false` by default, so the empty string *won't* be passed to a scope.

Examples:

```php
public $scopes = [
    'userId' => [
        'alias' => 'author_id',
        'allowEmpty' => true
    ]
];
```

`/posts?author_id` – the `userId` scope will be applied with `''` (empty string) as an argument.

> Please not that when `allowEmpty` is set to `false` (what is the default behavior), you always have to provide a default value for the scope argument (see an example from the [Usage](#usage-with-models) section where the `$param` argument of the `userId` scope has a default value set to `0`). Otherwise, the "Missing argument" exception will be thrown when `/posts?author_id` route is being hit

> Also note that when `allowEmpty` is set to `true`, a default value of a scope argument will be ignored and an empty string will be passed instead.


#### Keys (experimental)

Keys are used when a scope accepts multiple arguments.

Example:

```php
public $scopes = [
    'createdAt' => [
        'alias' => 'created_at',
        'keys' => ['from', 'to']
    ]
];
```

`/posts?created_at[from]=000-00-00&created_at[to]=2014-07-23` – the `createdAt` scope will be applied with `'0000-00-00'` as the first argument and `'2014-07-23'` as a second argument

> Please note that I don't recommend using this right now as it is an experimental feature. Create two separate scopes instead (`createdAtFrom` and `createAtTo`) until this feature is marked as "stable".

## A better usage (with Repositories)

ScopeApplicator can also be used with [Repositories](http://blog.armen.im/laravel-and-repository-pattern). It was actually designed to be used this way.


To achieve this, your repository has to extend the `Mobileka\ScopeApplicator\Laravel\Repository` class.

The ScopeApplicator is already attached to this class, so you'll have a new `applyScopes()` method available in repositories extending it.

Let's see an example `BaseRepository` *before* we extend the mentioned above class:

```php
<?php namespace Acme\Repositories;

class BaseRepository
{
    protected $dataProvider;
    
    public function __construct($dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }
    
    public function getDataProvider()
    {
        return $this->dataProvider;
    }
    
    public function all()
    {
        return $this->getDataProvider()->all();
    }
}
```

`DataProvider` is typically an instance of a `Model`.

And now what it looks like with ScopeApplicator:

```php
<?php namespace Acme\Repositories;

use Mobileka\ScopeApplicator\Laravel\Repository;

class BaseRepository extends Repository
{
    protected $dataProvider;
    
    public function __construct($dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }
    
    public function getDataProvider()
    {
        return $this->dataProvider;
    }
    
    public function all($scopes = [])
    {
        return $this->applyScopes($this->getDataProvider(), $scopes)->get();
    }
}
```

Pay closer attention to `all` method. Now it accepts an array of scopes (the same array we were passing to `Model::handleScopes()`).

Instead of directly calling `all` on our DataProvider, we now use the `applyScopes()` which accepts the DataProvider instance as a first argument and an array of scope configuration as the second.

## Other ways to use this trait

As described above, the main usage scenario of the ScopeApplicatior is, well, applying scopes for data filtering purposes.

But this trait just parses the URL query parameters and calls methods of the class which is provided as the first argument of the `applyScopes` method. In fact, it's possible to change the way it behaves: for example, it can call methods returned by a database query or an API call.

To do this, you just have to override the `getInputManager` method of the class ScopeApplicator is attached to and return an instance of a custom class which implements the `Mobileka\ScopeApplicator\InputManagerInterface`. An example of a such class is `Mobileka\ScopeApplicator\InputManagers\LaravelInputManager`.

## Credits

Scope Applicator is inspired by the [has_scope](https://github.com/plataformatec/has_scope) Ruby gem. 

## Contributing

If you have noticed a bug or have suggestions, you can always create an issue or a pull request (use PSR-2). We will discuss the problem or a suggestion and plan the implementation together.

## License

ScopeApplicator is an open-source software and licensed under the [MIT License](https://github.com/mobileka/scope-applicator/blob/master/license).