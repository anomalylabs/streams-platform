<?php

namespace Anomaly\Streams\Platform\Ui\Tree\Component\Item;

use Illuminate\View\View;
use StringTemplate\Engine;
use Illuminate\Contracts\Support\Arrayable;
use Anomaly\Streams\Platform\Support\Facades\Decorator;
use Anomaly\Streams\Platform\Support\Facades\Evaluator;
use Anomaly\Streams\Platform\Ui\Tree\TreeBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;

/**
 * Class ItemValue
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class ItemValue
{

    /**
     * The string parser.
     *
     * @var Engine
     */
    protected $parser;

    /**
     * Create a new ItemValue instance.
     *
     * @param Engine    $parser
     * @param Evaluator $evaluator
     * @param Decorator $decorator
     */
    public function __construct(Engine $parser)
    {
        $this->parser    = $parser;
    }

    /**
     * Return the item value.
     *
     * @param  TreeBuilder     $builder
     * @param                  $entry
     * @return View|mixed|null
     */
    public function make(TreeBuilder $builder, $entry)
    {
        $value = $builder->getTreeOption('item_value', 'entry.title');

        /*
         * If the entry is an instance of EntryInterface
         * then try getting the field value from the entry.
         */
        if ($entry instanceof EntryInterface && $entry->getField($value)) {
            if ($entry->assignmentIsRelationship($value)) {
                $value = $entry->{camel_case($value)}->getTitle();
            } else {
                $value = $entry->getFieldValue($value);
            }
        }

        /*
         * If the value matches a field with a relation
         * then parse the string using the eager loaded entry.
         */
        if (preg_match("/^entry.([a-zA-Z\\_]+)/", $value, $match)) {
            $fieldSlug = camel_case($match[1]);

            if (method_exists($entry, $fieldSlug) && $entry->{$fieldSlug}() instanceof Relation) {
                $entry = Decorator::decorate($entry);

                $value = data_get(
                    compact('entry'),
                    str_replace("entry.{$match[1]}.", 'entry.' . camel_case($match[1]) . '.', $value)
                );
            }
        }

        /*
         * Decorate the entry object before
         * sending to decorate so that data_get()
         * can get into the presenter methods.
         */
        $entry = Decorator::decorate($entry);

        /*
         * If the value matches a method in the presenter.
         */
        if (preg_match("/^entry.([a-zA-Z\\_]+)/", $value, $match)) {
            if (method_exists($entry, camel_case($match[1]))) {
                $value = $entry->{camel_case($match[1])}();
            }
        }

        /*
         * By default we can just pass the value through
         * the evaluator utility and be done with it.
         */
        $value = $this->evaluator->evaluate($value, compact('builder', 'entry'));

        /*
         * Lastly, prepare the entry to be
         * parsed into the string.
         */
        if ($entry instanceof Arrayable) {
            $entry = $entry->toArray();
        } else {
            $entry = null;
        }

        /*
         * Parse the value with the entry.
         */
        $value = $this->parser->render($builder->getTreeOption('item_wrapper', '{value}'), compact('value', 'entry'));

        /*
         * If the value looks like a language
         * key then try translating it.
         */
        if (str_is('*.*.*::*', $value)) {
            $value = trans($value);
        }

        return $value;
    }
}
