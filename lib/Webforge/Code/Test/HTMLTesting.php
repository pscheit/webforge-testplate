<?php

namespace Webforge\Code\Test;

interface HTMLTesting {

  public function getHTML();

  public function setHTML($html);

  public function setDebugContextHTML(CSSTester $css, $contextHtml, $selectorInfo);

}
