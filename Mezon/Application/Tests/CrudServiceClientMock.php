<?php
namespace Mezon\Application\Tests;

use Mezon\CrudService\CrudServiceClient;

// TODO can be mezon's CustomClient (wrapper for CURL) mock be used? Need to be done
class CrudServiceClientMock extends CrudServiceClient
{

    /**
     * Method returns token
     *
     * @return string session id
     */
    public function getToken(): string
    {
        return 'token';
    }

    /**
     * Method returns record by it's id
     *
     * @param int $id
     *            id of the fetching record
     * @param number $crossDomain
     *            domain id
     * @return object fetched record
     */
    public function getById($id, $crossDomain = 0)
    {
        $record = new \stdClass();
        $record->id = $id;
        $record->title = 'record title';

        return (array) $record;
    }

    /**
     * Method returns some records of the user's domain
     *
     * @param int $from
     *            The beginnig of the fetching sequence
     * @param int $limit
     *            Size of the fetching sequence
     * @param int $crossDomain
     *            Cross domain security settings
     * @param array $filter
     *            Filtering settings
     * @param array $order
     *            Sorting settings
     * @return array List of records
     */
    public function getList(int $from = 0, int $limit = 1000000000, $crossDomain = 0, $filter = false, $order = false): array
    {
        $record = new \stdClass();
        $record->id = 1;
        $record->title = 'record title';

        return [
            $record
        ];
    }

    /**
     * Method updates new record
     *
     * @param int $id
     *            Id of the updating record
     * @param array $data
     *            Data to be posted
     * @param int $crossDomain
     *            Cross domain policy
     * @return mixed Result of the RPC call
     * @codeCoverageIgnore
     */
    public function update(int $id, array $data, int $crossDomain = 0)
    {
        return 1;
    }

    /**
     * Method creates new record
     *
     * @param array $data
     *            data for creating record
     * @return int id of the created record
     */
    public function create($data)
    {
        return 1;
    }
}
