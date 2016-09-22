<?php namespace ca\acadiau\axeradio\timber\controllers\songs\ajax;

use ca\acadiau\axeradio\timber\Controller as Controller;
use ca\acadiau\axeradio\timber\repositories\Shows as Shows;
use ca\acadiau\axeradio\timber\repositories\Songs as Songs;
use ca\acadiau\axeradio\timber\repositories\Timeslots as Timeslots;

/**
 * Add a song to the log.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class AddSong extends Controller
{
    private $shows_repo;
    private $songs_repo;
    private $timeslots_repo;

    public function __construct(Shows $shows_repo, Songs $songs_repo, Timeslots $timeslots_repo)
    {
        $this->shows_repo = $shows_repo;
        $this->songs_repo = $songs_repo;
        $this->timeslots_repo = $timeslots_repo;
    }

    public function show()
    {
        header('Content-Type: application/json');

        $message = array('success' => false, 'message' => '');

        $song_date = $_POST['song_date'];
        if (empty($song_date)) $song_date = current_time('mysql');

        $timeslot = $this->timeslots_repo->getTimeslotByDate($song_date);
        $show = $this->shows_repo->getShowById($timeslot->timeslot_show);

        if ($_POST['song_artist'] == '' || $_POST['song_name'] == '')
        {
            $message['message'] = _('Artist or song name cannot be blank!');
        }
        elseif ($song_date == 0)
        {
            $message['message'] = _('Invalid time!');
        }
        elseif ($timeslot == null)
        {
            $message['message'] = _('No timeslot available for that time!');
        }
        elseif ($this->songs_repo->insertSong(stripslashes($_POST['song_artist']),
            stripslashes($_POST['song_name']), $song_date,
            $timeslot)
        )
        {
            $message['success'] = true;
            $message['message'] = _('Song added successfully.');
        }
        else
        {
            $message['message'] = _('Failed to add song!');
        }

        die(json_encode($message));
    }
}