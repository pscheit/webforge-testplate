<?php

namespace Webforge\Code\Test;

use Guzzle\Http\Message\Response;

class GuzzleResponseAsserterTest extends Base {
  
  public function setUp() {
    $this->chainClass = __NAMESPACE__ . '\\GuzzleResponseAsserter';
    parent::setUp();

    $this->response200 = new Response(200, array('content-type'=>'application/json'), '{"message": "ok"}');
    $this->response404 = new Response(404, array('content-type'=>'application/json'), '{"message": "page not found"}');
    $this->response400 = new Response(400, array('content-type'=>'text/html'), '<html><body>That was bad try again</body></html>');
    $this->response304 = new Response(304, array('content-type'=>'text/javascript'), 'alert("jep, not modified");');
  }

  public function testCodeVerifiesTheStatusCodeofTheResponse() {
    $this->setExpectedException('Webforge\Code\Test\GuzzleAssertion', 'Response Status Code 404 is not equal to 200');

    $this->assertGuzzleResponse($this->response404)->code(200);
  }

  public function testCodeVerifiesTheStatusCodeCorrectlyOfTheResponse() {
    $this->assertInstanceOf($this->chainClass, $this->assertGuzzleResponse($this->response404)->code(404));
  }

  /**
   * @dataProvider provideFormatTests
   */
  public function testFormatMatchesTheContentType($responseName, $format, $result) {
    $response = $this->$responseName;

    if (!$result)
      $this->setExpectedException('Webforge\Code\Test\GuzzleAssertion');

    $this->assertInstanceOf($this->chainClass, 
      $this->assertGuzzleResponse($response)->format($format)
    );
  }

  public static function provideFormatTests() {
    $tests = array();
  
    $test = function() use (&$tests) {
      $tests[] = func_get_args();
    };
  
    $test('response200', 'html', FALSE);
    $test('response200', 'json', TRUE);

    $test('response304', 'javascript', TRUE);
    $test('response304', 'js', TRUE);
    $test('response304', 'json', FALSE);

    $test('response400', 'html', TRUE);
    $test('response400', 'js', FALSE);
  
    return $tests;
  }
}
