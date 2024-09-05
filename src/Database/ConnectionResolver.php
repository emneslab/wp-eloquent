<?php

namespace Emneslab\ORM\Database;

use Illuminate\Database\ConnectionResolverInterface;
use Emneslab\ORM\Database\Connection;

/**
 * Class ConnectionResolver
 *
 * Implements the ConnectionResolverInterface to manage database connections.
 *
 * This class provides a way to resolve a database connection instance
 * and set the default connection name, adhering to Laravel's contract for
 * connection resolution.
 *
 * @package Emneslab\ORM\Database
 */
class ConnectionResolver implements ConnectionResolverInterface
{
    /**
     * @var string The name of the default database connection.
     */
    protected $defaultConnection;

    /**
     * Get a database connection instance.
     *
     * @param  string|null $name The name of the connection to retrieve.
     *                           If null, the default connection will be returned.
     * @return \Illuminate\Database\Connection
     */
    public function connection($name = null)
    {
        // In this implementation, we're always returning the singleton instance
        // of our custom Connection class. This could be expanded to support
        // multiple connections if needed.
        return Connection::instance();
    }

    /**
     * Get the default connection name.
     *
     * @return string The name of the default connection.
     */
    public function getDefaultConnection()
    {
        // Return the name of the default connection.
        // This would typically be set in configuration or dynamically.
        return $this->defaultConnection;
    }

    /**
     * Set the default connection name.
     *
     * @param  string $name The name of the connection to set as default.
     * @return void
     */
    public function setDefaultConnection($name)
    {
        // Set the default connection name.
        // This method would be called to change the active connection.
        $this->defaultConnection = $name;
    }
}
