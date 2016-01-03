<?php namespace Anomaly\Streams\Platform\Image;

use Anomaly\FilesModule\File\Contract\FileInterface;
use Anomaly\FilesModule\File\FilePresenter;
use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Application\Application;
use Collective\Html\HtmlBuilder;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\ImageManager;
use League\Flysystem\File;
use Mobile_Detect;
use Robbo\Presenter\Presenter;

/**
 * Class Image
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Asset
 */
class Image
{

    /**
     * The publish flag.
     *
     * @var bool
     */
    protected $publish = false;

    /**
     * The publishable base directory.
     *
     * @var null
     */
    protected $directory = null;

    /**
     * The image object.
     *
     * @var null|string
     */
    protected $image = null;

    /**
     * The file extension.
     *
     * @var null|string
     */
    protected $extension = null;

    /**
     * The default output method.
     *
     * @var string
     */
    protected $output = 'url';

    /**
     * The image attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Applied alterations.
     *
     * @var array
     */
    protected $alterations = [];

    /**
     * Image srcsets.
     *
     * @var array
     */
    protected $srcsets = [];

    /**
     * Image sources.
     *
     * @var array
     */
    protected $sources = [];

    /**
     * Allowed methods.
     *
     * @var array
     */
    protected $allowedMethods = [
        'blur',
        'brightness',
        'colorize',
        'contrast',
        'crop',
        'encode',
        'fit',
        'flip',
        'gamma',
        'greyscale',
        'heighten',
        'invert',
        'limitColors',
        'pixelate',
        'opacity',
        'resize',
        'rotate',
        'amount',
        'widen'
    ];

    /**
     * The quality of the output.
     *
     * @var int
     */
    protected $quality = 100;

    /**
     * The HTML builder.
     *
     * @var HtmlBuilder
     */
    protected $html;

    /**
     * Image path hints by namespace.
     *
     * @var ImagePaths
     */
    protected $paths;

    /**
     * The image macros.
     *
     * @var ImageMacros
     */
    protected $macros;

    /**
     * The file system.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The user agent utility.
     *
     * @var Mobile_Detect
     */
    protected $agent;

    /**
     * The image manager.
     *
     * @var ImageManager
     */
    protected $manager;

    /**
     * The stream application.
     *
     * @var Application
     */
    protected $application;

    /**
     * Create a new Image instance.
     *
     * @param HtmlBuilder  $html
     * @param Filesystem   $files
     * @param ImageManager $manager
     * @param Application  $application
     * @param ImagePaths   $paths
     * @param ImageMacros  $macros
     */
    public function __construct(
        HtmlBuilder $html,
        Filesystem $files,
        Mobile_Detect $agent,
        ImageManager $manager,
        Application $application,
        ImagePaths $paths,
        ImageMacros $macros
    ) {
        $this->html        = $html;
        $this->files       = $files;
        $this->agent       = $agent;
        $this->paths       = $paths;
        $this->macros      = $macros;
        $this->manager     = $manager;
        $this->application = $application;
    }

    /**
     * Make a new image instance.
     *
     * @param mixed $image
     * @return $this
     */
    public function make($image)
    {
        if ($image instanceof Image) {
            return $image;
        }

        $clone = clone($this);

        $clone->setAlterations([]);
        $clone->setSources([]);
        $clone->setSrcsets([]);
        $clone->setImage(null);

        try {
            return $clone->setImage($image);
        } catch (\Exception $e) {
            return $this;
        }
    }

    /**
     * Return the path to an image.
     *
     * @return string
     */
    public function path()
    {
        $path = $this->getCachePath();

        return $path;
    }

    /**
     * Run a macro on the image.
     *
     * @param $macro
     * @return Image
     * @throws \Exception
     */
    public function macro($macro)
    {
        return $this->macros->run($macro, $this);
    }

    /**
     * Return the URL to an image.
     *
     * @param array $parameters
     * @param null  $secure
     * @return string
     */
    public function url(array $parameters = [], $secure = null)
    {
        return url($this->path(), $parameters, $secure);
    }

    /**
     * Return the image tag to an image.
     *
     * @param null  $alt
     * @param array $attributes
     * @return string
     */
    public function image($alt = null, array $attributes = [])
    {
        if (!$alt) {
            $alt = array_get($this->getAttributes(), 'alt');
        }

        $attributes = array_merge($this->getAttributes(), $attributes);

        if ($srcset = $this->srcset()) {
            $attributes['srcset'] = $srcset;
        }

        return $this->html->image($this->path(), $alt, $attributes);
    }

    /**
     * Return the image tag to an image.
     *
     * @param null  $alt
     * @param array $attributes
     * @return string
     */
    public function img($alt = null, array $attributes = [])
    {
        return $this->image($alt, $attributes);
    }

    /**
     * Return a picture tag.
     *
     * @return string
     */
    public function picture(array $attributes = [])
    {
        $sources = [];

        $attributes = array_merge($this->getAttributes(), $attributes);

        /* @var Image $image */
        foreach ($this->getSources() as $media => $image) {
            if ($media != 'fallback') {
                $sources[] = $image->source();
            } else {
                $sources[] = $image->image();
            }
        }

        $sources = implode("\n", $sources);

        $attributes = $this->html->attributes($attributes);

        return "<picture {$attributes}>\n{$sources}\n</picture>";
    }

    /**
     * Return a source tag.
     *
     * @return string
     */
    public function source()
    {
        $this->addAttribute('srcset', $this->srcset() ?: $this->url() . ' 2x, ' . $this->url() . ' 1x');

        $attributes = $this->html->attributes($this->getAttributes());

        if ($srcset = $this->srcset()) {
            $attributes['srcset'] = $srcset;
        }

        return "<source {$attributes}>";
    }

    /**
     * Return the image response.
     *
     * @param null $format
     * @param int  $quality
     * @return String
     */
    public function encode($format = null, $quality = 100)
    {
        return $this->manager->make($this->getCachePath())->encode($format, $quality);
    }

    /**
     * Return the output.
     *
     * @return string
     */
    public function output()
    {
        return $this->{$this->output}();
    }

    /**
     * Set the quality.
     *
     * @param $quality
     * @return $this
     */
    public function quality($quality)
    {
        return $this->setQuality($quality);
    }

    /**
     * Set the quality.
     *
     * @param $quality
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->quality = (int)$quality;

        return $this;
    }

    /**
     * Get the cache path of the image.
     *
     * @return string
     */
    protected function getCachePath()
    {
        if (starts_with($this->getImage(), ['//', 'http'])) {
            return $this->getImage();
        }

        $filename = md5(
                var_export([md5($this->getImage()), $this->getAlterations()], true) . $this->getQuality()
            ) . '.' . $this->getExtension();

        $path = 'assets/' . $this->application->getReference() . '/cache/' . $filename;

        if ($this->shouldPublish($path)) {
            $this->publish($path);
        }

        return $path;
    }

    /**
     * Determine if the image needs to be published
     *
     * @param $path
     * @return bool
     */
    private function shouldPublish($path)
    {
        if (!$this->files->exists($path)) {
            return true;
        }

        if (is_string($this->image) && !str_is('*://*', $this->image) && filemtime($path) < filemtime($this->image)) {
            return true;
        }

        if (is_string($this->image) && str_is('*://*', $this->image) && filemtime($path) < app(
                'League\Flysystem\MountManager'
            )->getTimestamp($this->image)
        ) {
            return true;
        }

        if ($this->image instanceof File && filemtime($path) < $this->image->getTimestamp()) {
            return true;
        }

        if ($this->image instanceof FileInterface && filemtime($path) < $this->image->lastModified()->format('U')) {
            return true;
        }

        return false;
    }

    /**
     * Publish an image to the publish directory.
     *
     * @param $path
     */
    protected function publish($path)
    {
        $image = $this->makeImage();

        if (!$image) {
            return;
        }

        foreach ($this->getAlterations() as $method => $arguments) {
            if (in_array($method, $this->getAllowedMethods())) {
                if (is_array($arguments)) {
                    call_user_func_array([$image, $method], $arguments);
                } else {
                    call_user_func([$image, $method], $arguments);
                }
            }
        }

        $this->files->makeDirectory((new \SplFileInfo($path))->getPath(), 0777, true, true);

        $image->save($this->directory . $path, $this->getQuality());
    }

    /**
     * Set an attribute value.
     *
     * @param $attribute
     * @param $value
     * @return $this
     */
    public function attr($attribute, $value)
    {
        array_set($this->attributes, $attribute, $value);

        return $this;
    }

    /**
     * Return the image srcsets by set.
     *
     * @return array
     */
    public function srcset()
    {
        $sources = [];

        /* @var Image $image */
        foreach ($this->getSrcsets() as $descriptor => $image) {
            $sources[] = $image->url() . ' ' . $descriptor;
        }

        return implode(', ', $sources);
    }

    /**
     * Set the srcsets/alterations.
     *
     * @param array $srcsets
     */
    public function srcsets(array $srcsets)
    {
        foreach ($srcsets as $descriptor => &$alterations) {

            $image = $this->make(array_pull($alterations, 'image', $this->getImage()))->setOutput('url');

            foreach ($alterations as $method => $arguments) {
                if (is_array($arguments)) {
                    call_user_func_array([$image, $method], $arguments);
                } else {
                    call_user_func([$image, $method], $arguments);
                }
            }

            $alterations = $image;
        }

        $this->setSrcsets($srcsets);

        return $this;
    }

    /**
     * Set the sources/alterations.
     *
     * @param array $sources
     * @param bool  $merge
     * @return $this
     */
    public function sources(array $sources, $merge = true)
    {
        foreach ($sources as $media => &$alterations) {

            if ($merge) {
                $alterations = array_merge($this->getAlterations(), $alterations);
            }

            $image = $this->make(array_pull($alterations, 'image', $this->getImage()))->setOutput('source');

            if ($media != 'fallback') {
                call_user_func([$image, 'media'], $media);
            }

            foreach ($alterations as $method => $arguments) {
                if (is_array($arguments)) {
                    call_user_func_array([$image, $method], $arguments);
                } else {
                    call_user_func([$image, $method], $arguments);
                }
            }

            $alterations = $image;
        }

        $this->setSources($sources);

        return $this;
    }

    /**
     * Alter the image based on the user agents.
     *
     * @param array $agents
     * @param bool  $exit
     * @return $this
     */
    public function agents(array $agents, $exit = false)
    {
        foreach ($agents as $agent => $alterations) {
            if (
                $this->agent->is($agent)
                || ($agent == 'mobile' && $this->agent->isMobile())
                || ($agent == 'tablet' && $this->agent->isTablet())
            ) {
                foreach ($alterations as $method => $arguments) {
                    if (is_array($arguments)) {
                        call_user_func_array([$this, $method], $arguments);
                    } else {
                        call_user_func([$this, $method], $arguments);
                    }
                }

                if ($exit) {
                    return $this;
                }
            }
        }

        return $this;
    }

    /**
     * Set the image.
     *
     * @param  $image
     * @return $this
     */
    public function setImage($image)
    {
        if ($image instanceof Presenter) {
            $image = $image->getObject();
        }

        if ($image instanceof FieldType) {
            $image = $image->getValue();
        }

        // Replace path prefixes.
        if (is_string($image) && str_contains($image, '::')) {

            $image = $this->paths->realPath($image);

            $this->setExtension(pathinfo($image, PATHINFO_EXTENSION));
        }

        if (is_string($image) && str_is('*://*', $image) && !starts_with($image, ['http', 'https'])) {

            $this->image = app('League\Flysystem\MountManager')->get($image);

            $this->setExtension(pathinfo($image, PATHINFO_EXTENSION));
        }

        if ($image instanceof FileInterface) {
            $this->setExtension($image->getExtension());
        }

        if ($image instanceof FilePresenter) {

            $image = $image->getObject();

            $this->setExtension($image->getExtension());
        }

        $this->image = $image;

        return $this;
    }

    /**
     * Make an image instance.
     *
     * @return \Intervention\Image\Image
     */
    protected function makeImage()
    {
        if ($this->image instanceof FileInterface) {
            return $this->manager
                ->make(app('League\Flysystem\MountManager')->read($this->image->location()))
                ->encode($this->getExtension());
        }

        if ($this->image instanceof File) {
            return $this->manager
                ->make($this->image->read())
                ->encode($this->getExtension());
        }

        if (is_string($this->image) && str_is('*://*', $this->image)) {
            return $this->manager
                ->make(app('League\Flysystem\MountManager')->read($this->image))
                ->encode($this->getExtension());
        }

        if (is_string($this->image) && file_exists($this->image)) {
            return $this->manager->make($this->image);
        }

        if ($this->image instanceof Image) {
            return $this->image;
        }
    }

    /**
     * Get the image instance.
     *
     * @return \Intervention\Image\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get the alterations.
     *
     * @return array
     */
    public function getAlterations()
    {
        return $this->alterations;
    }

    /**
     * Set the alterations.
     *
     * @param array $alterations
     * @return $this
     */
    public function setAlterations(array $alterations)
    {
        $this->alterations = $alterations;

        return $this;
    }

    /**
     * Add an alteration.
     *
     * @param  $method
     * @param  $arguments
     * @return $this
     */
    public function addAlteration($method, $arguments)
    {
        $this->alterations[$method] = $arguments;

        return $this;
    }

    /**
     * Get the attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the attributes.
     *
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Add an attribute.
     *
     * @param  $attribute
     * @param  $value
     * @return $this
     */
    protected function addAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;

        return $this;
    }

    /**
     * Get the srcsets.
     *
     * @return array
     */
    public function getSrcsets()
    {
        return $this->srcsets;
    }

    /**
     * Set the srcsets.
     *
     * @param array $srcsets
     * @return $this
     */
    public function setSrcsets(array $srcsets)
    {
        $this->srcsets = $srcsets;

        return $this;
    }

    /**
     * Get the sources.
     *
     * @return array
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Set the sources.
     *
     * @param array $sources
     * @return $this
     */
    public function setSources(array $sources)
    {
        $this->sources = $sources;

        return $this;
    }

    /**
     * Get the quality.
     *
     * @return int
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Set the output mode.
     *
     * @param $output
     * @return $this
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Get the extension.
     *
     * @return null|string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set the extension.
     *
     * @param $extension
     * @return $this
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get the allowed methods.
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return $this->allowedMethods;
    }

    /**
     * Add a path by it's namespace hint.
     *
     * @param $namespace
     * @param $path
     * @return $this
     */
    public function addPath($namespace, $path)
    {
        $this->paths->addPath($namespace, $path);

        return $this;
    }

    /**
     * Return the output.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->output();
    }

    /**
     * If the method does not exist then
     * add an attribute and return.
     *
     * @param $name
     * @param $arguments
     * @return $this|mixed
     */
    function __call($name, $arguments)
    {
        if (in_array($name, $this->getAllowedMethods())) {
            return $this->addAlteration($name, $arguments);
        }

        if (!method_exists($this, $name)) {

            array_set($this->attributes, $name, array_shift($arguments));

            return $this;
        }

        return call_user_func_array([$this, $name], $arguments);
    }
}
