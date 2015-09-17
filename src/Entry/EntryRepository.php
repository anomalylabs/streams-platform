<?php

namespace Anomaly\Streams\Platform\Entry;

use Anomaly\Streams\Platform\Entry\Contract\EntryRepositoryInterface;
use Anomaly\Streams\Platform\Model\EloquentRepository;

/**
 * Class EntryRepository.
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Entry
 */
class EntryRepository extends EloquentRepository implements EntryRepositoryInterface
{
}
