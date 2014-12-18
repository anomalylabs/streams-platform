<?php namespace Anomaly\Streams\Platform\Ui\Table\Filter\Type;

use Anomaly\Streams\Platform\Ui\Table\Filter\Filter;

/**
 * Class InputFilter
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Ui\Table\Filter\Type
 */
class InputFilter extends Filter
{

    /**
     * The input type.
     *
     * @var string
     */
    protected $type;

    /**
     * Create a new InputFilter instance.
     *
     * @param        $slug
     * @param null   $prefix
     * @param bool   $active
     * @param null   $handler
     * @param null   $placeholder
     * @param string $text
     */
    public function __construct(
        $slug,
        $prefix = null,
        $active = false,
        $handler = null,
        $placeholder = null,
        $text = 'text'
    ) {
        $this->text = $text;

        parent::__construct($slug, $active, $handler, $placeholder, $prefix);
    }

    /**
     * Set the input type.
     *
     * @param  $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the input type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the input HTML.
     *
     * @return mixed
     */
    protected function getInput()
    {
        $class       = 'form-control';
        $placeholder = trans($this->getPlaceholder());

        $options = compact('class', 'placeholder');

        return app('form')->input($this->getType(), $this->getName(), $this->getValue(), $options);
    }
}
