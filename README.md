# About

This package allows watching folders for any change and being notified when it happens.

PHP does not implement native filesystem watchers so the package basically maps the content of the watched folders and rescan them regularly to identify any potential change.

This package has been initially developed to help monitoring file changes within a website static generator tool and improve workflow.

![](https://img.shields.io/badge/php-8.1%2B-7A86B8)
[![](https://img.shields.io/badge/gitmoji-%20😜%20😍-FFDD67.svg)](https://gitmoji.dev/)
![](https://analytics.chsxf.dev/GitHubStats.badge/folder-watcher/README.md)

# Requirements

This package requires PHP 8.1+ but has no other dependency.

# Installation

Use [Composer](https://getcomposer.org/) to install the package:

```sh
composer require chsxf/folder-watcher
```

# How to Use

This package has been designed to be easy to use but flexible.

## Instantiation

```php
require_once('vendor/autoload.php');

use chsxf\FolderWatcher\IWatchResponder;
use chsxf\FolderWatcher\Runner;

// Instantiates the runner and configures the watched folders
// A second int parameter is available to set the refresh interval in milliseconds (set by default at 50 ms)
$runner = new Runner(['assets', 'templates']);
```

## Reponders

Changes are reported to "responders". A responder can be any object implementing the `IWatchResponder` interface or a simple callable that accepts an array as its single parameter.

Using a callable allows you respond to changes only and you won't receive any other messages that the `IWatchResponder` interface supports, like a notification at the start of a watch loop.

In both cases, changes are reported as an array of `WatchChange` objects.

```php
function responderCallable(array $changes) {
    // Do something with the array
}

$runner->addResponder(responderCallable(...));
```

## Watching

When everything is configured, you can start watching for changes.

> [!IMPORTANT]
> This will effectively send your PHP process in an infinite loop, interrupted periodically by sleeping for how many milliseconds you've set as the refresh interval, or to notify your responders of any changes.

```php
$runner->watch();
```

# Support

This package is under active development.

However, support is not guaranteed in any way. [Pull requests](https://github.com/chsxf/folder-watcher/pulls) or [issues](https://github.com/chsxf/folder-watcher/issues) are welcomed but you may wait for some time before getting any answer.
