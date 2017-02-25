<?php

namespace ProAI\SuperMigrations;

use BadMethodCallException;

class Builder
{
    /**
     * The table associated with the schema.
     *
     * @var string
     */
    protected $table;

    /**
     * Schema builder instance.
     *
     * @var \Illuminate\Database\Schema\Builder
     */
    protected $schema;

    /**
     * Schema builder lock.
     *
     * @var bool
     */
    protected $lock = true;

    /**
     * The methods that should be returned from schema builder.
     *
     * @var array
     */
    protected $passthru = [
        'hasTable', 'hasColumn', 'hasColumns', 'getColumnType', 'getColumnListing',
        'table', 'create', 'drop', 'dropIfExists', 'rename'
    ];

    /**
     * Create a new builder instance.
     *
     * @param  $direction  string
     * @return void
     */
    public function __construct($table)
    {
        if (!$this->table) {
            $this->table = $table;
        }

        $this->schema = app('db')->connection()->getSchemaBuilder();
    }

    /**
     * Lock schema builder.
     *
     * @return void
     */
    public function lock()
    {
        $this->lock = true;
    }

    /**
     * Unlock schema builder.
     *
     * @return void
     */
    public function unlock()
    {
        $this->lock = false;
    }

    /**
     * Dynamically handle calls into the schema instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (! in_array($method, $this->passthru)) {
            throw new BadMethodCallException("Method [$method] does not exist.");
        }

        if (! $this->lock) {
            array_unshift($parameters , $this->table);

            return call_user_func_array([$this->schema, $method], $parameters);
        }
    }
}
