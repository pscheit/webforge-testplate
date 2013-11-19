# How to test objects

Complex objects are very difficult to test with PHPunit. Lets say you have an object structure like this:

```php
{
  "project": {
    
    "repositories": [
      {
        "url": "http://github.com/pschei/repository.git",
        "type": "vcs"
      },
      {
        "url": "http://github.com/pscheit/repository2.git",
        "type": "git"
      }
    ],
    
    "author": {
      "label": "Philipp Scheit",
      "firstName": "Philipp",
      "lastName": "Scheit",
      "credits": 7
    },

    "url": "www.repo.com"
  }
}
```

Lets say you want to assert ALL values in this structure, but its okay when new values come to the structure. E.g.: we don't care that author may have a property email later on (that should not make the test fail).

```php
$this->assertObjectHasAttribute($json, 'project');
$project = $json->project;
$this->assertObjectHasAttribute($project, 'repositories');
$this->assertInternalType('array', $project->repositories);
$this->assertInternalType('object', $repo0 = $project->repositories[0]);
$this->assertEquals($repo0->url);
```

etc. This is not nearly the half of it.. And this is without verbose messages like: "repo0 has no property url". It will just state "undefined property url" and you will have no clue why your tests are falling. So this needs a better solution.

## Using JSONPath

Json path seems to be something like XPath (W3C) for JSON. But the [implementation from the standard](http://goessner.net/articles/JsonPath/) looks, really really ugly. There is a port to php5 but people that name their functions callback03 and callback04 are very very suspicious to me.
Basically the tests could look like this:

```php
$this->assertJsonPathEquals("Philipp Scheit", "project.author.label");
$this->assertJsonPathEquals("vcs", "project.repositories[0].type");
```
But this isn't any better. This would require to hack into JSONPath (and build a version on your own) that would be able to say something like: "the json path project.repositories[1].type is not defined because repositories key 1 is not existing".
Seems complicated

## Using a DSL (implemented)

I just learned that fluid interfaces break object encapsulation and are not a good way to build your api - except for DSLs. And I think an object testing DSL seems to make sense:

```php
$this->assertThatObject($json)
  ->property('project')->isObject()
    ->property('repositories')->isArray()->is($this->greaterThanOrEqual(1))
      ->key(0)->isObject()
        ->property('url', $this->equalTo('http://github.com/pschei/repository.git'))->end()
        ->property('type', $this->equalTo('vcs'))->end()
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

This has the advantage that every assertion can be context sensitive. If the assertion for the label of the author does fail the DSL could show only the context of author and not the whole object class.
It's a little verbose with all those `end()` calls, but correctly indentend easy to understand. It is easy to extend because new assertions and getter like: wholePropertyEquals() can be easy inserted into the structure. It's easy to implement because you only need one object that changes the context where its currently reading in. There are non complicated assertions made (all with basic assertions from phpunit) because once you want to navigate to propery project and it is not existing, you have a failing and verbose assertion. The implementation can be fast, because no natural parsing is needed.
