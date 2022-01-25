<?php
namespace Mezon\Application\Tests;

use Mezon\DnsClient\DnsClient;
use PHPUnit\Framework\TestCase;
use Mezon\Application\ApplicationActions;
use Mezon\Redirect\Layer;
use Mezon\Conf\Conf;

class AttachActionsUnitTestBase extends TestCase
{

    /**
     * Common setup for all tests
     */
    public function setUp(): void
    {
        DnsClient::clear();
        DnsClient::setService('entity', 'http://entity.local/');
        if (isset($_POST)) {
            unset($_POST);
        }
        Layer::$redirectWasPerformed = false;
        Conf::setConfigStringValue('redirect/layer', 'mock');
        Conf::setConfigStringValue('headers/layer', 'mock');
    }

    /**
     * Method creates ApplicationActions object
     *
     * @return ApplicationActions object
     */
    protected function getApplicationActions(): ApplicationActions
    {
        $crudServiceClient = new CrudServiceClientMock('entity');

        $applicationActions = new TestApplicationActions(
            'test-entity',
            [
                'fields' => [
                    'id' => [
                        'type' => 'integer',
                        'title' => 'id'
                    ]
                ],
                'layout' => []
            ]);

        $applicationActions->setServiceClient($crudServiceClient);

        return $applicationActions;
    }

    /**
     * Creating objects for unit-testing
     *
     * @return array objects for unit-testing
     */
    protected function getObjects(): array
    {
        $applicationActions = $this->getApplicationActions();

        $application = new TestExtendingApplication();

        return [
            $applicationActions,
            $application
        ];
    }
}
