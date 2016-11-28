<?php

namespace ProAI\SuperMigrations;

use ProAI\SuperMigrations\Builder;

abstract class Table
{
    /**
     * The table associated with the schema.
     *
     * @var string
     */
    private $table;

    /**
     * Direction of the migration (up or down).
     *
     * @var string
     */
    private $direction;

    /**
     * Pro migrations schema builder instance.
     *
     * @var \ProAI\SuperMigrations\Builder
     */
    private $schema;

    /**
     * Create a new table schema instance.
     *
     * @param  $direction  string
     * @return void
     */
    public function __construct($table, $direction)
    {
        $this->table = $table;
        $this->direction = $direction;

        $this->schema = new Builder($table);
    }

    /**
     * Execute a command if migration direction is up.
     *
     * @param  $command  \Closure
     * @return void
     */
    protected function up($command)
    {
        if ($this->direction === 'up') {
            $command($this->table);
        }
    }

    /**
     * Execute a command if migration direction is down.
     *
     * @param  $command  \Closure
     * @return void
     */
    protected function down($command)
    {
        if ($this->direction === 'down') {
            $command($this->table);
        }
    }

    /**
     * Get a schema builder instance if migration direction is up.
     *
     * @return \ProAI\SuperMigrations\Builder
     */
    protected function upSchema()
    {
        if ($this->direction === 'up') {
            $this->schema->unlock();
        } else {
            $this->schema->lock();
        }

        return $this->schema;
    }

    /**
     * Get a schema builder instance if migration direction is up.
     *
     * @return \ProAI\SuperMigrations\Builder
     */
    protected function downSchema()
    {
        if ($this->direction === 'down') {
            $this->schema->unlock();
        } else {
            $this->schema->lock();
        }

        return $this->schema;
    }
}
