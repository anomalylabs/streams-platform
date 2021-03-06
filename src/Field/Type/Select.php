<?php

namespace Streams\Core\Field\Type;

use Illuminate\Support\Arr;
use Streams\Core\Field\FieldType;

class Select extends FieldType
{
    /**
     * Initialize the prototype.
     *
     * @param array $attributes
     * @return $this
     */
    protected function initializePrototypeAttributes(array $attributes)
    {
        return parent::initializePrototypeAttributes(array_merge([
            'rules' => [
                //'in_options',
            ],
        ], $attributes));
    }

    public function options()
    {
        return Arr::get($this->field->config, 'options', []);
    }
}
