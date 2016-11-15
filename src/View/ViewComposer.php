<?php namespace Anomaly\Streams\Platform\View;

use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\Module\Module;
use Anomaly\Streams\Platform\Addon\Theme\Theme;
use Anomaly\Streams\Platform\Application\Application;
use Anomaly\Streams\Platform\View\Event\ViewComposed;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mobile_Detect;

/**
 * Class ViewComposer
 *
 * @link    http://pyrocms.com/
 * @author  PyroCMS, Inc. <support@pyrocms.com>
 * @author  Ryan Thompson <ryan@pyrocms.com>
 */
class ViewComposer
{

    /**
     * The view factory.
     *
     * @var Factory
     */
    protected $view;

    /**
     * The agent utility.
     *
     * @var Mobile_Detect
     */
    protected $agent;

    /**
     * The event dispatcher.
     *
     * @var Dispatcher
     */
    protected $events;

    /**
     * The current theme.
     *
     * @var Theme|null
     */
    protected $theme;

    /**
     * The active module.
     *
     * @var Module|null
     */
    protected $module;

    /**
     * The addon collection.
     *
     * @var AddonCollection
     */
    protected $addons;

    /**
     * The request object.
     *
     * @var Request
     */
    protected $request;

    /**
     * The view overrides collection.
     *
     * @var ViewOverrides
     */
    protected $overrides;

    /**
     * The application instance.
     *
     * @var Application
     */
    protected $application;

    /**
     * The view mobile overrides.
     *
     * @var ViewMobileOverrides
     */
    protected $mobiles;

    /**
     * Create a new ViewComposer instance.
     *
     * @param Factory             $view
     * @param Mobile_Detect       $agent
     * @param Dispatcher          $events
     * @param AddonCollection     $addons
     * @param ViewOverrides       $overrides
     * @param Request             $request
     * @param ViewMobileOverrides $mobiles
     * @param Application         $application
     */
    public function __construct(
        Factory $view,
        Mobile_Detect $agent,
        Dispatcher $events,
        AddonCollection $addons,
        ViewOverrides $overrides,
        Request $request,
        ViewMobileOverrides $mobiles,
        Application $application
    ) {
        $this->view        = $view;
        $this->agent       = $agent;
        $this->events      = $events;
        $this->addons      = $addons;
        $this->mobiles     = $mobiles;
        $this->request     = $request;
        $this->overrides   = $overrides;
        $this->application = $application;

        $area = $request->segment(1) == 'admin' ? 'admin' : 'standard';

        $this->theme  = $this->addons->themes->active($area);
        $this->module = $this->addons->modules->active();

        $this->mobile = $this->agent->isMobile();
    }

    /**
     * Compose the view before rendering.
     *
     * @param  View $view
     * @return View
     */
    public function compose(View $view)
    {

        if (!$this->theme || !env('INSTALLED')) {

            $this->events->fire(new ViewComposed($view));

            return $view;
        }

        $mobile    = $this->mobiles->get($this->theme->getNamespace(), []);
        $overrides = $this->overrides->get($this->theme->getNamespace(), []);

        if ($this->mobile && $path = array_get($mobile, $view->getName(), null)) {
            $view->setPath($path);
        } elseif ($path = array_get($overrides, $view->getName(), null)) {
            $view->setPath($path);
        }

        if ($this->module) {

            $mobile    = $this->mobiles->get($this->module->getNamespace(), []);
            $overrides = $this->overrides->get($this->module->getNamespace(), []);

            if ($this->mobile && $path = array_get($mobile, $view->getName(), null)) {
                $view->setPath($path);
            } elseif ($path = array_get($overrides, $view->getName(), null)) {
                $view->setPath($path);
            } elseif ($path = array_get(config('streams.overrides'), $view->getName(), null)) {
                $view->setPath($path);
            }
        }

        if ($overload = $this->getOverloadPath($view)) {
            $view->setPath($overload);
        }

        $this->events->fire(new ViewComposed($view));

        return $view;
    }

    /**
     * Get the override view path.
     *
     * @param  $view
     * @return null|string
     */
    public function getOverloadPath(View $view)
    {

        /*
         * We can only overload namespaced
         * views right now.
         */
        if (!str_contains($view->getName(), '::')) {
            return null;
        }

        /*
         * Split the view into it's
         * namespace and path.
         */
        list($namespace, $path) = explode('::', $view->getName());

        $override = null;

        $path = str_replace('.', '/', $path);

        /*
         * If the namespace is shorthand
         * then check to see if we have
         * an active addon to use for it.
         */
        if ($namespace === 'module' && $this->module) {
            $namespace = $this->module->getNamespace();
        }

        if ($namespace === 'theme' && $this->theme) {
            $namespace = $this->theme->getNamespace();
        }

        /*
         * If the view is a streams view then
         * it's real easy to guess what the
         * override path should be.
         */
        if ($namespace == 'streams') {
            $path = $this->theme->getNamespace('streams/' . $path);
        }

        /*
         * If the view uses a dot syntax namespace then
         * transform it all into the override view path.
         */
        if ($addon = $this->addons->get($namespace)) {
            $override = $this->theme->getNamespace(
                "addons/{$addon->getVendor()}/{$addon->getSlug()}-{$addon->getType()}/" . $path
            );
        }

        if ($this->view->exists($override)) {
            return $override;
        }

        /**
         * Check if a published override exists.
         */
        if ($addon) {
            $override = "app::addons/{$addon->getVendor()}/{$addon->getSlug()}-{$addon->getType()}/views/" . $path;
        }

        if ($this->view->exists($override)) {
            return $override;
        }

        /*
         * If the view uses a dot syntax namespace then
         * transform it all into the override view path.
         *
         * @deprecated since v3.0.0
         */
        if ($addon) {
            $override = $this->theme->getNamespace(
                "addon/{$addon->getVendor()}/{$addon->getSlug()}-{$addon->getType()}/" . $path
            );
        }

        if ($this->view->exists($override)) {
            return $override;
        }

        return null;
    }
}
