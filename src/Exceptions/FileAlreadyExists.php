<?php

namespace Davesweb\Repositories\Exceptions;

use Exception;

class FileAlreadyExists extends Exception
{
    /**
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        parent::__construct(sprintf('The file %s already exists', $filename));
    }
}
