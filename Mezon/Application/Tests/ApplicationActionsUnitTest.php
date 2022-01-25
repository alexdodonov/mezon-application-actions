<?php
namespace Mezon\Application\Tests;

use Mezon\CrudService\CrudServiceClient;
use Mezon\DnsClient\DnsClient;
use PHPUnit\Framework\TestCase;
use Mezon\Redirect\Layer;
use Mezon\Conf\Conf;

class ApplicationActionsUnitTest extends TestCase
{

    /**
     * Method returns testing record
     *
     * @return object testing record
     */
    private function getRecord(): object
    {
        $record = new \stdClass();
        $record->id = 1;
        $record->title = 'record title';
        return $record;
    }

    /**
     * Creating mock of the application actions
     *
     * @return object Application actions
     */
    protected function getApplicationActionsMock(): object
    {
        $object = new TestApplicationActions(
            'entity',
            [
                'fields' => [
                    'id' => [
                        'type' => 'integer',
                        'title' => 'id'
                    ]
                ],
                'layout' => []
            ]);

        $crudServiceClient = $this->getMockBuilder(CrudServiceClient::class)
            ->onlyMethods([
            'getList',
            'delete',
            'getById',
            'update',
            'create'
        ])
            ->setConstructorArgs([
            'entity'
        ])
            ->getMock();

        $crudServiceClient->method('getList')->willReturn([
            $this->getRecord()
        ]);

        $crudServiceClient->method('delete')->willReturn('');

        $crudServiceClient->method('getById')->willReturn([
            'id' => 1,
            'title' => 'record title'
        ]);

        $object->setServiceClient($crudServiceClient);

        return $object;
    }

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
     * Testing attaching list method
     */
    public function testAttachListPageMthodInvalid(): void
    {
        // setup
        $object = $this->getApplicationActionsMock();

        $application = new TestExtendingApplication();

        // test body and assertions
        $this->expectException(\Exception::class);

        $object->attachListPage($application, []);
        $application->entityListingPage();
    }

    /**
     * Testing attaching list method
     */
    public function testAttachListPageMethod(): void
    {
        // setup
        $object = $this->getApplicationActionsMock();

        $application = new TestExtendingApplication();

        // test body
        $object->attachListPage($application, [
            'default-fields' => 'id'
        ]);

        $result = $application->entityListingPage();

        // assertions
        $this->assertTrue(isset($application->entityListingPage), 'Method "entityListingPage" does not exist');
        $this->assertStringContainsString('>1<', $result['main']);
        $this->assertStringContainsString('>id<', $result['main']);
    }

    /**
     * Testing attaching delete method
     */
    public function testAttachDeleteMethod(): void
    {
        // setup
        $object = $this->getApplicationActionsMock();
        $application = new TestExtendingApplication();

        // test body
        $object->attachDeleteRecord($application, []);

        $application->entityDeleteRecord('/route/', [
            'id' => 1
        ]);

        // assertions
        $this->assertTrue(isset($application->entityDeleteRecord), 'Method "entityDeleteRecord" does not exist');
    }
}
