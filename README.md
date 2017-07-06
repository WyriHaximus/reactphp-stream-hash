# On the fly hash calculator for [ReactPHP](https://github.com/reactphp/) streams

[![Linux Build Status](https://travis-ci.org/WyriHaximus/reactphp-stream-hash.png)](https://travis-ci.org/WyriHaximus/reactphp-stream-hash)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/react-stream-hash/v/stable.png)](https://packagist.org/packages/WyriHaximus/react-stream-hash)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/react-stream-hash/downloads.png)](https://packagist.org/packages/WyriHaximus/react-stream-hash/stats)
[![Code Coverage](https://scrutinizer-ci.com/g/WyriHaximus/reactphp-stream-hash/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/WyriHaximus/reactphp-stream-hash/?branch=master)
[![License](https://poser.pugx.org/WyriHaximus/react-stream-hash/license.png)](https://packagist.org/packages/wyrihaximus/react-stream-hash)
[![PHP 7 ready](http://php7ready.timesplinter.ch/WyriHaximus/reactphp-stream-hash/badge.svg)](https://travis-ci.org/WyriHaximus/reactphp-stream-hash)

### Installation ###

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `^`.

```
composer require wyrihaximus/react-stream-hash 
```

## Usage ##

Writable stream:

```php
$streamToHash = new ThroughStream();
// Constructor supports all hash_init arguments in the same order
$stream = new WritableStreamHash($streamToHash, $algo);
$stream->on('hash', function ($hash) {
    // Do with what you need the hash for
});

// Write to the stream
$stream->write('foo');
$stream->end('bar');
```

Readable stream:

```php
$streamToHash = new ThroughStream();
// Constructor supports all hash_init arguments in the same order
$stream = new ReadableStreamHash($streamToHash, $algo);
$stream->on('hash', function ($hash) {
    // Do with what you need the hash for
});

// The readable emits data when written to
$streamToHash->write('foo');
$streamToHash->end('bar');
```

## Contributing ##

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License ##

Copyright 2017 [Cees-Jan Kiewiet](http://wyrihaximus.net/)

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
