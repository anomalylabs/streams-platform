<?php

namespace Anomaly\Streams\Platform\Support;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Anomaly\Streams\Platform\Support\Traits\Prototype;
use Anomaly\Streams\Platform\Support\Traits\FiresCallbacks;

/**
 * Class Workflow
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class Workflow
{

    use FiresCallbacks;

    use Prototype {
        Prototype::__construct as private constructPrototype;
    }

    /**
     * The workflow steps.
     *
     * @var array
     */
    protected $steps = [];

    /**
     * Create a new class instance.
     *
     * @param array $steps
     */
    public function __construct(array $steps = [])
    {
        $this->steps = $this->named(array_merge($this->steps, $steps));

        $this->constructPrototype();
    }

    /**
     * Process the workflow.
     *
     * @param array $payload
     * @return mixed
     */
    public function process(array $payload = [])
    {
        foreach ($this->steps as $name => $step) {

            $this->triggerCallback('before_' . $name, $payload);

            $this->do($step, $payload);

            $this->triggerCallback('after_' . $name, $payload);
        }
    }

    /**
     * Default callbacks through the provided object.
     *
     * @param mixed $object
     */
    public function passThrough($object)
    {
        $this->object = $object;

        $this->callback = function ($callback, $payload) use ($object) {
            $object->fire(implode('_', $callback), $payload);
        };

        return $this;
    }

    /**
     * Trigger the callbacks.
     *
     * @param [type] $name
     * @param array $payload
     */
    protected function triggerCallback($name, array $payload)
    {
        $callback = array_filter([
            'workflow' => $this->name ?: $this->name($this),
            'name' => $name,
        ]);

        $payload = compact('payload', 'callback');

        $this->callback ? App::call($this->callback, $payload) : null;

        $method = Str::camel(implode('_', ['on'] + $callback));

        if ($this->object && method_exists($this->object, $method)) {
            App::call([$this->object, $method], $payload);
        }
    }

    /**
     * Add a step to the workflow.
     *
     * @param string $name
     * @param string|\Closure $step
     * @param integer $position
     * @return $this
     */
    public function add($name, $step = null, $position = null)
    {
        if (!$step && is_string($step)) {

            $step = $name;

            $name = $this->name($step);
        }

        if ($position === null) {
            $position = count($this->steps);
        }

        $this->steps = array_slice($this->steps, 0, $position, true) +
            [$name => $step] +
            array_slice($this->steps, $position, count($this->steps) - 1, true);

        return $this;
    }

    /**
     * Push a step to first.
     *
     * @param string $name
     * @param string|\Closure $step
     * @return $this
     */
    public function first($name, $step = null)
    {
        return $this->add($name, $step, 0);
    }

    /**
     * Add a step before another.
     *
     * @param string $target
     * @param string $name
     * @param string|\Closure $step
     * @return $this
     */
    public function before($target, $name, $step = null)
    {
        return $this->add($name, $step, array_search($target, array_keys($this->steps)));
    }

    /**
     * Add a step after another.
     *
     * @param string $target
     * @param string $name
     * @param string|\Closure $step
     * @return $this
     */
    public function after($target, $name, $step = null)
    {
        return $this->add($name, $step, array_search($target, array_keys($this->steps)) + 1);
    }

    /**
     * Add a step after another.
     *
     * @param string $name
     * @param string $name
     * @param string|\Closure $step
     * @return $this
     */
    public function set($name, $step)
    {
        $this->steps[$name] = $step;

        return $this;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function setSteps(array $steps): Workflow
    {
        $this->steps = $steps;

        return $this;
    }

    protected function named($steps): array
    {
        $named = [];

        array_walk($steps, function ($step, $name) use (&$named) {

            if (is_string($name)) {

                $named[$name] = $step;

                return;
            }

            if (is_string($step)) {

                $named[$this->name($step)] = $step;

                return true;
            }

            if (is_object($step)) {

                $named[$this->name($step)] = $step;

                return true;
            }

            $named[$name] = $step;
        });

        return $named;
    }

    protected function name($step): string
    {
        if ($step == $this) {
            return '';
        }

        if (is_object($step)) {
            $step = get_class($step);
        }

        $step = explode('\\', $step);

        $step = end($step);

        return Str::snake($step);
    }

    protected function do($step, array $payload = [])
    {
        if (is_array($step)) {
            return App::call($step, $payload);
        }

        return App::call($step, $payload, 'handle');
    }
}
