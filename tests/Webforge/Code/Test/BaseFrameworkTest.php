<?php

namespace Webforge\Code\Test;

use Webforge\Common\ArrayUtil as A;

class BaseFrameworkTest extends Base {

  public function setUp() {
    $this->chainClass = 'Webforge\\Code\\Test\\Base';
    parent::setUp();
  }


  public function testGetPackageReturnsTheLocalWebforgePackage() {
    $this->markTestSkipped('how should i test this without pulling whole webforge in?');
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
