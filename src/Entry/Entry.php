<?php

namespace Anomaly\Streams\Platform\Entry;

use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Anomaly\Streams\Platform\Stream\Stream;
use Anomaly\Streams\Platform\Support\Traits\Fluency;
use Anomaly\Streams\Platform\Support\Facades\Hydrator;
use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;

/**
 * Class Entry
 *
 * @link    http://pyrocms.com/
 * @author  PyroCMS, Inc. <support@pyrocms.com>
 * @author  Ryan Thompson <ryan@pyrocms.com>
 *
 */
class Entry implements EntryInterface, Arrayable, Jsonable
{

    use Fluency {
        Fluency::__construct as private constructFluency;
    }

    /**
     * The stream instance.
     *
     * @var Stream
     */
    public $stream;

    /**
     * Create a new
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->stream = Arr::pull($attributes, 'stream');

        $this->constructFluency($attributes);

        if ($handle = $this->getPrototypeAttribute('streams__extends')) {
            $this->extendEntry($handle);
        }
    }

    /**
     * Return the entry stream.
     * 
     * @return Stream
     */
    public function stream()
    {
        return $this->stream;
    }

    /**
     * Save the entry.
     *
     * @return bool
     */
    public function save()
    {
        return $this->stream
            ->repository()
            ->save($this);
    }

    /**
     * Delete the entry.
     *
     * @return bool
     */
    public function delete()
    {
        return $this->stream
            ->repository()
            ->delete($this);
    }

    /**
     * Return the entry validator.
     * 
     * @return Validator
     */
    public function validator()
    {
        return $this->stream->validator($this);
    }

    /**
     * Load an entry over this one.
     *
     * @param $identifier
     * @return $this
     */
    protected function loadEntry($identifier)
    {
        $loaded = $this->stream->repository()->find($identifier);

        $this->setPrototypeAttributes(
            array_merge($this->toArray(), $loaded->toArray())
        );

        return $this;
    }

    /**
     * Extend over another entry.
     *
     * @param $identifier
     * @return $this
     */
    protected function extendEntry($identifier)
    {
        $extended = $this->stream->repository()->find($identifier);

        $this->setPrototypeAttributes(
            array_merge($extended->toArray(), $this->toArray())
        );

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return Hydrator::dehydrate($this);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Return a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
