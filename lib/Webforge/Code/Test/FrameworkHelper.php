<?php

namespace Webforge\Code\Test;

use Psc\CMS\EnvironmentContainer;
use Psc\CMS\Session;
use Webforge\Common\Mock\Session as SessionMock;

class FrameworkHelper {

  public function createEnvironmentContainer(Session $session = NULL) {
    $env = new EnvironmentContainer();

    $env->setSession(
      $session ?: $this->createSession()
    );

    return $env;
  }

  public function createSession() {
    return new SessionMock();
  }

  /**
   * @return Webforge\Setup\BootContainer
   */
  public function getBootContainer() {
    return $GLOBALS['env']['container'];
  }

  /**
   * @return Webforge\Framework\Project
   */
  public function getProject() {
    return $this->getBootContainer()->getProject();
  }

  /**
   * @return Webforge\Framework\Container
   */
  public function getWebforge() {
    return $this->getBootContainer()->getWebforge();
  }
}
