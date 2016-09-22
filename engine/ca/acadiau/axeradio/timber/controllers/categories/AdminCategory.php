<?php namespace ca\acadiau\axeradio\timber\controllers\categories;

use ca\acadiau\axeradio\common\Template as Template;
use ca\acadiau\axeradio\timber\Controller as Controller;
use ca\acadiau\axeradio\timber\repositories\Categories as Categories;

/**
 * Admin operations on genres.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class AdminCategory extends Controller
{
    private $categories_repo;

    public function __construct(Categories $cateogies_repo)
    {
        $this->categories_repo = $cateogies_repo;
    }

    public function show()
    {
        if (!empty($_GET['id']))
            $id = intval($_GET['id']);

        if ($_GET['action'] == 'edit' && isset($id))
        {
            $template = new Template('category_edit');
        }
        elseif ($_GET['action'] == 'delete' && isset($id))
        {
            $template = new Template('category_delete');
            $template->set('categories', $this->categories_repo->getAllCategoriesExcept($id));
        }
        else
        {
            $template = new Template('category_add');
        }

        if (!empty($_POST['action']))
        {
            if ($_POST['action'] == 'add')
            {
                if ($this->categories_repo->insertCategory(
                    $_POST['category_name'], $_POST['category_color'])
                )
                    $template->set('message', _('Genre added successfully.'));
                else
                    $template->set('message', _('Failed to add genre!'));
            }
            elseif ($_POST['action'] == 'edit')
            {
                if ($this->categories_repo->updateCategory(
                    $_POST['ID'], $_POST['category_name'],
                    $_POST['category_color'])
                )
                    $template->set('message', _('Genre edited successfully.'));
                else
                    $template->set('message', _('Failed to edit genre!'));
            }
            elseif ($_POST['action'] == 'delete')
            {
                if ($this->categories_repo->deleteCategory(
                    intval($_POST['ID']), intval($_POST['show_category']))
                )
                    $template->set('message', _('Genre deleted successfully.'));
                else
                    $template->set('message', _('Failed to delete genre!'));
            }
        }

        if (isset($id))
            $template->set('category', $this->categories_repo->getCategoryById($id));

        $template->display();
    }
}