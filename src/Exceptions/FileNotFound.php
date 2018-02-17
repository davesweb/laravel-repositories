<?php

namespace Davesweb\Repositories\Exceptions;

use Exception;

class FileNotFound extends Exception
{
    /**
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        parent::__construct(sprintf('The file %s could not be found', $filename));
    }
}
