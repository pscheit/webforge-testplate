# Testing responses

Webforge testplate supports to types of Responses
  1. `Symfony\Component\HttpFoundation\Response`
  2. `Guzzle\Http\Message\Response`

testplate provides a consise api for testing both (allthough they have different apis). The `AbstractResponseAsserter` has the interface for this. If you extend from `Webforge\Code\Test\Base` your usage is like this.

## usage

Symfony:
```php
$response = new \Symfony\Component\HttpFoundation\Response('html-body', 304, array('content-type'=>'text/html'));

$this->assertSymfonyResponse($response)
   ->body('html-body')
   ->code(304)
   ->format('html');
```

Guzzle:
```php
$response = new \Guzzle\Http\Message\Response(304, array('content-type'=>'text/html'), 'html-body');

$this->assertSymfonyResponse($response)
   ->body('html-body')
   ->code(304)
   ->format('html');
```

formats are avaible as:

```
js          text/javascript
javascript  text/javascript
json        application/json
html        text/html
text        text/plain
css         text/css
less        text/less
```

you can pass the full content-type as well.