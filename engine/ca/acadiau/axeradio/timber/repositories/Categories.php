<?php namespace ca\acadiau\axeradio\timber\repositories;

/**
 * Genre retrieval.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class Categories extends Repository
{
    /**
     * Get all genres.
     *
     * @return array all genres
     */
    public function getAllCategories()
    {
        return $this->wpdb->get_results(<<<SQL
SELECT
    `{$this->wpdb->timber_show_categories}`.`ID`,
    `{$this->wpdb->timber_show_categories}`.`category_name`,
    `{$this->wpdb->timber_show_categories}`.`category_color`
FROM `{$this->wpdb->timber_show_categories}`
ORDER BY `{$this->wpdb->timber_show_categories}`.`category_name`
SQL
        );
    }

    /**
     * Get a genre by its database ID.
     *
     * @param int $id the ID of the genre
     * @return object the genre
     */
    public function getCategoryById($id)
    {
        return $this->wpdb->get_row(<<<SQL
SELECT
    `{$this->wpdb->timber_show_categories}`.`ID`,
    `{$this->wpdb->timber_show_categories}`.`category_name`,
    `{$this->wpdb->timber_show_categories}`.`category_color`
FROM `{$this->wpdb->timber_show_categories}`
WHERE `{$this->wpdb->timber_show_categories}`.`ID` = '$id'
SQL
        );
    }

    /**
     * Get all genres except for one given by its database ID.
     *
     * @param int $exception the ID of the genre to exclude
     * @return array all genres except the exception
     */
    public function getAllCategoriesExcept($exception)
    {
        return $this->wpdb->get_results(<<<SQL
SELECT
    `{$this->wpdb->timber_show_categories}`.`ID`,
    `{$this->wpdb->timber_show_categories}`.`category_name`,
    `{$this->wpdb->timber_show_categories}`.`category_color`
FROM `{$this->wpdb->timber_show_categories}`
WHERE `{$this->wpdb->timber_show_categories}`.`ID` <> '$exception'
ORDER BY `{$this->wpdb->timber_show_categories}`.`category_name`
SQL
        );
    }

    /**
     * Insert a genre.
     *
     * @param string $name the name of the genre
     * @param string $color the colour of the genre, in hexadecimal form
     * @return boolean false if the genre could not be inserted
     */
    public function insertCategory($name, $color)
    {
        return $this->wpdb->insert($this->wpdb->timber_show_categories,
            array(
                'category_name' => $name,
                'category_color' => hexdec($color)
            ));
    }

    /**
     * Update a genre.
     *
     * @param int $id the database ID of the genre to update
     * @param string $name the new name of the genre
     * @param string $color the new colour of the genre, in hexadecimal form
     * @return boolean false if the genre could not be updated
     */
    public function updateCategory($id, $name, $color)
    {
        return $this->wpdb->update($this->wpdb->timber_show_categories,
            array(
                'category_name' => $name,
                'category_color' => hexdec($color)
            ),
            array('ID' => $id));
    }

    /**
     * Delete a genre.
     *
     * @param int $id the database ID of the genre
     * @param int $replacement the database ID of the genre to which all genres matching the genre being deleted
     * should be assigned
     * @return bool false if the genre could not be deleted
     */
    public function deleteCategory($id, $replacement)
    {
        return
            $this->wpdb->update($this->wpdb->timber_shows,
                array(
                    'show_category' => $replacement
                ),
                array('show_category' => $id))
            && $this->wpdb->query(
                $this->wpdb->prepare(<<<SQL
DELETE FROM `{$this->wpdb->timber_show_categories}`
WHERE `{$this->wpdb->timber_show_categories}`.`ID` = %d
SQL
                    , $id));
    }
}