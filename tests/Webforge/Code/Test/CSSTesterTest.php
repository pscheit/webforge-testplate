<?php

namespace Webforge\Code\Test;

class CSSTesterTest extends Base implements HTMLTesting {

  protected $thtml;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Code\\Test\\CSSTester';
    parent::setUp();

    $this->thtml = <<<'HTML'
<body>    
<section class="team">
  <ul>
    <li>RoadRunner</li>
    <li>Coyote</li>
    <li>Tweety</li>
    <li>Silvester
    </li>
  </ul>
</section>
</body>
HTML;
  }

  public function testCSSIsAvaibleFromBase_WhenHTMLIsExplicitSet() {
    $this->assertChainable($tester = $this->css('body', $this->thtml));

    $tester->css('section.team')->count(1);
  }

  public function testCSSIsAvaibleFromBase_WhenHTMLIsNotExplicitSet_WhenTestCaseImplementsHTMLTesting() {
    $this->setHTML($this->thtml);
    
    $this->css('section.team')->count(1);
  }

  public function testCSSCalledTwiceFromRootDoesStillWork_ResetsNotHTMLAsContext() {
    $this->setHTML($this->thtml);

    $this->css('section.team ul')->count(1); // should not set context
    $this->css('body')->count(1);
  }

  public function testQueryObjectAsCSSToSetContext() {
    $this->html = $this->thtml;

    $section = $this->css('section')->getQuery();

    $this->css($section)->asContext()
      ->css('ul li')->count(4);
  }

  public function testSetsContextWithSetHTML() {
    $this->html = $this->thtml;

    $this->css('section ul li:eq(0)')->asContext();

    $this->assertEquals('<li>RoadRunner</li>', $this->html);
  }

  public function testContainsText() {
    $this->html = $this->thtml;

    $this->css('ul li:eq(0)')->exists()->containsText('Road');
  }

  public function testContainsTextFailure() {
    $this->html = $this->thtml;

    $this->expectAssertionFail();
    $this->css('ul li:eq(0)')->exists()->containsText('Rod');
  }

  public function testHasText() {
    $this->html = $this->thtml;

    $this->css('ul li:eq(0)')->exists()->hasText('RoadRunner');
  }

  public function testHasText_FailExactMatch() {
    $this->html = $this->thtml;

    $this->expectAssertionFail();
    $this->css('ul li:eq(0)')->exists()->hasText('RoadRunne');
  }

  public function testHasText_FailExactMatchWithWhitespace() {
    $this->html = $this->thtml;

    $this->expectAssertionFail();
    $this->css('ul li:eq(3)')->exists()->hasText('Silvester');
  }

  public function testTrimmedText() {
    $this->html = $this->thtml;

    $this->css('ul li:eq(3)')->exists()->hasTrimmedText('Silvester');
  }

  public function testexamples() {
$this->html = <<<'HTML'
<div class="team">
  <h1 class="active">team</h1>
    <div class="mitarbeiter">
      imme
    </div>
    <div class="mitarbeiter">
      philipp
    </div>
</div>
HTML;

$this->css('div.team')->count(1)
  ->css('h1')->count(1)->hasClass('active')->end()
   ->css('div.mitarbeiter')->count(2)->end()
 ;

  }
}
