# webforge-testplate

Testplate for webforge for base testcases

## installation
Use [Composer](http://getcomposer.org) to install.
```
composer require -v --prefer-source webforge/testplate:dev-master
```

to run the tests use:
```
phpunit
```

## configuration

if you want to use the `getFile()` or other file helpers you need to 
```php
$bootLoader->registerPackageRoot();
```
in your bootstrap.php. If you use `registerCMSContainer` this is already done.