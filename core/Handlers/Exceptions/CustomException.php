<?php
namespace Handlers\Exceptions;

use Exception;

class CustomException extends Exception
{

    private $codes = [
        100 => 'Failed initialization database',
        101 => 'Failed connect to MySQL',
        201 => 'Handler undefined',
        202 => 'Handler initialization failed',
        404 => 'Page not found',
        601 => 'Invalid model namespace',
        602 => 'Failed to model initialization',
        701 => 'Csrf token not transferred',
        702 => 'Invalid csrf token'
    ];

    public function __construct(int $code, string $message = '')
    {

        print_r($code);
        
    }

}