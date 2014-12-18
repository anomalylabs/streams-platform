<?php namespace Anomaly\Streams\Platform\Addon\FieldType;

use Anomaly\Streams\Platform\Contract\StringableInterface;
use Robbo\Presenter\Presenter;

/**
 * Class FieldTypePresenter
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Addon\FieldType
 */
class FieldTypePresenter extends Presenter implements StringableInterface
{

    /**
     * Create a new Presenter instance.
     *
     * @param $object
     */
    public function __construct($object)
    {
        if ($object instanceof FieldType) {
            $this->object = $object;
        }
    }

    /**
     * By default return the value.
     *
     * This can be dangerous if used in a loop!
     * There is a PHP bug that caches it's
     * output when used in a loop.
     * Take heed.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->object->getValue();
    }

    /**
     * Return the instance as a string.
     *
     * @return mixed
     */
    public function toString()
    {
        return (string)$this->object->getValue();
    }
}
