<?php namespace ca\acadiau\axeradio\timber\repositories;

/**
 * Charting.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class Charts extends Repository
{
    /**
     * Get a list of top songs for the given date range, inclusive.
     *
     * @return array a list of top songs for the given date range
     */
    public function getTopSongsByDateRange($low, $high, $limit = 10)
    {
        return $this->wpdb->get_results($this->wpdb->prepare(<<<SQL
SELECT
    COUNT(`ID`) AS `plays`,
    `{$this->wpdb->timber_songs}`.`song_artist`,
    `{$this->wpdb->timber_songs}`.`song_name`,
    MAX(`{$this->wpdb->timber_songs}`.`song_date`) AS `song_date`
FROM `{$this->wpdb->timber_songs}`
WHERE `{$this->wpdb->timber_songs}`.`song_date` >= %s
  AND `{$this->wpdb->timber_songs}`.`song_date` <= %s
GROUP BY
    `song_artist`,
    `song_name`
ORDER BY `plays` DESC, `song_date` DESC
LIMIT %d
SQL
        , $low, $high, $limit));
    }

    /**
     * Get a list of top songs for the current month.
     *
     * @return array a list of top songs for the current month
     */
    public function getTopSongsByMonth()
    {
        return $this->getTopSongsByDateRange(date('Y-m-01'), date('Y-m-t'));
    }

    /**
     * Get a list of top artists for the given date range, inclusive.
     *
     * @return array a list of top artists for the given date range
     */
    public function getTopArtistsByDateRange($low, $high, $limit = 10)
    {
        return $this->wpdb->get_results($this->wpdb->prepare(<<<SQL
SELECT
    COUNT(`ID`) AS `plays`,
    `{$this->wpdb->timber_songs}`.`ID`,
    `{$this->wpdb->timber_songs}`.`song_artist`,
    MAX(`{$this->wpdb->timber_songs}`.`song_date`) AS `song_date`
FROM `{$this->wpdb->timber_songs}`
WHERE `{$this->wpdb->timber_songs}`.`song_date` >= %s
  AND `{$this->wpdb->timber_songs}`.`song_date` <= %s
GROUP BY `song_artist`
ORDER BY `plays` DESC, `song_date` DESC
LIMIT %d
SQL
    , $low, $high, $limit));
    }

    /**
     * Get a list of top artists for the current month.
     *
     * @return array a list of top artists for the current month
     */
    public function getTopArtistsByMonth()
    {
        return $this->getTopArtistsByDateRange(date('Y-m-01'), date('Y-m-t'));
    }
}