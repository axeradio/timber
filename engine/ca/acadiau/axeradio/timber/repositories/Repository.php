<?php namespace ca\acadiau\axeradio\timber\repositories;

global $wpdb;

$wpdb->timber_show_users = $wpdb->prefix . 'timber_show_users';
$wpdb->timber_show_categories = $wpdb->prefix . 'timber_show_categories';
$wpdb->timber_shows = $wpdb->prefix . 'timber_shows';
$wpdb->timber_timeslots = $wpdb->prefix . 'timber_timeslots';
$wpdb->timber_songs = $wpdb->prefix . 'timber_songs';
$wpdb->timber_icecast_stats = $wpdb->prefix . 'timber_icecast_stats';

/**
 * Provides database access.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class Repository
{
    /*
     * To ease development, as the schema may change multiple times through the development of one version,
     * revision number is independent of plugin version.
     */
    const DB_VERSION = 3;

    protected $wpdb;

    /**
     * Construct the repository.
     *
     * @param object $wpdb database instance
     */
    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;
    }

    /**
     * Set up the database schema and some options.
     */
    public function install()
    {
        if (get_option('timber_db_version') != Repository::DB_VERSION)
        {
            $tables = <<<SQL
CREATE TABLE `{$this->wpdb->timber_show_users}` (
    `show` bigint(20) unsigned NOT NULL,
    `user` bigint(20) unsigned NOT NULL,
    KEY `user` (`show`),
    KEY `show` (`user`)
);

CREATE TABLE `{$this->wpdb->timber_show_categories}` (
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `category_name` varchar(255) NOT NULL,
    `category_color` mediumint(8) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`ID`)
);

CREATE TABLE `{$this->wpdb->timber_shows}` (
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `show_category` bigint(20) unsigned NOT NULL,
    `show_name` varchar(255) NOT NULL,
    `show_description` text NOT NULL,
    `show_facebook_url` text NOT NULL,
    `show_status` char(1) NOT NULL DEFAULT 'A',
    PRIMARY KEY (`ID`)
);

CREATE TABLE `{$this->wpdb->timber_timeslots}` (
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `timeslot_show` bigint(20) unsigned NOT NULL DEFAULT '0',
    `timeslot_day` tinyint(1) NOT NULL,
    `timeslot_start` time NOT NULL,
    `timeslot_end` time NOT NULL,
    `timeslot_status` char(1) NOT NULL DEFAULT 'A',
    PRIMARY KEY (`ID`)
);

CREATE TABLE `{$this->wpdb->timber_songs}` (
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `song_timeslot` bigint(20) unsigned NOT NULL DEFAULT '0',
    `song_artist` varchar(255) NOT NULL DEFAULT '',
    `song_name` varchar(255) NOT NULL DEFAULT '',
    `song_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`ID`)
);

CREATE TABLE `{$this->wpdb->timber_icecast_stats}` (
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `clients` int(10) unsigned NOT NULL DEFAULT '0',
    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`ID`)
);
SQL;

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($tables);
        }

        add_option('timber_week_start', '0');
        add_option('timber_week_end', '6');
        add_option('timber_day_start_hour', '10');
        add_option('timber_day_end_hour', '22');

        update_option('timber_db_version', Repository::DB_VERSION);
    }
}