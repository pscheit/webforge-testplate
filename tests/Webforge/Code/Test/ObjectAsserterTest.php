<?php

namespace Webforge\Code\Test;

use stdClass;

class ObjectAsserterTest extends Base {
  
  public function setUp() {
    $this->chainClass = __NAMESPACE__ . '\\ObjectAsserter';
    parent::setUp();

    $this->o = json_decode('
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
}'
    );
  }

  public function testNonObjectsFail() {
    $this->expectAssertionFail();

    $this->assertThatObject(array());
  }

  public function testEmptyObjectsPass() {
    $this->assertThatObject(new stdClass);
  }

  public function testAssertsPropertyToBeExisting() {
    $emptyProject = (object) array(
      'project'=>NULL
    );

    $this->assertThatObject($emptyProject)
      ->property('project');

    $this->expectAssertionFail();
    $this->assertThatObject($emptyProject)
      ->property('nonexisting');
  }

  public function testAssertsNestedProperties() {
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories');

    $this->expectAssertionFail();
    $this->assertThatObject($this->o)
      ->property('author')
        ->property('nonexisting');
  }

  public function testAssertsArrayTypes() {
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')->isArray();

    $this->expectAssertionFail('project.author');
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('author')->isArray();
  }

  public function testAssertsObjectTypes() {
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('author')->isObject();

    $this->expectAssertionFail('project.repositories is not an object');
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')->isObject();
  }

  public function testPropertyChecksForObject() {
    $this->expectAssertionFail('project.repositories is not an object');
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')
          ->property(0);
  }

  public function testAssertsKeysOfArrays() {
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')->isArray()
          ->key(0);

    $this->expectAssertionFail('project.repositories does not have key 2');
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')->isArray()
          ->key(2);
  }

  public function testAssertsPropertiesOfKeysOfAnArray() {
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')->isArray()
          ->key(0)
            ->property('type');

    $this->expectAssertionFail('property: $root.project.repositories[0].nonexisting does not exist');
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')->isArray()
          ->key(0)
            ->property('nonexisting');
  }


  public function testCanLeaveNestedAssertions() {
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')->isArray()
          ->key(0)
            ->property('type')->end()
          ->end()
        ->end()
        ->property('author')
          ->property('label');
  }

  public function testCanTestForConstraintsAsSecondParameterFromProperty() {
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('author')
          ->property('credits', $this->equalTo(7));

    $this->expectAssertionFail('project.author.credits does not match');
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('author')
          ->property('credits', $this->equalTo(5));
  }

  public function testCanTestForConstraintsAsSecondParameterFromKey() {
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')
          ->key(0, $this->logicalNot($this->isEmpty()));

    $this->expectAssertionFail('project.repositories[0] does not match');
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')
          ->key(0, $this->isEmpty());
  }

  public function testPropertyConstraintAsStringIsEqualTo() {
    $this->expectAssertionFail('two strings are equal');
    $this->assertThatObject($this->o)
      ->property('project')->property('author')->property('firstName', 'WrongFirstName');
  }

  public function testLengthOfArrayCanBeExpressedWithNumberToEqual() {
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')->isArray()->length(2);

    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')->isArray()->length($this->equalTo(2));
  }

  public function testLengthOfArrayCanBeExpressedGreaterThanConstraint() {
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')->isArray()->length($this->greaterThan(1));
  }

  public function testLengthOfArrayCanBeExpressedGreaterThanConstraint_AndFails() {
    $this->expectAssertionFail('length');
    $this->assertThatObject($this->o)
      ->property('project')
        ->property('repositories')->isArray()->length($this->greaterThan(2));
  }

  public function testDocuExample() {
    $this->assertThatObject($this->o)
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
  }
}
