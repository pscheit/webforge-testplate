# Test your web pages

sometimes you just need a little acceptance test to start off with. Those acceptance tests should be your start point, when you want to test if "the website acts normally".
Testplate gives you an easy way to accomplish those tests. It uses a real http client (powered by guzzle) that does a real request to your application url and returns the output. This can acceptance test your apache configuration and your whole application stack.

## usage

extend your test from `Webforge\Code\Test\Base` (as usual). For the `$this->css()` functions implement `Webforge\Code\Test\HTMLTesting`

```php
  public function setUp() {
    parent::setUp();
    $this->url = $this->frameworkHelper->getProject()->getHostUrl();

    $this->guzzleTester = $this->createGuzzleTester($this->url);
    //$this->guzzleTester->setDefaultAuth('admin', 'adminpass');
  }

  public function testDisplaysTheIndexPage() {
    $this->guzzleTester->get('/');
    $this->html = $this->guzzleTester->dispatchHTML();

    $this->css('html')->count(1)
      ->css('body')->count(1)
        ->css('div.container')->count(1)
          ->css('div.row')->atLeast(1)->end()
        ->end()
      ->end()
      ->css('head title')->text($this->matchesRegularExpression('/SerienLoader/i'))->end()
    ;
  }

  protected function onNotSuccessfulTest(\Exception $e) {
    $this->guzzleTester->debug();
    
    throw $e;
  }

}
```

This will:
  - display the dispatched request and response on a not successfultest. 
  - initialize a project url configured in the webforge container
  - test that the index request `GET /` will display a bootstrap page with a specific title

You can create other requests to your backend

```php
$this->guzzleTester->post('/my-entities', array('postfield'=>'value'));
```

```php
$this->guzzleTester->put('/my-entitiy/7', array('postfield'=>'value'));
```

```php
$this->guzzleTester->delete('/my-entitiy/7');
```

You can dispatch html and json requests

```php
$json = $this->guzzleTester->dispatchJSON();
```
This will assert that the response is from application/json and that the body is parseable. It will return the body decoded


```php
$html = $this->guzzleTester->dispatchHTML();
```
returns the body as $string, or at least testable with `$this->css()`
``` 

## Using Cookies

If you have a session on serverside which you want to keep in a test (for example to test login functionality, etc). Use 
```php
$this->guzzleTester->useCookies()
```
This uses a array cookie jar which is reseted to 0 on every test.