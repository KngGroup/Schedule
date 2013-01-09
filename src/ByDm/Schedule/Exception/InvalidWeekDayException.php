<?php
namespace ByDm\Schedule\Exception;

/**
 * InvalidWeekDayException
 * 
 * throw when try to get week day name by invalid weekday
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class InvalidWeekDayException extends \OutOfRangeException
{
    /**
     * Constructor
     * 
     * @param string $message
     * @param type $code
     * @param \Exception $previous
     */
    public function __construct($message = null, $code = null, \Exception $previous = null)
    {
        if (null === $message) {
            $message = 'Invaid week day. It should be between 1 and 7';
        }
        
        parent::__construct($message, $code, $previous);
    }
}
