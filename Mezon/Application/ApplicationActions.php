<?php
namespace Mezon\Application;

use Mezon\CrudService\CrudServiceClient;
use Mezon\CrudService\CrudServiceClientInterface;
use Mezon\Gui\ListBuilder\ListBuilder;
use Mezon\Functional\Fetcher;
use Mezon\Gui\FieldsAlgorithms;
use Mezon\Gui\FormBuilder\FormBuilder;
use Mezon\Gui\ListBuilder\CrudServiceClientAdapter;

/**
 * Class ApplicationActions
 *
 * @package CrudService
 * @subpackage ApplicationActions
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/12)
 * @copyright Copyright (c) 2019, aeon.org
 */
define('FIELD_NAME_DOMAIN_ID', 'domain_id');

/**
 * Class for basic Crud client
 */
class ApplicationActions
{

    /**
     * Entity nam
     */
    protected $entityName = '';

    /**
     * Entity name for method names
     */
    protected $safeEntityName = '';

    /**
     * Show create button
     */
    protected $createButton = false;

    /**
     * Show update button
     */
    protected $updateButton = false;

    /**
     * Show delete button
     */
    protected $deleteButton = false;

    /**
     * Service client
     *
     * @var CrudServiceClient
     */
    protected $crudServiceClient = null;

    /**
     * Fields
     *
     * @var array
     */
    private $fields = [];

    /**
     * Constructor
     *
     * @param string $entityName
     *            entity name
     * @param array $fields
     *            fields description
     * @param string $login
     *            login
     * @param string $password
     *            password
     */
    public function __construct(string $entityName, array $fields, string $login = '', string $password = '')
    {
        $this->crudServiceClient = new CrudServiceClient($entityName, $login, $password);

        $this->entityName = $entityName;

        $this->fields = $fields;

        $this->safeEntityName = str_replace('-', '_', $entityName);
    }

    /**
     * Method adds page parts to the result
     *
     * @param array $result
     *            View generation result
     * @param \Mezon\Application\CommonApplicationInterface $appObject
     *            Application object
     * @return array Compiled view
     */
    protected function addPageParts(array $result, CommonApplicationInterface &$appObject): array
    {
        if (method_exists($appObject, 'crossRender')) {
            $result = array_merge($result, $appObject->crossRender());
        }

        return $result;
    }

    /**
     * Method adds end-point for list displaying to the application object
     *
     * @param CommonApplicationInterface $appObject
     *            CommonApplicationInterface object
     * @param string $route
     *            Route
     * @param string $callback
     *            Callback name
     * @param string|array $method
     *            HTTP method name GET or POST
     */
    protected function loadRoute(CommonApplicationInterface &$appObject, string $route, string $callback, $method): void
    {
        $appObject->loadRoute([
            'route' => $route,
            'callback' => $callback,
            'method' => $method
        ]);
    }

    /**
     * List builder creation function
     *
     * @param array $options
     * @return ListBuilder
     */
    protected function createListBuilder(array $options): ListBuilder
    {
        // create adapter
        $crudServiceClientAdapter = new CrudServiceClientAdapter();
        $crudServiceClientAdapter->setClient($this->crudServiceClient);

        if (isset($options['default-fields']) === false) {
            throw (new \Exception('List of fields must be defined in the $options[\'default-fields\']', - 1));
        }

        // create list builder
        return new ListBuilder(explode(',', $options['default-fields']), $crudServiceClientAdapter);
    }

    /**
     * Method adds end-point for list displaying to the application object
     *
     * @param CommonApplicationInterface $appObject
     *            CommonApplicationInterface object
     * @param array $options
     *            Options
     */
    public function attachListPage(CommonApplicationInterface &$appObject, array $options): void
    {
        $methodName = $this->safeEntityName . 'ListingPage';

        $this->loadRoute($appObject, $this->entityName . '/list/', $methodName, 'GET');

        $options = $options === false ? [] : $options;

        $options['create_button'] = $this->createButton ? 1 : 0;
        $options['update_button'] = $this->updateButton ? 1 : 0;
        $options['delete_button'] = $this->deleteButton ? 1 : 0;

        $options[FIELD_NAME_DOMAIN_ID] = $this->getSelfId();

        $appObject->$methodName = function () use ($appObject, $options) {
            $listBuilder = $this->createListBuilder($options);

            // generate list
            $result = [
                'main' => $listBuilder->listingForm()
            ];

            // add page parts
            return $this->addPageParts($result, $appObject, $options);
        };
    }

    /**
     * Method adds end-point for list displaying to the application object
     *
     * @param CommonApplicationInterface $appObject
     *            CommonApplicationInterface object
     * @param array $options
     *            Options
     */
    public function attachSimpleListPage(CommonApplicationInterface $appObject, array $options): void
    {
        $methodName = $this->safeEntityName . 'SimpleListingPage';

        $this->loadRoute($appObject, $this->entityName . '/list/simple/', $methodName, 'GET');

        $options = $options === false ? [] : $options;

        $options[FIELD_NAME_DOMAIN_ID] = $this->getSelfId();

        $appObject->$methodName = function () use ($appObject, $options) {
            $listBuilder = $this->createListBuilder($options);

            // generate list
            $result = [
                'main' => $listBuilder->simpleListingForm()
            ];

            // add page parts
            return $this->addPageParts($result, $appObject, $options);
        };
    }

    /**
     * Method adds end-point for deleting record to the application object
     *
     * @param CommonApplicationInterface $appObject
     *            CommonApplicationInterface object
     * @param array $options
     *            Options
     */
    public function attachDeleteRecord(CommonApplicationInterface &$appObject, array $options): void
    {
        $this->DeleteButton = true;

        $methodName = $this->safeEntityName . 'DeleteRecord';

        $this->loadRoute($appObject, $this->entityName . '/delete/[i:id]/', $methodName, 'GET');

        $options = $options === false ? [] : $options;

        $options[FIELD_NAME_DOMAIN_ID] = $this->getSelfId();

        $appObject->$methodName = function (...$params) use ($appObject, $options) {
            $this->crudServiceClient->delete($params[1]['id'], $options[FIELD_NAME_DOMAIN_ID]);

            $appObject->redirectTo('../../list/');
        };
    }

    /**
     * Generating form
     *
     * @param string $type
     *            Form type
     * @param int $id
     *            id of the updating record
     * @return array Compiled result
     */
    protected function getCompiledForm(string $type = 'creation', int $id = 0): array
    {
        // construct $fieldsAlgorithms
        $fieldsAlgorithms = new FieldsAlgorithms(Fetcher::getField($this->fields, 'fields'), $this->entityName);

        // create form builder object
        $formBuilder = new FormBuilder(
            $fieldsAlgorithms,
            false,
            $this->entityName,
            Fetcher::getField($this->fields, 'layout'));

        // compile form
        if ($type == 'creation') {
            $result = [
                'main' => $formBuilder->creationForm()
            ];
        } else {
            $result = [
                'main' => $formBuilder->updatingForm(
                    $this->crudServiceClient->getToken(),
                    $this->crudServiceClient->getById($id, $this->getSelfId()))
            ];
        }

        return $result;
    }

    /**
     * Method gets create record controller for the remote service
     *
     * @param CommonApplicationInterface $appObject
     *            CommonApplicationInterface object
     * @param array $options
     *            Options
     */
    protected function addCreateRecordMethod(CommonApplicationInterface &$appObject, array $options): void
    {
        $methodName = $this->safeEntityName . 'CreateRecord';

        $appObject->$methodName = function () use ($appObject, $options) {
            if (! empty($_POST)) {
                $_POST[FIELD_NAME_DOMAIN_ID] = $this->getSelfId();

                $this->crudServiceClient->create(array_merge($_POST, $_FILES));

                $appObject->redirectTo('../list/');
            } else {
                return $this->addPageParts($this->getCompiledForm(), $appObject, $options);
            }
        };
    }

    /**
     * Method adds end-point for creating record to the application object
     *
     * @param CommonApplicationInterface $appObject
     *            CommonApplicationInterface object
     * @param array $options
     *            Options
     */
    public function attachCreateRecord(CommonApplicationInterface &$appObject, array $options): void
    {
        $this->CreateButton = true;

        $options = $options === false ? [] : $options;

        $route = isset($options['create-page-endpoint']) ? $options['create-page-endpoint'] : $this->entityName .
            '/create/';

        $this->loadRoute($appObject, $route, $this->safeEntityName . 'CreateRecord', [
            'POST',
            'GET'
        ]);

        $this->addCreateRecordMethod($appObject, $options);
    }

    /**
     * Method gets update record controller for the remote service.
     *
     * @param CommonApplicationInterface $appObject
     *            CommonApplicationInterface object
     * @param array $options
     *            Options
     */
    protected function addUpdateRecordMethod(CommonApplicationInterface &$appObject, array $options): void
    {
        $methodName = $this->safeEntityName . 'UpdateRecord';

        $appObject->$methodName = function (...$params) use ($appObject, $options) {
            if (! empty($_POST)) {
                $_POST[FIELD_NAME_DOMAIN_ID] = $this->getSelfId();

                $this->postRequest('/update/' . $params[1]['id'] . '/', $_POST);

                $this->crudServiceClient->update($params[1]['id'], $_POST, $_POST[FIELD_NAME_DOMAIN_ID]);

                $appObject->redirectTo('../../list/');
            } else {
                return $this->addPageParts($this->getCompiledForm('updating', $params[1]['id']), $appObject, $options);
            }
        };
    }

    /**
     * Method adds end-point for updating record to the application object
     *
     * @param CommonApplicationInterface $appObject
     *            CommonApplicationInterface object
     * @param array $options
     *            Options
     */
    public function attachUpdateRecord(CommonApplicationInterface &$appObject, array $options): void
    {
        $this->UpdateButton = true;

        $options = $options === false ? [] : $options;

        $route = isset($options['update-record-endpoint']) ? $options['update-record-endpoint'] : $this->entityName .
            '/update/[i:id]/';

        $this->loadRoute($appObject, $route, $this->safeEntityName . 'UpdateRecord', [
            'POST',
            'GET'
        ]);

        $this->addUpdateRecordMethod($appObject, $options);
    }

    /**
     * Method sets service client
     *
     * @param CrudServiceClientInterface $crudServiceClient
     *            CRUD service client
     */
    public function setServiceClient(CrudServiceClientInterface $crudServiceClient): void
    {
        $this->crudServiceClient = $crudServiceClient;
    }
}
