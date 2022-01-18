<?php
namespace Mezon\Application\Tests;

use Mezon\Redirect\Layer;

class AttachCreateActionUnitTest extends AttachActionsUnitTestBase
{

    /**
     * Testing attaching create method wich renders form
     */
    public function testAttachCreateMethodRender(): void
    {
        // setup
        unset($_POST);
        list ($applicationActions, $application) = $this->getObjects();

        // test body
        $applicationActions->attachCreateRecord($application, []);

        $result = $application->test_entityCreateRecord('/create/');

        // assertions
        $this->assertStringContainsString('x_panel', $result['main']);
        $this->assertArrayHasKey('part', $result);
    }

    /**
     * Testing attaching create method wich handles submitted form
     */
    public function testAttachCreateMethodSubmit(): void
    {
        // setup
        $_POST['title'] = 'new title';
        list ($applicationActions, $application) = $this->getObjects();

        // test body
        $applicationActions->attachCreateRecord($application, []);

        $application->test_entityCreateRecord('/create/');

        // assertions
        $this->assertTrue(Layer::$redirectWasPerformed);
    }
}
