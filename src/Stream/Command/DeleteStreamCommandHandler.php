<?php namespace Anomaly\Streams\Platform\Stream\Command;

use Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface;

/**
 * Class DeleteStreamCommandHandler
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Stream\Command
 */
class DeleteStreamCommandHandler
{

    /**
     * Handle the command.
     *
     * @param DeleteStreamCommand       $command
     * @param StreamRepositoryInterface $streams
     */
    public function handle(DeleteStreamCommand $command, StreamRepositoryInterface $streams)
    {
        return $streams->delete($command->getNamespace(), $command->getSlug());
    }
}
