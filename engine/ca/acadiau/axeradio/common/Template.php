<?php namespace ca\acadiau\axeradio\common;

/**
 * A file-based templates.
 *
 * @author scoleman
 * @date 2013-01-29
 */
class Template
{
    private $template;
    private $vars;

    public function __construct($template)
    {
        $template = AXEPATH . 'templates/' . $template . '.html';
        if (file_exists($template))
            $this->template = $template;
        else throw new \Exception('Unknown templates ' . $template);

        $this->vars = array();
    }

    /**
     * Set a templates variable.
     *
     * @param $key string the variable name
     * @param $value mixed the variable value
     * @return Template the templates
     */
    public function set($key, $value)
    {
        $this->vars[$key] = $value;
        return $this;
    }

    /**
     * Set multiple templates variables at once.
     *
     * @param $array array an associative array
     * @return Template the templates
     */
    public function setVars($array)
    {
        $this->vars = array_merge($this->vars, $array);
        return $this;
    }

    /**
     * Display the templates.
     */
    public function display()
    {
        extract($this->vars);
        include($this->template);
    }

    /**
     * Render the templates to a string.
     *
     * @return string the rendered templates
     */
    public function render()
    {
        ob_start();
        $this->display();
        return ob_get_clean();
    }
}