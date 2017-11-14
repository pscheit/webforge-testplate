<?php

namespace Webforge\Code\Test;

class GuzzleMockerTest extends Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Code\\Test\\GuzzleMocker';
    parent::setUp();

    $this->markTestSkipped('no guzzle mock plugin found');
    $this->guzzleMocker = new GuzzleMocker($this->getTestDirectory('guzzle-responses/'));
  }

  public function testGuzzleMockerCanMockAGuzzleServiceFifo() {
    $this->guzzleMocker->addResponse('eu');
    $this->guzzleMocker->addResponse(new \Guzzle\Http\Message\Response(201));

    $client = $this->guzzleMocker->getClient();

    // The following request will get the mock response from the plugin in FIFO order
    $request = $client->get('http://www.doesnotmatter.com/');
    $response = $request->send();

    // The MockPlugin maintains a list of requests that were mocked
    $this->assertContainsOnly($request, $this->guzzleMocker->getReceivedRequests());

    $this->assertEquals(
      '<?xml version="1.0" encoding="UTF-8"?>'."\n".
      '<LocationConstraint xmlns="http://s3.amazonaws.com/doc/2006-03-01/">EU</LocationConstraint>',
      (string) $response->getBody()
    );

    $request = $client->get('http://www.doesnotmatter.com/');
    $response = $request->send();

    $this->assertEquals(201, $response->getStatusCode());
  }
}
