<?php

namespace Anomaly\Streams\Platform\Ui\ControlPanel\Component\Section\Command;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Anomaly\Streams\Platform\Ui\Breadcrumb\BreadcrumbCollection;
use Anomaly\Streams\Platform\Ui\ControlPanel\ControlPanelBuilder;
use Anomaly\Streams\Platform\Ui\ControlPanel\Component\Section\Contract\SectionInterface;
use Illuminate\Support\Facades\Gate;

/**
 * Class SetActiveSection
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class SetActiveSection
{

    /**
     * The control_panel builder.
     *
     * @var ControlPanelBuilder
     */
    protected $builder;

    /**
     * Create a new SetActiveSection instance.
     *
     * @param ControlPanelBuilder $builder
     */
    public function __construct(ControlPanelBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Handle the command.
     *
     * @param Request              $request
     * @param BreadcrumbCollection $breadcrumbs
     */
    public function handle(Request $request, BreadcrumbCollection $breadcrumbs)
    {
        $controlPanel = $this->builder->getControlPanel();
        $sections     = $controlPanel->getSections();

        /*
         * If we already have an active section
         * then we don't need to do this.
         */
        if ($active = $sections->active()) {
            return;
        }

        /* @var SectionInterface $section */
        foreach ($sections as $section) {

            if (($matcher = $section->matcher) && str_is($matcher, $request->path())) {
                $active = $section;
            }

            /*
             * Get the HREF for both the active
             * and loop iteration section.
             */
            $href       = Str::parse($section->permalink ?: array_get($section->htmlAttributes, 'href'));
            $activeHref = '';

            if ($active && $active instanceof SectionInterface) {
                $activeHref = $active->permalink ?: array_get($active->htmlAttributes, 'href');
            }

            /*
             * If the request URL does not even
             * contain the HREF then skip it.
             */
            if (!str_contains($request->url(), $href)) {
                continue;
            }

            /*
             * Compare the length of the active HREF
             * and loop iteration HREF. The longer the
             * HREF the more detailed and exact it is and
             * the more likely it is the active HREF and
             * therefore the active section.
             */
            $hrefLength       = strlen($href);
            $activeHrefLength = strlen($activeHref);

            if ($hrefLength > $activeHrefLength) {
                $active = $section;
            }
        }

        /**
         * If we have an active section determined
         * then mark it as such.
         *
         * @var SectionInterface $active
         * @var SectionInterface $section
         */
        if ($active) {
            if ($active->getParent()) {
                $active->setActive(true);

                $section = $sections->get($active->getParent(), $sections->first());

                $section->setHighlighted(true);

                $breadcrumbs->put($section->breadcrumb ?: $section->title, $section->href());
            } else {
                $active->active = true;
                $active->highlighted = true;
            }
        } elseif ($active = $sections->first()) {
            $active->active = true;
            $active->highlighted = true;
        }

        // No active section!
        if (!$active) {
            return;
        }

        // Authorize the active section.
        if ($active->policy && !Gate::any((array) $active->policy)) {
            abort(403);
        }

        // Add the bread crumb.
        if (($breadcrumb = $active->breadcrumb) !== false) {
            $breadcrumbs->put($breadcrumb ?: $active->title, $active->href());
        }
    }
}
