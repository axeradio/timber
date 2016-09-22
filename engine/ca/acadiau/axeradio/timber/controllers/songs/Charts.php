<?php namespace ca\acadiau\axeradio\timber\controllers\songs;

use ca\acadiau\axeradio\common\Template as Template;
use ca\acadiau\axeradio\timber\Controller as Controller;
use ca\acadiau\axeradio\timber\repositories\Charts as ChartsRepo;

/**
 * Logging and admin-area archives access.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class Charts extends Controller
{
    private $charts_repo;

    private $size;
    private $datatype;
    private $from;
    private $to;
    private $output;

    private $results;

    public function __construct(ChartsRepo $charts_repo)
    {
        $this->charts_repo = $charts_repo;

        $this->size = isset($_GET['size']) ? $_GET['size'] : 10;
        $this->datatype = isset($_GET['datatype']) ? $_GET['datatype'] : 'songs';
        $this->from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
        $this->to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-t');
        $this->output = isset($_GET['output']) ? $_GET['output'] : 'html';

        if ($this->datatype == 'songs')
            $this->results = $this->charts_repo->getTopSongsByDateRange($this->from, $this->to, $this->size);
        else if ($this->datatype = 'artists')
            $this->results = $this->charts_repo->getTopArtistsByDateRange($this->from, $this->to, $this->size);
    }

    public function show()
    {
        $template = new Template('charts');

        $template->set('size', $this->size);
        $template->set('datatype', $this->datatype);
        $template->set('from', $this->from);
        $template->set('to', $this->to);
        $template->set('output', $this->output);

        $template->set('results', $this->results);

        $template->display();
    }
}