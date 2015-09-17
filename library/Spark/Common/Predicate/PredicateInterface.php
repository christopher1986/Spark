<?php

namespace Spark\Common\Predicate;

/**
 * A {@link PredicateInterface} contains a boolean-valued method that determines whether the object 
 * that is being tested meets the criteria defined by the predicate. Predicates are usually associated
 * with collection types that allow their elements or items to be filtered.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.0.0
 */
interface PredicateInterface
{
    /**
     * Determines whether the specified argument meets the criteria of this predicate.
     *
     * @param mixed $arg the object or value to be tested.
     * @return bool true if the argument meets the criteria, false otherwise.
     */
    public function test($arg);
}
