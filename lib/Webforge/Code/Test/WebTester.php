<?php

namespace Webforge\Code\Test;

class WebTester {

  /**
   * Checks all referenced resources in the last response to be avaible
   */
  public function checkHTMLResources(HTMLTesting $test, GuzzleTester $guzzle) {

    $resources = array();
    foreach ($test->css('html head link[rel="stylesheet"]')->getQuery() as $link) {
      $resources[] = (object) array('relativeUrl'=>$link->attr('href'), 'format'=>'css', 'description'=>'head stylesheet');
    }

    foreach ($test->css('html script[type="text/javascript"]') as $script) {
      $src = $script->attr('src');
      if (!empty($src)) {
        $resources[] = (object) array('relativeUrl'=>$src, 'format'=>'javascript', 'description'=>'javascript in head or body');
      }
    }

    return $this->checkResources($resources, $guzzle);
  }

  /**
   * @param array $resources an array of .relativeUrl .description .format (css|javascript)
   * @return array $resources to each resource there is a .response and a .request added
   */
  public function checkResources(Array $resources, GuzzleTester $guzzle) {
    foreach ($resources as $resource) {
      $resource->request = $guzzle->get($resource->relativeUrl);

      $guzzle->assertResponse($resource->response = $guzzle->dispatch($resource->request))
        ->code(200)
        ->format($resource->format);
    }

    return $resources;
  }
}
