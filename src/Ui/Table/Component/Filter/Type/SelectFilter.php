<?php namespace Anomaly\Streams\Platform\Ui\Table\Component\Filter\Type;

use Anomaly\SelectFieldType\SelectFieldType;
use Anomaly\Streams\Platform\Ui\Table\Component\Filter\Contract\SelectFilterInterface;
use Anomaly\Streams\Platform\Ui\Table\Component\Filter\Filter;

/**
 * Class SelectFilter
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 */
class SelectFilter extends Filter implements SelectFilterInterface
{

    /**
     * The filter options.
     *
     * @var array
     */
    protected $options;

    /**
     * Get the input HTML.
     *
     * @return string
     */
    public function getInput()
    {
        return app(SelectFieldType::class)
            ->setPlaceholder($this->getPlaceholder())
            ->setField('filter_' . $this->getSlug())
            ->setPrefix($this->getPrefix())
            ->setValue($this->getValue())
            ->mergeConfig(
                [
                    'options' => $this->getOptions(),
                ]
            )->getFilter();
    }

    /**
     * Get the options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the options.
     *
     * @param  array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        if (array_key_exists('handler', $options)) {
            $options = app()->call($options['handler']);
        }
        $this->options = $options;

        return $this;
    }
}
