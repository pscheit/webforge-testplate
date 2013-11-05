<?php

namespace Webforge\Code\Test;

use Guzzle\Http\Message\Response;

class GuzzleResponseAsserter extends AbstractResponseAsserter {

  public function __construct(Response $response) {
    $this->assertionClass = __NAMESPACE__.'\\GuzzleAssertion';
    $this->response = $response;
  }

  public static function create(Response $response) {
    return new static($response);
  }

  protected function getCode() {
    return $this->response->getStatusCode();
  }

  protected function getBodyAsString() {
    return (string) $this->response->getBody();
  }

  protected function isContentType($contentType) {
    return $this->response->isContentType($contentType);
  }

  protected function getContentType() {
    return $this->response->getContentType();
  }
}
