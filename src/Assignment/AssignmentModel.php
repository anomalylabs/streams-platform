<?php namespace Anomaly\Streams\Platform\Assignment;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface;
use Anomaly\Streams\Platform\Field\Contract\FieldInterface;
use Anomaly\Streams\Platform\Model\EloquentModel;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;

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

    /**
     * Do not use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Default attributes.
     *
     * @var array
     */
    protected $attributes = [
        'rules'  => 'a:0:{}',
        'config' => 'a:0:{}'
    ];

    /**
     * The cache minutes.
     *
     * @var int
     */
    protected $cacheMinutes = 99999;

    /**
     * The foreign key for translations.
     *
     * @var string
     */
    protected $translationForeignKey = 'assignment_id';

    /**
     * Translatable attributes.
     *
     * @var array
     */
    protected $translatedAttributes = [
        'label',
        'placeholder',
        'instructions'
    ];

    /**
     * The translation model.
     *
     * @var string
     */
    protected $translationModel = 'Anomaly\Streams\Platform\Assignment\AssignmentModelTranslation';

    /**
     * The database table name.
     *
     * @var string
     */
    protected $table = 'streams_assignments';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        self::observe(app(substr(__CLASS__, 0, -5) . 'Observer'));

        parent::boot();
    }

    /**
     * Because the assignment record holds translatable data
     * we have a conflict. The assignment table has translations
     * but not all assignment are translatable. This helps avoid
     * the translatable conflict during specific procedures.
     *
     * @param  array $attributes
     * @return static
     */
    public static function create(array $attributes)
    {
        $model = parent::create($attributes);

        $model->saveTranslations();

        return;
    }

    /**
     * Set the field attribute.
     *
     * @param FieldInterface $field
     */
    public function setFieldAttribute(FieldInterface $field)
    {
        $this->attributes['field_id'] = $field->getId();
    }

    /**
     * Set the stream attribute.
     *
     * @param StreamInterface $stream
     */
    public function setStreamAttribute(StreamInterface $stream)
    {
        $this->attributes['stream_id'] = $stream->getId();
    }

    /**
     * Get the field slug.
     *
     * @return string
     */
    public function getFieldSlug()
    {
        $field = $this->getField();

        return $field->getSlug();
    }

    /**
     * Get the assignment's field's type.
     *
     * @return null|FieldType
     */
    public function getFieldType()
    {
        $field = $this->getField();

        if (!$field) {
            return null;
        }

        $type = $field->getType();

        if (!$type) {
            return null;
        }

        $type->mergeRules($this->getRules());
        $type->mergeConfig($this->getConfig());
        $type->setRequired($this->isRequired());

        return $type;
    }

    /**
     * Get the field name.
     *
     * @return string
     */
    public function getFieldName()
    {
        $field = $this->getField();

        return $field->getName();
    }

    /**
     * Get the assignment's field's config.
     *
     * @return string
     */
    public function getFieldConfig()
    {
        $field = $this->getField();

        return $field->getConfig();
    }

    /**
     * Get the assignment's field's rules.
     *
     * @return array
     */
    public function getFieldRules()
    {
        $field = $this->getField();

        return $field->getRules();
    }

    /**
     * Get the rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Get the config.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get the instructions.
     *
     * @return null
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * Get the placeholder.
     *
     * @return null
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * Get the related stream.
     *
     * @return StreamInterface
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Get the related stream's slug.
     *
     * @return string
     */
    public function getStreamSlug()
    {
        return $this->stream->getSlug();
    }

    /**
     * Get the related stream's prefix.
     *
     * @return string
     */
    public function getStreamPrefix()
    {
        return $this->stream->getPrefix();
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
     * Get the related field ID.
     *
     * @return null|int
     */
    public function getFieldId()
    {
        $field = $this->getField();

        if (!$field) {
            return null;
        }

        return $field->getId();
    }

    /**
     * Get the unique flag.
     *
     * @return mixed
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * Get the required flag.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Get the translatable flag.
     *
     * @return bool
     */
    public function isTranslatable()
    {
        return $this->translatable;
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
     * Set config attribute.
     *
     * @param array $config
     */
    public function setConfigAttribute($config)
    {
        $this->attributes['config'] = serialize((array)$config);
    }

    /**
     * Return the decoded config attribute.
     *
     * @param  $config
     * @return mixed
     */
    public function getConfigAttribute($config)
    {
        return (array)unserialize($config);
    }

    /**
     * Serialize the rules attribute
     * before setting to the model.
     *
     * @param $rules
     */
    public function setRulesAttribute($rules)
    {
        $this->attributes['rules'] = serialize((array)$rules);
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
        return (array)unserialize($rules);
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
     * Compile the assignment's stream.
     *
     * @return AssignmentInterface
     */
    public function compileStream()
    {
        if ($stream = $this->getStream()) {
            $stream->compile();
        }

        return $this;
    }

    /**
     * Return the stream relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stream()
    {
        return $this->belongsTo('Anomaly\Streams\Platform\Stream\StreamModel');
    }

    /**
     * Return the field relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function field()
    {
        return $this->belongsTo('Anomaly\Streams\Platform\Field\FieldModel', 'field_id');
    }
}
