# Testing Guzzle Clients

Do you know the [Guzzle HTTP client](http://guzzlephp.org/)? It's a really straight forward, well coded and tested HTTP Client. Symfony likes it, Drupal uses it.  
But HTTP Clients are really a mess to test. Testplate (and Guzzle) will help you:

## Mocking a response with the GuzzleMocker

First of all: the credit goes to guzzle. They invented a plugin that does the hard word. I just make it work in the testplate. Use it in your testcase like this:

```php
use Webforge\Code\Test\GuzzleMocker;

$this->guzzleMocker = new GuzzleMocker($this->getTestDirectory('guzzle-responses/'));
```

The mocker returns a `Guzzle\Http\Client` instance for your application. Lets say we have an HTTPService which is based on a guzzle client. And we want to test if the service is working correctly (e.g.: doing the right things when getting the responses from the guzzle client).
We inject the guzzle client into our service like this:

```php
$mockedService = new ACME\HTTPService($this->guzzleTester->getClient());
```

after or before, we inject a response to the guzzleMocker.
```php
$this->guzzleMocker->addResponse('TVDB/search-bbt');

// or create a full message
$this->guzzleMocker->addResponse(new \Guzzle\Http\Message\Response(201));
```

This mocks the responses for the next(!) two requests (any url) made to the client.
The first addResponse is more fancy, because it refers to a file. The file is searched for with:

```php
$this->getTestDirectory('guzzle-responses/')->getFile('TVDB/search-bbt.guzzle-response');
```

and should include something like this

```
HTTP/1.1 200 OK
Date: Sun, 22 Sep 2013 22:15:46 GMT
Content-Type: text/html
Transfer-Encoding: chunked
Connection: keep-alive
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Vary: User-Agent,Accept-Encoding


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
<body>
  bbt Not found!
</body>
</html>
```

This looks nice, isn't it? But I'm way more to lazy to create such a file for all those acceptance tests. Thats why the guzzleMocker can now record the last record made. 

## Recording real requests as mocks

You can use: 
```php
$this->guzzleMocker->recordLastResponse('target-dir/name-of-response');
```

Thats it! Use Guzzle to receive it and to write it. The debug-format from Guzzle is exactly what the guzzle mock plugin is trying to read. 
You can even use this to save POST requests or anything. Get your tests mocked down in a few seconds. Your workflow would be like this

  1. Create your client as a real client (with a real guzzle client instance)
  1. write a test that actually uses the remote server to create a service request (and response)
  1. use `guzzleMocker::recordLastResponse($responseLocation)` to record the last request. Run your test isolated to record the right request
  1. use `guzzleMocker::addResponse($responseLocation)` to make your mocked guzzle client expect the recorded response and inject it into your client

## Testing requests made

You can now start testing your Service and start mocking your responses the service should get. If this is not enough for your testing, you can use: 
```php
$this->guzzleMocker->getReceivedRequests();
```
to get a list of the triggered requests from the service (in order) and assert them however you like.

> be aware: this array is empty if you don't add a response to the guzzle mocker (this is a limitation from the guzzle plugin)
