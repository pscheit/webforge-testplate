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
}
