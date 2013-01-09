<?php
namespace ByDm\Schedule\Tests;

use ByDm\Schedule\WeeklyIterator;
use ByDm\Schedule\ScheduleRules;
use ByDm\Schedule\ScheduleRulesInterface;
use ByDm\Schedule\Utils\Date;

/**
 * Tests of WeeklyIterator
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class WeeklyIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests iterator
     * 
     * @dataProvider weeklyDataProvider
     */
    public function testWeeklyIterator(WeeklyIterator $weeklyIterator, array $expectedDates)
    {
        foreach($weeklyIterator as $date) {
            $this->assertEquals(array_shift($expectedDates), $date->format('Y-m-d'));
        }
        
        $this->assertEmpty($expectedDates);
    }
    
    /**
     * Provides test data for testWeeklyIterator
     * 
     * @return array
     */
    public function weeklyDataProvider()
    {
        $data = array();
        
        $dateUtil = new Date();
        $startDate = new \DateTime('2013-01-09');
        $scheduleRules = new ScheduleRules();
        $scheduleRules->setFrequency(ScheduleRulesInterface::WEEKLY);
        
        //limit integer, every monday wednesday, every to week
        $limitIntEveryMonAndWed2WeekIntervalRules = clone $scheduleRules;
        $limitIntEveryMonAndWed2WeekIntervalRules->setInterval(2)
                                                 ->setRepeatByDaysOfWeek(array(1, 3))
                                                 ->setLimitation(5);    
        
        $data[] = array(new WeeklyIterator($dateUtil, clone $startDate, $limitIntEveryMonAndWed2WeekIntervalRules), array(
            '2013-01-09',
            '2013-01-21',
            '2013-01-23',
            '2013-02-04',
            '2013-02-06'
        ));
        
        //check for valid start date
        $checkForValidStartDateLimitByDateRules = clone $scheduleRules;
        $checkForValidStartDateLimitByDateRules->setInterval(3)
                                               ->setRepeatByDaysOfWeek(array(1, 2))
                                               ->setLimitation(new \DateTime('2013-03-01'));
        
        $data[] = array(new WeeklyIterator($dateUtil, clone $startDate, $checkForValidStartDateLimitByDateRules), array(
            '2013-01-28',
            '2013-01-29',
            '2013-02-18',
            '2013-02-19'
        ));
        
        //limit until date every mon and tue
        $limitUntilDateEveryWeekOnMonAndTueRules = clone $scheduleRules;
        $limitUntilDateEveryWeekOnMonAndTueRules->setInterval(1)
                                                ->setRepeatByDaysOfWeek(array(1, 2))
                                                ->setLimitation(new \DateTime('2013-01-22'));  
        
        $data[] = array(new WeeklyIterator($dateUtil, clone $startDate, $limitUntilDateEveryWeekOnMonAndTueRules), array(
            '2013-01-14',
            '2013-01-15',
            '2013-01-21',
            '2013-01-22'
        ));
        
        return $data;
    }
}
