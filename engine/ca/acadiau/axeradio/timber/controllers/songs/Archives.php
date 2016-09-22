<?php namespace ca\acadiau\axeradio\timber\controllers\songs;

use ca\acadiau\axeradio\common\Template as Template;
use ca\acadiau\axeradio\timber\Controller as Controller;
use ca\acadiau\axeradio\timber\repositories\Shows as Shows;
use ca\acadiau\axeradio\timber\repositories\Songs as Songs;

/**
 * Archive of logged songs.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class Archives extends Controller
{
    private $shows_repo;
    private $songs_repo;
    private $admin;

    public function __construct(Shows $shows_repo, Songs $songs_repo, $admin = true)
    {
        $this->shows_repo = $shows_repo;
        $this->songs_repo = $songs_repo;
        $this->admin = $admin;
    }

    public function show()
    {
        $template = new Template('archives');

        $page = intval($_GET['song_page']);
        $page = ($page < 1) ? 1 : $page;
        $template->set('page', $page);

        $show = intval($_GET['show']);
        if ($show != 0)
        {
            $song_count = $this->songs_repo->getShowSongCount($show);
            $template->set('songs', $this->songs_repo->getShowSongsByPage($show, $page - 1));
            $template->set('show', $show);
        }
        else
        {
            $song_count = $this->songs_repo->getSongCount();
            $template->set('songs', $this->songs_repo->getSongsByPage($page - 1));
        }

        $current_user = wp_get_current_user();
        $template->set('shows', $this->shows_repo->getAllShows($current_user->ID));

        $template->set('song_count', $song_count);
        $template->set('page_links', paginate_links(array(
            'base' => add_query_arg('song_page', '%#%'),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => ceil($song_count / 50),
            'current' => $page
        )));

        $template->set('admin', $this->admin);

        $template->display();
    }
}
