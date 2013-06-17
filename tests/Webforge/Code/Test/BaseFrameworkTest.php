<?php

namespace Webforge\Code\Test;

use Webforge\Common\ArrayUtil as A;
use Webforge\Framework\Container;

class BaseFrameworkTest extends Base {

  public function setUp() {
    $this->chainClass = 'Webforge\\Code\\Test\\Base';
    parent::setUp();
  }

  public function testGetPackageReturnsTheLocalWebforgePackage() {
    $GLOBALS['env']['container'] = (object) array('webforge'=>$mock = $this->getMock('Webforge\Framework\Container'));

    $mock->expects($this->once())->method('getLocalPackage');

    $this->getPackage();
  }

  public function testGetPackageDirIsAFakeAndReturnsTheGlobalsEnvRootDir() {
    $this->assertEquals(
      (string) $GLOBALS['env']['root']->sub('something/'),
      $this->getPackageDir('something/')
    );
  }

  public function testFrameworkHelperCanCreateAEnvironmentContainer() {
    $this->assertInstanceOf('Webforge\Code\Test\FrameworkHelper', $this->frameworkHelper);

    $this->assertInstanceOf('Webforge\CMS\EnvironmentContainer', $this->frameworkHelper->createEnvironmentContainer());
  }
}
