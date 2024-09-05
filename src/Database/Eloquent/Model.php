<?php

namespace Emneslab\ORM\Database\Eloquent;

use Emneslab\ORM\Database\ConnectionResolver;
use Emneslab\ORM\Database\Query\Builder;
use Illuminate\Support\Str;

/**
 * Abstract Model Class
 *
 * This class serves as a base model for the Smerp ORM, extending Laravel's
 * Eloquent model to integrate seamlessly with WordPress's database structure.
 * It provides custom functionality such as handling table names with WordPress
 * table prefixes and utilizing a custom query builder.
 *
 * @package Emneslab\ORM\Database\Eloquent
 */
abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Constructor for the Model class.
     *
     * Initializes the connection resolver and sets up the model attributes.
     *
     * @param array $attributes Initial model attributes.
     */
    public function __construct(array $attributes = [])
    {
        // Set the static connection resolver to our custom resolver.
        static::$resolver = new ConnectionResolver();

        // Call the parent constructor to initialize Eloquent model.
        parent::__construct($attributes);
    }

    /**
     * Get the table associated with the model.
     *
     * If no table name is set, this method will append the WordPress table prefix
     * to the plural, snake-case version of the model's class name.
     *
     * @return string The name of the table associated with the model.
     */
    public function getTable()
    {
        // If a table name is not explicitly defined, derive it based on the model's class name.
        return $this->table ?? $this->getConnection()->getTablePrefix() . Str::snake(Str::pluralStudly(class_basename($this)));
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * This method overrides the base query builder with a custom builder
     * tailored for the Smerp ORM's needs.
     *
     * @return \Illuminate\Database\Query\Builder The new query builder instance.
     */
    protected function newBaseQueryBuilder()
    {
        // Get the database connection instance.
        $connection = $this->getConnection();

        // Return a new instance of the custom query builder.
        return new Builder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );
    }
}
