<?php namespace Anomaly\Streams\Platform;

use Illuminate\Foundation\Providers\ConsoleSupportServiceProvider;

class StreamsConsoleProvider extends ConsoleSupportServiceProvider
{

    /**
     * The provider class names.
     *
     * @var array
     * @todo: Broken as shit
     */
    protected $providers = [
        'Illuminate\Database\DatabaseServiceProvider',
        'Illuminate\Queue\QueueServiceProvider',
        //'Illuminate\Console\ScheduleServiceProvider',
        'Illuminate\Database\MigrationServiceProvider',
        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Foundation\Providers\ComposerServiceProvider',
        'Anomaly\Streams\Platform\Database\DatabaseServiceProvider',
    ];
}
