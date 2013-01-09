<?php
namespace ByDm\Schedule\Exception;

/**
 * InvalidFrequencyType
 * 
 * throw when try to set invalid frequency type
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class InvalidFrequencyTypeException extends \InvalidArgumentException
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
            $message = 'Invaid frequency type. It should be one of ScheduleRulesInterface constants';
        }
        
        parent::__construct($message, $code, $previous);
    }
}
