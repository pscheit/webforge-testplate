<?php

namespace Webforge\Code\Test\Behat;

use Behat\Behat\Context\BehatContext;
use tiptoi\tests\fixtures\MainFixture;
use Psc\Doctrine\FixturesManager;

class Context extends BehatContext {

    protected $bootContainer;

    protected $dc, $em;


    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $parameters = array_replace(array(
          'con'=>'tests'
        ), $parameters);

        $this->bootContainer = $GLOBALS['env']['container'];

        $module = $this->bootContainer->getModule('Doctrine');
        $module->setConnectionName($parameters['con']);

        $this->dc = $module->createDoctrinePackage();
        $this->em = $this->dc->getEntityManager($parameters['con']);
    }


    /**
     * @Given /^(?:the )?fixture "?([a-zA-Z\\0-9_]+)"? is loaded$/
     */
    public function fixtureIsLoaded($fqn)
    {
        $fm = new FixturesManager($this->em);
        $fm->add(new $fqn);
        $fm->execute();
    }

    public function getEntityManager() {
      return $this->em;
    }

    public function getDoctrinePackage() {
      return $this->dc;
    }
}
