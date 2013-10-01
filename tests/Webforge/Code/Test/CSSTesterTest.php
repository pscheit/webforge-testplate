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
    <li>Silverster</li>
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
