<?php

namespace Emneslab\ORM\Support\Facades;

use Emneslab\ORM\Database\Schema\Builder;
use Emneslab\ORM\Database\Connection;
use Illuminate\Support\Facades\Facade;

/**
 * Class Schema
 *
 * A custom facade for accessing the schema builder instance in the Smerp ORM.
 * This facade provides a static interface to the schema builder for managing
 * database schemas, such as creating, updating, or deleting tables.
 *
 * @package Emneslab\ORM\Support\Facades
 */
class Schema extends Facade
{
    /**
     * Get a schema builder instance for a connection.
     *
     * Provides a convenient way to access the schema builder for a specific database connection.
     * This method allows users to specify the connection they want to use, and it returns
     * the corresponding schema builder instance.
     *
     * @param  string|null  $name The name of the database connection.
     * @return \Illuminate\Database\Schema\Builder The schema builder instance.
     */
    public static function connection($name = null)
    {
        // Use the application container to get the database connection by name
        // and return its schema builder instance.
        return static::$app['db']->connection($name)->getSchemaBuilder();
    }

    /**
     * Get the registered name of the component.
     *
     * This method is used by the base Facade class to get the service container binding.
     * It is essential for resolving the correct instance from the service container.
     *
     * @return string The name of the binding in the service container.
     * @throws \RuntimeException If the facade accessor is not defined.
     */
    protected static function getFacadeAccessor()
    {
        // Return the service container binding name for the schema builder.
        return 'db.schema';
    }

    /**
     * Resolve the facade root instance from the container.
     *
     * Overrides the default method to handle the creation and initialization
     * of the ORM's Builder instance if it hasn't been resolved already.
     * This ensures that the schema builder is correctly set up for use.
     *
     * @param  string  $name The name of the facade instance.
     * @return mixed The resolved facade instance.
     */
    protected static function resolveFacadeInstance($name)
    {
        // Check if the instance has not been resolved and the application container is not set or does not contain the instance.
        if (!isset(static::$resolvedInstance[$name]) && (!isset(static::$app) || !isset(static::$app[$name]))) {
            // Initialize a new instance of the custom Builder class with a test connection.
            $class = Builder::class;
            static::swap(new $class(Connection::instance()));
        }

        // Call the parent method to return the resolved facade instance.
        return parent::resolveFacadeInstance($name);
    }
}
