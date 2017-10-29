<?php
/**
 * Created by PhpStorm.
 * User: kennychan
 * Date: 10/29/17
 * Time: 11:20 AM
 */

namespace Classes;


use Exception;

class conflictCalendar extends calendar
{

    public function get_conflicts()
    {
        sort($this->calendar_entries);
        $temp = [];
        $conflicts = [];

        foreach($this->calendar_entries as $key => $interval) {
            try{
                list($start_time, $end_time) = $this->validate_interval($interval);

            } catch(Exception $e) {
                //can do some logging
                continue;
            }

            if(empty($temp)) {
                $temp[] = [$start_time, $end_time];
            } else {

                $previous = end($temp);
                if($start_time >= $previous[0] && $start_time <= $previous[1]) {
                    //found a conflict
                    $conflicts[] = $this->calendar_entries[$key-1];
                    $conflicts[] = $interval;
                } else {
                    $temp[] = [$start_time, $end_time];
                }
            }
        }

        return $conflicts;
    }


}