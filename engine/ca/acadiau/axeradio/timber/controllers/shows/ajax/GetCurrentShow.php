<?php namespace ca\acadiau\axeradio\timber\controllers\shows\ajax;

use ca\acadiau\axeradio\timber\Controller as Controller;
use ca\acadiau\axeradio\timber\repositories\Shows as Shows;
use ca\acadiau\axeradio\timber\repositories\Timeslots as Timeslots;

/**
 * Get information about the currently-playing show in JSON form.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class GetCurrentShow extends Controller
{
    private $show_repository;
    private $timeslot_repository;

    public function __construct(Shows $show_repository, Timeslots $timeslot_repository)
    {
        $this->show_repository = $show_repository;
        $this->timeslot_repository = $timeslot_repository;
    }

    public function show()
    {
        header('Content-Type: application/json');

        $timeslot = $this->timeslot_repository->getTimeslotByDate(current_time('mysql'));
        $show = $this->show_repository->getShowById($timeslot->timeslot_show);
        if ($show != null)
            $show->timeslot = $timeslot;
        die(json_encode($show));
    }
}