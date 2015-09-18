<?php

namespace Anomaly\Streams\Platform\Ui\Table\Component\Action;

use Anomaly\Streams\Platform\Ui\Table\Component\Action\Handler\Delete;
use Anomaly\Streams\Platform\Ui\Table\Component\Action\Handler\Edit;
use Anomaly\Streams\Platform\Ui\Table\Component\Action\Handler\Reorder;

/**
 * Class ActionRegistry.
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Ui\Table\Component\Action
 */
class ActionRegistry
{
    /**
     * Available actions.
     *
     * @var array
     */
    protected $actions = [
        'delete'  => [
            'handler' => Delete::class,
        ],
        'edit'    => [
            'handler' => Edit::class,
        ],
        'reorder' => [
            'handler' => Reorder::class,
            'text'    => 'streams::button.reorder',
            'icon'    => 'fa fa-sort-amount-asc',
            'class'   => 'reorder',
            'type'    => 'success',
        ],
    ];

    /**
     * Get a action.
     *
     * @param  $action
     * @return array|null
     */
    public function get($action)
    {
        if (! $action) {
            return;
        }

        return array_get($this->actions, $action);
    }

    /**
     * Register a action.
     *
     * @param       $action
     * @param array $parameters
     * @return $this
     */
    public function register($action, array $parameters)
    {
        array_set($this->actions, $action, $parameters);

        return $this;
    }
}
