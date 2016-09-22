<?php namespace ca\acadiau\axeradio\timber\repositories;

/**
 * Show retrieval.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class Shows extends Repository
{
    /**
     * Get all shows.
     *
     * @return array all shows
     */
    public function getAllShows()
    {
        return $this->wpdb->get_results(<<<SQL
SELECT
    `{$this->wpdb->timber_shows}`.`ID`,
    `{$this->wpdb->timber_shows}`.`show_name`,
    `{$this->wpdb->timber_shows}`.`show_status`,
    `{$this->wpdb->timber_shows}`.`show_description`,
    `{$this->wpdb->timber_shows}`.`show_facebook_url`,
    `{$this->wpdb->timber_show_categories}`.`category_name`
FROM `{$this->wpdb->timber_shows}`
LEFT JOIN `{$this->wpdb->timber_show_categories}`
    ON `{$this->wpdb->timber_shows}`.`show_category` = `{$this->wpdb->timber_show_categories}`.`ID`
WHERE
    `{$this->wpdb->timber_shows}`.`show_status` <> 'D'
ORDER BY `{$this->wpdb->timber_shows}`.`show_name`
SQL
        );
    }

    /**
     * Get all shows with active timeslots.
     *
     * @return array all shows with active timeslots
     */
    public function getAllShowsWithTimeslots()
    {
        return $this->wpdb->get_results(<<<SQL
SELECT
    `{$this->wpdb->timber_shows}`.`ID`,
    `{$this->wpdb->timber_shows}`.`show_name`,
    `{$this->wpdb->timber_shows}`.`show_status`,
    `{$this->wpdb->timber_shows}`.`show_description`,
    `{$this->wpdb->timber_shows}`.`show_facebook_url`,
    `{$this->wpdb->timber_show_categories}`.`category_name`
FROM `{$this->wpdb->timber_timeslots}`
LEFT JOIN `{$this->wpdb->timber_shows}`
    ON `{$this->wpdb->timber_timeslots}`.`timeslot_show` = `{$this->wpdb->timber_shows}`.`ID`
LEFT JOIN `{$this->wpdb->timber_show_categories}`
    ON `{$this->wpdb->timber_shows}`.`show_category` = `{$this->wpdb->timber_show_categories}`.`ID`
WHERE
    `{$this->wpdb->timber_shows}`.`show_status` <> 'D'
    AND `{$this->wpdb->timber_timeslots}`.`timeslot_status` <> 'D'
GROUP BY `{$this->wpdb->timber_shows}`.`show_name`
ORDER BY `{$this->wpdb->timber_shows}`.`show_name`
SQL
        );
    }

    public function getAllShowsByTimeslot()
    {
        return $this->wpdb->get_results(<<<SQL
SELECT
    `{$this->wpdb->timber_shows}`.`show_name`,
    `{$this->wpdb->timber_show_categories}`.`category_color`,
    `{$this->wpdb->timber_timeslots}`.`timeslot_day`,
    `{$this->wpdb->timber_timeslots}`.`timeslot_start`,
    `{$this->wpdb->timber_timeslots}`.`timeslot_end`
FROM `{$this->wpdb->timber_timeslots}`
LEFT JOIN `{$this->wpdb->timber_shows}`
    ON `{$this->wpdb->timber_timeslots}`.`timeslot_show` = `{$this->wpdb->timber_shows}`.`ID`
LEFT JOIN `{$this->wpdb->timber_show_categories}`
    ON `{$this->wpdb->timber_shows}`.`show_category` = `{$this->wpdb->timber_show_categories}`.`ID`
WHERE
    `{$this->wpdb->timber_timeslots}`.`timeslot_status` = 'A'
    AND `{$this->wpdb->timber_shows}`.`show_status` = 'A'
ORDER BY `{$this->wpdb->timber_timeslots}`.`timeslot_start` ASC
SQL
        );
    }

    /**
     * Get all shows associated with a user.
     *
     * @param int $user the database ID of the user
     * @return array all shows associated with the user
     */
    public function getShowsByUser($user)
    {
        return $this->wpdb->get_results(<<<SQL
SELECT
    `{$this->wpdb->timber_shows}`.`ID`,
    `{$this->wpdb->timber_shows}`.`show_name`
FROM `{$this->wpdb->timber_show_users}`
LEFT JOIN `{$this->wpdb->timber_shows}`
    ON `{$this->wpdb->timber_shows}`.`ID` = `{$this->wpdb->timber_show_users}`.`show`
WHERE
    `{$this->wpdb->timber_show_users}`.`user` = '$user'
    AND `{$this->wpdb->timber_shows}`.`show_status` <> 'D'
ORDER BY `{$this->wpdb->timber_shows}`.`show_name`
SQL
        );
    }

    /**
     * Get a show by its ID.
     *
     * @param int $id the database ID of the show
     * @return object the show
     */
    public function getShowById($id)
    {
        return $this->wpdb->get_row(<<<SQL
SELECT
    `{$this->wpdb->timber_shows}`.`ID`,
    `{$this->wpdb->timber_shows}`.`show_name`,
    `{$this->wpdb->timber_shows}`.`show_category`,
    `{$this->wpdb->timber_shows}`.`show_description`,
    `{$this->wpdb->timber_shows}`.`show_facebook_url`,
    `{$this->wpdb->timber_show_categories}`.`category_name`,
    `{$this->wpdb->timber_show_categories}`.`category_color`
FROM `{$this->wpdb->timber_shows}`
LEFT JOIN `{$this->wpdb->timber_show_categories}`
    ON `{$this->wpdb->timber_show_categories}`.`ID` = `{$this->wpdb->timber_shows}`.`show_category`
WHERE
    `{$this->wpdb->timber_shows}`.`show_status` = 'A'
    AND `{$this->wpdb->timber_shows}`.`ID` = '$id'
SQL
        );
    }

    /**
     * Get all users associated with a show.
     *
     * @param int $show the database ID of the show
     * @return array all users associated with the show
     */
    public function getShowBroadcasters($show)
    {
        return $this->wpdb->get_results(<<<SQL
SELECT
    `{$this->wpdb->users}`.`ID`,
    `{$this->wpdb->users}`.`display_name`
FROM `{$this->wpdb->timber_show_users}`
LEFT JOIN `{$this->wpdb->users}`
    ON `{$this->wpdb->timber_show_users}`.`user` = `{$this->wpdb->users}`.`ID`
WHERE
    `{$this->wpdb->timber_show_users}`.`show` = '$show'
SQL
        );
    }

    /**
     * Get all timeslots associated with a show.
     *
     * @param int $show the database ID of the show
     * @return array all timeslots associated with the show
     */
    public function getShowTimeslots($show)
    {
        return $this->wpdb->get_results(<<<SQL
SELECT
    `{$this->wpdb->timber_timeslots}`.`ID`,
    `{$this->wpdb->timber_timeslots}`.`timeslot_day`,
    `{$this->wpdb->timber_timeslots}`.`timeslot_start`,
    `{$this->wpdb->timber_timeslots}`.`timeslot_end`
FROM `{$this->wpdb->timber_timeslots}`
WHERE
    `{$this->wpdb->timber_timeslots}`.`timeslot_show` = '$show'
    AND `{$this->wpdb->timber_timeslots}`.`timeslot_status` <> 'D'
ORDER BY
    `{$this->wpdb->timber_timeslots}`.`timeslot_day`,
    `{$this->wpdb->timber_timeslots}`.`timeslot_start`
SQL
        );
    }

    /**
     * Insert a show.
     *
     * @param string $name the name of the show
     * @param int $category the database ID of the show genre
     * @param string $description the description of the show
     * @param string $facebook_url the Facebook URL of the show
     * @throws \Exception if the show cannot be inserted
     */
    public function insertShow($name, $category, $description = null, $facebook_url = null)
    {
        if (!$this->wpdb->insert($this->wpdb->timber_shows,
            array(
                'show_name' => $name,
                'show_category' => $category,
                'show_description' => $description,
                'show_facebook_url' => $facebook_url,
                'show_status' => 'A'
            )))
        {
            throw new \Exception($this->wpdb->last_error);
        }
    }

    /**
     * Update a show. To render a show description or Facebook URL, you <em>must</em> pass a blank string as the value
     * for that parameter instead of null.
     *
     * @param int $id the ID of the show
     * @param string $name the new name of the show
     * @param int $category the new database ID of the show genre
     * @param string $description the new show description
     * @param string $facebook_url the Facebook URL of the show
     * @return boolean false if the genre could not be updated
     */
    public function updateShow($id, $name, $category, $description = null, $facebook_url = null)
    {
        $values = array(
            'show_name' => $name,
            'show_category' => $category
        );

        if ($description !== null)
            $values['show_description'] = $description;

        if ($facebook_url !== null)
            $values['show_facebook_url'] = $facebook_url;

        return $this->wpdb->update($this->wpdb->timber_shows,
            $values,
            array('ID' => $id));
    }

    /**
     * Delete a show.
     *
     * @param int $id the database ID of the show
     * @return bool false if the genre could not be deleted
     */
    public function deleteShow($id)
    {
        $this->wpdb->update($this->wpdb->timber_timeslots,
            array('timeslot_status' => 'D'),
            array('timeslot_show' => $id));
        return $this->wpdb->update($this->wpdb->timber_shows,
            array('show_status' => 'D'),
            array('ID' => $id));
    }
}
