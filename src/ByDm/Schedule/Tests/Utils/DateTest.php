<?php
namespace ByDm\Schedule\Tests\Utils;

use ByDm\Schedule\Utils\Date;

/**
 * Description of DateTest
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class DateTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * Date Util
     * 
     * @var Date
     */
    private $dateUtil;
    
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->dateUtil = new Date();
    }
    
    /**
     * @dataProvider invalidWeekDaysProvider
     * 
     * @expectedException \ByDm\Schedule\Exception\InvalidWeekDayException
     */
    public function testInvalidWeekdayException($weekDay)
    {
        $this->dateUtil->getDayOfWeekName($weekDay);
    }
    
    /**
     * @param integer $weekDay
     * @param string $weekDayName
     * @dataProvider weekDaysProvider
     */
    public function testGetDayOfWeekName($weekDay, $weekDayName)
    {
        $this->assertEquals($this->dateUtil->getDayOfWeekName($weekDay), $weekDayName);
    }
    
    /**
     * @param \DateTime $date
     * @param array $weekDays
     * @param \DateTime $expected
     * 
     * @dataProvider datesProvider
     */
    public function testModifyToClosestWeekday(\DateTime $date, array $weekDays, \DateTime $expected)
    {
        $this->dateUtil->modifyToClosestWeekday($date, $weekDays);
        $this->assertEquals($expected->format('Y-m-d'), $date->format('Y-m-d'));
    }
    
    /**
     * Provides dates
     * 
     * @return array
     */
    public function datesProvider()
    {
        return array(
            //weekdays less than current week day
            array(new \DateTime('2013-01-09'), array(2, 1), new \DateTime('2013-01-14')),
            //there is weekday greater than current
            array(new \DateTime('2013-01-08'), array(4,1,5), new \DateTime('2013-01-10')),
            //there is weekday which equal current and there is greater one
            array(new \DateTime('2013-01-08'), array(2, 3), new \DateTime('2013-01-09')),
            
        );
    }
    
    /**
     * Provides valid weekdays
     * 
     * @return array
     */
    public function weekDaysProvider()
    {
        return array(
            array(1, 'Monday'),
            array(5, 'Friday'),
            array(7, 'Sunday')
        );
    }
    
    /**
     * Provides invalid weekdays
     * 
     * @return array
     */
    public function invalidWeekDaysProvider()
    {
        return array(array(0), array(8));
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->dateUtil = null;
    }
}
