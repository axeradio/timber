<?php namespace ca\acadiau\axeradio\timber\controllers\categories;

use ca\acadiau\axeradio\common\Template as Template;
use ca\acadiau\axeradio\timber\Controller as Controller;
use ca\acadiau\axeradio\timber\repositories\Categories as Categories;

/**
 * Category list.
 *
 * @author Samuel Coleman <105709c@acadiau.ca>
 */
class AllCategories extends Controller
{
    private $categories_repo;

    public function __construct(Categories $categories_repo)
    {
        $this->categories_repo = $categories_repo;
    }

    public function show()
    {
        $template = new Template('all_categories');
        $template->set('categories', $this->categories_repo->getAllCategories());
        $template->display();
    }
}