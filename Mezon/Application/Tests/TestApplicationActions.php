<?php
namespace Mezon\Application\Tests;

use Mezon\Application\ApplicationActions;

class TestApplicationActions extends ApplicationActions
{

    public function getSelfId(): string
    {
        return 1;
    }
}
