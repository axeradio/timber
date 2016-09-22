<?php namespace ca\acadiau\axeradio\timber\controllers\shows;

use ca\acadiau\axeradio\common\Template as Template;
use ca\acadiau\axeradio\timber\Controller as Controller;
use ca\acadiau\axeradio\timber\repositories\Categories as Categories;
use ca\acadiau\axeradio\timber\repositories\Shows as Shows;
use ca\acadiau\axeradio\timber\repositories\Songs as Songs;

/**
 * Show list.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class AllShows extends Controller
{
    private $categories_repo;
    private $shows_repo;
    private $songs_repo;

    public function __construct(Categories $categories_repo, Shows $shows_repo, Songs $songs_repo)
    {
        $this->categories_repo = $categories_repo;
        $this->shows_repo = $shows_repo;
        $this->songs_repo = $songs_repo;
    }

    public function show()
    {
        $template = new Template('all_shows');

        $shows = $this->shows_repo->getAllShows();

        foreach ($shows as $show)
        {
            $show->broadcasters = $this->shows_repo->getShowBroadcasters($show->ID);
            $show->timeslots = $this->shows_repo->getShowTimeslots($show->ID);
            $show->song_count = $this->songs_repo->getSongCountByShow($show->ID);
            $show->week_count = $this->songs_repo->getRecentSongCountByShow($show->ID);
        }

        $template->set('shows', $shows);
        $template->set('schedule', self::generateSchedule());

        $template->display();
    }

    private function generateSchedule()
    {
        $shows = $this->shows_repo->getAllShowsByTimeslot();
        $categories = $this->categories_repo->getAllCategories();

        $day_start = self::timeslotToDecimal(get_option('timber_day_start_hour'));
        $day_end = self::timeslotToDecimal(get_option('timber_day_end_hour'));
//        $week_start = get_option('timber_week_start');
//        $week_end = get_option('timber_week_end');

        $table = array();
        foreach ($shows as $show)
        {
            $timeslot = self::timeslotToDecimal($show->timeslot_start);
            $timeslot_end = self::timeslotToDecimal($show->timeslot_end);
            $rows = $timeslot_end - $timeslot;
            $table[$timeslot][$show->timeslot_day]
                = array('name' => $show->show_name, 'rowspan' => $rows * 2, 'color' => $show->category_color);
            for ($i = .5; $i < $rows; $i += .5)
            {
                $nullslot = sprintf('%1.1f', $timeslot + $i);
                if (!isset($table[$nullslot][$show->timeslot_day]))
                    $table[$nullslot][$show->timeslot_day] = '';
            }
        }

        $days = array(6, 0, 1, 2, 3, 4, 5);

        $ret = '
<table class="schedule" border="1">
    <thead>
        <tr>
            <th width="8%">' . _('Time') . '</th>
            <th width="13%">' . _('Sunday') . '</th>
            <th width="13%">' . _('Monday') . '</th>
            <th width="13%">' . _('Tuesday') . '</th>
            <th width="13%">' . _('Wednesday') . '</th>
            <th width="13%">' . _('Thursday') . '</th>
            <th width="13%">' . _('Friday') . '</th>
            <th width="13%">' . _('Saturday') . '</th>
        </tr>
    </thead>
    <tbody>';
        for ($i = $day_start; $i <= $day_end; $i = sprintf('%1.1f', $i + .5))
        {
            $ret .= '
        <tr>
            <td>' . self::decimalToTimeslot($i) . '</td>';
            foreach ($days as $j)
            {
                if (isset($table[$i][$j]) && !empty($table[$i][$j]))
                {
                    $ret .= '
            <td rowspan="' . $table[$i][$j]['rowspan'] . '" style="background-color: ' . self::intToColor($table[$i][$j]['color']) . '; color: ' . self::getLabelColor($table[$i][$j]['color']) . '; text-align: center;">';
                    $ret .= '<a href="#' . preg_replace('/[^a-z]/', '', strtolower($table[$i][$j]['name'])) . '">';
                    $ret .= $table[$i][$j]['name'];
                    $ret .= '</a></td>';
                }
                elseif (!isset($table[$i][$j]))
                {
                    $ret .= '
            <td>&nbsp;</td>';
                }
            }
            $ret .= '
        </tr>';
        }
        $ret .= <<<HTML

    </tbody>
</table>
<h4>Genre Key</h4>
<table style="width: 65%">
    <tbody>
HTML;

        $rows = count($categories) / 3;

        for ($i = 0; $i < $rows; $i++)
        {
            $ret .= '
        <tr>';
            for ($j = 0; $j < 3; $j++)
            {
                $category = $categories[($i * 3) + $j];
                if ($category)
                {
                    $ret .= '
            <td style="background-color: ' . self::intToColor($category->category_color) . '; color: ' .
                        self::getLabelColor($category->category_color) . '; text-align: center;">' .
                        $category->category_name . '</td>';
                }
                else
                {
                    $ret .= '
            <td>&nbsp;</td>';
                }
            }
            $ret .= '
        </tr>';
        }
        $ret .= '
    </tbody>
</table>
';

        return $ret;
    }

    public static function timeslotToDecimal($t)
    {
        $d = str_replace(':', '.', substr($t, 0, 5));
        return sprintf('%1.1f', intval($d) + (($d - floor($d)) * 100 / 60));
    }

    public static function decimalToTimeslot($d)
    {
        if ($d - floor($d) == 0)
            return intval($d) . ':00';
        return intval($d) . ':' . sprintf('%2d', 60 * ($d - floor($d)));
    }

    public static function intToColor($col)
    {
        return '#' . str_pad(dechex($col), 6, '0', STR_PAD_LEFT);
    }

    public static function getLabelColor($dec, $dark = '#000000', $light = '#ffffff')
    {
        $hex = str_pad(dechex($dec), 6, '0', STR_PAD_LEFT);
        $red = hexdec(substr($hex, 0, 2));
        $green = hexdec(substr($hex, 2, 4));
        $blue = hexdec(substr($hex, 4, 6));
        $brightness = ($red * 0.3 + $green * .59 + $blue * .11) / 38770.2;
        return ($brightness) > 0.6 ? $dark : $light;
    }
}