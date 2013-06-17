<?php

namespace Webforge\Code\Test;

use Webforge\CMS\EnvironmentContainer;
use Webforge\CMS\Session;
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
}
