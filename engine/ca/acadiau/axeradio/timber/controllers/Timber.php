<?php namespace ca\acadiau\axeradio\timber\controllers;

use ca\acadiau\axeradio\common\Template as Template;
use ca\acadiau\axeradio\timber\repositories\Charts as Charts;
use ca\acadiau\axeradio\timber\repositories\Icecast as Icecast;

/**
 * Timber stats/home page.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class Timber
{
    private $icecast_repo;
    private $charts_repo;

    public function __construct(Icecast $icecast_repo, Charts $charts_repo)
    {
        $this->icecast_repo = $icecast_repo;
        $this->charts_repo = $charts_repo;
    }

    public function show()
    {
        $template = new Template('stats');

        $instantaneous_stats = false;
        if (is_admin())
            $instantaneous_stats = get_user_meta(wp_get_current_user()->ID, 'timber_instantaneous_stats', true);

        $template->set('day_history', $this->icecast_repo->getLastDayClientCounts($instantaneous_stats));
        $template->set('top_songs', $this->charts_repo->getTopSongsByMonth());
        $template->set('top_artists', $this->charts_repo->getTopArtistsByMonth());
        $template->set('instantaneous_stats', $instantaneous_stats);

        $template->display();
    }
}
