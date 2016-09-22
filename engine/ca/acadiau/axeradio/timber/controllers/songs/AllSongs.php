<?php namespace ca\acadiau\axeradio\timber\controllers\songs;

use ca\acadiau\axeradio\common\Template as Template;
use ca\acadiau\axeradio\timber\Controller as Controller;
use ca\acadiau\axeradio\timber\repositories\Shows as Shows;
use ca\acadiau\axeradio\timber\repositories\Songs as Songs;
use ca\acadiau\axeradio\timber\repositories\Timeslots as Timeslots;

/**
 * Logging and admin-area archives access.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class AllSongs extends Controller
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
        $template = new Template('all_songs');

        if (!empty($_POST['action']))
        {
            if ($_POST['action'] == 'add')
            {
                $song_date = $_POST['song_date'];
                if (empty($song_date)) $song_date = current_time('mysql');

                $show = intval($_POST['timeslot_show']);
                $timeslot = $this->timeslots_repo->getTimeslotByDate($song_date, $show);

                if ($_POST['song_artist'] == '' || $_POST['song_name'] == '')
                {
                    $template->set('message', _('Artist or song name cannot be blank!'));
                }
                elseif ($song_date == 0)
                {
                    $template->set('message', _('Invalid time!'));
                }
                elseif ($timeslot == null)
                {
                    $template->set('message', _('No timeslot available for that time!'));
                }
                elseif ($this->songs_repo->insertSong(stripslashes($_POST['song_artist']),
                    stripslashes($_POST['song_name']), $song_date,
                    $timeslot)
                )
                {
                    $template->set('message', _('Song added successfully.'));
                }
                else
                {
                    $template->set('message', _('Failed to add song!'));
                }
            }
            elseif ($_POST['action'] == 'delete')
            {
                if ($this->songs_repo->deleteSong(intval($_POST['ID'])) !== false)
                {
                    $template->set('message', _('Song deleted successfully.'));
                }
                else
                {
                    $template->set('message', _('Unable to delete song! Unless you are an admin, '
                        . 'you are only able to delete songs for shows you broadcast.'));
                }
            }
        }

        $current_user = wp_get_current_user();
        $template->set('shows', $this->shows_repo->getShowsByUser($current_user->ID));
        $template->set('ordering', get_user_meta(wp_get_current_user()->ID, 'timber_log_form_field_ordering', true));

        $archives = new Archives($this->shows_repo, $this->songs_repo);
        $template->set('archives', $archives->render());

        $template->display();
    }
}