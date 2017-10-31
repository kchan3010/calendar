<?php
/**
 * Created by PhpStorm.
 * User: kennychan
 * Date: 10/29/17
 * Time: 8:58 AM
 */

namespace Classes;


use Exception;
use Interfaces\calendarInterface;

class calendar implements calendarInterface
{

    const SECONDS_IN_A_DAY = 86400;

    protected $ranges;
    protected $output;
    protected $calendar_entries;
    protected $start_of_day;
    protected $end_of_day;
    protected $response = [];


    public function __construct($calendar_entries)
    {
        date_default_timezone_set('UTC');
        $this->calendar_entries = $calendar_entries;
        $this->calculate_day_range();
    }

    /**
     * Calculates the beginning and end of days in terms of
     * seconds
     * @throws Exception
     */
    public function calculate_day_range()
    {
        sort($this->calendar_entries);
        if(!isset($this->calendar_entries[0])) {
            throw new Exception("There are no calendar entries");
        }

        $parts = explode("T", $this->calendar_entries[0]);

        if(isset($parts[0])) {
            $this->start_of_day  = strtotime($parts[0] . "00:00:00");
            $this->end_of_day = $this->start_of_day + self::SECONDS_IN_A_DAY;
        } else {
            throw new Exception("Dates are not formatted correctly");
        }

    }

    /**
     * Validate start time
     * @param $start_time
     * @return bool
     */
    private function is_valid_start_time($start_time)
    {
        $ret_val = true;
        if($start_time < $this->start_of_day || $start_time > $this->end_of_day) {
            $ret_val = false;
        }

        return $ret_val;
    }

    /**
     * Validate end time
     * @param $end_time
     * @return bool
     */
    private function is_valid_end_time($end_time)
    {
        $ret_val = true;
        if($end_time > $this->end_of_day) {
            $ret_val = false;
        }

        return $ret_val;
    }

    /**
     * Wrapper method for validation of time intervals
     * @param $interval
     * @return array
     * @throws Exception
     */
    public function validate_interval($interval)
    {
        $interval = preg_replace('/\s+/', ' ', $interval);
        $interval_parts = explode(" ", $interval);

        if(empty($interval_parts[0]) || empty($interval_parts[1])) {
            throw new Exception('Parsing error: date formatting');
        }

        $start_time = strtotime($interval_parts[0]);
        $end_time = strtotime($interval_parts[1]);

        //validate method
        if($this->is_valid_start_time($start_time) == false) {
            throw new Exception('Invalid start time');
        }

        if($this->is_valid_end_time($end_time) == false) {
            throw new Exception('Invalid end time');
        }

        return [$start_time, $end_time];

    }

}