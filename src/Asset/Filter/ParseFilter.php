<?php

namespace Anomaly\Streams\Platform\Asset\Filter;

use Anomaly\Streams\Platform\Asset\AssetParser;
use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

/**
 * Class ParseFilter.
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Asset\Filter
 */
class ParseFilter implements FilterInterface
{
    /**
     * The asset parser utility.
     *
     * @var AssetParser
     */
    protected $parser;

    /**
     * Create a new ParseFilter instance.
     *
     * @param AssetParser $parser
     */
    public function __construct(AssetParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Filters an asset after it has been loaded.
     *
     * @param AssetInterface $asset
     */
    public function filterLoad(AssetInterface $asset)
    {
        //
    }

    /**
     * Filters an asset just before it's dumped.
     *
     * @param AssetInterface $asset
     */
    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent($this->parser->parse($asset->getContent()));
    }
}
