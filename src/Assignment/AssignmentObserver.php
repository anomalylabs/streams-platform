<?php

namespace Anomaly\Streams\Platform\Assignment;

use Anomaly\Streams\Platform\Assignment\Command\AddAssignmentColumn;
use Anomaly\Streams\Platform\Assignment\Command\DropAssignmentColumn;
use Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface;
use Anomaly\Streams\Platform\Assignment\Event\AssignmentWasCreated;
use Anomaly\Streams\Platform\Assignment\Event\AssignmentWasDeleted;
use Anomaly\Streams\Platform\Assignment\Event\AssignmentWasSaved;
use Anomaly\Streams\Platform\Support\Observer;

/**
 * Class AssignmentObserver.
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Assignment
 */
class AssignmentObserver extends Observer
{
    /**
     * Fired before creating an assignment.
     *
     * @param AssignmentInterface|AssignmentModel $model
     */
    public function creating(AssignmentInterface $model)
    {
        $model->sort_order = $model->newQuery()->count('id') + 1;
    }

    /**
     * Run after a record is created.
     *
     * @param AssignmentInterface $model
     */
    public function created(AssignmentInterface $model)
    {
        $model->flushCache();
        $model->compileStream();

        $this->dispatch(new AddAssignmentColumn($model));

        $this->events->fire(new AssignmentWasCreated($model));
    }

    /**
     * Run after saving a record.
     *
     * @param AssignmentInterface $model
     */
    public function saved(AssignmentInterface $model)
    {
        $model->flushCache();
        $model->compileStream();

        $this->events->fire(new AssignmentWasSaved($model));
    }

    /**
     * Run after a record has been deleted.
     *
     * @param AssignmentInterface $model
     */
    public function deleted(AssignmentInterface $model)
    {
        $model->flushCache();
        $model->compileStream();

        $this->dispatch(new DropAssignmentColumn($model));

        $this->events->fire(new AssignmentWasDeleted($model));
    }
}
