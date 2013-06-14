<?php

namespace Webforge\Code\Test;

class GuzzleTest extends Base {

  protected $guzzle;

  protected $baseUrl = 'http://localhost/does/not/matter';

  public function setUp() {
    parent::setUp();

    $this->guzzle = new GuzzleTester($this->baseUrl);
    $this->requestClass = 'Guzzle\Http\Message\RequestInterface';
  }

  public function testCreatesAClientWithTheCorrectBaseUrl__AndConstructsOnlyOnce() {
    $this->assertInstanceOf('Guzzle\Http\Client', $client = $this->guzzle->getClient());
    $this->assertEquals($this->baseUrl, $client->getBaseUrl());

    $this->assertSame($client, $this->guzzle->getClient());
  }

  public function testCreatedRequestHasDefaultAuthWhenAuthIsSet() {
    $user = 'mySelf';
    $pw = 'its secret';

    $this->guzzle->setDefaultAuth($user, $pw);

    $request = $this->guzzle->createRequest('GET', '/');

    $this->assertEquals($user, $request->getUserName());
    $this->assertEquals($pw, $request->getPassword());
  }

  public function testGetShortageForCreate() {
    $this->assertInstanceOf($this->requestClass, $request = $this->guzzle->get('/'));

    $this->assertEquals('GET', $request->getMethod());
  }

  public function testPostShortageForCreate() {
    $this->assertInstanceOf($this->requestClass, $request = $this->guzzle->post('/', $body = array('some'=>'postdata')));

    $this->assertEquals('POST', $request->getMethod());
    $this->assertEquals('some=postdata', (string) $request->getPostFields(), 'post body is not injected into request');
  }

  public function testPutShortageForCreate() {
    $this->assertInstanceOf($this->requestClass, $request = $this->guzzle->put('/'));

    $this->assertEquals('PUT', $request->getMethod());
  }

  public function testDeleteShortageForCreate() {
    $this->assertInstanceOf($this->requestClass, $request = $this->guzzle->delete('/'));

    $this->assertEquals('DELETE', $request->getMethod());
  }
}