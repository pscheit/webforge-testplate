<?php

namespace Webforge\Code\Test;

use Webforge\Common\ArrayUtil as A;

class BaseFileUtilsTest extends Base {

  public function setUp() {
    $this->chainClass = 'Webforge\\Code\\Test\\Base';
    parent::setUp();
  }

  public function testGetFileReturnsAnEXISTINGFileInstanceFromTheTestDirectory() {
    $this->assertInstanceOf('Webforge\Common\System\File', $this->getFile('existing.txt'));
    $this->assertInstanceOf('Webforge\Common\System\File', $this->getFile('images/1.jpg'));
  }

  public function testOtherParamsFromGetFileAreDeprecated() {
    $this->setExpectedException('Webforge\Common\DeprecatedException');

    $this->getFile('1.jpg', 'images/');
  }

  public function testLastParamFromGetFileIsDeprecated() {
    $this->setExpectedException('Webforge\Common\DeprecatedException');

    $this->getFile('1.jpg', 'images/', 'common');
  }

  public function testIsAssertingExistance() {
    $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');

    $this->getFile('non-existing');
  }

  public function testRunPHPFileDoesExist() {
    $this->assertTrue(method_exists($this, 'runPHPFile'));
  }
}
