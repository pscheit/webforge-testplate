<?php

namespace Webforge\Code\Test;

abstract class AbstractResponseAsserter {

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

  protected $assertionClass = 'Webforge\Common\Exception';

  abstract protected function getCode();
  abstract protected function isContentType($contentType);
  abstract protected function getBodyAsString();

  public function body($content) {
    if (($body = $this->getBodyAsString()) !== $content) {
      throw $this->newAssertion(
        sprintf('Body %s is not equal to %s', $body, $content)
      );
    }
    return $this;
  }

  public function code($number) {
    if ($this->getCode() !== $number) {
      throw $this->newAssertion(
        sprintf('Response Status Code %d is not equal to %d', $this->response->getStatusCode(), $number)
      );
    }

    return $this;
  }

  public function format($formatOrContentType) {
    $contentType = array_key_exists($formatOrContentType, $this->formats)? $this->formats[$formatOrContentType] : $formatOrContentType;

    if (!$this->isContentType($contentType)) {
      throw $this->newAssertion(
        sprintf('Response ContentType %s does not match %s (%s)', $this->response->getContentType(), $formatOrContentType, $contentType)
      );
    }

    return $this;
  }

  protected function newAssertion($msg) {
    $c = $this->assertionClass;
    return new $c($msg);
  }
}
