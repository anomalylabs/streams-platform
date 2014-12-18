<?php namespace Anomaly\Streams\Platform\Addon\Module;

use Anomaly\Streams\Platform\Addon\Addon;

/**
 * Class Module
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Addon\Module
 */
class Module extends Addon
{

    /**
     * The module's sections.
     *
     * @var array
     */
    protected $sections = [];

    /**
     * The module's navigation group.
     *
     * @var null
     */
    protected $navigation = null;

    /**
     * The installed flag.
     *
     * @var bool
     */
    protected $installed = false;

    /**
     * The enabled flag.
     *
     * @var bool
     */
    protected $enabled = false;

    /**
     * The active flag.
     *
     * @var bool
     */
    protected $active = false;

    /**
     * Get the module's tag class.
     *
     * @var string
     */
    protected $tag = 'Anomaly\Streams\Platform\Addon\Module\ModuleTag';

    /**
     * Get the module's sections.
     *
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Get the module's navigation group.
     *
     * @return string|null|false
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * Set the installed flag.
     *
     * @param  $installed
     * @return $this
     */
    public function setInstalled($installed)
    {
        $this->installed = $installed;

        return $this;
    }

    /**
     * Get the installed flag.
     *
     * @return bool
     */
    public function isInstalled()
    {
        return $this->installed;
    }

    /**
     * Set the enabled flag.
     *
     * @param  $enabled
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get the enabled flag.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled && $this->installed;
    }

    /**
     * Set the active flag.
     *
     * @param  $active
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get the active flag.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set the module's tag class.
     *
     * @param  $tag
     * @return $this
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get the module's tag class.
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    public function newInstaller()
    {
        $installer = get_class($this) . 'Installer';

        if (!class_exists($installer)) {
            $installer = 'Anomaly\Streams\Platform\Addon\Module\ModuleInstaller';
        }

        return app()->make($installer, [$this]);
    }
}
