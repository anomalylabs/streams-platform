<?php

namespace Anomaly\Streams\Platform\Support;

/**
 * Class Presenter.
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Support
 */
abstract class Presenter extends \Robbo\Presenter\Presenter
{
    /**
     * Pass any unknown variable calls to present{$variable} or fall through to the injected object.
     *
     * @param  string $var
     * @return mixed
     */
    public function __get($var)
    {
        if ($method = $this->getPresenterMethodFromVariable($var)) {
            return $this->$method();
        }

        // Check the presenter for a getter.
        if (method_exists($this, camel_case('get_'.$var))) {
            return call_user_func_array([$this, camel_case('get_'.$var)], []);
        }

        // Check the presenter for a method.
        if (method_exists($this, camel_case($var))) {
            return call_user_func_array([$this->object, camel_case($var)], []);
        }

        // Check the object for a getter.
        if (method_exists($this->object, camel_case('get_'.$var))) {
            return call_user_func_array([$this->object, camel_case('get_'.$var)], []);
        }

        // Check the object for a getter.
        if (method_exists($this->object, camel_case('is_'.$var))) {
            return call_user_func_array([$this->object, camel_case('is_'.$var)], []);
        }

        // Check the object for a method.
        if (method_exists($this->object, camel_case($var))) {
            return call_user_func_array([$this->object, camel_case($var)], []);
        }

        try {
            // Lastly try generic property access.
            return $this->__getDecorator()->decorate(
                is_array($this->object) ? $this->object[$var] : $this->object->$var
            );
        } catch (\Exception $e) {
            // Don't do anything.
        }
    }

    /**
     * Get the object.
     *
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Fetch the presenter method name for the given variable.
     *
     * @param  string $variable
     * @return string|null
     */
    protected function getPresenterMethodFromVariable($variable)
    {
        $method = camel_case($variable);

        if (method_exists($this, $method)) {
            return $method;
        }
    }

    /**
     * Return the objects string method.
     *
     * @return string
     */
    public function __toString()
    {
        if (method_exists($this->object, '__toString')) {
            return $this->object->__toString();
        }

        parent::__toString();
    }
}
