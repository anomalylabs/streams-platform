<?php namespace Anomaly\Streams\Platform\Ui\Table\Command;

use Anomaly\Streams\Platform\Ui\Table\TableBuilder;

/**
 * Class SetTableStreamCommand
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Ui\Table\Command
 */
class SetTableStreamCommand
{

    /**
     * The table builder.
     *
     * @var \Anomaly\Streams\Platform\Ui\Table\TableBuilder
     */
    protected $builder;

    /**
     * Create a new BuildTableColumnsCommand instance.
     *
     * @param TableBuilder $builder
     */
    public function __construct(TableBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Get the table builder.
     *
     * @return TableBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }
}
