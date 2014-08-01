[![Build Status](https://travis-ci.org/mobileka/scope-applicator.svg)](https://travis-ci.org/mobileka/scope-applicator)
[![Code Climate](https://codeclimate.com/github/mobileka/scope-applicator.png)](https://codeclimate.com/github/mobileka/scope-applicator)
[![Coverage Status](https://coveralls.io/repos/mobileka/scope-applicator/badge.png?branch=master)](https://coveralls.io/r/mobileka/scope-applicator?branch=master)

Scope Applicator is a PHP trait that makes data filtering and sorting easy.

It can be used with Laravel, Symfony and any other PHP (5.4 and newer) application.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**

- [Introduction](#introduction)
- [Installation](#installation)
- [Theory](#theory)
- [Usage](#usage)
- [Scope configuration options](#scope-configuration-options)
- [How to use without repositories](#how-to-use-without-repositories)
- [How to use Scope Applicator with other frameworks](#how-to-use-scope-applicator-with-other-frameworks)
- [Other ways to use this trait](#other-ways-to-use-this-trait)
- [Future plans](#future-plans)
- [Credits](#credits)
- [Contributing](#contributing)
- [License](#license)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

### Introduction

In every single project we have pages where we list data. In most cases we need to implement some kind of filtering functionality there.

Scope Applicatior makes this easy because in several simple steps you'll get something like this:

`example.com/posts` - returns a list of all posts

`example.com/posts?popular` - returns only popular posts

`example.com/posts?new&votes=5&order_by_title=desc` - show only new posts with 5 votes and sort them by title in descending order 

`example.com/posts?search=query` - show only posts having "query" in their title or content

### Installation

Add `mobileka/scope-applicator` to your project dependencies with composer:

`composer require mobileka/scope-applicator dev-master`

### Theory
Scope Applicator adds `applyScope` method to any class it is attached to.

As a first argument this method receives an instance of a class (typically, a model or a repository) which contains scope implementations.

The second argument is an array of allowed scopes.

This method always returns an instance provided as a first argument, so you can call other methods on it.

### Usage

> If you are not familiar with repository pattern, I recommend you to read [this](http://blog.armen.im/laravel-and-repository-pattern/) article in my blog before you dive in.

The first example will be for Laravel framework, because Scope Applicator contains pre-written classes for it.

> You should also be familiar with [query scopes](http://laravel.com/docs/eloquent#query-scopes) in Laravel.

The easiest way to use Scope Applicator is to create a repository which extends `Mobileka\ScopeApplicator\Laravel\Repository` class.

The trait is already attached to it, so you can use it right from the box:

```php
<?php namespace Acme\Repositories;

use Acme\Models\Post;
use Mobileka\ScopeApplicator\Laravel\Repository as BaseRepository;

class EloquentPostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function getPosts($scopes = [])
    {
        return $this->applyScopes(new Post, $scopes)->get();
    }
}
```

In a controller we should provide an array of scopes which can be applied to the model, then pass it to the repository method:

```php
<?php namespace Acme\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Acme\Repositories\PostRepositoryInterface;

class PostController extends BaseController
{
    protected $repository;

    // Configuration options will be discussed in an appropriate section
    protected $scopes = [
        'popular', 'new', 'old',
        'contains' => [
            'alias' => 'search'
        ],
        'votes' => [
            'type' => 'int'
        ],
        'orderByTitle' => [
            'alias' => 'order_by_title'
        ]
    ];

    public function __construct(PostRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->repository->getPosts($this->scopes);
    }
}

```

And, finally, the model:

```php
<?php namespace Acme\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function scopePopular($query)
    {
        return $query->where('votes', '>', 5);
    }

    public function scopeVotes($query, $votes)
    {
        return $query->whereVotes($votes);
    }

    public function scopeNew($query)
    {
        return $query->whereNew(1);
    }

    public function scopeOld($query)
    {
        return $query->whereNew(0);
    }

    public function scopeContains($query, $phrase)
    {
        return $query->where('title', 'like', "%{$phrase}%")
            ->orWhere('content', 'like', "%{$phrase}%");
    }

    public function scopeOrderByTitle($query, $direction = 'asc')
    {
        return $query->orderBy('title', $direction);
    }
}
```

That's it! Now you can filter and sort your posts as you want with a simple change in the url.

### Scope configuration options

As mentioned above, we need to configure scopes for every single controller.

These are all possible configuration options:

* `alias` - sometimes we don't want our users to see the actual scope name. Alias is a key that maps a url parameter to a scope name.

Example:

```php
public $scopes = [
	'orderByTitle' => ['alias' => 'order_by_title']
];
```

Now  `posts?order_by_title` url will be parsed by the Scope Applicator and it will understand that `orderByTitle` scope should be called.

* `type` - this option allows to cast a type of the parameter value. When set to `bool` or `boolean`, only `1` and `true` will be converted to `true`. Everything else is considered  to be `false`. If `type` is set to something different than `bool` or `boolean`, `settype` php function will be called.

Examples:

```php
public $scopes = [
	'votes' => ['type' => 'int'],
	'new' => ['type' => 'bool']
];
```

`posts?votes=123sometext555` - the `votes` scope will be called with integer `123` as an argument  
`posts?new=true` - the `new` scope will be called with `true` as its argument  
`posts?new=yes` - the `new` scope will be called with `false` as its argument  

* `default` - when this option is set, the scope will be called on every single request. When there are no parameters in url matching a scope name with `default` parameter, the scope will still be called with `default` passed as an argument.

Examples:

```php
public $scopes = [
	'orderByTitle' => ['alias' => 'order_by_title', 'default' => 'asc'],
];
```

`posts?order_by_title=desc` - the `orderByTitle` scope will be called with `'desc'` as an argument  
`posts?order_by_title=asc` - the `orderByTitle` scope will be called with `'asc'` as an argument  
`posts` - the `orderByTitle` scope will be called with `'asc'` as an argument  

* `keys` - is used when a scope accepts several arguments.

Example:

```php
public $scopes = [
	'createdAt' => [
		'alias' => 'created_at',
		'keys' => ['from', 'to']
	]
];
```

`posts?created_at[from]=000-00-00&created_at[to]=2014-07-23` - the `createdAt` scope will be called with `'0000-00-00'` as the first argument and `'2014-07-23'` as a second argument

### How to use without repositories

I don't recommend doing this but if you don't want to use repositories, you can attach the trait directly to your model.

In this case you should create a BaseModel which implements the `getInputManager` abstract method of the `ScopeApplicator` trait. It is still easy in Laravel:

```php
<?php namespace Acme\Models;

use Mobileka\ScopeApplicator\Laravel\InputManager;

abstract class BaseModel extends \Eloquent {
	use \Mobileka\ScopeApplicator\ScopeApplicator;

    public function getInputManager()
    {
        return new LaravelInputManager;
    }
}
```

Of course, your models should now extend this class and the controller will look something like this:

```php
<?php namespace Acme\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Acme\Models\Post;

class PostController extends BaseController
{
    protected $model;

    protected $scopes = [
        'popular', 'new', 'old',
        'contains' => [
            'alias' => 'search'
        ],
        'votes' => [
            'type' => 'int'
        ],
        'orderByTitle' => [
            'alias' => 'order_by_title'
        ]
    ];

    public function __construct()
    {
        $this->model = new Post;
    }

    public function index()
    {
        return $this->model->applyScopes($this->model, $this->scopes)->get();
    }
}
```
Wrong and ugly! Don't be lazy and use [repositories](http://blog.armen.im/laravel-and-repository-pattern/) instead :)

### How to use Scope Applicator with other frameworks

The Scope Applicator is not tested with other frameworks but I am pretty sure that it can be used with almost any PHP project.

There are several steps to make this trait usable:

1) First of all, read the documentation I provided for Laravel. All frameworks are almost identical in terms of usage.

2) Create a class which has a `get` method accepting two arguments: `key` and `default`. This class should return HTTP Request parameters by `key` and if there is no `key` found, return the `default` value. In other words, you need to create a class which implements the `Mobileka\ScopeApplicator\InputManagerInterface`. Let us call this class "InputManager" in the future.

3) You need to `use` the `\Mobileka\ScopeApplicator\ScopeApplicator` trait in a class which handles database queries. In other words, this class should be called from a controller. This is going to take a role of the "Repository" which is discussed in Laravel-related documentation chapters.

4) The `Mobileka\ScopeApplicator\ScopeApplicator` has the `getInputManager` abstract method. You need to override it and return an instance of the InputManager we discussed before. 

The rest is identical to Laravel. If it is still not clear, read the next chapter and try to analyze tests of the package.

### Other ways to use this trait
As described above, the main usage scenario of the Scope Applicatior is... well... applying scopes for data filtering purposes.

But, as experienced developers have already guessed, this trait just parses the url parameters and calls methods of the class which is provided as the first argument of the `applyScopes` method. In fact, it is possible to change the way it behaves: for example, it can call methods returned by a database query or an API call.

To do this, you just need to override the `getInputManager` method of the `Mobileka\ScopeApplicator\ScopeApplicator` trait and return an instance of a custom class which implements the `Mobileka\ScopeApplicator\InputManagerInterface`. An example of a such class is `Mobileka\ScopeApplicator\InputManagers\LaravelInputManager`.

You can also avoid using scopes if, for some reason, you don't like them. This model will work fine too:

```php
<?php namespace Acme\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function popular()
    {
        return $this->where('votes', '>', 5);
    }

    public function votes($votes)
    {
        return $this->whereVotes($votes);
    }

    public function new()
    {
        return $this->whereNew(1);
    }

    public function old()
    {
        return $this->whereNew(0);
    }

    public function contains($phrase)
    {
        return $this->where('title', 'like', "%{$phrase}%")
            ->orWhere('content', 'like', "%{$phrase}%");
    }

    public function orderByTitle($direction = 'asc')
    {
        return $this->orderBy('title', $direction);
    }
}
```

> Note that custom model methods as above **can't** be mixed with scopes. Therefore, make sure to use a consistent way of defining these methods.


### Future plans

Two more scope configuration options should be implemented:

1. `allowEmpty` - by default empty parameters (an empty string) are not passed as a scope argument. This is made to allow default scope argument values. This behavior should be possible to change because sometimes a scope can accept an empty string as an argument.

2. `in` - this option will filter parameters and make sure that their values are enumerated in `in` array.

I also want to make a video tutorial explaining usage and internals.

### Credits

Scope Applicator is heavily influenced by the [has_scope](https://github.com/plataformatec/has_scope) Ruby gem. 

### Contributing

If you have noticed a bug or have suggestions, you can always create an issue. We will discuss the problem or / and a suggestion and plan the implementation.

### License

Scope Applicator is an open-source library and licensed under the [MIT License](https://github.com/mobileka/scope-applicator/blob/master/license).








