<?php

namespace Anomaly\Streams\Platform\Stream;

use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

/**
 * Class StreamSchema.
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Stream
 */
class StreamSchema
{
    /**
     * The schema builder.
     *
     * @var Builder
     */
    protected $schema;

    /**
     * Create a new StreamSchema instance.
     */
    public function __construct()
    {
        $this->schema = app('db')->connection()->getSchemaBuilder();
    }

    /**
     * Create a table.
     *
     * @param StreamInterface
     */
    public function createTable(StreamInterface $stream)
    {
        $table = $stream->getEntryTableName();

        $this->schema->dropIfExists($table);

        $this->schema->create(
            $table,
            function (Blueprint $table) use ($stream) {

                $table->increments('id');
                $table->integer('sort_order')->nullable();
                $table->datetime('created_at');
                $table->integer('created_by')->nullable();
                $table->datetime('updated_at')->nullable();
                $table->integer('updated_by')->nullable();

                if ($stream->isTrashable()) {
                    $table->datetime('deleted_at')->nullable();
                }
            }
        );
    }

    /**
     * Create translations table.
     *
     * @param $table
     * @param $foreignKey
     */
    public function createTranslationsTable($table)
    {
        $this->schema->dropIfExists($table);

        $this->schema->create(
            $table,
            function (Blueprint $table) {

                $table->increments('id');
                $table->integer('entry_id');
                $table->datetime('created_at');
                $table->integer('created_by')->nullable();
                $table->datetime('updated_at')->nullable();
                $table->integer('updated_by')->nullable();
                $table->string('locale')->index();
            }
        );
    }

    /**
     * Drop a table.
     *
     * @param $table
     */
    public function dropTable($table)
    {
        $this->schema->dropIfExists($table);
    }
}
