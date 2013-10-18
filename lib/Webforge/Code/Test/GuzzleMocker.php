<?php

namespace Webforge\Code\Test;

use Webforge\Common\System\Dir;
use Webforge\Common\System\File;
use Webforge\Common\String as S;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Plugin\History\HistoryPlugin;
use Guzzle\HTTP\Message\Response as GuzzleResponse;

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
   * @var Guzzle\Plugin\History\HistoryPlugin
   */
  protected $history;

  /**
   * @var Dir
   */
  protected $responseDirectory;

  public function __construct(Dir $responseDirectory) {
    $this->responseDirectory = $responseDirectory;

    $this->plugin = new MockPlugin();
    $this->history = new HistoryPlugin();
  }

  public function getClient($baseUrl = NULL) {
    if (!isset($this->client)) {
      $this->client = new \Guzzle\Http\Client($baseUrl);
      $this->client->addSubscriber($this->plugin);
      $this->client->addSubscriber($this->history);
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

  /**
   * Records a Response Result to an api to a file which then can be used for the guzzle Mocker
   * 
   * @param string the name where to store the response
   */
  public function record(GuzzleResponse $response, $responseName) {
    $fileUrl = S::expand(rtrim($responseName, '/'), '.guzzle-response');
    $file = File::createFromUrl($fileUrl, $this->responseDirectory);
    $file->getDirectory()->create();

    $file->writeContents((string) $response);

    return $file;
  }

  /**
   * Records the last response which was made with the guzzle mocked client
   * @return Webforge\Common\System\File written file
   */
  public function recordLastResponse($responseName) {
    return $this->record($this->history->getLastRequest()->getResponse(), $responseName);
  }

  public function getReceivedRequests() {
    return $this->plugin->getReceivedRequests();
  }
}
