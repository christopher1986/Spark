<?php

namespace Spark\Common\Predicate;

/**
 * This class allows a predicate to be called as a function. A predicate implementing this class can be
 * used with methods whose parameter list will only accept callable functions. Using a predicate object
 * does have one advantage over using (traditional) functions. A pedicate object can hold it's own state
 * in one or more fields which could be part of the evalution process which determines whether the
 * specified argument meets the criteria.
 *
 * @author Chris Harris
 * @version 1.0.0
 * @since 1.0.0
 */
abstract class AbstractPredicate implements PredicateInterface
{
    /**
     * Allows the predicate to be called as a function.
     *
     * @link http://php.net/manual/en/language.oop5.magic.php#object.invoke __invoke()
     */
    public function __invoke()
    {
        return call_user_func_array(array($this, 'test'), func_get_args());
    }
}
