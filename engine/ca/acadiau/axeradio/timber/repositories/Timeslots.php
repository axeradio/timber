<?php namespace ca\acadiau\axeradio\timber\repositories;

/**
 * Timeslot retrieval.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class Timeslots extends Repository
{
    /**
     * Get the timeslot of a date and time.
     *
     * @param string $date the date
     * @param int $show the database ID of a show to limit to
     * @return object the timeslot at the date and time
     */
    public function getTimeslotByDate($date, $show = null)
    {
        $time = date('H:i:s', strtotime($date));
        $day = date('N', strtotime($date)) - 1;

        $query = "
SELECT
    `" . $this->wpdb->timber_timeslots . "`.`ID`,
    `" . $this->wpdb->timber_timeslots . "`.`timeslot_show`,
    `" . $this->wpdb->timber_timeslots . "`.`timeslot_day`,
    `" . $this->wpdb->timber_timeslots . "`.`timeslot_start`,
    `" . $this->wpdb->timber_timeslots . "`.`timeslot_end`
FROM `" . $this->wpdb->timber_timeslots . "`
WHERE";
        if ($show != null)
            $query .= " `" . $this->wpdb->timber_timeslots . "`.`timeslot_show` = '$show' AND";
        $query .= " `" . $this->wpdb->timber_timeslots . "`.`timeslot_day` = '$day'
    AND TIME('$time') >= `" . $this->wpdb->timber_timeslots . "`.`timeslot_start`
    AND TIME('$time') < `" . $this->wpdb->timber_timeslots . "`.`timeslot_end`
    AND `" . $this->wpdb->timber_timeslots . "`.`timeslot_status` = 'A'
";
        return $this->wpdb->get_row($query);
    }

    /**
     * Get the next timeslot after the current date and time, or after a given date and time.
     *
     * @param string $date the date and time from which to start the search
     * @return object the timeslot
     */
    public function getNextTimeslot($date = null)
    {
        if ($date == null)
            $date = current_time('timestamp');
        $time = date('H:i:s', $date);
        $day = date('N', $date) - 1;

        $query = "
SELECT
    `" . $this->wpdb->timber_timeslots . "`.`ID`,
    `" . $this->wpdb->timber_timeslots . "`.`timeslot_show`,
    `" . $this->wpdb->timber_timeslots . "`.`timeslot_day`,
    `" . $this->wpdb->timber_timeslots . "`.`timeslot_start`,
    `" . $this->wpdb->timber_timeslots . "`.`timeslot_end`
FROM `" . $this->wpdb->timber_timeslots . "`
WHERE
    `" . $this->wpdb->timber_timeslots . "`.`timeslot_day` = '$day'
    AND `" . $this->wpdb->timber_timeslots . "`.`timeslot_start` > '$time'
    AND `" . $this->wpdb->timber_timeslots . "`.`timeslot_status` = 'A'
ORDER BY `" . $this->wpdb->timber_timeslots . "`.`timeslot_start` ASC
";
        return $this->wpdb->get_row($query);
    }

    /**
     * Insert a timeslot.
     *
     * @param int $show the database ID of the show the timeslot is associated with
     * @param string $day the day of the week on which the timeslot occurs
     * @param string $start the start time of the timeslot
     * @param string $end the end time of the timeslot
     * @return boolean false if the timeslot could not be inserted
     */
    public function insertTimeslot($show, $day, $start, $end)
    {
        return $this->wpdb->insert($this->wpdb->timber_timeslots,
            array(
                'timeslot_show' => $show,
                'timeslot_day' => $day,
                'timeslot_start' => $start,
                'timeslot_end' => $end,
                'timeslot_status' => 'A'
            ));
    }

    /**
     * Delete a timeslot. Does not actually remove the timeslot from the database; rather, marks it as deleted.
     *
     * @param int $id the database ID of the timeslot
     */
    public function deleteTimeslot($id)
    {
        return $this->wpdb->update($this->wpdb->timber_timeslots,
            array('timeslot_status' => 'D'),
            array('ID' => $id));
    }
}