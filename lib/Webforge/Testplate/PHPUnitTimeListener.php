<?php

namespace Webforge\Testplate;

use PHPUnit_Framework_TestSuite;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_AssertionFailedError;
use Exception;

class PHPUnitTimeListener implements \PHPUnit_Framework_TestListener {
  
  protected $testsTimes = array();
  protected $suitesTimes = array();
  
  protected $mainTestSuite;

  public function __construct($mainSuite) {    
    $this->mainTestSuite = $mainSuite;
  }

  public function addError(PHPUnit_Framework_Test $test, Exception $e, $time) {
    //printf("Error while running test '%s'.\n", $test->getName());
  }
 
  public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {
    //printf("Test '%s' failed.\n", $test->getName());
  }
 
  public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
    //printf("Test '%s' is incomplete.\n", $test->getName());
  }
 
  public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
    //printf("Test '%s' has been skipped.\n", $test->getName());
  }
 
  public function startTest(PHPUnit_Framework_Test $test) {
    //printf("Test '%s' started.\n", $test->getName());
  }

  public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
  }
 
  public function endTest(PHPUnit_Framework_Test $test, $time) {
    //printf("Test '%s' needed: %.3f seconds\n", $test->getName(), $time);
    $this->testsTimes[] = array($time, $test);
  }
 
  public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {
    if ($suite->getName() === $this->mainTestSuite) {
      print "Running with PHPUnitTimeListener from Webforge\Testplate\n";
      printf("Measuring Times for mainSuite '%s'.\n", $suite->getName());
    }
  }
 
  public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {
    if ($suite->getName() === $this->mainTestSuite) {
    
      uasort($this->suitesTimes, function ($pair1, $pair2) {
        
        if ($pair1[0] === $pair2[0]) {
          return 0;
        } else {
          return $pair1[0] < $pair2[0] ? 1 : -1; // < means reverse sorting
        }
      });
      
      $tests = array();
      $slowest = '';
      foreach ($this->suitesTimes as $pair) {
        list($time, $testSuite) = $pair;
        
        $slowest .= sprintf("[%.2f seconds] %s)\n", $time, $testSuite->getName());
      }
      
      printf("\nTimeListener Report for TestSuite '%s'\nSlowest TestSuites:\n%s\n",  $suite->getName(), $slowest);
      
    } else {
      $suiteTime = 0;
      foreach ($this->testsTimes as $pair) {
        list($time, $test) = $pair;
        $suiteTime += $time;
      }
      
      $this->suitesTimes[] = array($suiteTime, $suite);
      $this->testsTimes = array();

      printf("\n%.2f seconds for %s\n",  $suiteTime, $suite->getName());
    }
  }
}
?>