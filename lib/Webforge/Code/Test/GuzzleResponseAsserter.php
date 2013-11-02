<?php

namespace Webforge\Code\Test;

use Guzzle\Http\Message\Response;

class GuzzleResponseAsserter {

  protected $response;

  protected $formats = array(
    'js'=>'text/javascript',
    'javascript'=>'text/javascript',
    'json'=>'application/json',
    'html'=>'text/html',
    'text'=>'text/plain',
    'css'=>'text/css',
    'less'=>'text/less',
  );

  public function __construct(Response $response) {
    $this->response = $response;
  }

  public static function create(Response $response) {
    return new static($response);
  }

  public function code($number) {
    if ($this->response->getStatusCode() !== $number) {
      throw new GuzzleAssertion(
        sprintf('Response Status Code %d is not equal to %d', $this->response->getStatusCode(), $number)
      );
    }

    return $this;
  }

  public function format($formatOrContentType) {
    $contentType = array_key_exists($formatOrContentType, $this->formats)? $this->formats[$formatOrContentType] : $formatOrContentType;

    if (!$this->response->isContentType($contentType)) {
      throw new GuzzleAssertion(
        sprintf('Response ContentType %s does not match %s (%s)', $this->response->getContentType(), $formatOrContentType, $contentType)
      );
    }

    return $this;
  }
}
