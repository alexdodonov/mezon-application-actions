<?php
namespace Mezon\Application\Tests;

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
    {}
}
