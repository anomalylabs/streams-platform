<?php

namespace Anomaly\Streams\Platform\Addon\Extension\Command\Handler;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

/**
 * Class InstallExtensionsTableHandler.
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Addon\Extension\Command
 */
class InstallExtensionsTableHandler
{
    /**
     * The schema builder object.
     *
     * @var Builder
     */
    protected $schema;

    /**
     * Create a new InstallExtensionsTableHandler instance.
     */
    public function __construct()
    {
        $this->schema = app('db')->connection()->getSchemaBuilder();
    }

    /**
     * Install the extensions table.
     */
    public function handle()
    {
        $this->schema->dropIfExists('addons_extensions');

        $this->schema->create(
            'addons_extensions',
            function (Blueprint $table) {

                $table->increments('id');
                $table->string('slug');
                $table->boolean('installed')->default(0);
                $table->boolean('enabled')->default(0);
            }
        );
    }
}
