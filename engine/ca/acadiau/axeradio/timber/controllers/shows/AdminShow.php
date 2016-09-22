<?php namespace ca\acadiau\axeradio\timber\controllers\shows;

use ca\acadiau\axeradio\common\Template as Template;
use ca\acadiau\axeradio\timber\Controller as Controller;
use ca\acadiau\axeradio\timber\repositories\Categories as Categories;
use ca\acadiau\axeradio\timber\repositories\Shows as Shows;
use ca\acadiau\axeradio\timber\repositories\Timeslots as Timeslots;
use ca\acadiau\axeradio\timber\repositories\Users as Users;

/**
 * Admin operations on shows.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class AdminShow extends Controller
{
    private $categories_repo;
    private $shows_repo;
    private $timeslots_repo;
    private $users_repo;

    public function __construct(Categories $categories_repo, Shows $shows_repo,
                                Timeslots $timeslots_repo, Users $users_repo)
    {
        $this->categories_repo = $categories_repo;
        $this->shows_repo = $shows_repo;
        $this->timeslots_repo = $timeslots_repo;
        $this->users_repo = $users_repo;
    }

    public function show()
    {
        if ($_GET['action'] == 'edit' && !empty($_GET['id']))
        {
            $template = new Template('show_edit');
        }
        elseif ($_GET['action'] == 'delete' && !empty($_GET['id']))
        {
            $template = new Template('show_delete');
        }
        else
        {
            $template = new Template('show_add');
        }

        $template->set('categories', $this->categories_repo->getAllCategories());

        if (!empty($_POST['action']))
        {
            if ($_POST['action'] == 'add')
            {
                $show_name = stripslashes($_POST['show_name']);
                $show_category = intval($_POST['show_category']);
                $show_description = stripslashes($_POST['show_description']);
                $show_facebook_url = stripslashes($_POST['show_facebook_url']);
                try
                {
                    $this->shows_repo->insertShow($show_name, $show_category,
                                                  $show_description,
                                                  $show_facebook_url);
                    $template->set('message', _('Show added successfully.'));
                }
                catch (\Exception $e)
                {
                    $template->set('message', _('Failed to add show!')
                                              . ' ' . $e->getMessage());
                }
            }
            elseif ($_POST['action'] == 'edit')
            {
                $show_name = stripslashes($_POST['show_name']);
                $show_description = stripslashes($_POST['show_description']);
                $show_facebook_url = stripslashes($_POST['show_facebook_url']);
                if ($this->shows_repo->updateShow(intval($_POST['ID']), $show_name,
                    intval($_POST['show_category']), $show_description, $show_facebook_url)
                )
                {
                    $template->set('message', _('Show edited successfully.'));
                }
                else
                {
                    $template->set('message', _('Failed to edit show!'));
                }
            }
            elseif ($_POST['action'] == 'delete')
            {
                if ($this->shows_repo->deleteShow(intval($_POST['ID'])) != false)
                {
                    $template->set('message', _('Show archived successfully.'));
                }
                else
                {
                    $template->set('message', _('Failed to archive show!'));
                }
            }
            elseif ($_POST['action'] == 'timeslot_add')
            {
                if ($this->timeslots_repo->insertTimeslot($_POST['ID'], $_POST['day'],
                    $_POST['start'] . ':' . $_POST['start_offset'],
                    $_POST['end'] . ':' . $_POST['end_offset'])
                )
                {
                    $template->set('message', _('Timeslot added successfully.'));
                }
                else
                {
                    $template->set('message', _('Failed to add timeslot!'));
                }
            }
            elseif ($_POST['action'] == 'timeslot_delete')
            {
                if ($this->timeslots_repo->deleteTimeslot(intval($_POST['ID'])) != false)
                {
                    $template->set('message', _('Timeslot deleted successfully.'));
                }
                else
                {
                    $template->set('message', _('Failed to delete timeslot!'));
                }
            }
            elseif ($_POST['action'] == 'user_add')
            {
                if ($this->users_repo->addUserToShow(intval($_POST['ID']),
                    intval($_POST['user']))
                )
                {
                    $template->set('message', _('User added successfully.'));
                }
                else
                {
                    $template->set('message', _('Failed to add user!'));
                }
            }
            elseif ($_POST['action'] == 'user_delete')
            {
                if ($this->users_repo->removeUserFromShow(intval($_POST['show']),
                    intval($_POST['ID'])) !== false
                )
                {
                    $template->set('message', _('User deleted successfully.'));
                }
                else
                {
                    $template->set('message', _('User to delete timeslot!'));
                }
            }
        }

        if (!empty($_GET['id']))
        {
            $id = intval($_GET['id']);
            $show = $this->shows_repo->getShowById($id);
            if ($show != null)
            {
                $show->broadcasters = $this->shows_repo->getShowBroadcasters($id);
                $show->timeslots = $this->shows_repo->getShowTimeslots($id);
            }
            $template->set('show', $show);
        }

        $template->display();
    }
}