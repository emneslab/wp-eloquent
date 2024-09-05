<?php

namespace Emneslab\ORM\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Emneslab\ORM\Database\Connection;

/**
 * Class DB
 *
 * A custom facade for database operations in the Smerp ORM, extending Laravel's Facade functionality.
 * This class provides a static interface to the ORM's Connection class, allowing easy access to
 * database methods without instantiating the Connection class directly.
 *
 * @package Emneslab\ORM\Support\Facades
 *
 * @see \Illuminate\Database\DatabaseManager
 * @see \Illuminate\Database\Connection
 */
class DB extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * This method is called by the base Facade class to retrieve the service container binding.
     * It is used to resolve the appropriate service instance from the container.
     *
     * @return string The name of the binding in the service container.
     * @throws \RuntimeException If the facade accessor is not defined.
     */
    protected static function getFacadeAccessor()
    {
        // Return the service container binding name for the database connection.
        return 'db';
    }

    /**
     * Resolve the facade root instance from the container.
     *
     * Overrides the default method to handle the creation and initialization
     * of the ORM's Connection instance if it hasn't been resolved already.
     *
     * @param  string  $name The name of the facade instance.
     * @return mixed The resolved facade instance.
     */
    protected static function resolveFacadeInstance($name)
    {
        // Check if the instance has not been resolved and the application container is not set or does not contain the instance.
        if (!isset(static::$resolvedInstance[$name]) && (!isset(static::$app) || !isset(static::$app[$name]))) {
            // Create a singleton instance of the ORM Connection class
            $class = Connection::instance();

            // Swap the newly created instance into the facade's resolved instance
            static::swap($class);
        }

        // Call the parent method to return the resolved facade instance
        return parent::resolveFacadeInstance($name);
    }
}
