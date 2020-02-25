<?php
namespace Mezon\Application;

/**
 * Class ApplicationActions
 *
 * @package ApplicationActions
 * @subpackage CommonApplicationInterface
 * @author Dodonov A.A.
 * @version v.1.0 (2020/02/25)
 * @copyright Copyright (c) 2020, aeon.org
 */

/**
 * Class for basic Crud client
 */
interface CommonApplicationInterface
{

    /**
     * Function generates common parts for all application's pages
     *
     * @return array list of common parts
     */
    public function crossRender(): array;

    /**
     * Method loads route
     * 
     * @return array $route route description
     */
    public function loadRoute(array $route);
}
