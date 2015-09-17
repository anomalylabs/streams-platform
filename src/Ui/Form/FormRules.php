<?php

namespace Anomaly\Streams\Platform\Ui\Form;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;

/**
 * Class FormRules.
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Ui\Form
 */
class FormRules
{
    /**
     * Compile rules from form fields.
     *
     * @param FormBuilder $builder
     * @return array
     */
    public function compile(FormBuilder $builder)
    {
        $rules = [];

        $entry  = $builder->getFormEntry();
        $stream = $builder->getFormStream();

        /* @var FieldType $field */
        foreach ($builder->getEnabledFormFields() as $field) {
            if ($field->isDisabled()) {
                continue;
            }

            if (in_array($field->getField(), $builder->getSkips())) {
                continue;
            }

            $fieldRules = array_filter(array_unique($field->getRules()));

            if (! $stream instanceof StreamInterface) {
                $rules[$field->getInputName()] = implode('|', $fieldRules);

                continue;
            }

            if ($assignment = $stream->getAssignment($field->getField())) {
                $type = $assignment->getFieldType();

                if ($type->isRequired()) {
                    $fieldRules[] = 'required';
                }

                if (! isset($fieldRules['unique']) && $assignment->isUnique() && ! $assignment->isTranslatable()) {
                    $unique = 'unique:'.$stream->getEntryTableName().','.$field->getColumnName();

                    if ($entry && $id = $entry->getId()) {
                        $unique .= ','.$id;
                    }

                    $fieldRules[] = $unique;
                }

                if ($assignment->isTranslatable() && $field->getLocale() !== config('app.fallback_locale')) {
                    $fieldRules = array_diff($fieldRules, ['required']);
                }
            }

            $rules[$field->getInputName()] = implode('|', array_unique($fieldRules));
        }

        return array_filter($rules);
    }
}
