Phar Compact
============

[![Build Status][]](https://travis-ci.org/phine/lib-phar-compact)
[![Coverage Status][]](https://coveralls.io/r/phine/lib-phar-compact)
[![Latest Stable Version][]](https://packagist.org/packages/phine/phar-compact)
[![Total Downloads][]](https://packagist.org/packages/phine/phar-compact)

Integrates the compact library with the phar library.

Requirement
-----------

- PHP >= 5.3.3
- [Phine Compact][] >= 1.2
- [Phine Exception][] >= 1.0.0
- [Phine Observer][] >= 2.0
- [Phine Phar][] >= 1.0.0

Installation
------------

Via [Composer][]:

    $ composer require "phine/phar-compact=~1.0"

Usage
-----

The library provides a single subject observer for lib-phar. This observer
can be registered to the following subjects in order to compact file contents
as they are being added to the archive:

- `Builder::ADD_FILE`
- `Builder::ADD_STRING`

To create an observer, you will need a new instance of `CompactObserver`.

```php
use Phine\Compact;
use Phine\Compact\Collection;
use Phine\Phar\Builder;
use Phine\Phar\Compact\CompactObserver;

// create the archive builder
$builder = Builder::create('example.phar');

// create the collection of compactors
$collection = new Collection();
$collection->addCompactor(new Compact\Json());
$collection->addCompactor(new Compact\Php());
$collection->addCompactor(new Compact\Xml());

// create the compactor observer
$observer = new CompactObserver($collection);

// register it with the builder subjects
$builder->observe(Builder::ADD_FILE, $observer);
$builder->observe(Builder::ADD_STRING, $observer);
```

With the observer registered, any time a file is added to the archive via the
`addFile()` or `addFromString()` methods, the contents of the supported file
types will be automatically compacted before being added to the archive.

Documentation
-------------

You can find the API [documentation here][].

License
-------

This library is available under the [MIT license](LICENSE).

[Build Status]: https://travis-ci.org/phine/lib-phar-compact.png?branch=master
[Coverage Status]: https://coveralls.io/repos/phine/lib-phar-compact/badge.png
[Latest Stable Version]: https://poser.pugx.org/phine/phar-compact/v/stable.png
[Total Downloads]: https://poser.pugx.org/phine/phar-compact/downloads.png
[Phine Compact]: https://github.com/phine/lib-compact
[Phine Exception]: https://github.com/phine/lib-exception
[Phine Observer]: https://github.com/phine/lib-observer
[Phine Phar]: https://github.com/phine/lib-phar
[Composer]: http://getcomposer.org/
[documentation here]: http://phine.github.io/lib-phar-compact
