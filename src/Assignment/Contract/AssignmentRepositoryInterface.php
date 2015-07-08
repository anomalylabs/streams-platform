<?php namespace Anomaly\Streams\Platform\Assignment\Contract;

use Anomaly\Streams\Platform\Assignment\AssignmentCollection;
use Anomaly\Streams\Platform\Field\Contract\FieldInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;

/**
 * Interface AssignmentRepositoryInterface
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Assignment\Contract
 */
interface AssignmentRepositoryInterface
{

    /**
     * Create a new assignment.
     *
     * @param array $attributes
     * @return AssignmentInterface
     */
    public function create(array $attributes);

    /**
     * Find an assignment.
     *
     * @param $id
     * @return null|AssignmentInterface
     */
    public function find($id);

    /**
     * Find an assignment by stream and field.
     *
     * @param StreamInterface $stream
     * @param FieldInterface  $field
     * @return null|AssignmentInterface
     */
    public function findByStreamAndField(StreamInterface $stream, FieldInterface $field);

    /**
     * Find all assignments by stream.
     *
     * @param StreamInterface $stream
     * @return AssignmentCollection
     */
    public function findByStream(StreamInterface $stream);

    /**
     * Delete an assignment.
     *
     * @param AssignmentInterface $assignment
     */
    public function delete(AssignmentInterface $assignment);

    /**
     * Clean up abandoned assignments.
     */
    public function cleanup();
}
