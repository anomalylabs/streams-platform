<?php namespace Anomaly\Streams\Platform\Assignment;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface;
use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Field\Contract\FieldInterface;
use Anomaly\Streams\Platform\Model\EloquentModel;
use Dimsav\Translatable\Translatable;

/**
 * Class AssignmentModel
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Assignment
 */
class AssignmentModel extends EloquentModel implements AssignmentInterface
{

    use Translatable;

    /**
     * Do not use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * The foreign key for translations.
     *
     * @var string
     */
    protected $translationForeignKey = 'assignment_id';

    /**
     * The database table name.
     *
     * @var string
     */
    protected $table = 'streams_assignments';

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        self::observe(new AssignmentObserver());
    }

    /**
     * Get the field slug.
     *
     * @return mixed
     */
    public function getFieldSlug()
    {
        return $this->field->slug;
    }

    /**
     * Get the assignment's field's type.
     *
     * @param  EntryInterface $entry
     * @param  null           $locale
     * @return FieldType
     */
    public function getFieldType(EntryInterface $entry = null, $locale = null)
    {
        // Get the type object from our related field.
        $type = $this->getField()->getType($entry, $locale);

        // These are always on or off so set em.
        $type->setRequired($this->isRequired());
        $type->setTranslatable($this->isTranslatable());

        /**
         * This is already set as the field name.
         * If the label is available (translated)
         * set it as type's label.
         */
        if ($label = $this->getLabel($locale)) {
            $type->setLabel($label);
        }

        /**
         * This defaults to null but it's translation
         * string is automated. If the translation is
         * available set the placeholder on the type.
         */
        if ($placeholder = $this->getPlaceholder($locale)) {
            $type->setPlaceholder($placeholder);
        }

        /**
         * This defaults to null but it's translation
         * string is automated. If the translation is
         * available set the  instructions on the type.
         */
        if ($instructions = $this->getInstructions($locale)) {
            $type->setInstructions($instructions);
        }

        return $type;
    }

    /**
     * Get the label. If it is not translated then
     * then just return null instead.
     *
     * @param  null $locale
     * @return string|null
     */
    public function getLabel($locale = null)
    {
        $locale = $locale ? : config('app.locale');

        $assignment = $this->translate($locale) ? : $this;

        $label = $assignment->label;

        if (trans($label) !== $label) {
            return trans($label, [], null, $locale);
        }

        return null;
    }

    /**
     * Get the placeholder. If it is not translated
     * then just return null instead.
     *
     * @param  null $locale
     * @return null|string
     */
    public function getPlaceholder($locale = null)
    {
        $locale = $locale ? : config('app.locale');

        $assignment = $this->translate($locale) ? : $this;

        $placeholder = $assignment->placeholder;

        if (trans($placeholder) !== $placeholder) {
            return trans($placeholder, [], null, $locale);
        }

        return null;
    }

    /**
     * Get the instructions. If it is not translated
     * then just return null instead.
     *
     * @param  null $locale
     * @return null|string
     */
    public function getInstructions($locale = null)
    {
        $locale = $locale ? : config('app.locale');

        $assignment = $this->translate($locale) ? : $this;

        $instructions = $assignment->instructions;

        if (trans($instructions) !== $instructions) {
            return trans($instructions, [], null, $locale);
        }

        return null;
    }

    /**
     * Get the related stream.
     *
     * @return mixed
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Get the related field.
     *
     * @return FieldInterface
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Get the unique flag.
     *
     * @return mixed
     */
    public function isUnique()
    {
        return ($this->unique);
    }

    /**
     * Get the required flag.
     *
     * @return mixed
     */
    public function isRequired()
    {
        return ($this->required);
    }

    /**
     * Get  the translatable flag.
     *
     * @return bool|mixed
     */
    public function isTranslatable()
    {
        return ($this->translatable && $this->stream->translatable);
    }

    /**
     * Get the column name.
     *
     * @return mixed
     */
    public function getColumnName()
    {
        $type = $this->getFieldType();

        return $type->getColumnName();
    }

    /**
     * Serialize the rules attribute
     * before setting to the model.
     *
     * @param $rules
     */
    public function setRulesAttribute($rules)
    {
        $this->attributes['rules'] = serialize($rules);
    }

    /**
     * Unserialize the rules attribute
     * after getting from the model.
     *
     * @param  $rules
     * @return mixed
     */
    public function getRulesAttribute($rules)
    {
        return unserialize($rules);
    }

    /**
     * @param array $items
     * @return AssignmentCollection
     */
    public function newCollection(array $items = array())
    {
        return new AssignmentCollection($items);
    }

    /**
     * Return the stream relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stream()
    {
        return $this->belongsTo('Anomaly\Streams\Platform\Stream\StreamModel', 'stream_id');
    }

    /**
     * Return the field relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function field()
    {
        return $this->belongsTo('Anomaly\Streams\Platform\Field\FieldModel');
    }
}
