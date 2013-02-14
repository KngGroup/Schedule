<?php
namespace ByDm\Schedule;

use ByDm\Schedule\Exception\InvalidIntervalException;
use ByDm\Schedule\Exception\InvalidFrequencyTypeException;
use ByDm\Schedule\Utils\Date;

/**
 * ScheduleIterator returns new date on every iteration by repeat options
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class ScheduleIterator implements \IteratorAggregate
{
    /**
     * Iterator
     * 
     * @var \Iterator
     */
    private $iterator;
    
    /**
     * Constructor
     * 
     * @param \DateTime $startDate                  start date 
     * @param \ByDm\Schedule\ScheduleRulesInterface $rules rules for schedule
     */
    public function __construct(
        \DateTime $startDate, 
        ScheduleRulesInterface $rules,
        $excludeStartDate = false
    ) {
        $startDate = clone $startDate;
        
        $interval = (int) $rules->getInterval();
        if ($interval < 1) {
            throw new InvalidIntervalException();
        }
        
        $frequency = $rules->getFrequency();
        
        switch ($frequency) {
            case ScheduleRulesInterface::DAILY:
                $dateInterval = new \DateInterval('P' . $interval . 'D');
                
                break;
            case ScheduleRulesInterface::WEEKLY:
                $dateUtil = new Date();
                $repeatByDaysOfWeek = $rules->getRepeatByDaysOfWeek();
                if (count($repeatByDaysOfWeek) > 1) {
                    $this->iterator = new WeeklyIterator($dateUtil, $startDate, $rules);
                } else {
                    if (!empty($repeatByDaysOfWeek)) {
                        $dayOfWeek = $repeatByDaysOfWeek[0];
                        if ($startDate->format('N') != $dayOfWeek) {
                            $startDate->modify(
                                'next ' . $dateUtil->getDayOfWeekName($dayOfWeek)
                            );
                            // start date is already excluded now
                            $excludeStartDate = false;
                        }
                        
                    }
                    
                    $dateInterval = new \DateInterval('P' . $interval * 7 . 'D');
                }
                
                break;
            case ScheduleRulesInterface::MONTHLY:
                $dateInterval = new \DateInterval('P' . $interval . 'M');
                $repeatByDay = $rules->getRepeatByDay();
                
                if (null !== $repeatByDay) {
                    $addMonth = false;
                    
                    if ($repeatByDay < $startDate->format('d')) {
                        $addMonth = true;
                    }
                    
                    if ($startDate->format('d') != $repeatByDay) {
                        $startDate = new \DateTime(
                            $startDate->format('Y-m-' . sprintf('%02d', $repeatByDay))
                        );
                        // start date is already excluded now
                        $excludeStartDate = false;
                    }
                    
                    if ($addMonth) {
                        $startDate->modify('+' . $interval . 'month');
                        // start date is already excluded now
                        $excludeStartDate = false;
                    }
                }
                
                break;
            case ScheduleRulesInterface::YEARLY:
                $dateInterval  = new \DateInterval('P' . $interval . 'Y');
                $repeatByDay   = $rules->getRepeatByDay();
                $repeatByMonth = $rules->getRepeatByMonth();
                
                if (null !== $repeatByDay || null !== $repeatByMonth) {
                    $previousStartDate = clone $startDate;
                    
                    $year = $startDate->format('Y');
                    if (null === $repeatByDay) {
                        $day = $startDate->format('d');
                    } else {
                        $day = sprintf('%02d', $repeatByDay);
                    }
                    
                    if (null === $repeatByMonth) {
                        $month = $startDate->format('m');
                    } else {
                        $month = sprintf('%02d', $repeatByMonth);
                    }
                    
                    $startDateStr = $year . '-' . $month . '-' . $day;
                    if ($startDate->format('Y-m-d') !== $startDateStr) {
                        $startDate = new \DateTime($year . '-' . $month . '-' . $day);
                        // start date is already excluded now
                        $excludeStartDate = false;
                    }
                    
                    
                    if ($previousStartDate->format('Ymd') > $startDate->format('Ymd')) {
                        $startDate->modify('+' . $interval . 'year');
                        // start date is already excluded now
                        $excludeStartDate = false;
                    }
                }
                
                break;
                
            default:
                throw new InvalidFrequencyTypeException();
                break;
        }
        
        $limitation = $rules->getLimitation();
        if (!$limitation instanceof \DateTime) {
            if (!$excludeStartDate) {
                //because start date is not a reccurency
                $limitation--;
            }
        } else {
            $limitation = clone $limitation;
            //becasuse we want to include end date
            $limitation->modify('+1 day');
        }
        
        if (!$this->iterator instanceof \Traversable) {
            $this->iterator = new \DatePeriod(
                $startDate, 
                $dateInterval, 
                $limitation,
                $excludeStartDate
            );
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return $this->iterator;
    }
}
