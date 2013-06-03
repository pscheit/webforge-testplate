<?php

namespace Webforge\Code\Test;

class BaseTest extends Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Code\\Test\\Base';
    parent::setUp();
  }

  public function testItsRunning() {
    $this->assertInstanceOf('Webforge\Code\Test\Base', $this);
  }
}
