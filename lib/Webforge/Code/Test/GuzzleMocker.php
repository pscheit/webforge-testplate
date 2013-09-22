<?php

namespace Webforge\Code\Test;

use Webforge\Common\System\Dir;
use Webforge\Common\System\File;
use Webforge\Common\String as S;
use Guzzle\Plugin\Mock\MockPlugin;

/**
 * Creates a unit testable Guzzle Client and mocks its responses from files
 * 
 */
class GuzzleMocker {

  /**
   * 
   */
  protected $client;

  /**
   * @var Guzzle\Plugin\Mock\MockPlugin
   */
  protected $plugin;

  /**
   * @var Dir
   */
  protected $responseDirectory;

  public function __construct(Dir $responseDirectory) {
    $this->responseDirectory = $responseDirectory;

    $this->plugin = new \Guzzle\Plugin\Mock\MockPlugin();
    
  }

  public function getClient() {
    if (!isset($this->client)) {
      $this->client = new \Guzzle\Http\Client();
      $this->client->addSubscriber($this->plugin);
    }

    return $this->client;
  }

  /**
   * Adds a mocked response in FIFO order for the requests
   * 
   * if $response is a string it can be a url for a file searched in $responseDirectory.
   * The mocker searches for a file with .guzzle-response as extension
   * 
   * e.g. addResponse('search1')  searches in $responseDirectory for 'search1.guzzle-response' as a file
   * Files have to be like this: http://guzzlephp.org/testing/unit-testing.html (see Queing Mock responses)
   * @param Guzzle\Http\Message\Response|string 
   * @return Guzzle\Http\Message\Response the actually added response
   */
  public function addResponse($response) {
    if (is_string($response)) {
      $fileUrl = S::expand(rtrim($response, '/'), '.guzzle-response');
      $file = File::createFromUrl($fileUrl, $this->responseDirectory);
      return $this->plugin->addResponse((string) $file); // plugin checks existance
    } else {
      return $this->plugin->addResponse($response);
    }
  }

  public function getReceivedRequests() {
    return $this->plugin->getReceivedRequests();
  }
}
