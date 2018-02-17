<?php

namespace Davesweb\Repositories\Implementations\Eloquent;

use Davesweb\Repositories\Criteria\Criteria;
use Davesweb\Repositories\Repository;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

abstract class EloquentRepository implements Repository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Criteria[]
     */
    private $criteria = [];

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $id
     *
     * @return Model|null
     */
    public function find(int $id)
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * @param int $id
     *
     * @throws ModelNotFoundException
     *
     * @return Model
     */
    public function findOrFail(int $id)
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    /**
     * @param string $column
     * @param mixed  $value
     *
     * @return Model|null
     */
    public function findBy(string $column, $value)
    {
        return $this->model->newQuery()->where($column, $value)->first();
    }

    /**
     * @param string $column
     * @param mixed  $value
     *
     * @throws ModelNotFoundException
     *
     * @return Model
     */
    public function findOrFailBy(string $column, $value)
    {
        return $this->model->newQuery()->where($column, $value)->firstOrFail();
    }

    /**
     * @return Model|null
     */
    public function first()
    {
        $query = $this->applyCriteria($this->model->newQuery());

        return $query->first();
    }

    /**
     * @throws ModelNotFoundException
     *
     * @return Model
     */
    public function firstOrFail()
    {
        $query = $this->applyCriteria($this->model->newQuery());

        return $query->firstOrFail();
    }

    /**
     * @return Model[]
     */
    public function all(): array
    {
        $query = $this->applyCriteria($this->model->newQuery());

        return iterator_to_array($query->get());
    }

    /**
     * @param int      $perPage
     * @param int|null $page
     * @param string   $pageName
     *
     * @return Paginator
     */
    public function paginate(int $perPage, int $page = null, string $pageName = 'page'): Paginator
    {
        $query = $this->applyCriteria($this->model->newQuery());

        return $query->paginate($perPage, ['*'], $pageName, $page);
    }

    /**
     * @param Model $model
     *
     * @throws Throwable|Exception
     *
     * @return bool
     */
    public function save($model): bool
    {
        return $model->saveOrFail();
    }

    /**
     * @param Model $model
     *
     * @throws Throwable|Exception
     *
     * @return bool
     */
    public function update($model): bool
    {
        return $model->update();
    }

    /**
     * @param Model $model
     *
     * @throws Throwable|Exception
     *
     * @return bool
     */
    public function delete($model): bool
    {
        $model->delete();

        return true;
    }

    /**
     * @param Criteria $criteria
     *
     * @return Repository
     */
    public function pushCriteria(Criteria $criteria): Repository
    {
        $this->criteria[] = $criteria;

        return $this;
    }

    /**
     * @return Repository
     */
    public function clearCriteria(): Repository
    {
        $this->criteria = [];

        return $this;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    private function applyCriteria(Builder $builder)
    {
        foreach ($this->criteria as $criteria) {
            $criteria->apply($builder, $this);
        }

        return $builder;
    }
}
