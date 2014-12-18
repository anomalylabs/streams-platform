<?php namespace Anomaly\Streams\Platform\Ui\Form\Action\Command;

/**
 * Class StandardizeActionInputCommandHandler
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Ui\Form\Action\Command
 */
class StandardizeActionInputCommandHandler
{

    /**
     * Handle the command.
     *
     * @param StandardizeActionInputCommand $command
     */
    public function handle(StandardizeActionInputCommand $command)
    {
        $builder = $command->getBuilder();

        $actions = [];

        foreach ($builder->getActions() as $key => $action) {
            /**
             * If the key is numeric and the action is
             * a string then treat the string as both the
             * action and the slug. This is OK as long as
             * there are not multiple instances of this
             * input using the same action which is not likely.
             */
            if (is_numeric($key) && is_string($action)) {
                $action = [
                    'slug'   => $action,
                    'action' => $action,
                ];
            }

            /**
             * If the key is not numeric and the action is an
             * array without an action then use the key for
             * the action.
             */
            if (is_array($action) && !isset($action['action']) && !is_numeric($key)) {
                $action['action'] = $key;
            }

            /**
             * If the action is an array and action is not set
             * but the slug is.. use the slug as the action.
             */
            if (is_array($action) && !isset($action['action']) && isset($action['slug'])) {
                $action['action'] = $action['slug'];
            }

            /**
             * If the action is an array and a slug is not set
             * but the action is.. use the action as the slug.
             */
            if (is_array($action) && !isset($action['slug']) && isset($action['action'])) {
                $action['slug'] = $action['action'];
            }

            $actions[] = $action;
        }

        $builder->setActions($actions);
    }
}
