<?php namespace Anomaly\Streams\Platform\Model;

use Anomaly\Streams\Platform\Model\Contract\EloquentRepositoryInterface;
use Anomaly\Streams\Platform\Traits\FiresCallbacks;
use Anomaly\Streams\Platform\Traits\Hookable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class EloquentRepository
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Model
 */
class EloquentRepository implements EloquentRepositoryInterface
{

    use FiresCallbacks;
    use Hookable;

    /**
     * Return all records.
     *
     * @return EloquentCollection
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Find a record by it's ID.
     *
     * @param $id
     * @return EloquentModel
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Find all records by IDs.
     *
     * @param array $ids
     * @return EloquentCollection
     */
    public function findAll(array $ids)
    {
        return $this->model->whereIn('id', $ids)->get();
    }

    /**
     * Find a trashed record by it's ID.
     *
     * @param $id
     * @return null|EloquentModel
     */
    public function findTrashed($id)
    {
        return $this->model
            ->onlyTrashed()
            ->orderBy('id', 'ASC')
            ->where('id', $id)
            ->first();
    }

    /**
     * Create a new record.
     *
     * @param array $attributes
     * @return EloquentModel
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Return a new instance.
     *
     * @return EloquentModel
     */
    public function newInstance()
    {
        return $this->model->newInstance();
    }

    /**
     * Count all records.
     *
     * @return int
     */
    public function count()
    {
        return $this->model->count();
    }

    /**
     * Return a paginated collection.
     *
     * @param array $parameters
     * @return LengthAwarePaginator
     */
    public function paginate(array $parameters = [])
    {
        $paginator = array_pull($parameters, 'paginator');
        $perPage   = array_pull($parameters, 'per_page', config('streams::system.per_page', 15));

        /* @var Builder $query */
        $query = $this->model->newQuery();

        /**
         * First apply any desired scope.
         */
        if ($scope = array_pull($parameters, 'scope')) {
            call_user_func([$query, camel_case($scope)], array_pull($parameters, 'scope_arguments', []));
        }

        /**
         * Lastly we need to loop through all of the
         * parameters and assume the rest are methods
         * to call on the query builder.
         */
        foreach ($parameters as $method => $arguments) {

            $method = camel_case($method);

            if (in_array($method, ['update', 'delete'])) {
                continue;
            }

            if (is_array($arguments)) {
                call_user_func_array([$query, $method], $arguments);
            } else {
                call_user_func_array([$query, $method], [$arguments]);
            }
        }

        if ($paginator === 'simple') {
            $pagination = $query->simplePaginate($perPage);
        } else {
            $pagination = $query->paginate($perPage);
        }

        return $pagination;
    }

    /**
     * Save a record.
     *
     * @param EloquentModel $entry
     * @return bool
     */
    public function save(EloquentModel $entry)
    {
        return $entry->save();
    }

    /**
     * Update multiple records.
     *
     * @param array $attributes
     * @return bool
     */
    public function update(array $attributes = [])
    {
        return $this->model->update($attributes);
    }

    /**
     * Delete a record.
     *
     * @param EloquentModel $entry
     * @return bool
     */
    public function delete(EloquentModel $entry)
    {
        return $entry->delete();
    }

    /**
     * Force delete a record.
     *
     * @param EloquentModel $entry
     * @return bool
     */
    public function forceDelete(EloquentModel $entry)
    {
        $entry->forceDelete();

        /**
         * If we were not able to force delete
         */
        return !$entry->exists;
    }

    /**
     * Restore a trashed record.
     *
     * @param EloquentModel $entry
     * @return bool
     */
    public function restore(EloquentModel $entry)
    {
        return $entry->restore();
    }

    /**
     * Truncate the entries.
     *
     * @return $this
     */
    public function truncate()
    {
        $this->model->flushCache();

        foreach ($this->model->all() as $entry) {
            $this->delete($entry);
        }

        $this->model->truncate(); // Clear trash

        if ($this->model->isTranslatable() && $translation = $this->model->getTranslationModel()) {

            $translation->flushCache();

            foreach ($translation->all() as $entry) {
                $this->delete($entry);
            }

            $translation->truncate(); // Clear trash
        }

        return $this;
    }

    /**
     * Set the model.
     *
     * @param EloquentModel $model
     * @return $this
     */
    public function setModel(EloquentModel $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the model.
     *
     * @return EloquentModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Pipe non-existing calls through hooks.
     *
     * @param $method
     * @param $parameters
     * @return mixed|null
     */
    public function __call($method, $parameters)
    {
        if ($this->hasHook($hook = snake_case($method))) {
            return $this->call($hook, $parameters);
        }

        return null;
    }
}
