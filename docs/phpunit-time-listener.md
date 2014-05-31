# Taking time of your unit tests in PHPUnit

add this to your phpunit.xml:

```xml
    <listeners>
      <listener class="Webforge\Testplate\PHPUnitTimeListener">
        <arguments>
            <string>MyMainTestSuite</string>
        </arguments>
      </listener>
    </listeners>
```

Make sure that "MyMainTestSuite" matches 

```xml
    <testsuites>
        <testsuite name="MyMainTestSuite">
```
in your phpunit.xml file.

**Note**: Make sure that the class is avaible to autoload from PHPUnit, because otherwise it will just fail silently. (You can pass a file="" attribute to require a .php file)

Run phpunit with your phpunit.xml and wait for the output:

```
TestSuite 'MyMainTestSuite'. Slowest TestSuites:
[31.97 seconds] MyProject\Controller\FrontendControllerTest::testResponseOnAllFrontendPages)
[25.16 seconds] MyProject\Controller\CMSUsersControllerTest)
[21.68 seconds] MyProject\Controller\CMSRegistrationsControllerTest)
[13.85 seconds] MyProject\Controller\CMSNewsControllerTest)
[13.57 seconds] MyProject\Controller\RegistrationControllerTest)
[9.86 seconds] MyProject\Controller\CMSPagesControllerTest)
[9.40 seconds] MyProject\Controller\CMSUsersControllerSuperAdminTest)
[5.50 seconds] MyProject\Controller\CMSWorkshopControllerTest)
[4.92 seconds] MyProject\Controller\CMSPartnersControllerTest)
[3.33 seconds] MyProject\Controller\ApiUsersControllerTest)
[2.57 seconds] MyProject\Form\CMSFormulatorTest)
[1.76 seconds] MyProject\Test\CMSAcceptanceTest)
[1.57 seconds] MyProject\RenderUtilsTest)
[1.43 seconds] MyProject\Controller\FilesControllerTest)
[1.31 seconds] MyProject\Controller\CMSSettingsControllerTest)
[0.89 seconds] MyProject\Controller\ApiModelControllerTest)
[0.23 seconds] MyProject\Views\RegistrationTeaserAndViewFactoryTest)
[0.07 seconds] MyProject\Form\ContentStreamFormTypesTest)
[0.06 seconds] MyProject\Form\FormulatorTest)
[0.02 seconds] MyProject\CMS\PageStubberTest)
[0.02 seconds] MyProject\Views\ContentPageViewTest)
[0.01 seconds] MyProject\Entities\RegistrationTest)
[0.01 seconds] MyProject\Views\CMS\Widgets\VideoWidgetTest)
[0.00 seconds] MyProject\Controller\FrontendControllerTest)
```
