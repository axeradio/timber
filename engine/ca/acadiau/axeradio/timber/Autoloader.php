<?php namespace ca\acadiau\axeradio\timber;

/**
 * Timber autoloader.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class Autoloader
{
    public static function load($name)
    {
        $file = ABSPATH . 'wp-content/plugins/timber/engine/' . str_replace('\\', DIRECTORY_SEPARATOR, $name) . '.php';
        if (file_exists($file) && is_readable($file))
            include($file);
    }
}