<?php

namespace Webforge\Code\Test;

use Webforge\Common\ArrayUtil as A;

class BaseTest extends Base {

  protected $objects;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Code\\Test\\Base';
    parent::setUp();

    $this->objects = Array(
      (object) array('identifier'=>1, 'label'=>'moose'),
      (object) array('identifier'=>3, 'label'=>'joose'),
      (object) array('identifier'=>2, 'label'=>'plank')
    );
  }

  public function testItsRunning() {
    $this->assertInstanceOf('Webforge\Code\Test\Base', $this);
  }

  public function testPluckAliasReduceCollectionReducesToProperties() {
    $plucked = A::pluck($this->objects, 'identifier');

    $this->assertEquals(
      $plucked,
      $this->pluck($this->objects, 'identifier')
    );

    $this->assertEquals(
      $plucked,
      $this->reduceCollection($this->objects, 'identifier')
    );

    /* this is difficult to test without pulling Psc\Data\* in? (Doctrine in?) */
    /*
    $this->assertEquals(
      $plucked,
      $this->reduceCollection(new \Psc\Data\ArrayCollection($this->objects), 'identifier')
    );
    */
  }

  public function testGetMockForAbstractClassDoesExpandWithNamespace() {
    $mock = $this->getMockForAbstractClass('AnAbstractClass');

    $this->assertInstanceOf(__NAMESPACE__.'\\AnAbstractClass', $mock);
  }
}

abstract class AnAbstractClass {

}