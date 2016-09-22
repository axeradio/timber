<?php namespace ca\acadiau\axeradio\timber\repositories;

/**
 * Song retrieval.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class Songs extends Repository
{
    /**
     * Get a page worth of logged songs.
     *
     * @param int $page the page to return (default: 0, the first)
     * @param int $songs_per_page the number of songs per page
     * @return array the songs on the page
     */
    public function getSongsByPage($page = 0, $songs_per_page = 50)
    {
        $page_offset = $page * $songs_per_page;
        return $this->wpdb->get_results(<<<SQL
SELECT
    `{$this->wpdb->timber_songs}`.`ID`,
    `{$this->wpdb->timber_songs}`.`song_artist`,
    `{$this->wpdb->timber_songs}`.`song_name`,
    `{$this->wpdb->timber_songs}`.`song_date`,
    `{$this->wpdb->timber_shows}`.`show_name`
FROM `{$this->wpdb->timber_songs}`
LEFT JOIN `{$this->wpdb->timber_timeslots}`
    ON `{$this->wpdb->timber_timeslots}`.`ID` = `{$this->wpdb->timber_songs}`.`song_timeslot`
LEFT JOIN `{$this->wpdb->timber_shows}`
    ON `{$this->wpdb->timber_shows}`.`ID` = `{$this->wpdb->timber_timeslots}`.`timeslot_show`
ORDER BY `{$this->wpdb->timber_songs}`.`song_date` DESC
LIMIT $page_offset,$songs_per_page
SQL
        );
    }
    /**
     * Get a page worth of logged songs for a specific show.
     *
     * @param int $show the ID of the show
     * @param int $page the page to return (default: 0, the first)
     * @param int $songs_per_page the number of songs per page
     * @return array the songs on the page
     */
    public function getShowSongsByPage($show, $page = 0, $songs_per_page = 50)
    {
        $page_offset = $page * $songs_per_page;
        return $this->wpdb->get_results(<<<SQL
SELECT
    `{$this->wpdb->timber_songs}`.`ID`,
    `{$this->wpdb->timber_songs}`.`song_artist`,
    `{$this->wpdb->timber_songs}`.`song_name`,
    `{$this->wpdb->timber_songs}`.`song_date`,
    `{$this->wpdb->timber_shows}`.`show_name`
FROM `{$this->wpdb->timber_songs}`
LEFT JOIN `{$this->wpdb->timber_timeslots}`
    ON `{$this->wpdb->timber_timeslots}`.`ID` = `{$this->wpdb->timber_songs}`.`song_timeslot`
LEFT JOIN `{$this->wpdb->timber_shows}`
    ON `{$this->wpdb->timber_shows}`.`ID` = `{$this->wpdb->timber_timeslots}`.`timeslot_show`
WHERE `{$this->wpdb->timber_shows}`.`ID` = $show
ORDER BY `{$this->wpdb->timber_songs}`.`song_date` DESC
LIMIT $page_offset,$songs_per_page
SQL
        );
    }

    /**
     * Get a logged song.
     *
     * @param int $id the database ID of the song
     * @return object the song
     */
    public function getSongById($id)
    {
        return $this->wpdb->get_row(<<<SQL
SELECT
    `{$this->wpdb->timber_songs}`.`ID`,
    `{$this->wpdb->timber_songs}`.`song_artist`,
    `{$this->wpdb->timber_songs}`.`song_name`,
    `{$this->wpdb->timber_songs}`.`song_date`,
    `{$this->wpdb->timber_shows}`.`show_name`
FROM `{$this->wpdb->timber_songs}`
LEFT JOIN `{$this->wpdb->timber_timeslots}`
    ON `{$this->wpdb->timber_timeslots}`.`ID` = `{$this->wpdb->timber_songs}`.`song_timeslot`
LEFT JOIN `{$this->wpdb->timber_shows}`
    ON `{$this->wpdb->timber_shows}`.`ID` = `{$this->wpdb->timber_timeslots}`.`timeslot_show`
WHERE `{$this->wpdb->timber_songs}`.`ID` = '$id'
SQL
        );
    }

    /**
     * Get the total number of logged songs.
     *
     * @return int the total number of logged songs
     */
    public function getSongCount()
    {
        return $this->wpdb->get_var(<<<SQL
SELECT
    COUNT(`{$this->wpdb->timber_songs}`.`ID`)
FROM `{$this->wpdb->timber_songs}`
SQL
        );
    }

    /**
     * Get the total number of logged songs for a specific show.
     *
     * @param int $show the ID of the show
     * @return int the total number of logged songs
     */
    public function getShowSongCount($show)
    {
        return $this->wpdb->get_var(<<<SQL
SELECT
    COUNT(`{$this->wpdb->timber_songs}`.`ID`)
FROM `{$this->wpdb->timber_songs}`
LEFT JOIN `{$this->wpdb->timber_timeslots}`
    ON `{$this->wpdb->timber_timeslots}`.`ID` = `{$this->wpdb->timber_songs}`.`song_timeslot`
WHERE `{$this->wpdb->timber_timeslots}`.`timeslot_show` = $show
SQL
        );
    }

    /**
     * Get the total number of songs logged by a show.
     *
     * @param int $show the database ID of the show
     * @return int the total number of songs logged by the show
     */
    public function getSongCountByShow($show)
    {
        return $this->wpdb->get_var(<<<SQL
SELECT
    COUNT(`{$this->wpdb->timber_songs}`.`ID`)
FROM `{$this->wpdb->timber_songs}`
LEFT JOIN `{$this->wpdb->timber_timeslots}`
    ON `{$this->wpdb->timber_songs}`.`song_timeslot` = `{$this->wpdb->timber_timeslots}`.`ID`
LEFT JOIN `{$this->wpdb->timber_shows}`
    ON `{$this->wpdb->timber_timeslots}`.`timeslot_show` = `{$this->wpdb->timber_shows}`.`ID`
WHERE `{$this->wpdb->timber_shows}`.`ID` = '$show'
SQL
        );
    }

    /**
     * Get the total number of songs logged in the last timeslot of a show.
     *
     * @param int $show the database ID of the show
     * @return int the total number of songs logged in the last timeslot of the show
     */
    public function getRecentSongCountByShow($show)
    {
        return $this->wpdb->get_var(<<<SQL
SELECT
    COUNT(`{$this->wpdb->timber_songs}`.`ID`)
FROM `{$this->wpdb->timber_songs}`
LEFT JOIN `{$this->wpdb->timber_timeslots}`
    ON `{$this->wpdb->timber_songs}`.`song_timeslot` = `{$this->wpdb->timber_timeslots}`.`ID`
LEFT JOIN `{$this->wpdb->timber_shows}`
    ON `{$this->wpdb->timber_timeslots}`.`timeslot_show` = `{$this->wpdb->timber_shows}`.`ID`
WHERE `{$this->wpdb->timber_shows}`.`ID` = '$show'
AND `{$this->wpdb->timber_songs}`.`song_date` > DATE_SUB(NOW(), INTERVAL 1 WEEK)
SQL
        );
    }

    /**
     * Log a song.
     *
     * @param string $artist the song artist
     * @param string $name the song name
     * @param string $date the date and time at which the song was played
     * @param null $timeslot the database ID of the timeslot in which the song was played
     * @return bool false if the song could not be logged
     */
    public function insertSong($artist, $name, $date, $timeslot = null)
    {
        $timeslot_repository = new Timeslots($this->wpdb);

        if ($timeslot == null)
            $timeslot = $timeslot_repository->getTimeslotByDate($date);
        if (!$timeslot)
            return false;

        return $this->wpdb->insert($this->wpdb->timber_songs,
            array(
                'song_timeslot' => $timeslot->ID,
                'song_artist' => $artist,
                'song_name' => $name,
                'song_date' => $date
            ));
    }

    /**
     * Update a logged song.
     *
     * @param int $id the database ID of the song
     * @param string $artist the new song artist
     * @param string $name the new song name
     * @param string $date the date and time at which the song was played
     * @param null $timeslot the database ID of the timeslot in which the song was played
     * @return bool false if the song could not be updated
     */
    public function updateSong($id, $artist, $name, $date, $timeslot = null)
    {
        $user = wp_get_current_user();
        if (is_admin() || $this->wpdb->query(<<<SQL
SELECT `{$this->wpdb->timber_show_users}`.`user`
FROM `{$this->wpdb->timber_songs}`
LEFT JOIN `{$this->wpdb->timber_timeslots}`
ON `{$this->wpdb->timber_timeslots}`.`ID` = `{$this->wpdb->timber_songs}`.`song_timeslot`
LEFT JOIN `{$this->wpdb->timber_shows}`
ON `{$this->wpdb->timber_shows}`.`ID` = `{$this->wpdb->timber_timeslots}`.`timeslot_show`
LEFT JOIN `{$this->wpdb->timber_show_users}`
ON `{$this->wpdb->timber_show_users}`.`show` = `{$this->wpdb->timber_shows}`.`ID`
WHERE `{$this->wpdb->timber_songs}`.`ID` = '$id'
AND `{$this->wpdb->timber_show_users}`.`user` = '$user->ID'
SQL
        )
        )
        {
            $timeslot_repository = new Timeslots($this->wpdb);

            if ($timeslot == null)
            {
                $timeslot = $timeslot_repository->getTimeslotByDate($date);
                $timeslot = $timeslot->ID;
            }
            if (!$timeslot)
                return false;

            return $this->wpdb->update($this->wpdb->timber_songs,
                array(
                    'song_timeslot' => $timeslot,
                    'song_artist' => $artist,
                    'song_name' => $name,
                    'song_date' => $date
                ),
                array('ID' => $id));
        }
        else
            return false;
    }

    /**
     * Delete a song from the logs.
     *
     * @param int $id the database ID of the song
     * @return bool false if the song could not be deleted
     */
    public function deleteSong($id)
    {
        $user = wp_get_current_user();
        $user_has_show = $this->wpdb->query(
            $this->wpdb->prepare(<<<SQL
SELECT `{$this->wpdb->timber_show_users}`.`user`
FROM `{$this->wpdb->timber_songs}`
LEFT JOIN `{$this->wpdb->timber_timeslots}`
ON `{$this->wpdb->timber_timeslots}`.`ID` = `{$this->wpdb->timber_songs}`.`song_timeslot`
LEFT JOIN `{$this->wpdb->timber_shows}`
ON `{$this->wpdb->timber_shows}`.`ID` = `{$this->wpdb->timber_timeslots}`.`timeslot_show`
LEFT JOIN `{$this->wpdb->timber_show_users}`
ON `{$this->wpdb->timber_show_users}`.`show` = `{$this->wpdb->timber_shows}`.`ID`
WHERE `{$this->wpdb->timber_songs}`.`ID` = %d
        AND `{$this->wpdb->timber_show_users}`.`user` = %d
SQL
                , $id, $user->ID));
        if (is_admin() || $user_has_show)
            return $this->wpdb->query($this->wpdb->prepare(
                <<<SQL
DELETE FROM `{$this->wpdb->timber_songs}` WHERE `{$this->wpdb->timber_songs}`.`ID` = %d
SQL
            , $id));
        else
            return false;
    }
}