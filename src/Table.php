<?php

namespace ProAI\ProMigrations;

use ProAI\ProMigrations\Builder;

abstract class Table
{
    /**
     * Direction of the migration (up or down).
     *
     * @var string
     */
    private $direction;

    /**
     * Pro migrations schema builder instance.
     *
     * @var \ProAI\ProMigrations\Builder
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
        $this->direction = $direction;

        $this->schema = new Builder($table);
    }

    /**
     * Run the migrations.
     *
     * @return \ProAI\ProMigrations\Builder
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
     * Reverse the migrations.
     *
     * @return void
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
