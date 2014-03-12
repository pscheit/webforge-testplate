# Objects Asserter

to read the full story goto (objects-testing.md)[./objects-testing.md].

## Documentation

Start with this example:

```php
$this->assertThatObject($json)
  ->property('project')->isObject()
    ->property('repositories')->isArray()->length($this->greaterThanOrEqual(1))
      ->key(0)->isObject()
        ->property('url', $this->equalTo('http://github.com/pscheit/repository.git'))->end()
        ->property('type', $this->stringContains('vcs'))->end()
      ->end()
      ->key(1)->isObject()
        // ...
      ->end()
    ->end()
    ->property('author')
      ->property('label', 'Philipp Scheit')->end() // short form for equals
      ->property('firstName', 'Philipp')->end()
      ->property('lastName', 'Scheit')->end()
    ->end()
    ->property('url', 'www.repo.com')->end()
  ->end()
;
```

  - Every call to `->property()` or `->key()` changes the current assertion context
  - `->isObject()` is implicit checked if `->property()` is called
  - `->is()` accepts all constraints from phpunit that you can use in with for mocks. Per default it uses `$this->equalTo` for strings
  - use `->length($constraint)` for arrays where `$constraint` can be an integer for exact matches or `$this->greaterThan(2)` for example
  - `->get()` leaves the context chain and returns the object of the current graph
  - `->debug()` calls var_dump for the current context (very handy at the top, if you want to look around)
  - note that `->property('xx')->is('value')` should be prefered over `->property('xx', 'value')` because of readability