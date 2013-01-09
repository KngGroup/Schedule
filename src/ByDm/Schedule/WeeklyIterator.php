<?php
namespace ByDm\Schedule;

use ByDm\Schedule\Utils\Date;

/**
 * Iterates by days of week
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class WeeklyIterator implements \Iterator
{
    /**
     * @var \DateTime start date
     */
    private $startDate;
    
    /**
     * @var array days of week
     */
    private $daysOfWeek;
    
    /**
     * @var integer current day of week index
     */
    private $curDayOfWeekIndex = 0;
    
    /**
     *
     * @var integer initial day of week index 
     */
    private $initialDayOfWeekIndex = 0;
    
    /**
     * @var \DateTime current iteration date
     */
    private $date;
    
    /**
     * @var integer|\DateTime
     */
    private $limitation;
    
    /**
     * @var integer number of iteration 
     */
    private $counter = 0;
    
    /**
     * @var integer interval
     */
    private $interval;
    
    /**
     * Constructor
     * 
     * @param \ByDm\Schedule\Utils\Date $dateUtil date util
     * @param \DateTime $startDate initial date
     * @param \ByDm\Schedule\ScheduleRulesInterface $rules repetition rules
     * @param integer|\DateTime $recurrences number of recurrences or end date
     */
    public function __construct(
        Date $dateUtil,    
        \DateTime $startDate, 
        ScheduleRulesInterface $rules
    ) {
        $startDate = clone $startDate;
        
        $this->daysOfWeek = $rules->getRepeatByDaysOfWeek();
        sort($this->daysOfWeek);
        
        $startDateWeekDay = $startDate->format('N');
        
        //if there is no needed weekdays on current week
        //then modify start date considering interval
        $modifyStartDateWithInterval = false;
        
        if (!in_array($startDateWeekDay, $this->daysOfWeek)) {
            $this->curDayOfWeekIndex = $dateUtil->modifyToClosestWeekday(
                $startDate, 
                $this->daysOfWeek
            );
            
            if ($this->curDayOfWeekIndex == 0) {
                $modifyStartDateWithInterval = true;
            }
        } else {
            $this->curDayOfWeekIndex = array_search(
                $startDateWeekDay, 
                $this->daysOfWeek
            );
        }
        
        $this->initialDayOfWeekIndex = $this->curDayOfWeekIndex;
        
        //transform weekday to weekday name, e.g. 1 to Monday etc
        foreach($this->daysOfWeek as &$weekday) {
            $weekday = $dateUtil->getDayOfWeekName($weekday);
        }
        
        //destroy link
        unset($weekday);
        
        $this->limitation = $rules->getLimitation();
        $this->interval   = $rules->getInterval();
        $this->startDate  = $startDate;
        
        if ($modifyStartDateWithInterval && $this->interval > 1) {
            $this->startDate->modify('+' . ($this->interval - 1) * 7 . ' days');
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return clone $this->date;
    }
    
    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->counter;
    }
    
    /**
     * {@inheritDoc}
     */
    public function next()
    {
        $modifyInterval = false; 
        
        if (isset($this->daysOfWeek[$this->curDayOfWeekIndex + 1])) {
            $this->curDayOfWeekIndex++;
        } else {
            $this->curDayOfWeekIndex = 0;
            $modifyInterval = true;
        }
        
        $this->date->modify('next ' . $this->daysOfWeek[$this->curDayOfWeekIndex]);
        
        if ($modifyInterval && $this->interval > 1) {
            $this->date->modify('+ ' . 7 * ($this->interval - 1) . ' days');
        }
        
        ++$this->counter;
        
    }
    
    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        $this->counter           = 0;
        $this->date              = $this->startDate;
        $this->curDayOfWeekIndex = $this->initialDayOfWeekIndex;
    }
    
    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        if ($this->limitation instanceof \DateTime) {
            return $this->date->format('Ymd') <= $this->limitation->format('Ymd');
        } else {
            return $this->counter < $this->limitation;
        }
    }
    
}
