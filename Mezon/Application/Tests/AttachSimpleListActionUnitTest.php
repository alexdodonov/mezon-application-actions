<?php
namespace Mezon\Application\Tests;

class AttachSimpleListActionUnitTest extends AttachActionsUnitTestBase
{

    /**
     * Testing attaching simple list method
     */
    public function testAttachSimpleListPageMethod(): void
    {
        // setup
        list ($applicationActions, $application) = $this->getObjects();

        // test body
        $applicationActions->attachSimpleListPage($application, [
            'default-fields' => 'id'
        ]);
        $application->test_entitySimpleListingPage();

        // assertions
        $this->assertTrue(isset($application->test_entitySimpleListingPage));
    }

    /**
     * Testing exception while attaching simple list
     */
    public function testExceptionForAttachSimpleListPageMethod(): void
    {
        // assertions
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(- 1);
        $this->expectExceptionMessage('List of fields must be defined in the $options[\'default-fields\']');

        // setup
        list ($applicationActions, $application) = $this->getObjects();
        $applicationActions->attachSimpleListPage($application, []);

        // test body
        $application->test_entitySimpleListingPage();
    }
}
