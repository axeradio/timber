<?php namespace ca\acadiau\axeradio\timber\repositories;

/**
 * Icecast stats.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class Icecast extends Repository
{
    /**
     * Get listenership stats for the last day.
     *
     * @return array listenership stast for the last day
     */
    public function getLastDayClientCounts($instantaneous_stats = false)
    {
        $query = <<<SQL
SELECT
    `{$this->wpdb->timber_icecast_stats}`.`date`,
    `{$this->wpdb->timber_icecast_stats}`.`clients`
FROM `{$this->wpdb->timber_icecast_stats}`
SQL;
        if ($instantaneous_stats)
            $query .= <<<SQL
WHERE `{$this->wpdb->timber_icecast_stats}`.`date` >= SUBDATE(NOW(), INTERVAL 1 DAY)
    AND `{$this->wpdb->timber_icecast_stats}`.`date` < NOW()
SQL;
        else
            $query .= <<<SQL
WHERE `{$this->wpdb->timber_icecast_stats}`.`date` >= SUBDATE(SUBDATE(NOW(), INTERVAL 1 DAY), INTERVAL 1 HOUR)
    AND `{$this->wpdb->timber_icecast_stats}`.`date` < SUBDATE(NOW(), INTERVAL 1 HOUR)
SQL;
        $query .= <<<SQL
ORDER BY `{$this->wpdb->timber_icecast_stats}`.`date` ASC
SQL;

        return $this->wpdb->get_results($query);
    }
}