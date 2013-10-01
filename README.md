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

## usage (css tester)

if you install `webforge/dom` along testplate you can use fancy css tests (when you extend from `Webforge\Code\Test\Base`).

```php
$this->html = <<<'HTML'
<div class="team">
  <h1 class="active">team</h1>
    <div class="mitarbeiter">
      imme
    </div>
    <div class="mitarbeiter">
      philipp
    </div>
</div>
HTML;

$this->css('div.team')->count(1)
  ->css('h1')->count(1)->hasClass('active')->end()
   ->css('div.mitarbeiter')->count(2)->end()
 ;
```

[See here for a more detailed documentation](http://wiki.ps-webforge.com/psc-cms:dokumentation:css-tester)