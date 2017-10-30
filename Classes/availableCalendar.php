<?php
/**
 * Created by PhpStorm.
 * User: kennychan
 * Date: 10/29/17
 * Time: 10:00 AM
 */

namespace Classes;


use Exception;

class availableCalendar extends calendar
{

    /**
     * @return array
     */
    public function get_available()
    {
        $this->build_ranges();
        $this->build_output();
        $this->format_output();

        return $this->response;

    }

    /**
     * Helps build the time intervals by collapsing any
     * overlaps
     */
    private function build_ranges()
    {
        sort($this->calendar_entries);

        foreach($this->calendar_entries as $key => $interval) {
            try{
                list($start_time, $end_time) = $this->validate_interval($interval);

            } catch(Exception $e) {
                //can do some logging
                continue;
            }

            if(empty($this->ranges)) {
                $this->ranges[] = [$start_time, $end_time];
            } else {

                $previous = end($this->ranges);
                if($start_time >= $previous[0] && $start_time <= $previous[1]) {
                    /**
                     * Found an overlap and will remove previous interval
                     * and add new merged interval
                     */
                    if($end_time > $previous[1]) {
                        array_pop($this->ranges);
                        $this->ranges[] = [$previous[0], $end_time];
                    }

                } else {
                    $this->ranges[] = [$start_time, $end_time];
                }
            }
        }

    }

    /**
     * @return array
     */
    private function build_output()
    {
        if(!count($this->ranges)) {
            return [];
        }

        foreach ($this->ranges as $key => $interval) {
            $start_time              = $interval[0];
            $end_time                = $interval[1];

            if($key < count($this->ranges) - 1) {
                $tail_interval       = $this->ranges[$key + 1][0];
            } else {
                $tail_interval       = $this->end_of_day;
            }

            if ($key == 0 && $start_time != $this->start_of_day) {
                if($start_time != $this->start_of_day) {
                    $this->output[]      = [$this->format_time($this->start_of_day), $this->format_time($start_time)];
                }

                $this->output[]      = [$this->format_time($end_time), $this->format_time($tail_interval)];
            } else {
                if($end_time < $this->end_of_day) {
                    $this->output[]  = [$this->format_time($end_time), $this->format_time($tail_interval)];
                }
            }

        }

    }

    /**
     * @param $time
     * @return false|string
     */
    private function format_time($time)
    {
        return date("Y-m-d\TH:i\Z", $time);
    }

    /**
     * @return array
     * @throws Exception
     */
    private function format_output()
    {
        if(!count($this->output)) {
            return $this->response;
        }

        foreach($this->output as $row) {
            if(count($row) != 2) {
                throw new Exception("Invalid output format");
            }

            $this->response[] = implode(" ", $row);

        }

    }

}