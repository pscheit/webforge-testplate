<?php

namespace Webforge\Code\Test;

use Symfony\Component\HttpFoundation\Response;

class SymfonyResponseAsserter extends AbstractResponseAsserter {

  public function __construct(Response $response) {
    $this->assertionClass = __NAMESPACE__.'\\SymfonyAssertion';
    $this->response = $response;
  }

  public static function create(Response $response) {
    return new static($response);
  }

  protected function getCode() {
    return $this->response->getStatusCode();
  }

  protected function getBodyAsString() {
    return (string) $this->response->getContent();
  }

  protected function isContentType($contentType) {
    return $this->response->headers->get('content-type') === $contentType;
  }
}
