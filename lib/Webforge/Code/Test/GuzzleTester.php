<?php

namespace Webforge\Code\Test;

/* 
in Psc:

use Webforge\Code\Test\GuzzleTester;

$this->guzzle = new GuzzleTester($this->getProject()->getBaseUrl());
$hostConfig = $this->getProject()->getHostConfig();
$this->guzzle->setDefaultAuth($hostConfig->req('cmf.user'),$hostConfig->req('cmf.password'));
*/

use Webforge\Common\JS\JSONConverter;
use Webforge\Common\JS\JSONParsingException;

class GuzzleTester {

  protected $response;

  protected $request;

  protected $defaultAuth;
  protected $baseUrl;

  protected $client;

  protected $jsonParser;

  public function __construct($baseUrl) {
    $this->baseUrl = $baseUrl;

    if (!class_exists('Guzzle\Http\Client')) {
      throw new \RuntimeException('You need to require guzzle/guzzle: 3.5.* in your composer.json to run the Guzzle Tester');
    }

    $this->jsonParser = new JSONConverter();
  }

  public function getClient() {
    if (!isset($this->client)) {
      $this->client = new \Guzzle\Http\Client($this->baseUrl);

      $defaultAuth = $this->defaultAuth;
      $this->client->getEventDispatcher()->addListener('client.create_request', function (\Guzzle\Common\Event $e) use ($defaultAuth) {
        $request = $e['request'];

        if (isset($defaultAuth)) {
          list($user, $pw) = $defaultAuth;

          $request->setAuth($user, $pw);
        }
        
        $request->setHeader('X-Psc-Cms-Connection', 'tests');
        $request->setHeader('X-Psc-Cms-Debug-Level', 15);
        if ($request->getMethod() != 'POST' && $request->getMethod() != 'GET') {
          $request->setHeader('X-Psc-Cms-Request-Method', $request->getMethod());
        }

        $sendDebugSessionCookie = FALSE;
        if ($sendDebugSessionCookie && $hostConfig->get('uagent-key') != NULL) {
          $request->addCookie('XDEBUG_SESSION', $hostConfig->get('uagent-key'));
        }

        if (!$request->hasHeader('Accept')) {
          $request->setHeader('Accept', $request->getHeader('Content-Type').'; q=0.8');
        }
        
      });
    }

    return $this->client;
  }

  /**
   * @return Guzzle\Http\Message\RequestInterface
   */
  public function createRequest($method, $uri, $headers = NULL, $body = NULL, $options = array()) {
    return $this->request = $this->getClient()->createRequest($method, $uri, $headers, $body, $options);
  }

  /**
   * @return Guzzle\Http\Message\RequestInterface
   */
  public function get($uri, $headers = NULL, $options = array()) {
    return $this->request = $this->getClient()->get($uri, $headers, $options);
  }

  /**
   * @return Guzzle\Http\Message\RequestInterface
   */
  public function post($uri, $body = NULL, $headers = NULL, $options = array()) {
    return $this->request = $this->getClient()->post($uri, $headers, $body, $options);
  }

  /**
   * @return Guzzle\Http\Message\RequestInterface
   */
  public function put($uri, $body = NULL, $headers = NULL, $options = array()) {
    return $this->request = $this->getClient()->put($uri, $headers, $body, $options);
  }

  /**
   * @return Guzzle\Http\Message\RequestInterface
   */
  public function delete($uri, $body = NULL, $headers = NULL, $options = array()) {
    return $this->request = $this->getClient()->delete($uri, $headers, $body, $options);
  }


  /**
   * @return Guzzle\Http\Message\ResponseInterface
   */
  public function dispatch($request = NULL) {
    try {

      return $this->response = $this->getClient()->send($this->request = $request ?: $this->request);
    } catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
      return $this->handleServerError($e);
    }
  }

  public function handleServerError(\Guzzle\Http\Exception\ServerErrorResponseException $e) {
    $response = $e->getResponse();

    if (
      $response->getStatusCode() >= 400 && 
      $response->getHeader('X-Psc-Cms-Error') == 'true' && 
      ($msg = $response->getHeader('X-Psc-Cms-Error-Message')) != NULL
    ) {
      $msg = "\n".'Fehler auf der Seite: '.$msg;
      throw new \RuntimeException($msg, 0, $e);
    } else {
      throw $e;
    }
  }


  public function dispatchJSON($request = NULL) {
    $response = $this->dispatch($request);

    try {
      $raw = $response->getBody($asString = TRUE);

      return $this->jsonParser->parse($raw);
    } catch (JSONParsingException $e) {
      throw new \RuntimeException(sprintf('JSON Parse Fehler. Mit Response: %s', $response), 0, $e);
    }
  }

  public function setDefaultAuth($user, $password) {
    $this->defaultAuth = array($user, $password);
    return $this;
  }

  public function debug() {
    if (isset($this->repsonse)) {
      print '------------ Acceptance - Guzzle (Fail) ------------'."\n";
      print '--------- Request ---------'."\n";
      print $this->request."\n";
      print '--------- Response ---------'."\n";
      print $this->response;
      print "\n";
      print '------------ / Guzzle ------------'."\n";
    }
  }
}
