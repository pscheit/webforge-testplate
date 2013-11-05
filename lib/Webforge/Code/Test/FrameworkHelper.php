<?php

namespace Webforge\Code\Test;

use Psc\CMS\EnvironmentContainer;
use Psc\CMS\Session;
use Webforge\Common\Mock\Session as SessionMock;

class FrameworkHelper {

  protected $projectContainer;

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
   * @return Webforge\Framework\Project
   */
  public function getProject() {
    return $this->getBootContainer()->getProject();
  }

  /**
   * @return Webforge\Setup\BootContainer
   */
  public function getBootContainer() {
    return $GLOBALS['env']['container'];
  }

  public function getProjectContainer() {
    if (!isset($this->projectContainer))
      $this->projectContainer = new \Webforge\ProjectStack\Container($this->getProject());

    return $this->projectContainer;
  }

  /**
   * @return Webforge\Framework\Container
   */
  public function getWebforge() {
    return $this->getBootContainer()->getWebforge();
  }
}
