<?php namespace ca\acadiau\axeradio\timber;

/**
 * This view is made for rendering.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class View
{
    /**
     * @var string the filename of the template relative to the executing script (i.e., immediately accessible)
     */
    private $name;
    /**
     * @var array an array of strings for substitution into the template
     */
    private $vars;

    /**
     * Instantiate a view.
     *
     * @param string $name the name of the template (filename, sans extension, relative to the "views" directory)
     * @throws \Exception when the requested view does not exist
     */
    public function __construct($name)
    {
        $name = ABSPATH . 'wp-content/plugins/timber/views/' . $name . '.php';
        if (is_readable($name))
            $this->name = $name;
        else
            throw new \Exception("Unknown view");
    }

    /**
     * Render the view. The rendering occurs in the context of this method call, so properties of the view instance will
     * will be accessible by the view itself. Setting such properties is the recommended way to inform the view of
     * dynamic content.
     */
    public function render()
    {
        ob_start();
        $vars = $this->vars;
        include $this->name;
        return ob_get_flush();
    }

    /**
     * Output the view. The rendering occurs in the context of this method call, so properties of the view instance will
     * will be accessible by the view itself. Setting such properties is the recommended way to inform the view of
     * dynamic content.
     */
    public function show()
    {
        $vars = $this->vars;
        include $this->name;
    }

    /**
     * Set a template field.
     *
     * @param string $var the field
     * @param string $value its value
     */
    public function set($var, $value)
    {
        $this->vars[$var] = $value;
    }
}
