<?php namespace Anomaly\Streams\Platform\Ui\Table\Column;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Ui\Table\Column\Contract\ColumnInterface;

/**
 * Class Column
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Ui\Table\Column
 */
class Column implements ColumnInterface
{

    /**
     * The entry object.
     *
     * @var null
     */
    protected $entry = null;

    /**
     * The column value.
     *
     * @var null
     */
    protected $value = null;

    /**
     * The column class.
     *
     * @var null
     */
    protected $class = null;

    /**
     * The column prefix.
     *
     * @var null
     */
    protected $prefix = null;

    /**
     * The column header.
     *
     * @var null
     */
    protected $header = null;

    /**
     * Create a new Column instance.
     *
     * @param null $class
     * @param null $entry
     * @param null $header
     * @param null $prefix
     * @param null $value
     */
    function __construct($class = null, $entry = null, $header = null, $prefix = null, $value = null)
    {
        $this->class  = $class;
        $this->entry  = $entry;
        $this->value  = $value;
        $this->header = $header;
        $this->prefix = $prefix;
    }

    /**
     * Return the view data.
     *
     * @param  array $arguments
     * @return array
     */
    public function getTableData()
    {
        $value = $this->getValue();
        $class = $this->getClass();

        if ($this->entry instanceof EntryInterface && is_string($value)) {
            $value = $this->getValueFromField($value);
        }

        return compact('value', 'class');
    }

    /**
     * Get the value from a field.
     *
     * @param  $value
     * @return mixed
     */
    protected function getValueFromField($value)
    {
        if ($this->entry instanceof EntryInterface) {
            return $this->entry->getFieldValue($value);
        }

        return $value;
    }

    /**
     * Set the class.
     *
     * @param  $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get the class.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set the entry.
     *
     * @param  $entry
     * @return $this
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;

        return $this;
    }

    /**
     * Get the entry.
     *
     * @return EntryInterface|null
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Set the header.
     *
     * @param  $header
     * @return $this
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get the header.
     *
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set the prefix.
     *
     * @param  $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get the prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the value.
     *
     * @param  $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
