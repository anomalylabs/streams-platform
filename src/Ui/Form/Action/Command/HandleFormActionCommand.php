<?php namespace Anomaly\Streams\Platform\Ui\Form\Action\Command;

use Anomaly\Streams\Platform\Ui\Form\FormBuilder;

/**
 * Class HandleFormActionCommand
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Ui\Form\Action\Command
 */
class HandleFormActionCommand
{

    /**
     * The form builder.
     *
     * @var \Anomaly\Streams\Platform\Ui\Form\FormBuilder
     */
    protected $builder;

    /**
     * Create a new HandleFormActionCommand instance.
     *
     * @param FormBuilder $builder
     */
    public function __construct(FormBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Get the form builder.
     *
     * @return FormBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }
}
