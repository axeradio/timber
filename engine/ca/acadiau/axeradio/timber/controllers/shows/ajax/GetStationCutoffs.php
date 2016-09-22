<?php namespace ca\acadiau\axeradio\timber\controllers\shows\ajax;

use ca\acadiau\axeradio\timber\Controller as Controller;

/**
 * Get information about the station schedule day cutoffs in JSON form.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class GetStationCutoffs extends Controller
{
    public function show()
    {
        header('Content-Type: application/json');

        $cutoffs = array(
            'start_hour' => get_option('timber_day_start_hour'),
            'end_hour' => get_option('timber_day_end_hour'));
        die(json_encode($cutoffs));
    }
}