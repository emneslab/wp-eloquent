<?php

namespace Emneslab\ORM\Database;

use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\QueryException;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Grammars\MySqlGrammar;
use Illuminate\Support\Arr;

/**
 * Class Connection
 *
 * Custom database connection class tailored for the Smerp ORM.
 * Extends Laravel's Illuminate\Database\Connection to provide additional
 * functionality and integration with WordPress's wpdb class.
 *
 * @package Emneslab\ORM\Database
 */
class Connection extends \Illuminate\Database\Connection
{
    /**
     * WordPress database global instance.
     *
     * @var \wpdb
     */
    public $database;

    /**
     * Count of active transactions.
     *
     * @var int
     */
    public $transactionCount = 0;

    /**
     * The database connection configuration options.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Create a singleton instance of the Connection.
     *
     * Ensures a single instance is used throughout the application.
     *
     * @return \Emneslab\ORM\Database\Connection
     */
    public static function instance()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Constructor for the Connection class.
     *
     * Initializes the connection with WordPress's wpdb class and sets
     * up the necessary configurations.
     */
    public function __construct()
    {
        global $wpdb;

        $this->config = [
            'name' => 'eloquent-mysql',
        ];

        $this->database = $wpdb;
        $this->tablePrefix = $wpdb->prefix;
    }

    /**
     * Get the database connection name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getConfig('name');
    }

    /**
     * Begin a fluent query against a database table.
     *
     * @param  string $table The name of the table.
     * @param  string|null $as Optional alias for the table.
     * @return \Illuminate\Database\Query\Builder
     */
    public function table($table, $as = null)
    {
        $processor = $this->getPostProcessor();
        $table = $this->database->prefix . $table;
        $query = new Builder($this, $this->getQueryGrammar(), $processor);

        return $query->from($table);
    }

    /**
     * Get a new raw query expression.
     *
     * @param  mixed $value The raw value.
     * @return \Illuminate\Database\Query\Expression
     */
    public function raw($value)
    {
        return new Expression($value);
    }

    /**
     * Run a select statement and return a single result.
     *
     * @param  string $query The SQL query.
     * @param  array $bindings The bindings for the query.
     * @param  bool $useReadPdo Whether to use the read PDO connection.
     * @throws QueryException If there's an error in the query.
     * @return mixed The first result of the query.
     */
    public function selectOne($query, $bindings = [], $useReadPdo = true)
    {
        $query = $this->bind_params($query, $bindings);

        $result = $this->database->get_row($query);

        if ($result === false || $this->database->last_error) {
            throw new QueryException($this->getName(), $query, $bindings, new \Exception($this->database->last_error));
        }

        return $result;
    }

    /**
     * Run a select statement against the database.
     *
     * @param  string $query The SQL query.
     * @param  array $bindings The bindings for the query.
     * @param  bool $useReadPdo Whether to use the read PDO connection.
     * @throws QueryException If there's an error in the query.
     * @return array An array of results.
     */
    public function select($query, $bindings = [], $useReadPdo = true)
    {
        $query = $this->bind_params($query, $bindings);

        $result = $this->database->get_results($query);

        if ($result === false || $this->database->last_error) {
            throw new QueryException($this->getName(), $query, $bindings, new \Exception($this->database->last_error));
        }

        return $result;
    }

    /**
     * Run a select statement against the database and return a generator.
     * TODO: Implement cursor and all the related sub-methods.
     *
     * @param  string $query The SQL query.
     * @param  array $bindings The bindings for the query.
     * @param  bool $useReadPdo Whether to use the read PDO connection.
     * @return \Generator
     */
    public function cursor($query, $bindings = [], $useReadPdo = true)
    {
        // TODO: Implement cursor method to support streaming results.
    }

    /**
     * Emulate bind parameters into SQL query.
     *
     * @param string $query The SQL query.
     * @param array $bindings The bindings for the query.
     * @param bool $update Indicates if this is an update query.
     * @return string The processed query.
     */
    private function bind_params($query, $bindings, $update = false)
    {
        $query = str_replace('"', '`', $query);
        $bindings = $this->prepareBindings($bindings);

        if (!$bindings) {
            return $query;
        }

        $bindings = array_map(function ($replace) {
            if (is_string($replace)) {
                $replace = "'" . esc_sql($replace) . "'";
            } elseif ($replace === null) {
                $replace = "null";
            }

            return $replace;
        }, $bindings);

        $query = str_replace(array('%', '?'), array('%%', '%s'), $query);
        $query = vsprintf($query, $bindings);

        return $query;
    }

    /**
     * Bind and run the query.
     *
     * @param  string $query The SQL query.
     * @param  array $bindings The bindings for the query.
     * @throws QueryException If there's an error in the query.
     * @return array The result set as an array.
     */
    public function bind_and_run($query, $bindings = [])
    {
        $new_query = $this->bind_params($query, $bindings);

        $result = $this->database->query($new_query);

        if ($result === false || $this->database->last_error) {
            throw new QueryException($this->getName(), $new_query, $bindings, new \Exception($this->database->last_error));
        }

        return (array) $result;
    }

    /**
     * Execute an SQL statement and return the boolean result.
     *
     * @param  string $query The SQL query.
     * @param  array $bindings The bindings for the query.
     * @return bool True on success, false on failure.
     */
    public function statement($query, $bindings = [])
    {
        $new_query = $this->bind_params($query, $bindings, true);

        return $this->unprepared($new_query);
    }

    /**
     * Run an insert statement against the database.
     *
     * @param  string $query The SQL query.
     * @param  array $bindings The bindings for the query.
     * @return bool True on success, false on failure.
     */
    public function insert($query, $bindings = [])
    {
        return $this->statement($query, $bindings);
    }

    /**
     * Run an update statement against the database.
     *
     * @param  string $query The SQL query.
     * @param  array $bindings The bindings for the query.
     * @return int The number of affected rows.
     */
    public function update($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Run a delete statement against the database.
     *
     * @param  string $query The SQL query.
     * @param  array $bindings The bindings for the query.
     * @return int The number of affected rows.
     */
    public function delete($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Run an SQL statement and get the number of rows affected.
     *
     * @param  string $query The SQL query.
     * @param  array $bindings The bindings for the query.
     * @return int The number of affected rows.
     */
    public function affectingStatement($query, $bindings = [])
    {
        $new_query = $this->bind_params($query, $bindings, true);

        $result = $this->database->query($new_query);

        if ($result === false || $this->database->last_error) {
            throw new QueryException($this->getName(), $new_query, $bindings, new \Exception($this->database->last_error));
        }

        return intval($result);
    }

    /**
     * Run a raw, unprepared query against the PDO connection.
     *
     * @param  string $query The SQL query.
     * @return bool True on success, false on failure.
     */
    public function unprepared($query)
    {
        $result = $this->database->query($query);

        return ($result !== false && !$this->database->last_error);
    }

    /**
     * Prepare the query bindings for execution.
     *
     * @param  array $bindings The bindings for the query.
     * @return array The prepared bindings.
     */
    public function prepareBindings(array $bindings)
    {
        $grammar = $this->getQueryGrammar();

        foreach ($bindings as $key => $value) {
            if (is_bool($value)) {
                $bindings[$key] = intval($value);
            } elseif (is_scalar($value)) {
                continue;
            } elseif ($value instanceof \DateTime) {
                $bindings[$key] = $value->format($grammar->getDateFormat());
            }
        }

        return $bindings;
    }

    /**
     * Execute a Closure within a transaction.
     *
     * @param  \Closure $callback The callback function to execute.
     * @param  int $attempts The number of attempts.
     * @return mixed The result of the callback function.
     * @throws \Exception If an exception occurs during the transaction.
     */
    public function transaction(\Closure $callback, $attempts = 1)
    {
        $this->beginTransaction();
        try {
            $data = $callback();
            $this->commit();
            return $data;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Start a new database transaction.
     *
     * @return void
     */
    public function beginTransaction()
    {
        $transaction = $this->unprepared("START TRANSACTION;");
        if (false !== $transaction) {
            $this->transactionCount++;
        }
    }

    /**
     * Commit the active database transaction.
     *
     * @return void
     */
    public function commit()
    {
        if ($this->transactionCount < 1) {
            return;
        }
        $transaction = $this->unprepared("COMMIT;");
        if (false !== $transaction) {
            $this->transactionCount--;
        }
    }

    /**
     * Get the number of active transactions.
     *
     * @return int
     */
    public function transactionLevel()
    {
        return $this->transactionCount;
    }

    /**
     * Execute the given callback in "dry run" mode.
     * TODO: Implement pretend() method to support dry run queries.
     *
     * @param  \Closure $callback The callback function to execute.
     * @return array
     */
    public function pretend(\Closure $callback)
    {
        // TODO: Implement pretend() method to simulate query execution.
    }

    /**
     * Get the post processor instance.
     *
     * @return Processor
     */
    public function getPostProcessor()
    {
        return new Processor();
    }

    /**
     * Return self as PDO.
     *
     * @return \Emneslab\ORM\Database\Connection
     */
    public function getPdo()
    {
        return $this;
    }

    /**
     * Return the last insert id.
     *
     * @param  string|null $args The sequence name for the last insert ID.
     * @return int The ID of the last inserted row.
     */
    public function lastInsertId($args = null)
    {
        return $this->database->insert_id;
    }

    /**
     * Get the query grammar instance.
     *
     * @return Grammar
     */
    public function getQueryGrammar()
    {
        return new Grammar();
    }

    /**
     * Get the name of the connected database.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->database->dbname;
    }

    /**
     * Get the schema grammar used by the connection.
     *
     * @return \Illuminate\Database\Schema\Grammars\Grammar
     */
    public function getSchemaGrammar()
    {
        return new MySqlGrammar();
    }

    /**
     * Get an option from the configuration options.
     *
     * @param  string|null $option The option key to retrieve.
     * @return mixed The configuration value or null if not found.
     */
    public function getConfig($option = null)
    {
        return Arr::get($this->config, $option);
    }
}
