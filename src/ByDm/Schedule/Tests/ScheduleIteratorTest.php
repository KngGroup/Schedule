<?php
namespace ByDm\Schedule\Tests;

use ByDm\Schedule\ScheduleIterator;
use ByDm\Schedule\ScheduleRules;
use ByDm\Schedule\ScheduleRulesInterface;

/**
 * Test of ScheduleIteratorTest
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class ScheduleIteratorTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @expectedException \ByDm\Schedule\Exception\InvalidIntervalException
     */
    public function testInvalidIntervalException()
    {
        $scheduleRules = new ScheduleRules();
        $scheduleIterator = new ScheduleIterator(new \DateTime(), $scheduleRules);
    }
    
    /**
     * @expectedException \ByDm\Schedule\Exception\InvalidFrequencyTypeException
     */
    public function testInvalidFrequencyTypeException()
    {
        $scheduleRules = new ScheduleRules();
        $scheduleRules->setInterval(1);
        $scheduleIterator = new ScheduleIterator(new \DateTime(), $scheduleRules);
    }
    
    /**
     * @dataProvider testIteratorProvider
     * 
     * @param string $expectedClass
     * @param \ByDm\Schedule\ScheduleIterator $iterator
     */
    public function testIteratorClass($expectedClass, ScheduleIterator $iterator)
    {
        $this->assertInstanceOf($expectedClass, $iterator->getIterator());
    }
    
    /**
     * Returns data for testing class of iterator
     * 
     * @return array
     */
    public function testIteratorProvider()
    {
        $data = array();
        $scheduleRules = new ScheduleRules();
        $scheduleRules->setInterval(1);
        
        $startDate     = new \DateTime();
        
        $dailyRules = clone $scheduleRules;
        $dailyRules->setFrequency(ScheduleRulesInterface::DAILY);
        $dailySchedule = new ScheduleIterator($startDate, $dailyRules);
        
        $data[] = array('\DatePeriod', $dailySchedule);
        
        $weeklyRules1 = clone $scheduleRules;
        $weeklyRules1->setRepeatByDaysOfWeek(array(1));
        $weeklyRules1->setFrequency(ScheduleRulesInterface::WEEKLY);
        $weeklySchedule1 = new ScheduleIterator($startDate, $weeklyRules1);
        
        $data[] = array('\DatePeriod', $weeklySchedule1);
        
        $weeklyRules2 = clone $scheduleRules;
        $weeklyRules2->setRepeatByDaysOfWeek(array(1, 3));
        $weeklyRules2->setFrequency(ScheduleRulesInterface::WEEKLY);
        $weeklySchedule2 = new ScheduleIterator($startDate, $weeklyRules2);
        
        $data[] = array('\ByDm\Schedule\WeeklyIterator', $weeklySchedule2);
        
        return $data;
    }
    
    /**
     * Main test func for schedule iterator
     */
    public function scheduleIteratorTester($schedule, $expected)
    {
        foreach($schedule as $date) {
            $this->assertEquals(array_shift($expected), $date->format('Y-m-d'));
        }
        $this->assertEmpty($expected);
    }
    
    
    /**
     * @dataProvider dailyFrequencyProviderIntLimit
     */
    public function testDailyFrequencyProviderIntLimit($schedule, $expected)
    {
        $this->scheduleIteratorTester($schedule, $expected);
    }
    
    /**
     * Returns data for testing DailyFrequencyIntLimit
     * 
     * @return array
     */
    public function dailyFrequencyProviderIntLimit()
    {
        $data = array();
        $startDate = new \DateTime('2013-01-09');
        
        $scheduleRules = new ScheduleRules();
        $scheduleRules->setFrequency(ScheduleRulesInterface::DAILY);
        
        $oneDayIntervalRules = clone $scheduleRules;
        $oneDayIntervalRules->setInterval(1);
        $oneDayIntervalRules->setLimitation(3);
        $scheduleIterator = new ScheduleIterator(clone $startDate, $oneDayIntervalRules);
        
        $data[] = array($scheduleIterator, array(
            '2013-01-09',
            '2013-01-10',
            '2013-01-11',
        ));
        
        $threeDayIntervalRules = clone $scheduleRules;
        $threeDayIntervalRules->setInterval(3)
                              ->setLimitation(4); 
        
        $threeDayIntervalSchedule = new ScheduleIterator(clone $startDate, $threeDayIntervalRules);
        $data[] = array($threeDayIntervalSchedule, array(
            '2013-01-09',
            '2013-01-12',
            '2013-01-15',
            '2013-01-18',
        ));
        
        return $data;
    }
    
    /**
     * @dataProvider dailyFrequencyProviderIntLimit
     */
    public function testDailyFrequencyProviderUntilDate($schedule, $expected)
    {
        $this->scheduleIteratorTester($schedule, $expected);
    }
    
    /**
     * Returns data for testing Daily Frequency until date
     * 
     * @return array
     */
    public function dailyFrequencyProviderUntilDate()
    {
        $data = array();
        
        $startDate = new \DateTime('2013-01-09');
        
        $scheduleRules = new ScheduleRules();
        $scheduleRules->setFrequency(ScheduleRulesInterface::DAILY);
        
        $untilJan12Rules = clone $scheduleRules;
        $untilJan12Rules->setLimitation(new \DateTime('2013-01-12'))
                        ->setInterval(1);
        
        $oneDayIntervalSchedule = new ScheduleIterator(clone $startDate, $untilJan12Rules);
        
        $data[] = array($oneDayIntervalSchedule, array(
            '2013-01-09',
            '2013-01-10',
            '2013-01-11',
            '2013-01-12',
        ));
        
        return $data;
    }
    
    /**
     * @dataProvider monthlyDataProvider
     */
    public function testMonthlyIterator($schedule, $expected)
    {
        $this->scheduleIteratorTester($schedule, $expected);
    }
    
    /**
     * data for monthly iterator
     */
    public function monthlyDataProvider()
    {
        $data = array();
        
        $startDate = new \DateTime('2013-01-09');
        
        $scheduleRules = new ScheduleRules();
        $scheduleRules->setFrequency(ScheduleRulesInterface::MONTHLY);
                 
        //test int limit without day number
        $intLimitWithoutDateRules = clone $scheduleRules;
        $intLimitWithoutDateRules->setInterval(2)
                                 ->setLimitation(3);
        
        $data[] = array(
            new ScheduleIterator($startDate, $intLimitWithoutDateRules),
            array(
                '2013-01-09',
                '2013-03-09',
                '2013-05-09',
            )
        );
        
        //test int limit with day number
        $intLimitWithDayRules = clone $scheduleRules;
        $intLimitWithDayRules->setInterval(4)
                             ->setLimitation(4)
                             ->setRepeatByDay(12);
        
        $data[] = array(new ScheduleIterator($startDate, $intLimitWithDayRules), array(
            '2013-01-12',
            '2013-05-12',
            '2013-09-12',
            '2014-01-12',
        ));
        
        //test int limit with day number, when day less than startdate day
        $intLimitWithLessDayRules = clone $scheduleRules;
        $intLimitWithLessDayRules->setInterval(1)
                                 ->setLimitation(4)
                                 ->setRepeatByDay(7);
        
        $data[] = array(new ScheduleIterator($startDate, $intLimitWithLessDayRules), array(
            '2013-02-07',
            '2013-03-07',
            '2013-04-07',
            '2013-05-07'
        ));
        
        //test due date limit with day number
        $untilLimitWithLessDayRules = clone $scheduleRules;
        $untilLimitWithLessDayRules->setInterval(1)
                                   ->setLimitation(new \DateTime('2013-04-03'))
                                   ->setRepeatByDay(5);
        
        $data[] = array(new ScheduleIterator($startDate, $untilLimitWithLessDayRules), array(
            '2013-02-05',
            '2013-03-05'
        ));
        
        //test due date equals generated date
        $untilLimitWithUntilEqalsGenDateRules = clone $scheduleRules;
        $untilLimitWithUntilEqalsGenDateRules->setInterval(1)
                                             ->setLimitation(new \DateTime('2013-04-03'))
                                             ->setRepeatByDay(3);
        
        $data[] = array(new ScheduleIterator($startDate, $untilLimitWithUntilEqalsGenDateRules), array(
            '2013-02-03',
            '2013-03-03',
            '2013-04-03'
        ));
        
        return $data;
    }
    
    /**
     * @dataProvider yearlyDataProvider
     */
    public function testYearlyIterator($schedule, $expected)
    {
        $this->scheduleIteratorTester($schedule, $expected);
    }
    
    /**
     * Provides data for testYearlyIterator
     * 
     * @return array
     */
    public function yearlyDataProvider()
    {
        $data = array();
        
        $startDate = new \DateTime('2013-01-09');
        $scheduleRules = new ScheduleRules();
        $scheduleRules->setFrequency(ScheduleRulesInterface::YEARLY);
        
        $intLimitWithoutDayAndMonthRules = clone $scheduleRules;
        $intLimitWithoutDayAndMonthRules->setInterval(2)
                                        ->setLimitation(3);
        
        //int limit without repeatByDay and repeat by Month
        $data[] = array(new ScheduleIterator($startDate, $intLimitWithoutDayAndMonthRules), array(
            '2013-01-09',
            '2015-01-09',
            '2017-01-09',
        ));
        
        //until limit with repeatByDay and repeatByMonth
        $untilLimitWithRepeatByDate = clone $scheduleRules;
        $untilLimitWithRepeatByDate->setInterval(3)
                                   ->setRepeatByDay(1)
                                   ->setRepeatByMonth(1)
                                   ->setLimitation(new \DateTime('2022-01-01'));
        $data[] = array(new ScheduleIterator($startDate, $untilLimitWithRepeatByDate), array(
            '2016-01-01',
            '2019-01-01',
            '2022-01-01',
        ));
        
        //until limit with repByMonth  and without repeatByDay
        $untilLimitWithRepeatByMonth = clone $scheduleRules;
        $untilLimitWithRepeatByMonth->setInterval(1)
                                   ->setRepeatByMonth(8)
                                   ->setLimitation(3);
        $data[] = array(new ScheduleIterator($startDate, $untilLimitWithRepeatByMonth), array(
            '2013-08-09',
            '2014-08-09',
            '2015-08-09',
        ));
        
        return $data;
    }
    
    /**
     * Test for weekly iterator
     * @dataProvider weeklyDatesProvider
     */
    public function testWeeklyIterator($schedule, $expected)
    {
        $this->scheduleIteratorTester($schedule, $expected);
    }
    
    /**
     * Provides dates for testWeeklyIterator
     * 
     * @return array
     */
    public function weeklyDatesProvider()
    {
        $data = array();
        
        $scheduleRules = new ScheduleRules();
        $scheduleRules->setFrequency(ScheduleRulesInterface::WEEKLY);
        $startDate = new \DateTime('2013-01-09');
        
        //test only one week day with int limit
        $oneWeekdayWithIntLimitRules = clone $scheduleRules;
        $oneWeekdayWithIntLimitRules->setInterval(3)
                                    ->setLimitation(4)
                                    ->setRepeatByDaysOfWeek(array(2));
        $data[] = array(new ScheduleIterator($startDate, $oneWeekdayWithIntLimitRules), array(
            '2013-01-15',
            '2013-02-05',
            '2013-02-26',
            '2013-03-19'
        ));
        
        return $data;
    }
}
