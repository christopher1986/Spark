<?php

namespace Framework\Cache;

/**
 * A storage that is capable of storing an item's metadata should implement this interface.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
interface MetadataCapableInterface
{      
    /**
     * Retrieves if present metadata for an item.
     *
     * @param string $key the key of the item for which to retrieve metadata.
     * @return mixed metadata associated with the item, or null if no metadata is present.
     */
    public function getMetadata($key);
}
