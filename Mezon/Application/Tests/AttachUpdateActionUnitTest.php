<?php
namespace Mezon\Application\Tests;

use Mezon\Redirect\Layer;

class AttachUpdateActionUnitTest extends AttachActionsUnitTestBase
{

    /**
     * Testing attaching update method wich renders form
     */
    public function testAttachUpdateMethodRender(): void
    {
        // setup
        unset($_POST);
        list ($applicationActions, $application) = $this->getObjects();

        // test body
        $applicationActions->attachUpdateRecord($application, []);

        $result = $application->test_entityUpdateRecord('/update/', [
            'id' => 1
        ]);

        // assertions
        $this->assertStringContainsString('x_panel', $result['main']);
        $this->assertArrayHasKey('part', $result);
    }

    /**
     * Testing attaching update method wich handles submitted form
     */
    public function testAttachUpdateMethodSubmit(): void
    {
        // setup
        $_POST['title'] = 'new title';
        list ($applicationActions, $application) = $this->getObjects();

        // test body
        $applicationActions->attachUpdateRecord($application, []);

        $application->test_entityUpdateRecord('/update/', [
            'id' => 1
        ]);

        // assertions
        $this->assertTrue(Layer::$redirectWasPerformed);
    }
}
