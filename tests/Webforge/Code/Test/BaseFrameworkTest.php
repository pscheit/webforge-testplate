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
    $this->assertInstanceOf('Webforge\Framework\Package\Package', $package = $this->getPackage());
    $this->assertEquals('webforge/testplate', $package->getIdentifier());
  }

  public function testGetPackageDirIsAFakeAndReturnsTheGlobalsEnvRootDir() {
    $this->assertEquals(
      (string) $GLOBALS['env']['root']->sub('something/'),
      $this->getPackageDir('something/')
    );
  }

  public function testFrameworkHelperCanCreateAEnvironmentContainer() {
    $this->assertInstanceOf('Webforge\Code\Test\FrameworkHelper', $this->frameworkHelper);
    $this->assertInstanceOf('Psc\CMS\EnvironmentContainer', $this->frameworkHelper->createEnvironmentContainer());
  }
}
