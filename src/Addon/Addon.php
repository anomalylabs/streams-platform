<?php namespace Anomaly\Streams\Platform\Addon;

use Robbo\Presenter\PresentableInterface;
use Robbo\Presenter\Presenter;

/**
 * Class Addon
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Addon
 */
class Addon implements PresentableInterface
{

    /**
     * The addon path.
     *
     * @var string
     */
    protected $path = null;

    /**
     * The addon type.
     *
     * @var string
     */
    protected $type = null;

    /**
     * The addon slug.
     *
     * @var string
     */
    protected $slug = null;

    /**
     * The addon vendor.
     *
     * @var string
     */
    protected $vendor = null;

    /**
     * Get the addon's presenter.
     *
     * @return Presenter
     */
    public function getPresenter()
    {
        return new AddonPresenter($this);
    }

    /**
     * Get the core addon flag.
     *
     * @return bool
     */
    public function isCore()
    {
        return str_contains($this->getPath(), 'core/' . $this->getVendor());
    }

    /**
     * Get the addon name string.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getNamespace('addon.name');
    }

    /**
     * Get the addon description string.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getNamespace('addon.description');
    }

    /**
     * Get a namespaced key string.
     *
     * @param  null $key
     * @return string
     */
    public function getNamespace($key = null, $namespace = null)
    {   
        $namespace = ($namespace)?$namespace : $this->getSlug();
        return "{$this->getVendor()}.{$this->getType()}.{$namespace}" . ($key ? '::' . $key : $key);
    }

    /**
     * Get the composer json contents.
     *
     * @return mixed|null
     */
    public function getComposerJson()
    {
        $json = $this->getPath('composer.json');

        if (!file_exists($json)) {
            return null;
        }

        return json_decode(file_get_contents($json));
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the path appended by the provided path.
     *
     * @return string
     */
    public function getPath($path = null)
    {
        return $this->path . ($path ? '/' . $path : $path);
    }

    /**
     * Set the addon slug.
     *
     * @param  $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the addon slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the addon type.
     *
     * @param  $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the addon type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the vendor.
     *
     * @param $vendor
     * @return $this
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get the vendor.
     *
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }
}
