# Dependencies to other packages

The testplate tries to aggregate a lot of common code needed by webforge or psc-cms. That is why the dev dependencies in dev are so much more than in require.
Most dependencies are used to test the behaviour, but will not be needed if the testplate is just used to run tests more easily.

## list of dependencies

* the FrameworkHelper creates a (dev)-dependency to webforge/webforge because of the `Webforge\CMS\EnvironmentContainer`