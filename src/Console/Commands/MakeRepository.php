<?php

namespace Davesweb\Repositories\Console\Commands;

use Davesweb\Repositories\Services\MakeRepositoryService;
use Illuminate\Console\Command;

class MakeRepository extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'davesweb:make:repository
                            {name : The name of the repository}
                            {--namespace= : The namespace in which to create this repository.}
                            {--concrete=* : Which implementations to create this repository for. Default uses all configured.}
                            {--entity=true : Whether or not to create the entity class as well}
                            {--migration=false : Whether or not to create the migration file as well}';

    /**
     * Handle the creation of the repositories.
     *
     * @param MakeRepositoryService $makeService
     *
     * @return int
     */
    public function handle(MakeRepositoryService $makeService): int
    {
        $makeService->setOutput($this->getOutput());

        $makeService->make(
            $this->argument('name'),
            $this->option('namespace') ?: '',
            $this->option('concrete') ?: [],
            'true' == $this->option('entity'),
            'true' == $this->option('migration')
        );

        return 0;
    }
}
