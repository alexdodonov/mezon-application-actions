<?php

/**
 * Test application
 *
 * @author Dodonov A.A.
 */
class TestExtendingApplication implements \Mezon\Application\CommonApplicationInterface
{

    /**
     * Function generates common parts for all application's pages
     *
     * @return array list of common parts
     */
    public function crossRender(): array
    {
        return [];
    }

    /**
     * Method loads route
     *
     * @return array $route route description
     */
    public function loadRoute(array $route)
    {}

    /**
     * Allowing to call methods added on the fly
     *
     * @param string $method
     *            Method to be called
     * @param array $args
     *            Arguments
     * @return mixed Result of the call
     */
    public function __call(string $method, array $args)
    {
        if (isset($this->$method)) {
            $function = $this->$method;

            return call_user_func_array($function, $args);
        } else {
            throw (new \Exception('Method ' . $method . ' was not found in the application ' . get_class($this)));
        }
    }

    /**
     * Method redirects user to another page
     *
     * @param string $url
     *            New page
     */
    public function redirectTo($url): void
    {
    }
}

class TestApplicationActions extends \Mezon\Application\ApplicationActions
{

    public function getSelfId(): string
    {
        return 1;
    }
}

class ApplicationActionsUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Creating mock of the application actions
     *
     * @return object Application actions
     */
    protected function getApplicationActions(): object
    {
        $object = new TestApplicationActions('entity');

        $crudServiceClient = $this->getMockBuilder(\Mezon\CrudService\CrudServiceClient::class)
            ->setMethods([
            'getList',
            'delete',
            'getRemoteCreationFormFields'
        ])
            ->disableOriginalConstructor()
            ->getMock();

        $crudServiceClient->method('getList')->willReturn([
            [
                'id' => 1
            ]
        ]);

        $crudServiceClient->method('delete')->willReturn('');

        $crudServiceClient->method('getRemoteCreationFormFields')->willReturn([
            'fields' => [
                'id' => [
                    'type' => 'integer',
                    'title' => 'id'
                ]
            ],
            'layout' => []
        ]);

        $object->setServiceClient($crudServiceClient);

        return $object;
    }

    /**
     * Common setup for all tests
     */
    public function setUp(): void
    {
        \Mezon\DnsClient\DnsClient::clear();
        \Mezon\DnsClient\DnsClient::setService('entity', 'http://entity.local/');
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
        $this->expectException(Exception::class);

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
        $this->assertTrue(isset($application->entitySimpleListingPage), 'Method "entitySimpleListingPage" does not exist');
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

        // assertions
        $this->assertTrue(isset($application->entityUpdateRecord), 'Method "entityUpdateRecord" does not exist');
    }
}
