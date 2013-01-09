<?php
namespace ByDm\Schedule;

use ByDm\Schedule\Exception\InvalidFrequencyTypeException;
use ByDm\Schedule\Exception\InvalidIntervalException;

/**
 * Simple ScheduleRules
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class ScheduleRules implements ScheduleRulesInterface
{
    /**
     * Available frequency types
     * 
     * @var array 
     */
    public static $frequencyTypes = array(
        ScheduleRulesInterface::DAILY   => 'daily',
        ScheduleRulesInterface::WEEKLY  => 'weekly',
        ScheduleRulesInterface::MONTHLY => 'monthly',
        ScheduleRulesInterface::YEARLY  => 'yearly',
    );
    
    /**
     * @var integer frequency type
     */
    private $frequency;
    
    /**
     * @var integer interval
     */
    private $interval;
    
    /**
     * @var integer repeat by day number
     */
    private $repeatByDay;
    
    /**
     * @var array days of week numbers(1-7) 
     */
    private $repeatByDaysOfWeek;
    
    /**
     * @var integer month number
     */
    private $repeatByMonth;
    
    /**
     * @var integer|\DateTime
     */
    private $limitation;
    
    /**
     * {@inheritDoc}
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * Sets frequency
     * 
     * @return ScheduleRules
     */
    public function setFrequency($frequency)
    {
        if (!array_key_exists($frequency, self::$frequencyTypes)) {
            throw new InvalidFrequencyTypeException();
        }
        
        $this->frequency = $frequency;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Sets interval
     * 
     * @return ScheduleRules
     */
    public function setInterval($interval)
    {
        $interval = (int) $interval;
        
        if ($interval < 1) {
            throw new InvalidIntervalException();
        }
        
        $this->interval = $interval;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRepeatByDay()
    {
        return $this->repeatByDay;
    }
    
    /**
     * Sets Repeat By Day
     * 
     * @return ScheduleRules
     */
    public function setRepeatByDay($repeatByDay)
    {
        $this->repeatByDay = $repeatByDay;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRepeatByDaysOfWeek()
    {
        return $this->repeatByDaysOfWeek;
    }
    
    /**
     * Sets Repeat By Days Of Week
     * 
     * @return ScheduleRules
     */
    public function setRepeatByDaysOfWeek($repeatByDaysOfWeek)
    {
        $this->repeatByDaysOfWeek = $repeatByDaysOfWeek;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRepeatByMonth()
    {
        return $this->repeatByMonth;
    }
    
    /**
     * Sets Repeat By Month
     * 
     * @return ScheduleRules
     */
    public function setRepeatByMonth($repeatByMonth)
    {
        $this->repeatByMonth = $repeatByMonth;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLimitation()
    {
        return $this->limitation;
    }
    
    /**
     * Sets Limitation
     * 
     * @return ScheduleRules
     */
    public function setLimitation($limitation)
    {
        $this->limitation = $limitation;
        return $this;
    }

}
