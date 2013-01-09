<?php
namespace ByDm\Schedule;

/**
 * Defines rules for ScheduleIterator
 * 
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
interface ScheduleRulesInterface
{
    /**
     * Daily frequency
     */
    const DAILY   = 1;
    
    /**
     * Weekly frequency
     */
    const WEEKLY  = 2;
    
    /**
     * Monthly frequency
     */
    const MONTHLY = 3;
    
    /**
     * Yearly frequency
     */
    const YEARLY  = 4;
    
    /**
     * Returns frequency type
     * 
     * one of interface constant (DAILY, MONTHLY, WEEKLY, YEARLY)
     * 
     * @return integer
     */
    public function getFrequency();
    
    /**
     * Returns interval
     * 
     * @return integer
     */
    public function getInterval();
    
    /**
     * Returns days of week numbers(1-7) 
     * 
     * for weekly frequency only
     * 
     * @return array
     */
    public function getRepeatByDaysOfWeek();
    
    /**
     * Returns repeat by day number
     * 
     * for monthly and yearly frequency
     * 
     * @return integer
     */
    public function getRepeatByDay();
    
    /**
     * Returns repeat by month number
     * 
     * for yearly frequency only
     * 
     * @return integer
     */
    public function getRepeatByMonth();
    
    /**
     * Returns recurrences number or end date
     * 
     * @return integer|\DateTime
     */
    public function getLimitation();

}
