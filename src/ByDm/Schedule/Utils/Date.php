<?php
namespace ByDm\Schedule\Utils;

use ByDm\Schedule\Exception\InvalidWeekDayException;

/**
 * Date util
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class Date
{
    /**
     * @var array day of week names
     */
    private $weekDaysNames;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->weekDaysNames = array();
        $dateInterval        = new \DateInterval('P1D');
        $startDate           = new \DateTime('next Monday');
        $datePeriod          = new \DatePeriod($startDate, $dateInterval, 6);

        foreach ($datePeriod as $date) {
            $this->weekDaysNames[$date->format('N')] = $date->format('l');
        };
    }

    /**
     * Returns day of week name by number
     * 
     * @param type $weekDay
     */
    public function getDayOfWeekName($weekDay)
    {
        $weekDay = (int) $weekDay;
        if ($weekDay < 1 || $weekDay > 7) {
            throw new InvalidWeekDayException();
        }
        
        return $this->weekDaysNames[$weekDay];
    }
    
    /**
     * Modifies date to next closest weekday
     * 
     * side effect: weekdays will be sorted
     * 
     * @param \DateTime $date date to modify
     * @param array $weekDays week days numbers array
     * 
     * @return integer day of week name
     */
    public function modifyToClosestWeekday(\DateTime $date, &$weekDays)
    {
        sort($weekDays);
        $currentDayOfWeek = $date->format('N');

        foreach($weekDays as $index => $weekDay) {
            if ($weekDay > $currentDayOfWeek) {
                $date->modify('next ' . $this->getDayOfWeekName($weekDay));
                return $index;
            }
        }
        
        $date->modify('next ' . $this->getDayOfWeekName($weekDays[0]));
        return 0;
    }
}
