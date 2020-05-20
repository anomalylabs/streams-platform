<?php

namespace Anomaly\Streams\Platform\Ui\Table\Component\Column;

use Anomaly\Streams\Platform\Ui\Ui;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Anomaly\Streams\Platform\Support\Facades\Hydrator;

/**
 * Class Column
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class Column extends Ui implements Arrayable, Jsonable
{

    /**
     * The object attributes.
     *
     * @var array
     */
    protected $attributes = [
        'view' => null,
        'value' => null,
        'entry' => null,
        'heading' => null,
        'wrapper' => null,
    ];

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
     * Dynamically retrieve attributes.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes.
     *
     * @param  string  $key
     * @param  mixed $value
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }
}
