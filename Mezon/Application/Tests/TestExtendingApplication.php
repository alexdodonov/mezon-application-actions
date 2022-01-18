<?php
namespace Mezon\Application\Tests;

use Mezon\Application\CommonApplicationInterface;
use Mezon\Redirect\Layer;

/**
 * Test application
 *
 * @author Dodonov A.A.
 */
class TestExtendingApplication implements CommonApplicationInterface
{

    /**
     * Function generates common parts for all application's pages
     *
     * @return array list of common parts
     */
    public function crossRender(): array
    {
        return [
            'part' => 'part'
        ];
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
     *            method to be called
     * @param array $args
     *            arguments
     * @return mixed result of the call
     */
    public function __call(string $method, array $args)
    {
        if (isset($this->$method)) {
            /** @var callable $function */
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
     *            page to redirect to
     */
    public function redirectTo($url): void
    {
        Layer::redirectTo($url);
    }
}
