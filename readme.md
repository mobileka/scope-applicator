[![Build Status](https://travis-ci.org/mobileka/scope-applicator.svg)](https://travis-ci.org/mobileka/scope-applicator)
[![Code Climate](https://codeclimate.com/github/mobileka/scope-applicator.svg)](https://codeclimate.com/github/mobileka/scope-applicator)
[![Coverage Status](https://coveralls.io/repos/mobileka/scope-applicator/badge.svg?branch=master)](https://coveralls.io/r/mobileka/scope-applicator?branch=master)

ScopeApplicator brings an elegant way of sorting and filtering data to your PHP projects.

- [Overview](#overview)
- [Requirements](#requirements)
- [Installation](#installation)
- [Supported frameworks](#supported-frameworks)
- [Usage](#usage)
- [Configuration options](#configuration-options)
    - [Alias](#alias)
    - [Type](#type)
    - [Default](#default)
    - [Allow Empty](#allow-empty)
    - [Keys (experimental)](#keys-experimental)
- [Credits](#credits)
- [Contributing](#contributing)
- [License](#license)

## Overview

ScopeApplicator is an easy, logical and framework-agnostic way to achieve something like this:

`/posts` – returns a list of all posts

`/posts?recent` – returns only recent posts

`/posts?author_id=5` – returns posts belonging to an author with an `id=5`

`/posts?author_id=5&order_by_title=desc&status=active` – returns only active posts belonging to an author with an `id=5` and sorts them by a title in a descending order

## Requirements

— php >= 5.4

## Supported frameworks:

* [Laravel 5.1.x](https://github.com/mobileka/scope-applicator-laravel) ([documentation](https://github.com/mobileka/scope-applicator-laravel/blob/master/readme.md))
* [Laravel 5.0.x](https://github.com/mobileka/scope-applicator-laravel/tree/laravel_4_and_5.0) ([documentation](https://github.com/mobileka/scope-applicator-laravel/blob/laravel_4_and_5.0/readme.md))
* [Laravel 4.x.x](https://github.com/mobileka/scope-applicator-laravel/tree/laravel_4_and_5.0) ([documentation](https://github.com/mobileka/scope-applicator-laravel/blob/laravel_4_and_5.0/readme.md))
* [Laravel 3.x.x](https://github.com/mobileka/scope-applicator-laravel/tree/laravel_4_and_5.0) ([documentation](https://github.com/mobileka/scope-applicator-laravel/blob/laravel_4_and_5.0/readme.md))
* [Yii 2.0.x](https://github.com/mobileka/scope-applicator-yii2) ([documentation](https://github.com/mobileka/scope-applicator-yii2/blob/master/readme.md))


## Installation

If you're using one of the [supported frameworks](#supported-frameworks), follow framework-specific installation instructions on its page.

Otherwise, you can install ScopeApplicator as follows:

`composer require mobileka/scope-applicator 1.1.*`

## Usage

If you're using one of the [supported frameworks](#supported-frameworks), follow framework-specific usage instructions on its page.

Otherwise, you have to create a custom binding for your own framework or PHP-project.

*TODO: add detailed instructions about bindings*

## Configuration options

ScopeApplicator supports several configuration options and these are described in this chapter.


#### Alias

Sometimes we don't want our users to see the actual scope name. Alias is a key that maps a URL query parameter to a scope name.

Example:

```php
public $scopes = [
    'orderByTitle' => ['alias' => 'order_by_title']
];
```

`/posts?order_by_title` – an `orderByTitle` scope will be applied

`/posts?orderByTitle` – no scope will be applied

#### Type

This option allows to cast a type of a parameter value before it will passed to a scope.

When type is set to `bool` or `boolean`, only `1` and `true` will be converted to `true`. Everything else is considered to be `false`.

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

`/posts?author_id=123sometext555` – a `userId` scope will be applied with integer `123` as an argument

`/posts?new=true` – a `new` scope will be applied with boolean `true` as its argument  

`/posts?new=yes` – a `new` scope will be called with boolean `false` as its argument  

#### Default

When this option is set, a scope will be applied on every single request, even when there are no query parameters in URL matching a scope name or alias.

Examples:

```php
public $scopes = [
    'userId' => [
        'alias' => 'author_id',
        'default' => 5
    ]
];
```

`/posts?author_id=1` - a `userId` scope will be applied with `1` as an argument  

`/posts` - a `userId` scope will be applied with `5` as an argument

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

`/posts?author_id` – a `userId` scope will be applied with `''` (empty string) as an argument.

> Please note that when `allowEmpty` is set to `false` (what is a default behavior), you always have to provide a default value for the scope argument. Otherwise, the "Missing argument" exception will be thrown when `/posts?author_id` route is being hit.

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

`/posts?created_at[from]=000-00-00&created_at[to]=2014-07-23` – a `createdAt` scope will be applied with `'0000-00-00'` as a first argument and `'2014-07-23'` as a second

> Please note that I don't recommend using this right now as it's an experimental feature. Create two separate scopes instead (`createdAtFrom` and `createAtTo`) until this feature is marked as "stable".

## Credits

Scope Applicator is inspired by [has_scope](https://github.com/plataformatec/has_scope) Ruby gem. 

## Contributing

If you have noticed a bug or have suggestions, you can always create an issue or a pull request (use PSR-2). We will discuss the problem or a suggestion and plan the implementation together.

## License

ScopeApplicator is an open-source software and licensed under the [MIT License](https://github.com/mobileka/scope-applicator/blob/master/license).
