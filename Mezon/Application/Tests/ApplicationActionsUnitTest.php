<?php
namespace Mezon\Application\Tests;

use Mezon\CrudService\CrudServiceClient;
use Mezon\DnsClient\DnsClient;
use PHPUnit\Framework\TestCase;

class ApplicationActionsUnitTest extends TestCase
{

    /**
     * Creating mock of the application actions
     *
     * @return object Application actions
     */
    protected function getApplicationActions(): object
    {
        $object = new TestApplicationActions('entity');

        $crudServiceClient = $this->getMockBuilder(CrudServiceClient::class)
            ->setMethods([
            'getList',
            'delete',
            'getRemoteCreationFormFields',
            'getById'
        ])
            ->disableOriginalConstructor()
            ->getMock();

        $crudServiceClient->method('getList')->willReturn([
            [
                'id' => 1
            ]
        ]);

        $crudServiceClient->method('delete')->willReturn('');

        $crudServiceClient->method('getRemoteCreationFormFields')->willReturn(
            [
                'fields' => [
                    'id' => [
                        'type' => 'integer',
                        'title' => 'id'
                    ]
                ],
                'layout' => []
            ]);

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
    }

    /**
     * Testing attaching list method
     */
    public function testAttachListPageMthodInvalid(): void
    {
        // setup
        $object = $this->getApplicationActions();

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
        $object = $this->getApplicationActions();

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
     * Testing attaching simple list method
     */
    public function testAttachSimpleListPageMethod(): void
    {
        // setup
        $object = $this->getApplicationActions();
        $application = new TestExtendingApplication();

        // test body
        $object->attachSimpleListPage($application, [
            'default-fields' => 'id'
        ]);
        $application->entitySimpleListingPage();

        // assertions
        $this->assertTrue(
            isset($application->entitySimpleListingPage),
            'Method "entitySimpleListingPage" does not exist');
    }

    /**
     * Testing attaching delete method
     */
    public function testAttachDeleteMethod(): void
    {
        // setup
        $object = $this->getApplicationActions();
        $application = new TestExtendingApplication();

        // test body
        $object->attachDeleteRecord($application, []);

        $application->entityDeleteRecord('/route/', [
            'id' => 1
        ]);

        // assertions
        $this->assertTrue(isset($application->entityDeleteRecord), 'Method "entityDeleteRecord" does not exist');
    }

    /**
     * Testing attaching create method
     */
    public function testAttachCreateMethod(): void
    {
        // setup
        $object = $this->getApplicationActions();
        $application = new TestExtendingApplication();

        // test body
        $object->attachCreateRecord($application, []);
        $result = $application->entityCreateRecord();

        // assertions
        $this->assertStringContainsString('x_title', $result['main'], 'Method "entityCreateRecord" does not exist');
    }

    /**
     * Testing attaching update method
     */
    public function testAttachUpdateMethod(): void
    {
        // setup
        $object = $this->getApplicationActions();
        $application = new TestExtendingApplication();

        // test body
        $object->attachUpdateRecord($application, []);
        $result = $application->entityUpdateRecord('/update/', [
            'id' => 1
        ]);

        // assertions
        $this->assertStringContainsString('x_title', $result['main'], 'Method "entityUpdateRecord" does not exist');
    }
}
