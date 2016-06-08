<?php namespace Anomaly\Streams\Platform\Http\Controller;

use Anomaly\Streams\Platform\Asset\Asset;
use Anomaly\Streams\Platform\Event\Response;
use Anomaly\Streams\Platform\Message\MessageBag;
use Anomaly\Streams\Platform\Ui\Breadcrumb\BreadcrumbCollection;
use Anomaly\Streams\Platform\View\ViewTemplate;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Route;

/**
 * Class BaseController
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Http\Controller
 */
class BaseController extends Controller
{

    use DispatchesJobs;

    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The view factory.
     *
     * @var Factory
     */
    protected $view;

    /**
     * The asset manager.
     *
     * @var Asset
     */
    protected $asset;

    /**
     * The route object.
     *
     * @var Route
     */
    protected $route;

    /**
     * The event dispatcher.
     *
     * @var Dispatcher
     */
    protected $events;

    /**
     * The request object.
     *
     * @var Request
     */
    protected $request;

    /**
     * The flash messages.
     *
     * @var MessageBag
     */
    protected $messages;

    /**
     * The redirect utility.
     *
     * @var Redirector
     */
    protected $redirect;

    /**
     * The view template.
     *
     * @var ViewTemplate
     */
    protected $template;

    /**
     * The response factory.
     *
     * @var ResponseFactory
     */
    protected $response;

    /**
     * The breadcrumb collection.
     *
     * @var BreadcrumbCollection
     */
    protected $breadcrumbs;

    /**
     * Create a new BaseController instance.
     */
    public function __construct()
    {
        $this->container   = app();
        $this->request     = app('Illuminate\Http\Request');
        $this->redirect    = app('Illuminate\Routing\Redirector');
        $this->view        = app('Illuminate\Contracts\View\Factory');
        $this->asset       = app('Anomaly\Streams\Platform\Asset\Asset');
        $this->events      = app('Illuminate\Contracts\Events\Dispatcher');
        $this->template    = app('Anomaly\Streams\Platform\View\ViewTemplate');
        $this->messages    = app('Anomaly\Streams\Platform\Message\MessageBag');
        $this->response    = app('Illuminate\Contracts\Routing\ResponseFactory');
        $this->breadcrumbs = app('Anomaly\Streams\Platform\Ui\Breadcrumb\BreadcrumbCollection');

        $this->route = $this->request->route();

        $this->events->fire(new Response($this));

        // Let addons manipulate middleware first.
        foreach (app('Anomaly\Streams\Platform\Http\Middleware\MiddlewareCollection') as $middleware) {
            $this->middleware($middleware);
        }

        // These may be manipulated by the middleware above.
        $this->middleware('Anomaly\Streams\Platform\Http\Middleware\VerifyCsrfToken');
        $this->middleware('Anomaly\Streams\Platform\Http\Middleware\ApplicationReady');
        $this->middleware('Anomaly\Streams\Platform\Http\Middleware\RedirectProtocol');
        $this->middleware('Anomaly\Streams\Platform\Http\Middleware\RedirectPrefix');
        $this->middleware('Anomaly\Streams\Platform\Http\Middleware\SetLocale');
        $this->middleware('Anomaly\Streams\Platform\Http\Middleware\PoweredBy');
        $this->middleware('Barryvdh\HttpCache\Middleware\CacheRequests');
    }
}
