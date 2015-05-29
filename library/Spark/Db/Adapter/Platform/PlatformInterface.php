<?php

namespace Spark\Db\Adapter\Platform;

interface PlatformInterface
{
    /**
     * Applies the quote character to the given identifier.
     * 
     * @param string the identifier that needs to be quoted.
     * @return string the identifier wrapped with the quote character.
     */
    public function quoteIdentifier($identifier);
    
    /**
     * Returns the identifier quote character for this platform.
     * 
     * @return string the identifier quote character.
     */
    public function getQuoteIdentifier();
    
    /**
     * Returns the identifier separator for this platform.
     *
     * @return string the identifier separator.
     */
    public function getIdentifierSeparator();
    
    /**
     * Returns the limit clause for the given arguments.
     *
     * @param int|null $limit (optional)the number of results to retrieve.
     * @param int|null $offset (optional) the record to start retrieving results at.
     * @return string the correct clause for the given arguments.
     */
    public function getLimitClause($limit = null, $offset = null);
}
