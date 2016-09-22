<?php namespace ca\acadiau\axeradio\timber\controllers\songs\ajax;

use ca\acadiau\axeradio\timber\Controller as Controller;
use ca\acadiau\axeradio\timber\repositories\Songs as Songs;

/**
 * Edit a song in the log.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class EditSong extends Controller
{
    private $songs_repo;

    public function __construct(Songs $songs_repo)
    {
        $this->songs_repo = $songs_repo;
    }

    public function show()
    {
        $tokens = explode('-', $_POST['ID']);
        $id = intval($tokens[1]);
        $action = $tokens[0];

        if ($action == 'song_artist')
        {
            $song = $this->songs_repo->getSongById($id);
            $song_artist = stripslashes($_POST['value']);
            $this->songs_repo->updateSong($id, $song_artist, $song->song_name,
                $song->song_date, $song->song_timeslot);
            echo($song_artist);
        }
        elseif ($action == 'song_name')
        {
            $song = $this->songs_repo->getSongById($id);
            $song_name = stripslashes($_POST['value']);
            $this->songs_repo->updateSong($id, $song->song_artist, $song_name,
                $song->song_date, $song->song_timeslot);
            echo($song_name);
        }
        elseif ($action == 'song_date')
        {
            $song = $this->songs_repo->getSongById($id);
            $song_date = stripslashes($_POST['value']);
            $this->songs_repo->updateSong($id, $song->song_artist, $song->song_name,
                $song_date);
            echo($song_date);
        }

        die();
    }
}