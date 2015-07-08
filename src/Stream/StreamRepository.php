<?php namespace Anomaly\Streams\Platform\Stream;

use Anomaly\Streams\Platform\Model\EloquentCollection;
use Anomaly\Streams\Platform\Model\EloquentModel;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface;
use Illuminate\Database\Schema\Builder;

/**
 * Class StreamRepository
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Stream
 */
class StreamRepository implements StreamRepositoryInterface
{

    /**
     * The stream model.
     *
     * @var
     */
    protected $model;

    /**
     * The schema builder.
     *
     * @var Builder
     */
    protected $schema;

    /**
     * Create a new StreamRepository instance.
     *
     * @param StreamModel $model
     */
    public function __construct(StreamModel $model)
    {
        $this->model = $model;

        $this->schema = app('db')->connection()->getSchemaBuilder();
    }

    /**
     * Get all streams.
     *
     * @return EloquentCollection
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Create a new Stream.
     *
     * @param array $attributes
     * @return StreamInterface
     */
    public function create(array $attributes)
    {
        // Set some reasonable defaults.
        $attributes['order_by']     = array_get($attributes, 'order_by', 'id');
        $attributes['title_column'] = array_get($attributes, 'title_column', 'id');

        $attributes['locked']       = (array_get($attributes, 'locked', false));
        $attributes['trashable']    = (array_get($attributes, 'trashable', false));
        $attributes['translatable'] = (array_get($attributes, 'translatable', false));

        $attributes['prefix']       = array_get($attributes, 'prefix', array_get($attributes, 'namespace') . '_');
        $attributes['view_options'] = array_get($attributes, 'view_options', ['id', 'created_at']);

        // Format just in case.
        $attributes['slug'] = str_slug(array_get($attributes, 'slug'), '_');

        // Move to lang just in case.
        if (isset($attributes['name'])) {
            array_set(
                $attributes,
                config('app.fallback_locale') . '.name',
                array_pull($attributes, 'name')
            );
        }

        if (isset($attributes['description'])) {
            array_set(
                $attributes,
                config('app.fallback_locale') . '.description',
                array_pull($attributes, 'description')
            );
        }

        return $this->model->create($attributes);
    }

    /**
     * Save a Stream.
     *
     * @param StreamInterface|EloquentModel $stream
     * @return bool
     */
    public function save(StreamInterface $stream)
    {
        return $stream->save();
    }

    /**
     * Find a stream by it's namespace and slug.
     *
     * @param  $slug
     * @param  $namespace
     * @return null|StreamInterface
     */
    public function findBySlugAndNamespace($slug, $namespace)
    {
        return $this->model->where('slug', $slug)->where('namespace', $namespace)->first();
    }

    /**
     * Delete a Stream.
     *
     * @param StreamInterface|EloquentModel $stream
     * @return bool
     */
    public function delete(StreamInterface $stream)
    {
        if ($deleted = $stream->delete()) {
            $this->model->where('slug', $stream->getSlug())->where('namespace', $stream->getNamespace())->delete();
        }

        return $deleted;
    }

    /**
     * Clean up abandoned streams.
     */
    public function cleanup()
    {
        /* @var StreamInterface $stream */
        foreach ($this->model->all() as $stream) {
            if (!$this->schema->hasTable($stream->getEntryTableName())) {
                $this->delete($stream);
            }
        }
    }
}
