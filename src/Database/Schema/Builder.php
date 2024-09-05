<?php

namespace Emneslab\ORM\Database\Schema;

use Closure;

/**
 * Class Builder
 *
 * Extends the MySqlBuilder class to provide additional functionality 
 * for schema building specifically for the Smerp ORM. This class 
 * customizes the schema building process to automatically handle 
 * WordPress table prefixing and any additional ORM-specific requirements.
 *
 * @package Emneslab\ORM\Database\Schema
 */
class Builder extends \Illuminate\Database\Schema\MySqlBuilder
{
    /**
     * Create a new database table.
     *
     * This method extends the base table creation method by automatically
     * prefixing the table name with the database connection's table prefix.
     * It uses a Blueprint instance to define the table's columns, indexes,
     * and other schema-related operations.
     *
     * @param string $table The name of the table to create.
     * @param Closure $callback A closure that receives a Blueprint object,
     *                          which is used to define the table's columns, indexes, etc.
     * @return void
     */
    public function create($table, Closure $callback)
    {
        // Prepend the table prefix to the table name to ensure consistency with WordPress standards
        $table = $this->connection->getTablePrefix() . $table;

        // Create a new Blueprint instance for the table and use the callback to define its schema
        $this->build(tap($this->createBlueprint($table), function ($blueprint) use ($callback) {
            // Mark the Blueprint instance to indicate a 'create' operation
            $blueprint->create();

            // Execute the schema definition callback, which configures the table's columns and indexes
            $callback($blueprint);
        }));
    }
}
