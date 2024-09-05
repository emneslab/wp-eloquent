<?php

namespace Emneslab\ORM\Database\Query;


/**
 * Class Builder
 *
 * Extends the base Illuminate Query Builder to provide custom query-building capabilities
 * tailored for the Smerp ORM. This class can be further extended to add more specific
 * query methods or override existing methods from the base query builder.
 *
 * @package Emneslab\ORM\Database\Query
 */
class Builder extends \Illuminate\Database\Query\Builder
{
    /**
     * Add an "exists" clause to the query.
     *
     * This method allows adding an "exists" or "not exists" clause to the query.
     * It extends the base query builder functionality to support more complex
     * query conditions, useful for custom ORM implementations.
     *
     * @param  \Illuminate\Database\Query\Builder $query The subquery instance.
     * @param  string $boolean The boolean operator ('and' or 'or').
     * @param  bool $not Whether the clause should be "not exists".
     * @return $this
     */
    public function addWhereExistsQuery(\Illuminate\Database\Query\Builder $query, $boolean = 'and', $not = false)
    {
        // Determine the type of exists clause: 'Exists' or 'NotExists'.
        $type = $not ? 'NotExists' : 'Exists';

        // Add the exists clause to the 'wheres' array.
        $this->wheres[] = compact('type', 'query', 'boolean');

        // Bind the subquery bindings to the parent query's where bindings.
        $this->addBinding($query->getBindings(), 'where');

        // Return the current Builder instance for method chaining.
        return $this;
    }
}
