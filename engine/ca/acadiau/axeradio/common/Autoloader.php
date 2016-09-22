<?php namespace ca\acadiau\axeradio\common;

/**
 * Class autoloader.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class Autoloader
{
    private $path;

    /**
     * Construct the loader.
     *
     * @param $path string the base path under which class files are located
     */
    public function __construct($path)
    {
        if (substr($path, -1) !== '/')
            $path .= '/';
        $this->path = $path;
    }

    /**
     * Load a class.
     *
     * @param $name string the fully-qualified name of the class to load
     */
    public function load($name)
    {
        $file = $this->path . str_replace('\\', DIRECTORY_SEPARATOR, $name) . '.php';
        if (is_readable($file))
            include($file);
    }
}