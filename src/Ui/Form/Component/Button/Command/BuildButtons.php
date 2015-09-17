<?php

namespace Anomaly\Streams\Platform\Ui\Form\Component\Button\Command;

use Anomaly\Streams\Platform\Ui\Form\FormBuilder;

/**
 * Class BuildButtons.
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Ui\Form\Component\Button\Command
 */
class BuildButtons
{
    /**
     * The form builder.
     *
     * @var FormBuilder
     */
    protected $builder;

    /**
     * Create a new BuildButtons instance.
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
