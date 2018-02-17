<?php

namespace Davesweb\Repositories\Traits;

trait Identifiable
{
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
