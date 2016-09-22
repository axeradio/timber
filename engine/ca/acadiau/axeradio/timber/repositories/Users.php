<?php namespace ca\acadiau\axeradio\timber\repositories;

/**
 * User retrieval.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class Users extends Repository
{
    /**
     * Associate a WordPress user with a show.
     *
     * @param int $show the database ID of the show
     * @param int $user the database ID of the user
     * @return boolean false if the user could not be associated
     */
    public function addUserToShow($show, $user)
    {
        return $this->wpdb->insert($this->wpdb->timber_show_users,
            array(
                'show' => $show,
                'user' => $user,
            ));
    }

    /**
     * Disassociate a user from a show.
     *
     * @param int $show the database ID of the show
     * @param int $user the database ID of the user
     * @return boolean false if the user could not be disassociated
     */
    public function removeUserFromShow($show, $user)
    {
        return $this->wpdb->query(
            $this->wpdb->prepare(
                'DELETE FROM `' . $this->wpdb->timber_show_users . '` '
                    . 'WHERE `' . $this->wpdb->timber_show_users . '`.`user` = %d '
                    . 'AND `' . $this->wpdb->timber_show_users . '`.`show` = %d',
                $user, $show));
    }
}