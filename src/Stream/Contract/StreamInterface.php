<?php namespace Anomaly\Streams\Platform\Stream\Contract;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Assignment\AssignmentCollection;
use Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface;
use Anomaly\Streams\Platform\Field\Contract\FieldInterface;

/**
 * Interface StreamInterface
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Stream\Contract
 */
interface StreamInterface
{

    /**
     * Compile the entry models.
     *
     * @return mixed
     */
    public function compile();

    /**
     * Get the ID.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get the namespace.
     *
     * @return string
     */
    public function getNamespace();

    /**
     * Get the slug.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Get the prefix.
     *
     * @return string
     */
    public function getPrefix();

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the translatable flag.
     *
     * @return bool
     */
    public function isTranslatable();

    /**
     * Get the title column.
     *
     * @return mixed
     */
    public function getTitleColumn();

    /**
     * Get the related assignments.
     *
     * @return AssignmentCollection
     */
    public function getAssignments();

    /**
     * Get an assignment by it's field's slug.
     *
     * @param  $fieldSlug
     * @return AssignmentInterface
     */
    public function getAssignment($fieldSlug);

    /**
     * Get a stream field by it's slug.
     *
     * @param  $slug
     * @return FieldInterface
     */
    public function getField($slug);

    /**
     * Get a field's type by the field's slug.
     *
     * @param  $fieldSlug
     * @return FieldType
     */
    public function getFieldType($fieldSlug);

    /**
     * Get the entry table name.
     *
     * @return mixed
     */
    public function getEntryTableName();

    /**
     * Get the entry translations table name.
     *
     * @return mixed
     */
    public function getEntryTranslationsTableName();

    /**
     * Get the foreign key.
     *
     * @return mixed
     */
    public function getForeignKey();

    /**
     * Get all attributes.
     *
     * @return mixed
     */
    public function getAttributes();

    /**
     * Get an attribute.
     *
     * @param  $key
     * @return mixed
     */
    public function getAttribute($key);
}
