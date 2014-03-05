<?php

namespace Webforge\Code\Test;

use Webforge\DOM\Query;
use Webforge\DOM\XMLUtil as xml;
use PHPUnit_Framework_TestCase as TestCase;

class CSSTester {
  
  protected $testCase;
  
  protected $query;
  
  protected $selector;
  protected $html;
  
  protected $parent;

  protected $msgs;

  public function __construct(TestCase $testCase, $selector, $html = NULL) {
    if (!class_exists('Webforge\DOM\Query', TRUE)) {
      throw new \RuntimeException('webforge/dom needs to be installed. Run composer to install.');
    }

    $this->testCase = $testCase;
    if ($selector instanceof Query) {
      $this->query = $selector;
    } elseif ($html === NULL && $this->testCase instanceof HTMLTesting) { // weil ich depp immer $this->html als 2ten parameter vergesse :)
      $this->html = $testCase->getHTML();
      $this->selector = $selector;
    } elseif ($html instanceof Query) {
      $this->query = $html->find($selector);
      $this->html = NULL;
    } else {
      $this->selector = $selector;
      $this->html = $html;
    }

    $this->msgs = array(
      'hasClass'=>"Element hat die Klasse: '%s' nicht. %s.%s",
      'no-html'=>'html ist leer. Wurde als 2ter Parameter möglicherweise kein HTML übergeben? Oder $this->html wurde gesetzt aber der TestCase implementiert nicht Webforge\Code\Test\HTMLTesting?'
    );
  }
  
  /**
   * Überprüft ob der CSS Selector ein Ergebnis mit genau $expected Items zurückgibt
   * 
   * @param int $expected
   */
  public function count($expected, $message = NULL) {
    $this->testCase->assertInternalType('int', $expected, 'Erster Parameter von Count muss int sein');
    $this->testCase->assertCount(
      $expected,
      $this->getQuery(),
      sprintf("Selector: '%s'%s", $this->getSelector(), $message ? ': '.$message : '')
    );
    return $this;
  }

  /**
   * Überprüft ob der CSS Selector ein Ergebnis mit mindestens $expected Items zurückgibt
   * 
   * @param int $expected
   */
  public function atLeast($expected, $message = '') {
    $this->testCase->assertInternalType('int',$expected, 'Erster Parameter von atLeast muss int sein');
    $this->testCase->assertGreaterThanOrEqual($expected, count($this->getQuery()), $message ?: sprintf("Selector: '%s'",$this->getSelector()));
    return $this;
  }
  
  public function hasAttribute($expectedAttribute, $expectedValue = NULL) {
    $query = $this->assertQuery(__FUNCTION__);

    $this->testCase->assertTrue($query->getElement()->hasAttribute($expectedAttribute), 'Element hat das Attribut: "'.$expectedAttribute.'" nicht.'.$this->debugElement($query));
    
    if (func_num_args() >= 2) {
      $this->testCase->assertEquals($expectedValue, $query->attr($expectedAttribute), 'Wert des Attributes '.$expectedAttribute.' ist nicht identisch. '.$this->debugElement($query));
    }
    return $this;
  }
  
  public function attribute($expectedAttribute, $constraint, $msg = '') {
    $query = $this->assertQuery(__FUNCTION__);

    $this->testCase->assertTrue($query->getElement()->hasAttribute($expectedAttribute), 'Element hat das Attribut: '.$expectedAttribute.' nicht.'.$this->debugElement($query));
    
    $this->testCase->assertThat($query->attr($expectedAttribute), $constraint, 'Attribute: '.$expectedAttribute."\n".$msg.$this->debugElement($query));
    return $this;
  }

  public function hasNotAttribute($expectedAttribute) {
    $query = $this->assertQuery(__FUNCTION__);

    $this->testCase->assertFalse($query->getElement()->hasAttribute($expectedAttribute), 'Element hat das Attribut: '.$expectedAttribute.' es wurde aber erwartet, dass es nicht vorhanden sein soll.'.$this->debugElement($query));
    return $this;
  }
  
  public function hasClass($expectedClass, $msg = '') {
    $query = $this->getQuery();
    $this->testCase->assertTrue(
      $query->hasClass($expectedClass), 
      sprintf($this->msgs[__FUNCTION__], $expectedClass, $msg, $this->debugElement($query))
    );
    return $this;
  }

  public function hasNotClass($expectedClass) {
    $query = $this->assertQuery(__FUNCTION__);

    $this->testCase->assertFalse(
      $query->hasClass($expectedClass), 
      'Element hat die Klasse: '.$expectedClass.' obwohl es sie nicht haben soll'.$this->debugElement($query)
    );
    return $this;
  }

  public function hasText($expectedText, $msg = NULL) {
    $this->testCase->assertEquals(
      $expectedText, 
      $this->getQuery()->text(), 
      sprintf("%sThe text contents of element (%s) do not match.",
        $msg ? $msg.".\n" : '',
        $this->getSelector()
      )
    );

    return $this;
  }

  public function text($constraint, $msg = NULL) {
    $query = $this->assertQuery(__FUNCTION__);
    
    $this->testCase->assertThat($query->text(), $constraint, $msg ?: sprintf('Text of Element %s matches not constraint', $this->getSelector()));
    return $this;
  }
  
  public function containsText($expectedTextPart, $msg = NULL) {
    $this->testCase->assertContains(
      $expectedTextPart, 
      $this->getQuery()->text(), 
      sprintf("%sThe text contents of element %s do not match.", 
        $msg ? $msg.".\n" : '',
        $this->getSelector()
      )
    );
    return $this;
  }

  public function hasStyle($expectedStyle, $expectedValue = NULL) {
    $query = $this->assertQuery(__FUNCTION__);
    
    $this->testCase->assertTrue($query->getElement()->hasAttribute('style'), 'Element hat das Attribut: style nicht: '.$query->html());
    $this->testCase->assertContains($expectedStyle, $query->attr('style'));    
    
    if (func_num_args() >= 2) {
      $this->testCase->assertContains($expectedStyle.': '.$expectedValue, $query->attr('style'), 'Style '.$expectedStyle.' mit Wert '.$expectedValue.' nicht gefunden');
    }
    return $this;
  }
  
  /**
   * Überprüft ob der CSS Selector mindestens einmal matched
   * 
   */
  public function exists($message = '') {
    $this->testCase->assertGreaterThan(0,count($this->getQuery()));
    return $this;
  }
  
  /**
   * Startet einen neuen (Sub)Test mit find($selector)
   */
  public function css($selector) {
    $this->assertQuery(sprintf("css('%s')", $selector));
    $subTest = new static($this->testCase, $this->getQuery()->find($selector));
    $subTest->setParent($this);

    return $subTest;
  }

  /**
   * Sets this css test html as context for the testcase (if its an HTMLTesting)
   * 
   * this can be handy to reduce the debug output
   */
  public function asContext() {
    if ($this->testCase instanceof HTMLTesting) {
      $export = $this->getQuery()->export();
      if (is_array($export)) {
        $export = implode("\n", $export);
      }

      $this->testCase->setDebugContextHTML($this, $export, 'css: '.$this->getSelector());
    }
    return $this;
  }
  
  protected function assertQuery($function) {
    $query = $this->getQuery();
    $this->testCase->assertNotEmpty($query->getElement(), 'Element kann für '.$function.' nicht überprüft werden, da der Selector 0 zurückgibt: '.$query->getSelector());
    return $query;
  }
  
  public function getQuery() {
    if (!isset($this->query)) {
      $this->testCase->assertNotNull($this->html, $this->msgs['no-html']);
      
      $html = (string) $this->html;
      // simple heuristic, damit wir html documente korrekt asserten
      if (mb_strpos(trim($html),'<!DOCTYPE') === 0 || mb_strpos(trim($html),'<html') === 0 || mb_strpos(trim($html), '<?xml') === 0) {
        $html = xml::doc($html);
      }
      
      $this->query = new Query($this->getSelector(), $html);
    }
    
    return $this->query;
  }

  protected function debugElement(Query $query) {
    $el = new Query($query->getELement());

    return sprintf("\nElement: '%s'\n%s", $query->getSelector(), $el->outerHtml());
  }

  public function getjQuery() {
    return $this->getQuery();
  }
  
  public function html() {
    return $this->getQuery()->html();
  }
  
  public function getSelector() {
    return $this->selector ?: $this->getQuery()->getLiteralSelector();
  }
  
  public function end() {
    return $this->parent;
  }
  
  /**
   * @param $selector 'table tr:eq(0) td' z. B: 
   * @param Closure $do erster Parameter die query td/th
   */
  public function readRow($selector, $expectColumnsNum, Closure $do = NULL) {
    if (!isset($do)) {
      $do = function ($td) {
        return $td->html();
      };
    }
    
    $tds = $this->getQuery()->find($selector);
    $columns = array();
    foreach ($tds as $td) {
      $td = new Query($td);
      
      $columns[] = $do($td);
    }
    
    $this->testCase->assertCount($expectColumnsNum, $columns, 'Spalten der Zeile: '.$tds->getSelector().' haben nicht die richtige Anzahl '.print_r($columns,true));
    return $columns;
  }
  
  /**
   * @param  $parent
   * @chainable
   */
  public function setParent($parent) {
    $this->parent = $parent;
    return $this;
  }

  /**
   * @return 
   */
  public function getParent() {
    return $this->parent;
  }

  public function getHTML() {
    return $this->html;
  }
}
