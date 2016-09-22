<?php namespace ca\acadiau\axeradio\timber;

/**
 * A controller.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
abstract class Controller
{
    public abstract function show();

    public function render()
    {
        ob_start();
        $this->show();
        return ob_get_clean();
    }
}
