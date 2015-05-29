<?php

namespace Spark\Db\Adapter;

interface AdapterCapableInterface
{
    /**
     * Returns a {@link AdapterInterface} object.
     *
     * @return AdapterInterface a database adapter.
     */
    public function getDbAdapter();
}

