<?php

namespace Spark\Db\Adapter;

interface AdapterAwareInterface
{
    /**
     * Set a {@link AdapterInterface} object.
     *
     * @param AdapterInterface $adapter the database adapter.
     */
    public function setDbAdapter(AdapterInterface $adapter);
}
