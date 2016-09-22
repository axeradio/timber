<?php namespace ca\acadiau\axeradio\timber\controllers\songs\ajax;

use ca\acadiau\axeradio\timber\Controller as Controller;
use ca\acadiau\axeradio\timber\repositories\Songs as Songs;

/**
 * Get a page worth of songs from the log. The length of a page is defined by the repository.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class GetSongsPage extends Controller
{
    private $songs_repo;

    public function __construct(Songs $songs_repo)
    {
        $this->songs_repo = $songs_repo;
    }

    public function show()
    {
        header('Content-Type: application/json');

        die(json_encode($this->songs_repo->getSongsByPage()));
    }
}