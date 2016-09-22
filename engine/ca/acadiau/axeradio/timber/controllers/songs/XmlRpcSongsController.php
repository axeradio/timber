<?php namespace ca\acadiau\axeradio\timber\controllers\songs;

use ca\acadiau\axeradio\timber\Controller as Controller;
use ca\acadiau\axeradio\timber\repositories\Shows as Shows;
use ca\acadiau\axeradio\timber\repositories\Songs as Songs;
use ca\acadiau\axeradio\timber\repositories\Timeslots as Timeslots;

/**
 * Song-related XML-RPC methods.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class XmlRpcSongsController
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

    public function addSong($song_artist, $song_name, $song_date = null)
    {
        if (empty($song_date)) $song_date = current_time('mysql');

        $timeslot = $this->timeslots_repo->getTimeslotByDate($song_date);

        if ($song_artist == '' || $song_name == '')
        {
            return new \IXR_Error(400, _('Artist or song name cannot be blank!'));
        }
        elseif ($song_date == 0)
        {
            return new \IXR_Error(400, _('Invalid time!'));
        }
        elseif ($timeslot == null)
        {
            return new \IXR_Error(400, _('No timeslot available for that time!'));
        }
        elseif (!$this->songs_repo->insertSong(stripslashes($song_artist),
                                               stripslashes($song_name), $song_date, $timeslot))
        {
            return new \IXR_Error(400, _('Failed to add song!'));
        }
        else
        {
            return true;
        }
    }
}