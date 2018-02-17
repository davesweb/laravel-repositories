<?php

namespace Davesweb\Repositories;

use Davesweb\Repositories\Criteria\Criteria;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

interface Repository
{
    /**
     * @param int $id
     *
     * @return Model|null
     */
    public function find(int $id);

    /**
     * @param int $id
     *
     * @throws ModelNotFoundException
     *
     * @return Model
     */
    public function findOrFail(int $id);

    /**
     * @param string $column
     * @param mixed  $value
     *
     * @return Model|null
     */
    public function findBy(string $column, $value);

    /**
     * @param string $column
     * @param mixed  $value
     *
     * @throws ModelNotFoundException
     *
     * @return Model
     */
    public function findOrFailBy(string $column, $value);

    /**
     * @return Model|null
     */
    public function first();

    /**
     * @throws ModelNotFoundException
     *
     * @return Model
     */
    public function firstOrFail();

    /**
     * @return Model[]
     */
    public function all(): array;

    /**
     * @param int      $perPage
     * @param int|null $page
     * @param string   $pageName
     *
     * @return Paginator
     */
    public function paginate(int $perPage, int $page = null, string $pageName = 'page'): Paginator;

    /**
     * @param Model $model
     *
     * @throws Throwable|Exception
     *
     * @return bool
     */
    public function save($model): bool;

    /**
     * @param Model $model
     *
     * @throws Throwable|Exception
     *
     * @return bool
     */
    public function update($model): bool;

    /**
     * @param Model $model
     *
     * @throws Throwable|Exception
     *
     * @return bool
     */
    public function delete($model): bool;

    /**
     * @param Criteria $criteria
     *
     * @return Repository
     */
    public function pushCriteria(Criteria $criteria): self;

    /**
     * @return Repository
     */
    public function clearCriteria(): self;
}
