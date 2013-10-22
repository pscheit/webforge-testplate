<?php

namespace Webforge\Code\Test;

use Webforge\Common\System\Dir;
use Webforge\Common\System\File;
use Webforge\Common\String as S;
use Webforge\Common\Preg;
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

  /**
   * @var Dir
   */
  protected $requestDirectory;

  public function __construct(Dir $responseDirectory, Dir $requestDirectory = NULL) {
    $this->responseDirectory = $responseDirectory;
    $this->requestDirectory = $requestDirectory ?: $this->responseDirectory->sub('../requests/');

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
   * Records a Response/Request to an api to a file which then can be used for the guzzle Mocker
   * 
   * @param string the name where to store the response/request
   * @return file written file
   */
  public function record($responseOrRequest, $name, array $normalizeHeaders = array('Authorization')) {
    if ($responseOrRequest instanceof GuzzleResponse) {
      $type = 'response';
      $directory = $this->responseDirectory;
    } else {
      $type = 'request';
      $directory = $this->requestDirectory;
    }

    $fileUrl = S::expand(rtrim($name, '/'), '.guzzle-'.$type);
    $file = File::createFromUrl($fileUrl, $directory);

    $file->getDirectory()->create();
    $file->writeContents($this->normalizeHeaders((string) $responseOrRequest, $normalizeHeaders));

    return $file;
  }

  /**
   * Returns the text from the reponse/request with normalized headers and viewed line-endings
   * 
   * @return string
   */
  public function normalizeMessageText($messageText, $normalizeHeaders = array('Authorization')) {
    // remove authorization and such
    $messageText = $this->normalizeHeaders($messageText, $normalizeHeaders);

    return S::eolVisible($messageText);
  }

  /**
   * @return string
   */
  public function normalizeHeaders($messageText, Array $headers) {
    if (count($headers) == 0) return $messageText;

    $fieldsRegexp = implode('|', array_map('preg_quote', $headers));
    return Preg::replace($messageText, '/^('.$fieldsRegexp.'):(.*)$/im', '\1: (normalized)');
  }

  /**
   * Records the last response which was made with the guzzle mocked client
   * @return Webforge\Common\System\File written file
   */
  public function recordLastResponse($responseName) {
    return $this->record($this->history->getLastRequest()->getResponse(), $responseName);
  }

  /**
   * Records the last response which was made with the guzzle mocked client
   * @return Webforge\Common\System\File written file
   */
  public function recordLastRequest($requestName) {
    return $this->record($this->history->getLastRequest(), $requestName);
  }

  public function getHistory() {
    return $this->history;
  }

  public function getReceivedRequests() {
    return $this->plugin->getReceivedRequests();
  }
}
