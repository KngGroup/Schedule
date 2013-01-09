<?php
namespace ByDm\Schedule\Exception;

/**
 * InvalidIntervalException
 * 
 * throw when interval less than 1
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class InvalidIntervalException extends \InvalidArgumentException
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
            $message = 'Interval have to be grater than 0';
        }
        
        parent::__construct($message, $code, $previous);
    }
}
