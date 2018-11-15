# SimpleBusDebugBundle

The **SimpleBusDebugBundle** allows you to debug your [SimpleBus EventBus and CommandBus](https://github.com/SimpleBus/SimpleBus).

## Requirements
[simple-bus/symfony-bridge](https://github.com/SimpleBus/SymfonyBridge)

## Installation

Via Composer

``` bash
$ composer require omouren/simplebus-debug-bundle
```

Register the bundle in the application kernel :

```php
<?php
// app/AppKernel.php
// ...
public function registerBundles()
{
    $bundles = [
        // ...
        new Omouren\SimpleBusDebugBundle\OmourenSimpleBusDebugBundle(),
        // ...
    ];
// ...
```

Usage
=====

```
omouren:debug:simple-bus:event-bus
omouren:debug:simple-bus:command-bus
```