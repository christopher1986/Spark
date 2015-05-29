<?php

namespace Spark\Db\Adapter\Platform;

class Mysql implements PlatformInterface
{
    /**
     * Applies the quote character to the given identifier.
     * 
     * @param string the identifier that needs to be quoted.
     * @return string the identifier wrapped with the quote character.
     */
    public function quoteIdentifier($identifier)
    {
        return '`' . str_replace('`', '``', $identifier) . '`'; 
    }
    
    /**
     * Returns the identifier quote character for this platform.
     * 
     * @return string the identifier quote character.
     */
    public function getQuoteIdentifier()
    {
        return '`';
    }
    
    /**
     * Returns the identifier separator for this platform.
     *
     * @return string the identifier separator.
     */
    public function getIdentifierSeparator()
    {
        return '.';
    }
    
    /**
     * Returns the column separator for this platform.
     *
     * @return string the column separator.
     */
    public function getColumnSeparator()
    {
        return ',';
    }
    
    /**
     * Returns the limit clause for the given arguments.
     *
     * @param int|null $limit (optional)the number of results to retrieve.
     * @param int|null $offset (optional) the record to start retrieving results at.
     * @return string the correct clause for the given arguments.
     */
    public function getLimitClause($limit = null, $offset = null)
    {
        if (!(is_numeric($limit) || $limit === null)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: if limit is provided it should be a numeric value; received "%s"',
                __METHOD__,
                (is_object($limit)) ? get_class($limit) : $limit
            ));
        } else if (!(is_numeric($offset) || $offset === null)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: if offset is provided it should be a numeric value; received "%s"',
                __METHOD__,
                (is_object($offset)) ? get_class($offset) : $offset
            ));
        }

        $clause = '';
        if (is_numeric($limit)) {
            $clause = sprintf('LIMIT %d', (int) $limit);
            if (is_numeric($offset)) {
                $clause = sprintf('LIMIT %d, %d', (int) $offset, (int) $limit);
            }
        }
              
        return $clause;
    }
}
