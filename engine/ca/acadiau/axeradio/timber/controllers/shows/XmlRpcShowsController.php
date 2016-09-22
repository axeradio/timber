<?php namespace ca\acadiau\axeradio\timber\controllers\shows;

use ca\acadiau\axeradio\timber\repositories\Shows as Shows;
use ca\acadiau\axeradio\timber\repositories\Timeslots as Timeslots;

/**
 * Show information for XML-RPC calls.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class XmlRpcShowsController
{
    private $show_repository;
    private $timeslot_repository;

    public function __construct(Shows $show_repository, Timeslots $timeslot_repository)
    {
        $this->show_repository = $show_repository;
        $this->timeslot_repository = $timeslot_repository;
    }

    public function getCurrentShow()
    {
        $timeslot = $this->timeslot_repository->getTimeslotByDate(current_time('mysql'));
        $show = $this->show_repository->getShowById($timeslot->timeslot_show);
        if ($show != null)
            $show->timeslot = $timeslot;
        return $show;
    }

    public function getNextShow()
    {
        $timeslot = $this->timeslot_repository->getNextTimeslot();
        $show = $this->show_repository->getShowById($timeslot->timeslot_show);
        if ($show != null)
            $show->timeslot = $timeslot;
        return $show;
    }

    public function getStationCutoffs()
    {
        return array(
            'start_hour' => get_option('timber_day_start_hour'),
            'end_hour' => get_option('timber_day_end_hour'));
    }
}