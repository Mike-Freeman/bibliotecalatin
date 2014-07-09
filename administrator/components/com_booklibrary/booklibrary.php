<?php

if (!defined('_VALID_MOS') && !defined('_JEXEC')) {
    die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');
}
/**
 *
 * @package BookLibrary
 * @copyright Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru); Rob de Cleen(rob@decleen.com); 
 * Homepage: http://www.ordasoft.com
 * @version: 3.0 Free
 *
 * */
include_once( /* dirname(__FILE__) */JPATH_COMPONENT_SITE . '/compat.joomla1.5.php' );
include_once( /* dirname(__FILE__) */JPATH_COMPONENT_SITE . '/functions.php' );

jimport('joomla.filesystem.folder');

// load language
$languagelocale = "";
$database->setQuery("SELECT title, lang_code FROM #__booklibrary_languages");
$languages = $database->loadObjectList();
$lang = JFactory::getLanguage();
foreach ($lang->getLocale() as $locale) {
    foreach ($languages as $language) {
        if ($locale == $language->title || $locale == $language->lang_code) {
            $mosConfig_lang = $locale;
            $languagelocale = $language->lang_code;
            break;
        }
    }
}

if ($languagelocale == '')
    $languagelocale = "en-GB";

$query = "SELECT c.const, cl.value_const ";
$query .= "FROM #__booklibrary_const_languages as cl ";
$query .= "LEFT JOIN #__booklibrary_languages AS l ON cl.fk_languagesid=l.id ";
$query .= "LEFT JOIN #__booklibrary_const AS c ON cl.fk_constid=c.id ";
$query .= "WHERE l.lang_code = '$languagelocale'";
$database->setQuery($query);
$langConst = $database->loadObjectList();

foreach ($langConst as $item) {
    define($item->const, $item->value_const);
}

$my = $GLOBALS['my'];
if (get_magic_quotes_gpc()) {

    function stripslashes_gpc(&$value) {
        $value = stripslashes($value);
    }

    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}

if (version_compare(JVERSION, "3.0.0", "ge")) {
    require_once ($mosConfig_absolute_path . "/administrator/components/com_booklibrary/toolbar.booklibrary.php");
}
$css = $mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css';
$mainframe = JFactory::getApplication();

jimport('joomla.html.pagination');

require_once ($mosConfig_absolute_path . "/components/com_booklibrary/booklibrary.class.php");
require_once ($mosConfig_absolute_path . "/administrator/components/com_booklibrary/booklibrary.html.php");
require_once ($mosConfig_absolute_path . "/components/com_booklibrary/booklibrary.class.language.php");
require_once ($mosConfig_absolute_path . "/components/com_booklibrary/booklibrary.class.lend.php");
require_once ($mosConfig_absolute_path . "/components/com_booklibrary/booklibrary.class.lend_request.php");
require_once ($mosConfig_absolute_path . "/components/com_booklibrary/booklibrary.main.categories.class.php");
require_once ($mosConfig_absolute_path . "/administrator/components/com_booklibrary/admin.booklibrary.class.ws.php");
require_once ($mosConfig_absolute_path . "/administrator/components/com_booklibrary/admin.booklibrary.class.impexp.php");
require_once ($mosConfig_absolute_path . "/administrator/components/com_booklibrary/admin.booklibrary.class.conf.php");

$GLOBALS['booklibrary_configuration'] = $booklibrary_configuration;
$GLOBALS['database'] = $database;
$GLOBALS['my'] = $my;
$GLOBALS['mosConfig_absolute_path'] = $mosConfig_absolute_path;
$table_prefix = $database->getPrefix(); // for J 1.6
$GLOBALS['table_prefix'] = $table_prefix; // for J 1.6
$GLOBALS['task'] = $task = mosGetParam($_REQUEST, 'task', '');
$GLOBALS['option'] = $option = mosGetParam($_REQUEST, 'option', 'com_booklibrary');

global $mosConfig_lang;
$cls_path = $mosConfig_absolute_path . "/components/com_booklibrary/booklibrary.class.php";
require_once ($cls_path);

$bid = mosGetParam($_POST, 'bid', array(0));
if ($task == 'edit' && $bid[0] == 0) {
    $bid = $_GET['bid'];
}

$section = mosGetParam($_REQUEST, 'section', 'courses');

// --------------- Debug mode ----------------
$booklibrary_configuration['debug'] = 0;
$task = JRequest::getVar('task', '', '', 'string', JREQUEST_ALLOWRAW);

if ($booklibrary_configuration['debug'] == '1') {
    echo "Task: " . $task . "<br />";
    print_r($_POST);
    echo "<hr>";
    print_r($_GET);
    echo "<hr /><br />";

    echo $task;
}

// --------------------------------------------
if (isset($section) && $section == 'categories') {

    switch ($task) {

        case "edit":
            editCategory($option, $bid[0]);
            break;

        case "add": // for 1.6 task='add' instead 'new' as in 1.5
            editCategory($option, 0);
            break;

        case "cancel":
            cancelCategory();
            break;

        case "save":
            saveCategory();
            break;

        case "remove":
            removeCategories($option, $bid);
            break;

        case "publish":
            publishCategories("com_booklibrary", $id, $bid, 1);
            break;

        case "unpublish":
            publishCategories("com_booklibrary", $id, $bid, 0);
            break;

        case "orderup":
            orderCategory($bid[0], -1);
            break;

        case "orderdown":
            orderCategory($bid[0], 1);
            break;

        case "accesspublic":
            accessCategory($bid[0], 0);
            break;

        case "accessregistered":
            accessCategory($bid[0], 1);
            break;

        case "accessspecial":
            accessCategory($bid[0], 2);
            break;

        case "show":
        default :
            showCategories();
    }
} elseif ((isset($section))
        && ($section == 'language_manager')
) {
    switch ($task) {

        case "edit" :
            editLanguageManager($option, $bid[0]);
            break;

        case "cancel":
            cancelLanguageManager();
            break;

        case "save":
            saveLanguageManager();
            break;

        default:
            showLanguageManager($option);
            break;
    }
} else {
    switch ($task) {

        case "publish_manage_review":
            publish_manage_review($bid[0], 1, $option);
            break;

        case "unpublish_manage_review":
            publish_manage_review($bid[0], 0, $option);
            break;

        case "categories":
            echo "now work $section=='categories , this part not work";
            exit;
            mosRedirect("index.php?option=categories&extension=com_booklibrary");
            break;

        case "add" : // for 1.6 task='add' instead 'new' as in 1.5
            editBook($option, 0);
            break;

        case "edit" :
            editBook($option, array_pop($bid));
            break;

        case "show_all":
            unsetCatId();
            break;

        case "refetchInfos" :
            refetchInfo($option, $bid);
            break;

        case "apply":
        case "save" :
            saveBook($option, $task);
            break;

        case "remove" :
            removeBooks($bid, $option);
            break;

        case "publish" :
            publishBooks($bid, 1, $option);
            break;

        case "unpublish" :
            publishBooks($bid, 0, $option);
            break;

        case "approve" :
            ApproveBooks($bid, 1, $option);
            break;

        case "unapprove" :
            ApproveBooks($bid, 0, $option);
            break;

        case "cancel" :
            cancelBook($option);
            break;

        case "bookorderdown" :
            orderBooks($bid[0], 1, $option);
            break;

        case "bookorderup" :
            orderBooks($bid[0], -1, $option);
            break;

        case "show_import_export" :
            importExportBooks($option);
            break;

        case "import" :
            import($option);
            break;

        case "export" :
            export($option);
            break;

        case "config_frontend" :
            configure_frontend($option);
            break;

//***************   begin for manage reviews   ***********************
         
      
//**********   end for manage suggestion   ***************************
        case "config_backend" :
            configure_backend($option);
            break;

        case "config_save_frontend" :
            configure_save_frontend($option);
            break;

        case "config_save_backend" :
            configure_save_backend($option);
            break;

        case "lend" :
            if (mosGetParam($_POST, 'save') == 1) {
                saveLend($option, $bid);
            } else {
                lend($option, $bid);
            }
            break;

        case "lend_requests" :
            lend_requests($option, $bid);
            break;

        case "accept_lend_requests" :
            accept_lend_requests($option, $bid);
            break;

        case "decline_lend_requests" :
            decline_lend_requests($option, $bid);
            break;

        case "about" :
            HTML_booklibrary :: about();
            break;

        case "show_info" :
            showInfo($option, $bid);
            break;

        case "lend_return" :
            if (mosGetParam($_POST, 'save') == 1) {
                saveLend_return($option, $bid);
            } else {
                lend_return($option, $bid);
            }
            break;

        case "edit_lend" :
            if (mosGetParam($_POST, 'save') == 1) {
                if (count($bid) > 1) {
                    echo "<script> alert('You must select only one item for edit'); window.history.go(-1); </script>\n";
                    exit();
                }
                saveLend($option, $bid, "edit_lend");
            } else {
                edit_lend($option, $bid);
            }
            break;



        case "delete_review" :
            $ids = explode(',', $bid[0]);
            delete_review($option, $ids[1]);
            editBook($option, $ids[0]);
            break;

        case "edit_review" :
            $ids = explode(',', $bid[0]);
            edit_review($option, $ids[1], $ids[0]);
            break;

        case "update_review" :
            $title = mosGetParam($_POST, 'title');
            $comment = mosGetParam($_POST, 'comment');
            $rating = mosGetParam($_POST, 'rating');
            $book_id = mosGetParam($_POST, 'book_id');
            $review_id = mosGetParam($_POST, 'review_id');
            update_review($title, $comment, $rating, $review_id);
            editBook($option, $book_id);
            break;

        case "cancel_review_edit" :
            $book_id = mosGetParam($_POST, 'book_id');
            editBook($option, $book_id);
            break;

//******   begin add for button print in Manager Books   ***********
        case "print_books":
            print_books($option);
            showBooks($option);
            break;

        case "print_item":
            print_item($option);
            break;

//******   end add for button print in Manager Books   *************
        default :
            showBooks($option);
            break;
    }
}


/*
 * CAT_Utils Class
 */

class CAT_Utils {

    static function categoryArray() {
        global $database;
        // get a list of the menu items


        $query = "SELECT c.*, c.parent_id AS parent"
                . "\n FROM #__booklibrary_main_categories c"
                . "\n WHERE section='com_booklibrary'"
                . "\n AND published <> -2"
                . "\n ORDER BY ordering";
        $database->setQuery($query);

        $items = $database->loadObjectList();

        if ($items == null)
            echo "<strong style='color:red' > Please create categories for Book Library first!</strong>";
        // establish the hierarchy of the menu
        $children = array();
        // first pass - collect children
        foreach ($items as $v) {
            $pt = $v->parent;
            $list = @$children[$pt] ? $children[$pt] : array();
            array_push($list, $v);
            $children[$pt] = $list;
        }
        // second pass - get an indent list of the items
        $array = bookLibraryTreeRecurse(0, '', array(), $children); //for 1.6

        return $array;
    }

}

/**
 * HTML Class
 * Utility class for all HTML drawing classes
 * @desc class General HTML creation class. We use it for back/front ends.
 */
class HTML {

    // TODO :: merge categoryList and categoryParentList
    // add filter option ?
    function categoryList($id, $action, $options = array()) {
        $list = CAT_Utils::categoryArray();
        // assemble menu items to the array
        foreach ($list as $item) {
            $options[] = mosHTML::makeOption($item->id, $item->treename);
        }
        $parent = mosHTML::selectList($options, 'catid', 'id="catid" class="inputbox" size="1" onchange="' . $action . '"', 'value', 'text', $id);
        return $parent;
    }

    static function categoryParentList($id, $action, $is_new, $options = array()) {
        global $database;
        $list = CAT_Utils::categoryArray();
        $cat = new mainBooklibraryCategories($database); //for 1.6
        $cat->load($id);

        $this_treename = '';
        $childs_ids = Array();
        foreach ($list as $item) {
            if ($item->id == $cat->id || array_key_exists($item->parent_id, $childs_ids))
                $childs_ids[$item->id] = $item->id;
        }

        foreach ($list as $item) {

            if ($this_treename) {
                if ($item->id != $cat->id
                        && strpos($item->treename, $this_treename) === false
                        && array_key_exists($item->id, $childs_ids) === false) {
                    $options[] = mosHTML::makeOption($item->id, $item->treename);
                }
            } else {
                if ($item->id != $cat->id) {
                    $options[] = mosHTML::makeOption($item->id, $item->treename);
                } else {
                    $this_treename = "$item->treename/";
                }
            }
        }

        $parent = null;
        $parent = mosHTML::selectList($options, 'parent_id', 'class="inputbox" size="1"', 'value', 'text', $cat->parent_id);
        return $parent;
    }

    /*
     * function imageList ()
     */

    static function imageList($name, &$active, $javascript = null, $directory = null) {

        global $mosConfig_absolute_path;
        if (!$javascript) {
            $javascript = "onchange=\"javascript:if (document.adminForm." . $name .
                    ".options[selectedIndex].value!='')    " .
                    "{document.imagelib.src='../images/stories/' + document.adminForm."
                    . $name . ".options[selectedIndex].value} else {document.imagelib.src='../images/blank.png'}\"";
        }
        if (!$directory) {
            $directory = '/images/stories';
        }

// inserted by Wonderer
        if (!file_exists($mosConfig_absolute_path . $directory)) {
            @mkdir($mosConfig_absolute_path . $directory, 0777) or die("Error of directory creating: [" . $mosConfig_absolute_path . $directory . "] ");
        } else {
            
        }
// --
        $imageFiles = mosReadDirectory($mosConfig_absolute_path . $directory);
        $images = array(mosHTML::makeOption('', _BOOKLIBRARY_A_SELECT_IMAGE));
        foreach ($imageFiles as $file) {
            if (preg_match("/bmp|gif|jpg|png/i", $file)) {
                $images[] = mosHTML::makeOption($file);
            }
        }
        $images = mosHTML::selectList($images, $name, 'id="' . $name . '" class="inputbox" size="1" '
                        . $javascript, 'value', 'text', $active);
        return $images;
    }

}

/*
 * function bookLibraryTreeRecurse ()
 * Redefines a standard function to not display &nbsp;
 */

function publish_manage_review($bid, $publish, $option) {
    global $database;

    $database->setQuery("UPDATE #__booklibrary_review SET published = $publish WHERE id  = $bid ");
    if (!$database->query()) {
        echo "<script> alert(\"" . $database->getErrorMsg() . "\"); window.history.go(-1); </script>\n";
        exit();
    }

    mosRedirect("index.php?option=$option&task=manage_review");
}

function bookLibraryTreeRecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1
) {

    if (@$children[$id] && $level <= $maxlevel) {
        $parent_id = $id;
        foreach ($children[$id] as $v) {
            $id = $v->id;

            if ($type) {
                $pre = " "; //'<sup>|_</sup>_';
                $spacer = '. -- ';
            } else {
                $pre = "- ";
                $spacer = ' . -';
            }

            if ($v->parent == 0) {
                $txt = $v->name;
            } else {
                $txt = $pre . $v->name;
            }
            $pt = $v->parent;
            $list[$id] = $v;
            $list[$id]->treename = "$indent$txt";
            $list[$id]->children = count(@$children[$id]);
            $list[$id]->all_fields_in_list = count(@$children[$parent_id]);

            $list = bookLibraryTreeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type);
        }
    }
    return $list;
}

function showCategories() {
    global $database, $my, $option, $menutype, $mainframe, $mosConfig_list_limit, $acl;

    $grooups = get_group_children_bl();
    $section = "com_booklibrary";

    $sectionid = $mainframe->getUserStateFromRequest("sectionid{$section}{$section}", 'sectionid', 0);
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$section}limitstart", 'limitstart', 0);
    $levellimit = $mainframe->getUserStateFromRequest("view{$option}limit$menutype", 'levellimit', 10);

    $query = "SELECT  c.*, c.checked_out as checked_out_contact_category, c.parent_id as parent,
                        c.params, u.name AS editor, COUNT(bc.id) AS cc"
            . "\n FROM #__booklibrary_main_categories AS c"
            . "\n LEFT JOIN #__booklibrary_categories as bc ON bc.catid=c.id"
            . "\n LEFT JOIN #__users AS u ON u.id = c.checked_out"
            . "\n WHERE c.section='$section'"
            . "\n GROUP BY c.id "
            . "\n ORDER BY parent_id DESC, ordering";

    $database->setQuery($query);

    $rows = $database->loadObjectList();

    foreach ($rows as $k => $v) {
        $rows[$k]->ncourses = 0;

        foreach ($rows as $k1 => $v1) {
            if ($v->id == $v1->parent)
                $rows[$k]->cc +=$v1->cc;
        }
        ($rows[$k]->cc == 0) ? "-" : "<a href=\"?option=com_booklibrary&section=book&catid=" . $v->id . "\">" . ($v->cc) . "</a>"; //for 1.6

        $curgroup = "";
        $ss = explode(',', $v->params);
        foreach ($ss as $s) {
            if ($s == '')
                $s = '-2';
            $curgroup[] = $grooups[$s];
        }
        $rows[$k]->groups = implode(', ', $curgroup);
    }

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }
    // establish the hierarchy of the categories
    $children = array();
    // first pass - collect children

    foreach ($rows as $v) {
        $pt = $v->parent;
        $list = @$children[$pt] ? $children[$pt] : array();
        array_push($list, $v);
        $children[$pt] = $list;
    }

    // second pass - get an indent list of the items
    $list = bookLibraryTreeRecurse(0, '', array(), $children, max(0, $levellimit - 1));
    $total = count($list);

    $pageNav = new JPagination($total, $limitstart, $limit); // for J 1.6

    $levellist = mosHTML::integerSelectList(1, 20, 1, 'levellimit', 'size="1" onchange="document.adminForm.submit();"', $levellimit);
    // slice out elements based on limits
    $list = array_slice($list, $pageNav->limitstart, $pageNav->limit);

    $count = count($list);
    // get list of sections for dropdown filter
    $javascript = 'onchange="document.adminForm.submit();"';
    if (version_compare(JVERSION, "3.0.0", "lt")) {
        $mosAdminMenus = new mosAdminMenus();
        $lists['sectionid'] = $mosAdminMenus->SelectSection('sectionid', $sectionid, $javascript);
    }
    HTML_Categories::show($list, $my->id, $pageNav, $lists, 'other');
}

function editCategory($section = '', $uid = 0) {
    global $database, $my, $acl;
    global $mosConfig_absolute_path, $mosConfig_live_site;

    $type = mosGetParam($_REQUEST, 'type', '');
    $redirect = mosGetParam($_POST, 'section', '');
    $row = new mainBooklibraryCategories($database); //for 1.6
    // load the row from the db table
    $row->load($uid);
    // fail if checked out not by 'me'
    if ($row->checked_out && $row->checked_out <> $my->id) {
        mosRedirect('index.php?option=com_booklibrary&task=categories', 'The category ' . $row->title . ' is currently being edited by another administrator');
    }
    $is_new = false;
    if ($uid) {
        // existing record
        $row->checkout($my->id);
        // code for Link Menu
    } else {
        $is_new = true;
        // new record
        $row->section = $section;
        $row->published = 1;
    }
    // make order list
    $order = array();

    $database->setQuery("SELECT COUNT(*) FROM #__booklibrary_main_categories WHERE section='$row->section'"); //for 1.6

    $max = intval($database->loadResult()) + 1;

    for ($i = 1; $i < $max; $i++) {
        $order[] = mosHTML::makeOption($i);
    }
    // build the html select list for ordering
    $query = "SELECT ordering AS value, title AS text"
            . "\n FROM #__booklibrary_main_categories"
            . "\n WHERE section = '$row->section'"
            . "\n ORDER BY ordering";

    $mosAdminMenus = new mosAdminMenus();
    //$lists['ordering'] = mosAdminMenus::SpecificOrdering($row, $uid, $query);
    $lists['ordering'] = version_compare(JVERSION, '3.0', 'ge') ? NUll : $mosAdminMenus->SpecificOrdering($row, $uid, $query);
    // build the select list for the image positions
    $active = ($row->image_position ? $row->image_position : 'left');
    $lists['image_position'] = version_compare(JVERSION, '3.0', 'ge') ? NUll : $mosAdminMenus->Positions('image_position', $active, null, 0, 0);
    //$lists['image_position'] = mosAdminMenus::Positions('image_position', $active, null, 0, 0); 
    // Imagelist
    $lists['image'] = HTML::imageList('image', $row->image);
    // build the html radio buttons for published
    $lists['published'] = mosHTML::yesnoRadioList('published', 'class="inputbox"', $row->published);
    // build the html select list for paraent item
    $options = array();
    $options[] = mosHTML::makeOption('0', _BOOKLIBRARY_A_SELECT_TOP);
//***********access category
/*    $gtree[] = mosHTML :: makeOption('-2', 'Everyone');
    $gtree = get_group_children_tree_bl();
   $f = "";
    $s = explode(',', $row->params);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['category']['registrationlevel'] = mosHTML::selectList($gtree, 'category_registrationlevel[]', 'size="" ', 'value', 'text');
*/
    //********end access category
    //-------langarray-------------
    $retVal1 = mosBooklibraryOthers :: getLanguageArray();
    $lang = null;
    for ($i = 0, $n = count($retVal1); $i < $n; $i++) {
        $help = $retVal1[$i];
        $lang[] = mosHTML :: makeOption($help[0], $help[1]);
    }

    $lists['langlist'] = mosHTML :: selectList($lang, 'language', 'class="inputbox" size="1"', 'value', 'text'/* , $booklibrary_configuration['editbook']['default']['lang'] */);

    //-----------------------------
    $lists['parent'] = HTML::categoryParentList($row->id, "", $is_new, $options);
    $params2 = unserialize($row->params2);
    $alone = '';
    $view = '';

    if ($uid != 0) {
        if (isset($params2->alone_category) or isset($params2->view_book)) {
            $alone = $params2->alone_category;
            $view = $params2->view_book;
        }
    }

    $component_path = JPath::clean(JPATH_SITE . '/components/com_booklibrary/views/alone_category/tmpl');
    $component_layouts = array();
    $options = array();
    if (is_dir($component_path)
            && ($component_layouts = JFolder::files($component_path, '^[^_]*\.php$', false, true))
    ) {
        $alone_category[] = JHtml::_('select.option', '', 'Use Global');
        foreach ($component_layouts as $i => $file) {
            $select_file_name = pathinfo($file);
            $select_file_name = $select_file_name['filename'];
            $alone_category[] = JHtml::_('select.option', $select_file_name, $select_file_name);
        }
    }
    $lists['alone_category'] = mosHTML :: selectList($alone_category, 'alone_category', 'class="inputbox" size="1"', 'value', 'text', $alone/* $params2->alone_category */);

    $component_path = JPath::clean(JPATH_SITE . '/components/com_booklibrary/views/view_book/tmpl');
    $component_layouts = array();
    $options = array();
    if (is_dir($component_path)
            && ($component_layouts = JFolder::files($component_path, '^[^_]*\.php$', false, true))
    ) {
        $view_book[] = JHtml::_('select.option', '', 'Use Global');
        foreach ($component_layouts as $i => $file) {
            $select_file_name = pathinfo($file);
            $select_file_name = $select_file_name['filename'];
            $view_book[] = JHtml::_('select.option', $select_file_name, $select_file_name);
        }
    }
    $lists['view_book'] = mosHTML :: selectList($view_book, 'view_book', 'class="inputbox" size="1"', 'value', 'text', $view/* $params2->view_book */);


    HTML_Categories::edit($row, $section, $lists, $redirect);
}

function saveCategory() {
    global $database;
    $row = new mainBooklibraryCategories($database); //for 1.6

    $post = JRequest::get('post', JREQUEST_ALLOWHTML);

    $params2 = new stdClass();
    $params2->alone_category = $post['alone_category'];
    $params2->view_book = $post['view_book'];

    $post['params2'] = serialize($params2);

    //if (!$row->bind($_POST)) {
    if (!$row->bind($post)) {
        echo "<script> alert('" . addslashes($row->getError()) . "'); window.history.go(-1); </script>\n";
        exit();
    }
    $row->section = 'com_booklibrary';
    $row->parent_id = $_REQUEST['parent_id'];
    $row->language = $_REQUEST['language'];


    if (!$row->check()) {
        echo "<script> alert('" . addslashes($row->getError()) . "'); window.history.go(-1); </script>\n";
        exit();
    }
    //****set access level
    $row->params = implode(',', mosGetParam($_POST, 'category_registrationlevel', ''));
    //****end set access level
    if ($row->params == "")
        $row->params = "-2";

    if (!$row->store()) {
        echo "<script> alert('" . addslashes($row->getError()) . "'); window.history.go(-1); </script>\n";
        exit();
    }


    $row->checkin();
    $row->updateOrder("section='com_booklibrary' AND parent_id='$row->parent_id'");


    mosRedirect('index.php?option=com_booklibrary&section=categories');
}

//this function checks - is exist books in this folder and folders under this category 
function is_exist_curr_and_subcategory_books($catid) {
    global $database, $my;

    $query = "SELECT *, COUNT(a.id) AS numlinks FROM #__booklibrary_main_categories AS cc"
            . "\n  JOIN #__booklibrary_categories AS a ON a.catid = cc.id"
            . "\n WHERE section='com_booklibrary' AND cc.id='$catid' "
            . "\n GROUP BY cc.id"
            . "\n ORDER BY cc.ordering";
    $database->setQuery($query);
    $categories = $database->loadObjectList();
    if (count($categories) != 0)
        return true;

    $query = "SELECT id "
            . "FROM #__booklibrary_main_categories AS cc "
            . " WHERE section='com_booklibrary' AND parent_id='$catid' ";
    $database->setQuery($query);
    $categories = $database->loadObjectList();

    if (count($categories) == 0)
        return false;

    foreach ($categories as $k) {
        if (is_exist_curr_and_subcategory_books($k->id))
            return true;
    }
    return false;
}

//end function

function removeCategoriesFromDB($cid) {
    global $database, $my;

    $query = "SELECT id  "
            . "FROM #__booklibrary_main_categories AS cc "
            . " WHERE section='com_booklibrary' AND parent_id='$cid' ";
    $database->setQuery($query);
    $categories = $database->loadObjectList();

    if (count($categories) != 0) {
        //delete child
        foreach ($categories as $k) {
            removeCategoriesFromDB($k->id);
        }
    }

    $sql = "DELETE FROM #__booklibrary_main_categories WHERE id = $cid ";
    $database->setQuery($sql);
    $database->query();
}

/**
 * Deletes one or more categories from the categories table
 * 
 * @param string $ The name of the category section
 * @param array $ An array of unique category id numbers
 */
function removeCategories($section, $cid) {
    global $database;

    if (count($cid) < 1) {
        echo "<script> alert('Select a category to delete'); window.history.go(-1);</script>\n";
        exit;
    }

    foreach ($cid as $catid) {
        if (is_exist_curr_and_subcategory_books($catid)) {
            echo "<script> alert('Some category from yours select contain books. \\n Please remove books first!'); window.history.go(-1); </script>\n";
            exit;
        }
    }

    foreach ($cid as $catid) {
        removeCategoriesFromDB($catid);
    }

    $msg = (count($err) > 1 ? "Categories " : _BOOKLIBRARY_CATEGORIES_NAME . " ") . _BOOKLIBRARY_DELETED;
    mosRedirect('index.php?option=com_booklibrary&section=categories&mosmsg=' . $msg);
}

/**
 * Publishes or Unpublishes one or more categories
 * 
 * @param string $ The name of the category extension
 * @param integer $ A unique category id (passed from an edit form)
 * @param array $ An array of unique category id numbers
 * @param integer $ 0 if unpublishing, 1 if publishing
 * @param string $ The name of the current user
 */
function publishCategories($extension, $categoryid = null, $cid = null, $publish = 1) {
    global $database, $my;

    if (!is_array($cid)) {
        $cid = array();
    }
    if ($categoryid) {
        $cid[] = $categoryid;
    }

    if (count($cid) < 1) {
        $action = $publish ? _BOOKLIBRARY_PUBLISH : _BOOKLIBRARY_DML_UNPUBLISH;
        echo "<script> alert('" . _BOOKLIBRARY_DML_SELECTCATTO . " $action'); window.history.go(-1);</script>\n";
        exit;
    }

    $cids = implode(',', $cid);

    $query = "UPDATE #__booklibrary_main_categories SET published='$publish'" //for 1.6
            . "\nWHERE id IN ($cids) AND (checked_out=0 OR (checked_out='$my->id'))";
    $database->setQuery($query);
    if (!$database->query()) {
        echo "<script> alert('" . addslashes($database->getErrorMsg()) . "'); window.history.go(-1); </script>\n";
        exit();
    }

    if (count($cid) == 1) {
        $row = new mainBooklibraryCategories($database); //for 1.6
        $row->checkin($cid[0]);
    }

    mosRedirect('index.php?option=com_booklibrary&section=categories');
}

/**
 * Cancels an edit operation
 * 
 * @param string $ The name of the category extension
 * @param integer $ A unique category id
 */
function cancelCategory() {
    global $database;

    $row = new mainBooklibraryCategories($database); //for 1.6
    $row->bind($_POST);
    $row->checkin();
    mosRedirect('index.php?option=com_booklibrary&section=categories');
}

/**
 * Moves the order of a record
 * 
 * @param integer $ The increment to reorder by
 */
function orderCategory($uid, $inc) {

    global $database;
    $row = new mainBooklibraryCategories($database); //for 1.6
    $row->load($uid);

    if ($row->ordering == 1 && $inc == -1)
        mosRedirect('index.php?option=com_booklibrary&section=categories');

    $new_order = $row->ordering + $inc;

    //change ordering - for other element
    $query = "UPDATE #__booklibrary_main_categories SET ordering='" . ($row->ordering) . "'" //for 1.6
            . "\nWHERE parent_id = $row->parent_id and ordering=$new_order";
///
    $database->setQuery($query);
    $database->query();

    //change ordering - for this element
    $query = "UPDATE #__booklibrary_main_categories SET ordering='" . $new_order . "'" //for 1.6
            . "\nWHERE id = $uid";
    $database->setQuery($query);
    $database->query();

    mosRedirect('index.php?option=com_booklibrary&section=categories');
}

/**
 * changes the access level of a record
 * 
 * @param integer $ The increment to reorder by
 */
function accessCategory($uid, $access) {
    global $database;

    $row = new mainBooklibraryCategories($database); //for 1.6
    $row->load($uid);
    $row->access = $access;

    if (!$row->check()) {
        return $row->getError();
    }
    if (!$row->store()) {
        return $row->getError();
    }

    mosRedirect('index.php?option=com_booklibrary&extension=categories');
}

function update_review($title, $comment, $rating, $review_id) {
    global $database;

    $review = new mosBookLibrary_review($database);

    $review->load($review_id);

    if (!$review->bind($_POST)) {
        echo "<script> alert('" . $book->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    if (!$review->check()) {
        echo "<script> alert('" . $book->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }
    if (!$review->store()) {
        echo "<script> alert('" . $book->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }
}

function edit_review($option, $review_id, $book_id) {
    global $database;


    $database->setQuery("SELECT * FROM #__booklibrary_review WHERE id=" . $review_id . " ");

    $review = $database->loadObjectList();

    echo $database->getErrorMsg();


    HTML_booklibrary :: edit_review($option, $book_id, $review);
}

/*
 * Add Nikolay.
 * Function for delete coment
 * (comment for every book) 
 * in database.
 */

function delete_review($option, $id) {
    global $database;

    $database->setQuery("DELETE FROM #__booklibrary_review WHERE #__booklibrary_review.id=" . $id . ";");

    $database->query();
    echo $database->getErrorMsg();
}

//*************************************************************************************************************
//*********************************   begin for manage reviews   **********************************************
//*************************************************************************************************************
function delete_manage_review($option, $id) {
    global $database;

    for ($i = 0; $i < count($id); $i++) {
        $database->setQuery("DELETE FROM #__booklibrary_review WHERE #__booklibrary_review.id=" . $id[$i] . ";");

        $database->query();
        echo $database->getErrorMsg();
    }
}

function edit_manage_review($option, $review_id) {

    global $database;

    if (count($review_id) > 1) {
        echo "<script> alert('Please select one review for edit!!!'); window.history.go(-1); </script>\n";
    } else {
        $database->setQuery("SELECT * FROM #__booklibrary_review WHERE id=" . $review_id[0] . " ");
        $review = $database->loadObjectList();
        echo $database->getErrorMsg();

        HTML_booklibrary :: edit_manage_review($option, $review);
    }
}

//*********************************************************************************
//************************   end for manage reviews   *****************************
//*********************************************************************************
function showInfo($option, $bid) {

    if (is_array($bid) && count($bid) > 0) {
        $bid = $bid[0];
    }
    echo "Test: " . $bid;
}

function decline_lend_requests($option, $bids) {

    global $database, $booklibrary_configuration;
    $datas = array();
    if (is_array($bids[0]))
        $bids = $bids[0];


    foreach ($bids as $bid) {

        $lend_request = new mosBookLibrary_lend_request($database);

        if (!version_compare(JVERSION, '3.5', 'lt')) {
            $a = $bid[0];
            $bid = $a;
        }

        $lend_request->load($bid);

        $tmp = $lend_request->decline();

        if ($tmp != null) {

            echo "<script> alert('" . addslashes($tmp) . "'); window.history.go(-1); </script>\n";

            exit();
        }
        $datas[] = array('email' => $lend_request->user_email, 'name' => $lend_request->user_name, 'id' => $lend_request->fk_bookid);
    }
   


    mosRedirect("index.php?option=$option&task=lend_requests");
}

function accept_lend_requests($option, $bids) {
    global $database, $booklibrary_configuration;
    $datas = array();
    if (is_array($bids[0]))
        $bids = $bids[0];
    foreach ($bids as $bid) {

        $lend_request = new mosBookLibrary_lend_request($database);

        if (!version_compare(JVERSION, '3.5', 'lt')) {
            $a = $bid[0];
            $bid = $a;
        }

        $lend_request->load($bid);

        $tmp = $lend_request->accept();

        if ($tmp != null) {

            echo "<script> alert('" . addslashes($tmp) . "'); window.history.go(-1); </script>\n";

            exit();
        }
        $datas[] = array('email' => $lend_request->user_email, 'name' => $lend_request->user_name, 'id' => $lend_request->fk_bookid);
    }
    

    mosRedirect("index.php?option=$option&task=lend_requests");
}

//********************   begin add send mail for admin   ********


function sendMailLendRequest($datas, $answer) {
    global $database, $mosConfig_mailfrom, $booklibrary_configuration;
    $conf = JFactory::getConfig();

    foreach ($datas as $key => $data) {
        $mess = null;
        $zapros = "SELECT title FROM #__booklibrary WHERE id=" . $data['id'];
        $database->setQuery($zapros);
        $item = $database->loadResult();
        echo $database->getErrorMsg();
        $database->setQuery("SELECT u.name AS ownername,rm.owneremail
                          \nFROM #__users AS u
                          \nLEFT JOIN #__booklibrary AS rm ON rm.owneremail=u.email
                          \nWHERE rm.id=" . $data['id']);
        echo $database->getErrorMsg();
        $ownerdata = $database->loadObjectList();

        $datas[$key]['title'] = $item;

        mailLendRequestAnswer($data, $item, $answer);
        /* $message = $booklibrary_configuration['lend_form'];
          $message = str_replace("{title}", $datas[$key]['title'], $message);
          $message = str_replace("{answer}", $answer, $message);
          $message = str_replace("{username}", $datas[$key]['name'], $message);
          if ($answer == _BOOKLIBRARY_LENDREQUEST_EMAIL_ACCEPTED) {
          $message = str_replace("{ownername}", $ownerdata[0]->ownername, $message);
          $message = str_replace("{owneremail}", $ownerdata[0]->owneremail, $message);
          } else {
          $message = str_replace("{ownername}", '', $message);
          $message = str_replace("{owneremail}", '', $message);
          } */

        //mosMail($mosConfig_mailfrom, $conf->_registry['config']['data']->fromname, $data['email'], _BOOKLIBRARY_EMAIL_LEND_ANSWER_SUBJECT, $message, true);
    }


    //********************   end add send mail for admin   ****************
}

/*
  "<p>Dear {username}!</p>
  <p>Your lend request for</p>
  <p>{title} {owneremail} {ownername}</p>
  <p>{answer}</p>"
 */

function mailLendRequestAnswer($user, $title, $answer) {

    $message = _BOOKLIBRARY_EMAIL_NOTIFICATION_LEND_REQUEST_ANSWER;
    $message = str_replace("{username}", $user['name'], $message);
    $message = str_replace("{title}", $title, $message);
    $message = str_replace("{answer}", $answer, $message);

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $subject = "Answer";

    mail($user['email'], $subject, $message, $headers);
}

//*************************************************************************************************************
//*********************************   begin add for button print in Manager Books   ***************************
//*************************************************************************************************************
function print_books($option) {
    global $mosConfig_live_site, $database, $mainframe, $mosConfig_list_limit;

    if (!array_key_exists('bid', $_POST)) {
        echo "<script> alert('Please select some book'); window.history.go(-1); </script>\n";
        exit;
    } else {
        $bid = $_POST['bid'];
        $bids = implode(',', $bid);
    }
//**********************   begin for books request   ******************************************************
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
    $catid = $mainframe->getUserStateFromRequest("catid{$option}", 'catid', 0);
    $lend = $mainframe->getUserStateFromRequest("lend{$option}", 'lend', 0);
    $pub = $mainframe->getUserStateFromRequest("pub{$option}", 'pub', 0);
    $user = $mainframe->getUserStateFromRequest("user{$option}", 'user', 0);


    $search = $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));

    $where = array();
    if ($user == "user") {
        array_push($where, "a.checked_out <> 0");
    } else if ($user == "not_user") {
        array_push($where, "a.checked_out = 0");
    }
    if ($lend == "lend") {
        array_push($where, "a.fk_lendid <> 0");
    } else if ($lend == "not_lend") {
        array_push($where, "a.fk_lendid = 0");
    }

    if ($pub == "pub") {
        array_push($where, "a.published = 1");
    } else if ($pub == "not_pub") {
        array_push($where, "a.published = 0");
    }

    if ($catid > 0) {
        array_push($where, "a.catid='$catid'");
    }

    if ($search) {
        array_push($where, "(LOWER(a.title) LIKE '%$search%' OR LOWER(a.authors) LIKE '%$search%' OR LOWER(a.isbn) LIKE '%$search%' OR LOWER(a.comment) LIKE '%$search%')");
    }

    $database->setQuery("SELECT count(*) FROM #__booklibrary AS a" .
            "\nLEFT JOIN #__booklibrary_lend AS l" .
            "\nON a.fk_lendid = l.id" .
            (count($where) ? "\nWHERE " . implode(' AND ', $where) : ""));
    $total = $database->loadResult();
    echo $database->getErrorMsg();

    $pageNav = new JPagination($total, $limitstart, $limit); // for J 1.6

    $selectstring = "SELECT a.*, GROUP_CONCAT(cc.title SEPARATOR ', ') AS category, l.id as lendid, l.lend_from as lend_from, l.lend_return as
                 lend_return,            
                 l.lend_until as lend_until, u.name AS editor 
                 FROM #__booklibrary AS a
                 LEFT JOIN #__booklibrary_categories AS bc ON bc.bookid=a.id
                 LEFT JOIN #__booklibrary_main_categories AS cc ON cc.id = bc.catid
                 LEFT JOIN #__booklibrary_lend AS l ON a.fk_lendid = l.id
                 LEFT JOIN #__users AS u ON u.id = a.checked_out 
                 WHERE a.id IN ($bids) 
                 GROUP BY a.id";

    $database->setQuery($selectstring);
    $rows = $database->loadObjectList();

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    for ($i = 0; $i < count($rows); $i++) {
        if (isset($rows[$i]->lendid)) {
            $select_lend = "SELECT id, fk_userid, user_name, user_email, user_mailing" .
                    "\nFROM #__booklibrary_lend AS a" .
                    "\nWHERE a.id = " . $rows[$i]->lendid . ";";
            $database->setQuery($select_lend);
            $rows_add = $database->loadObjectList();

            // for register user
            if (isset($rows_add[0]->fk_userid)) {
                $select_lend_reg = "SELECT id, name, email" .
                        "\nFROM #__users AS u" .
                        "\nWHERE u.id = " . $rows_add[0]->fk_userid . ";";

                $database->setQuery($select_lend_reg);
                $rows_add_user = $database->loadObjectList();

                $rows[$i]->user_name = $rows_add_user[0]->name;
                $rows[$i]->user_email = $rows_add_user[0]->email;
                $rows[$i]->user_mailing = "--";
            } else {
                $rows[$i]->user_name = $rows_add[0]->user_name;
                $rows[$i]->user_email = $rows_add[0]->user_email;
                $rows[$i]->user_mailing = $rows_add[0]->user_mailing;
            }
        }
    }
//**********************   end for books request   ********************************************************

    HTML_booklibrary :: showPrintBooks($rows);
}

//end print_books()

function print_item($option) {

    $rows = $_SESSION['rows'];

    HTML_booklibrary :: showPrintItem($rows);
}

//*********************************   end add for button print in Manager Books   *****************************


function lend_requests($option, $bid) {

    global $database, $mainframe, $mosConfig_list_limit;

    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);

    $database->setQuery("SELECT count(*) FROM #__booklibrary AS a" .
            "\nLEFT JOIN #__booklibrary_lend_request AS l" .
            "\nON l.fk_bookid = a.id" .
            "\nWHERE l.status = 0");
    $total = $database->loadResult();
    echo $database->getErrorMsg();
    if ($database->getErrorNum()) {
        echo $database->stderr();
    }
    $pageNav = new JPagination($total, $limitstart, $limit); // for J 1.6

    $database->setQuery("SELECT * FROM #__booklibrary AS a" .
            "\nLEFT JOIN #__booklibrary_lend_request AS l" .
            "\nON l.fk_bookid = a.id" .
            "\nWHERE l.status = 0" .
            "\nORDER BY l.lend_from, l.lend_until, l.user_name" .
            "\nLIMIT $pageNav->limitstart,$pageNav->limit;");
    $lend_requests = $database->loadObjectList();
    echo $database->getErrorMsg();
    if ($database->getErrorNum()) {
        echo $database->stderr();
    }
    HTML_booklibrary :: showRequestLendBooks($option, $lend_requests, $pageNav);
}

// ------------------------- by Wonderer
function unsetCatId() {
    $option = 'com_booklibrary';

    $mainframe = JFactory::getApplication();
    $catid = $mainframe->getUserStateFromRequest("catid{$option}", 'catid', '-1');
    unset($catid);
    showBooks($option);
}

/**
 * Compiles a list of records
 * @param database - A database connector object
 * select categories
 */
function showBooks($option) { // Display table n the Books Tab
    $mainframe = JFactory::getApplication();
    global $database, $mosConfig_list_limit;

    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
    $catid = $mainframe->getUserStateFromRequest("catid{$option}", 'catid', '-1'); //old 0
    $lend = $mainframe->getUserStateFromRequest("lend{$option}", 'lend', '-1');
    $pub = $mainframe->getUserStateFromRequest("pub{$option}", 'pub', '-1');
    $owneremail = $mainframe->getUserStateFromRequest("owneremail{$option}", 'owneremail', '-1');
    $srch_for = $mainframe->getUserStateFromRequest("srch_for{$option}", 'srch_for', '0'); //

    $search = $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
    //$search = $database->getEscaped(trim(strtolower($search)));

    $where = array();

    if ($owneremail !== "-1" && $owneremail !== "0") {
        array_push($where, "jei.owneremail = '" . $owneremail . "' ");
    }
    if ($lend == "lend") {
        array_push($where, "jei.fk_lendid <> 0");
    } else if ($lend == "not_lend") {
        array_push($where, "jei.fk_lendid = 0");
    }
    if ($pub == "pub") {
        array_push($where, "jei.published = 1");
    } else if ($pub == "not_pub") {
        array_push($where, "jei.published = 0");
    }
    if ($catid > 0) {
        array_push($where, "jea.catid='$catid'");
    }
    if ($search) {
        switch ($srch_for) {
            case 'isbn':
                array_push($where, "LOWER(jei.isbn) LIKE '%$search%'");
                break;
            case 'title':
                array_push($where, "LOWER(jei.title) LIKE '%$search%'");
                break;
            case 'autors':
                array_push($where, "LOWER(jei.authors) LIKE '%$search%'");
                break;
            case 'category':
                array_push($where, "LOWER(cc.title) LIKE '%$search%'");
                break;
            case 'publisher':
                array_push($where, "LOWER(jei.manufacturer) LIKE '%$search%'");
                break;
            case 'description':
                array_push($where, "LOWER(jei.comment) LIKE '%$search%'");
                break;
            case 'id':
                array_push($where, "LOWER(jei.bookid) LIKE '%$search%'");
                break;
            default:
                array_push($where, "(LOWER(jei.title) LIKE '%$search%' OR LOWER(jei.authors) LIKE '%$search%' OR LOWER(jei.isbn) LIKE '%$search%' OR LOWER(jei.comment) LIKE '%$search%')");
                break;
        }
    }


    $q = "SELECT count(*) FROM #__booklibrary_categories AS jea" .
            "\nLEFT JOIN #__booklibrary_main_categories AS cc ON cc.id = jea.catid" .
            "\nLEFT JOIN #__booklibrary AS jei ON jei.id = jea.bookid" .
            "\nLEFT JOIN #__booklibrary_lend AS l" .
            "\nON jei.fk_lendid = l.id" .
            (count($where) ? "\nWHERE " . implode(' AND ', $where) : "");

    $database->setQuery($q);




    $total = $database->loadResult();

    echo $database->getErrorMsg();


    // Sort start
    // SORTING parameters start
    $prefix = '';
    $item_session = JFactory::getSession();
    $item_sort_param = mosGetParam($_GET, 'sort', '');
    $item_sort_param = preg_replace('/[^A-Za-z0-9_]*/', '', $item_sort_param);
    if ($item_sort_param == '') {
        if (is_array($sort_arr = $item_session->get('bl_bn_booksort', ''))) {
            $sort_string = $sort_arr['field'] . " " . $sort_arr['direction'];
        } else {
            $sort_string = 'title';
            $sort_arr = array();
            $sort_arr['field'] = 'title';
            $sort_arr['direction'] = '';

            $item_session->set('bl_bn_booksort', $sort_arr);
        }
    } else {
        if (is_array($sort_arr = $item_session->get('bl_bn_booksort', ''))) {
            if ($item_sort_param == $sort_arr['field']) {

                if ($sort_arr['direction'] == 'DESC')
                    $sort_arr['direction'] = '';
                else
                    $sort_arr['direction'] = 'DESC';
            }
            else {
                $sort_arr['field'] = $item_sort_param;
                $sort_arr['direction'] = '';
            }
            if ($sort_arr['field'] != 'category')
                $prefix = 'jei.';
            $sort_string = $prefix . $sort_arr['field'] . " " . $sort_arr['direction'];
        }
        else {
            $sort_string = 'title';
            $sort_arr = array();
            $sort_arr['field'] = 'title';
            $sort_arr['direction'] = '';
        }
        $item_session->set('bl_bn_booksort', $sort_arr);
    }
    $sort_let = mosGetParam($_GET, 'sortlet', '');
    $value = $sort_let;

    if ($sort_let != '' and $sort_let != 'none') {


        switch ($sort_let) {
            case 'lend_out':
                $sort_string = "";
                break;
            case 'lend_out_desc':
                $sort_string = "";
                break;
            case 'lend_until':
                $sort_string = "lend_until";
                break;
            case 'lend_until_desc':
                $sort_string = "lend_until DESC";
                break;
            case 'lend_from':
                $sort_string = "lend_from";
                break;
            case 'lend_from_desc':
                $sort_string = "lend_from DESC";
                break;
        }

        $sort_arr['field'] = "";
    }
    // Sort end


    $pageNav = new JPagination($total, $limitstart, $limit);


    $selectstring = "
	SELECT GROUP_CONCAT(cc.title  SEPARATOR ', ') AS category , jei.* ,  l.id as lendid, l.lend_from as lend_from, l.lend_return as lend_return, l.lend_until as lend_until, username AS owner_name
	FROM #__booklibrary_categories AS jea
    \nLEFT JOIN #__booklibrary_main_categories AS cc ON cc.id = jea.catid
    \nLEFT JOIN #__booklibrary AS jei ON jei.id = jea.bookid
    \nLEFT JOIN #__booklibrary_lend AS l ON l.id = jei.fk_lendid 
    \nLEFT JOIN #__users AS usr ON usr.id = jei.checked_out " .
            (count($where) ? "\nWHERE " . implode(' AND ', $where) : "") .
            " GROUP BY jea.bookid " .
            "\nORDER BY $sort_string" .
            "\nLIMIT $pageNav->limitstart,$pageNav->limit;";

    $database->setQuery($selectstring);
    $rows = $database->loadObjectList();
    $date = date(time());
    foreach ($rows as $row) {
        $check = strtotime($row->checked_out_time);
        $remain = ($check + 7200) - $date;

        if (($remain <= 0) && ($row->checked_out != 0)) {
            $database->setQuery("UPDATE #__booklibrary SET checked_out=0,checked_out_time=0 WHERE id='" . $row->id . "'");
            $database->query();
        }
    }
    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    // get list of categories
    /*
     * select list treeSelectList
     */

    $categories[] = mosHTML :: makeOption('0', _BOOKLIBRARY_LABEL_SELECT_CATEGORIES);
    $categories[] = mosHTML :: makeOption('-1', _BOOKLIBRARY_LABEL_SELECT_ALL_CATEGORIES);

    //*************   begin add for sub category in select in manager books   *************
    $options = $categories;
    $id = 0;
    $list = CAT_Utils::categoryArray();

    $cat = new mainBooklibraryCategories($database); //for 1.6
    $cat->load($id);

    $this_treename = '';
    foreach ($list as $item) {
        if ($this_treename) {
            if ($item->id != $cat->id && strpos($item->treename, $this_treename) === false) {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
            }
        } else {
            if ($item->id != $cat->id) {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
            } else {
                $this_treename = "$item->treename/";
            }
        }
    }

    $clist = mosHTML::selectList($options, 'catid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $catid); //new nik edit
    //*****  end add for sub category in select in manager books   **********

    $usermenu[] = mosHTML :: makeOption('0', _BOOKLIBRARY_LABEL_SELECT_USER);
    $usermenu[] = mosHTML :: makeOption('-1', _BOOKLIBRARY_LABEL_SELECT_ALL_USERS);
    $selectstring = "SELECT usr.username,jei.owneremail
	FROM  #__booklibrary AS jei 
	\nLEFT JOIN #__users AS usr ON usr.email = jei.owneremail" .
            " GROUP BY usr.id  " .
            "\nORDER BY usr.username ";

    $database->setQuery($selectstring);
    $users_list = $database->loadObjectList();
    $useranonimus = new stdClass();
    $useranonimus -> username = 'anonymous';
    $useranonimus -> owneremail = 'anonymous';
 
    if(count($users_list) > 1) { 
        $users_list[count($users_list)] = $useranonimus;
    }
    
    if(!isset($users_list[0]->username)) unset($users_list[0]);

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }
    
    $startSelecterPosition = 0;
    foreach ($users_list as $item) {
        $usermenu[2 + $startSelecterPosition] = mosHTML::makeOption($item->owneremail, $item->username);
        $startSelecterPosition++;
    }
    
    $userlist = mosHTML :: selectList($usermenu, 'owneremail', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $owneremail);

    $lendmenu[] = mosHTML :: makeOption('0', _BOOKLIBRARY_LABEL_SELECT_TO_LEND);
    $lendmenu[] = mosHTML :: makeOption('-1', _BOOKLIBRARY_LABEL_SELECT_ALL_LEND);
    $lendmenu[] = mosHTML :: makeOption('not_lend', _BOOKLIBRARY_LABEL_SELECT_NOT_LEND);
    $lendmenu[] = mosHTML :: makeOption('lend', _BOOKLIBRARY_LABEL_SELECT_LEND);

    $lendlist = mosHTML :: selectList($lendmenu, 'lend', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $lend);

    $pubmenu[] = mosHTML :: makeOption('0', _BOOKLIBRARY_LABEL_SELECT_TO_PUBLIC);
    $pubmenu[] = mosHTML :: makeOption('-1', _BOOKLIBRARY_LABEL_SELECT_ALL_PUBLIC);
    $pubmenu[] = mosHTML :: makeOption('not_pub', _BOOKLIBRARY_LABEL_SELECT_NOT_PUBLIC);
    $pubmenu[] = mosHTML :: makeOption('pub', _BOOKLIBRARY_LABEL_SELECT_PUBLIC);
    $publist = mosHTML :: selectList($pubmenu, 'pub', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $pub);

    $search_for[] = mosHTML :: makeOption('0', _BOOKLIBRARY_SHOW_SEARCH_FOR_ALL);
    $search_for[] = mosHTML :: makeOption('id', _BOOKLIBRARY_LABEL_BOOKID);
    $search_for[] = mosHTML :: makeOption('isbn', _BOOKLIBRARY_SHOW_SEARCH_FOR_ISBN);
    $search_for[] = mosHTML :: makeOption('title', _BOOKLIBRARY_SHOW_SEARCH_FOR_TITLE);
    $search_for[] = mosHTML :: makeOption('autors', _BOOKLIBRARY_SHOW_SEARCH_FOR_AUTORS);
    $search_for[] = mosHTML :: makeOption('category', _BOOKLIBRARY_SHOW_SEARCH_FOR_CATEGORY);
    $search_for[] = mosHTML :: makeOption('publisher', _BOOKLIBRARY_SHOW_SEARCH_FOR_PUBLISHER);
    $search_for[] = mosHTML :: makeOption('description', _BOOKLIBRARY_SHOW_SEARCH_FOR_DESCRIPTION);
    $search_for_list = mosHTML :: selectList($search_for, 'srch_for', 'class="inputbox" size="1" ', 'value', 'text', $srch_for);


    HTML_booklibrary :: showBooks($option, $rows, $clist, $lendlist, $userlist, $publist, $search, $pageNav, $sort_arr, $search_for_list, $value);
}

/**
 * Compiles information to add or edit books
 * @param integer bid The unique id of the record to edit (0 if new)
 * @param array option the current options
 */
function editBook($option, $bid) {
    global $mosConfig_absolute_path;
    global $database, $my, $mosConfig_live_site, $booklibrary_configuration;
    $book = new mosBookLibrary($database);

    // load the row from the db table
    $book->load(intval($bid));
    $numeric_bookids = array();
    if (empty($book->bookid) && $booklibrary_configuration['bookid']['auto-increment']['boolean'] == 1) {
        $database->setQuery("select bookid from #__booklibrary order by bookid");
        $bookids = $database->loadObjectList();

        foreach ($bookids as $bookid) {
            if (!is_numeric($bookid->bookid)) {
                echo "<script> alert('You have no numeric BookId. Please set option  " . _BOOKLIBRARY_ADMIN_CONFIG_BOOKID_AUTO_INCREMENT . " to \'No\' or change all BookID to numeric '); window.history.go(-1); </script>\n";
                exit();
            }
            $numeric_bookids[] = intval($bookid->bookid);
        }
        if (count($bookids) > 0) {
            sort($numeric_bookids);
            $book->bookid = $numeric_bookids[count($numeric_bookids) - 1] + 1;
        } else
            $book->bookid = 1;
    }

    // get list of categories
    $categories[] = mosHTML :: makeOption('0', _BOOKLIBRARY_LABEL_SELECT_CATEGORIES);

    $query = "SELECT  id ,name, parent_id as parent"
            . "\n FROM #__booklibrary_main_categories"
            . "\n WHERE section='com_booklibrary'"
            . "\n AND published > 0"
            . "\n ORDER BY parent_id, ordering";

    $database->setQuery($query);

    $rows = $database->loadObjectList();


    // establish the hierarchy of the categories
    $children = array();
    // first pass - collect children
    foreach ($rows as $v) {
        $pt = $v->parent;
        $list = @$children[$pt] ? $children[$pt] : array();
        array_push($list, $v);
        $children[$pt] = $list;
    }
    // second pass - get an indent list of the items
    $list = bookLibraryTreeRecurse(0, '', array(), $children);

    foreach ($list as $i => $item) {
        $item->text = $item->treename;
        $item->value = $item->id;
        $list[$i] = $item;
    }

    $categories = array_merge($categories, $list);

    if (count($categories) <= 1) {

        mosRedirect("index.php?option=com_booklibrary&section=categories", _BOOKLIBRARY_ADMIN_IMPEXP_ADD);
    }
    $query = "select catid from #__booklibrary_categories where bookid='" . $book->id . "'";
    $database->setQuery($query);
    //$cat_idlist = $database->loadResultArray();

    if (version_compare(JVERSION, '3.0', 'lt')) {
        $cat_idlist = $database->loadResultArray();
    } else {
        $cat_idlist = $database->loadColumn();
    }

    if (empty($cat_idlist))
        $cat_idlist[0] = '0';
    $clist = mosHTML :: selectList($categories, 'catid[]', 'class="inputbox" ', 'value', 'text', $cat_idlist);

    // get list of WS
    $retVal = mosBooklibraryWS :: getArray();
    $ws = null;
    for ($i = 0, $n = count($retVal); $i < $n; $i++) {
        $help = $retVal[$i];
        $ws[] = mosHTML :: makeOption($help[0], $help[1]);
    }

    if ($bid == 0) {
        $wslist = mosHTML :: selectList($ws, 'informationFrom', 'class="inputbox" size="1"', 'value', 'text', intval($booklibrary_configuration['editbook']['default']['host']));
    }
    else
        $wslist = mosHTML :: selectList($ws, 'informationFrom', 'class="inputbox" size="1"', 'value', 'text', intval($book->informationFrom));

    //get language List
    $retVal1 = mosBooklibraryOthers :: getLanguageArray();
    $lang = null;
    for ($i = 0, $n = count($retVal1); $i < $n; $i++) {
        $help = $retVal1[$i];
        $lang[] = mosHTML :: makeOption($help[0], $help[1]);
    }

    if ($bid == 0) {
        $langlist = mosHTML :: selectList($lang, 'language', 'class="inputbox" size="1"', 'value', 'text', $booklibrary_configuration['editbook']['default']['lang']);
        $langlistshow = mosHTML :: selectList($lang, 'langshow', 'class="inputbox" size="1"', 'value', 'text', '1');
    } else {
        $langlist = mosHTML :: selectList($lang, 'language', 'class="inputbox" size="1"', 'value', 'text', $book->language);
        $langlistshow = mosHTML :: selectList($lang, 'langshow', 'class="inputbox" size="1"', 'value', 'text', $book->langshow);
    }

    //get Rating
    $retVal2 = mosBooklibraryOthers :: getRatingArray();
    $rating = null;
    for ($i = 0, $n = count($retVal2); $i < $n; $i++) {
        $help = $retVal2[$i];
        $rating[] = mosHTML :: makeOption($help[0], $help[1]);
    }

    $ratinglist = mosHTML :: selectList($rating, 'rating', 'class="inputbox" size="1"', 'value', 'text', $book->rating);

    //delete ebook?
    $help = str_replace($mosConfig_live_site, "", $book->ebookURL);
    $delete_ebook_yesno[] = mosHTML :: makeOption($help, _BOOKLIBRARY_YES);
    $delete_ebook_yesno[] = mosHTML :: makeOption('0', _BOOKLIBRARY_NO);

    $delete_ebook = mosHTML :: RadioList($delete_ebook_yesno, 'delete_ebook', 'class="inputbox"', '0', 'value', 'text');

    // fail if checked out not by 'me'
    if ($book->checked_out && $book->checked_out <> $my->id) {
        mosRedirect("index.php?option=$option", _BOOKLIBRARY_IS_EDITED);
    }

    if ($bid) {
        $book->checkout($my->id);
    } else {
        // initialise new record
        $book->published = 0;
        $book->approved = 0;
    }
    if (($book->owneremail) == '' || $bid == 0) {
        $book->owneremail = $my->email;
    }

    $database->setQuery("SELECT username AS owner FROM #__users" . "\nWHERE email='$book->owneremail'");
    $book->owner = $database->loadResult();
//*****************************   begin for reviews **************************//
    $database->setQuery("select a.*, b.name from #__booklibrary_review a, #__users b" .
            " WHERE a.fk_userid = b.id and a.fk_bookid=" . $bid . " ORDER BY date ;");

    $reviews1 = $database->loadObjectList();

    //take review for anonymous users 	
    $database->setQuery("select a.*, 'anonymous' as name from #__booklibrary_review as a  " .
            " WHERE a.fk_userid = 0 and a.fk_bookid = " . $bid . " ORDER BY date ;");
    $reviews2 = $database->loadObjectList();

    $reviews = array_merge($reviews1, $reviews2);
//**********************   end for reviews   *****************************//
    HTML_booklibrary :: editBook($option, $book, $clist, $wslist, $langlist, $langlistshow, $ratinglist, $delete_ebook, $reviews);
}

function getTreeCateg(&$return = array(), $id = 0, $sublvl = '-1') {
    global $database;
    $sublvl++;
    $addpref = '';
    if ($sublvl) {
        $result->text = @$addpref . 'L&nbsp;' . $result->text;
    }
    $database->setQuery("SELECT id AS value, name AS text, parent_id FROM #__booklibrary_main_categories" .
            "\nWHERE section='com_booklibrary'
            AND parent_id=$id ORDER BY ordering");
    $results = $database->loadObjectList();
    if (count($results)) {
        foreach ($results as $result) {

            for ($c = 0; $c < $sublvl; $c++) {
                @$addpref.='&nbsp;';
            }


            $return[] = $result;
            getTreeCateg($return, $result->value, $sublvl);
        }
    }
    return $return;
}

/**
 * Saves the record on an edit form submit
 * @param database A database connector object
 */
function saveBook($option, $task) {
    global $langlist, $database, $my, $mosConfig_absolute_path, $mosConfig_live_site, $booklibrary_configuration;
    //check how the other info should be provided
    $book = new mosBookLibrary($database);


    if (!is_numeric($_POST['bookid']) &&
            $booklibrary_configuration['bookid']['auto-increment']['boolean'] == 1) {
        echo "<script> alert('You set no numeric BookID. Please set option " .
        _BOOKLIBRARY_ADMIN_CONFIG_BOOKID_AUTO_INCREMENT .
        " to \'No\' or change BookID to numeric '); window.history.go(-1); </script>\n";
        exit();
    }

    if (!$book->bind($_POST)) {
        echo "<script> alert('" . addslashes($book->getError()) . "'); window.history.go(-1); </script>\n";
        exit();
    }
    $book->catid = mosGetParam($_POST, 'catid', '');
    $book->owner_id = $my->id;
    //fetch all information from the webservices if necessary
    $book = mosBooklibraryWS :: fetchInfos($book);

    if (is_string($book)) {
        //there was an error while fetching!
        echo "<script> alert('" . addslashes($book) . "'); window.history.go(-1); </script>\n";
        exit();
    }

    if ($booklibrary_configuration['publish_on_add']['show']) {
        $book->published = 1;
    } else {
        $book->published = 0;
    }

    if ($_POST['ebook_Url'] != '')
        $book->ebookURL = $_POST['ebook_Url'];

    //delete ebook file if neccesary
    $delete_ebook = mosGetParam($_POST, 'delete_ebook', 0);

    if ($delete_ebook != '0') {

        $retVal = @unlink($mosConfig_absolute_path . $delete_ebook);
        $book->ebookURL = "";
    }

    //storing e-book
    $file = $_FILES['ebook_file'];
    //check if fileupload is correct

    if ($booklibrary_configuration['ebooks']['allow']
            && intval($file['error']) > 0
            && intval($file['error']) < 4) {
        echo "<script> alert('" . _BOOKLIBRARY_LABEL_EBOOK_UPLOAD_ERROR .
        "'); window.history.go(-1); </script>\n";
        exit();
    } else if ($booklibrary_configuration['ebooks']['allow']
            && intval($file['error']) != 4) {
        /* $file_new = $mosConfig_absolute_path . $booklibrary_configuration['ebooks']['location'] .
          $file['name']; */
        //---------------------

        $uploaddir = $mosConfig_absolute_path . $booklibrary_configuration['ebooks']['location'];
        $file_new = $uploaddir . $_FILES['ebook_file']['name'];
        //echo $file_new;

        $ext = pathinfo($_FILES['ebook_file']['name'], PATHINFO_EXTENSION);
        $allowed_exts = explode(",", $booklibrary_configuration['allowed_exts']);
        $ext = strtolower($ext);
        if (!in_array($ext, $allowed_exts)) {
            echo "<script> alert(' File ext. not allowed to upload! - " . $edfile['name'] . "'); window.history.go(-1); </script>\n";
            exit();
        }
        $db = JFactory::getDbo();
        $db->setQuery("SELECT mime_type FROM #__booklibrary_mime_types WHERE `mime_ext` = " . $db->quote($ext));
        $file_db_mime = $db->loadResult();
        $file['type'] = $_FILES['ebook_file']['type'];

        if ($file_db_mime != $file['type']) {
            echo "<script> alert(' File mime type not match file ext. - " . $edfile['name'] . "'); window.history.go(-1); </script>\n";
            exit();
        }
        //----------------------
        if (!/* move_uploaded_file($file['tmp_name'] */copy($_FILES['ebook_file']['tmp_name'], $file_new)) {
            echo "<script> alert('" . _BOOKLIBRARY_LABEL_EBOOK_UPLOAD_ERROR .
            "'); window.history.go(-1); </script>\n";
            exit();
        } else {
            $book->ebookURL = $mosConfig_live_site . $booklibrary_configuration['ebooks']['location'] .
                    $file['name'];
        }
    }

    $file = $_FILES['picture_file'];

    //-------------------
    if (intval($file['error']) != 4) {

        $ext = pathinfo($_FILES['picture_file']['name'], PATHINFO_EXTENSION);
        $allowed_exts = explode(",", $booklibrary_configuration['allowed_exts_img']);
        $ext = strtolower($ext);
        if (!in_array($ext, $allowed_exts)) {
            echo "<script> alert(' File ext. not allowed to upload! - " . $file['name'] . "'); window.history.go(-1); </script>\n";
            exit();
        }
    }
    //-------------------
    //check if fileupload is correct
    if ($file['size'] != 0 && ( $file['error'] != 0
            || strpos($file['type'], 'image') === false
            || strpos($file['type'], 'image') === "")) {

        echo "<script> alert('" . _BOOKLIBRARY_LABEL_PICTURE_URL_UPLOAD_ERROR .
        "'); window.history.go(-1); </script>\n";
        exit();
    }

    //store pictures locally if neccesary, first check remote URL
    $retVal = null;
    if (intval($booklibrary_configuration['fetchImages']['boolean']) == 1
            && trim($book->imageURL) != ""
            && $file['size'] == 0) {

        $retVal = mosBooklibraryOthers :: storeImageFile($book, null);
    }

    if (intval($booklibrary_configuration['fetchImages']['boolean']) == 1 && $file['size'] != 0) {
        $retVal = mosBooklibraryOthers :: storeImageFile($book, $file);
        if ($retVal != null) {
            echo "<script> alert('" . addslashes($retVal) . "'); window.history.go(-1); </script>\n";
            exit();
        }
    }


    if ($file['size'] == 0) {
        $file = null;
    }

    $book->date = date("Y-m-d H:i:s");
    if (!$book->check()) {
        echo "<script> alert('" . addslashes($book->getError()) . "'); window.history.go(-1); </script>\n";
        exit();
    }

    if (!$book->store()) {
        echo "<script> alert('" . addslashes($book->getError()) . "'); window.history.go(-1); </script>\n";
        exit();
    }

    $query = "delete from #__booklibrary_categories where bookid='" . (int) $book->id . "'";
    $database->setQuery($query);
    $database->query();
    foreach ($book->catid as $catitem) {
        $query = "insert into #__booklibrary_categories (bookid, catid) VALUES ('" . (int) $book->id . "','" . (int) $catitem . "')";
        $database->setQuery($query);
        $database->query();
    }
    $book->checkin();


    //mosRedirect("index.php?option=$option");
    //print_r($book);exit;
    switch ($task) {
        case 'apply':
            //$_POST['bid'] = $book->id;
// 	$_REQUEST['task'] = 'edit';
            mosRedirect("index.php?option=" . $option . "&task=edit&bid[]=" . $book->id);
            break;

        case 'save':
            mosRedirect("index.php?option=" . $option);
            break;
    }
}

/**
 * Deletes one or more records
 * @param array - An array of unique category id numbers
 * @param string - The current author option
 */
function removeBooks($bid, $option) {

    global $database, $book;

    if (!is_array($bid) || count($bid) < 1) {
        echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
        exit;
    }

    if (count($bid)) {
        foreach ($bid as $id) {
            $books4del = new mosBooklibrary($database);
            $books4del->load($id);
            $books4del->delete();
            //echo "<br /><pre>" . print_r($books4del, true) . "</pre>"; 
        }
    }

    mosRedirect("index.php?option=$option");
}

/**
 * Publishes or Unpublishes one or more records
 * @param array - An array of unique category id numbers
 * @param integer - 0 if unpublishing, 1 if publishing
 * @param string - The current author option
 */
function publishBooks($bid, $publish, $option) {

    global $database, $my;

    $catid = mosGetParam($_POST, 'catid', array(0));

    if (!is_array($bid) || count($bid) < 1) {
        $action = $publish ? 'publish' : 'unpublish';
        echo "<script> alert('Select an item to " . addslashes($action) . "'); window.history.go(-1);</script>\n";
        exit;
    }

    $bids = implode(',', $bid);

    $database->setQuery("UPDATE #__booklibrary SET published='$publish'" .
            "\nWHERE id IN ($bids) AND (checked_out=0 OR (checked_out='$my->id'))");
    if (!$database->query()) {
        echo "<script> alert('" . addslashes($database->getErrorMsg()) . "'); window.history.go(-1); </script>\n";
        exit();
    }

    if (count($bid) == 1) {
        $row = new mosBookLibrary($database);
        $row->checkin($bid[0]);
    }

    mosRedirect("index.php?option=$option");
}

/**
 * Approve or Unapprove one or more records
 * @param array - An array of unique category id numbers
 * @param integer - 0 if unapprove, 1 if approve
 * @param string - The current author option
 */
function approveBooks($bid, $approve, $option) {

    global $database, $my;
    $catid = mosGetParam($_POST, 'catid', array(0));

    if (!is_array($bid) || count($bid) < 1) {
        $action = $approve ? 'approve' : 'unapprove';
        echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
        exit;
    }

    $bids = implode(',', $bid);

//     $database->setQuery("UPDATE #__booklibrary SET approved='$approve'" .
//             "\nWHERE id IN ($bids) AND (checked_out=0 OR (checked_out='$my->id'))");

    $database->setQuery("UPDATE #__booklibrary SET published=$approve, approved='$approve'" .
            "\nWHERE id IN ($bids) AND (checked_out=0 OR (checked_out='$my->id'))");
    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    if (count($bid) == 1) {
        $row = new mosBookLibrary($database);
        $row->checkin($bid[0]);
    }

    mosRedirect("index.php?option=$option");
}

/**
 * Moves the order of a record
 * @param integer - The increment to reorder by
 */
function orderBooks($bid, $inc, $option) {

    global $database;

    $book = new mosBookLibrary($database);
    $book->load($bid);
    $book->move($inc);

    mosRedirect("index.php?option=$option");
}

/**
 * Cancels an edit operation
 * @param string - The current author option
 */
function cancelBook($option) {

    global $database;

    $row = new mosBookLibrary($database);
    $row->bind($_POST);
    $row->checkin();
    mosRedirect("index.php?option=$option");
}

/**
 * Refetches the book information
 * @param string - The current author option
 */



function refetchInfo($option, $bid) {

    global $database, $my, $booklibrary_configuration;

    $informationFrom = mosGetParam($_POST, 'informationFrom');

    if (!is_array($bid) || count($bid) < 1) {
        echo "<script> alert('Select an item to refetch'); window.history.go(-1);</script>\n";
        exit;
    }

    $bids = implode(',', $bid);
    $database->setQuery("SELECT id, bookid, isbn, title, informationFrom from #__booklibrary WHERE id IN ($bids)");

    if (!$database->query()) {
        echo "<script> alert('" . addslashes($database->getErrorMsg()) . "'); window.history.go(-1); </script>\n";
        exit();
    }

    $books = $database->loadObjectList();

    // get list of WS
    $retVal = mosBooklibraryWS :: getWSArray();
    $ws = null;
    for ($i = 0, $n = count($retVal); $i < $n; $i++) {
        $help = $retVal[$i];
        $ws[] = mosHTML :: makeOption($help[0], $help[1]);
    }
    $wslist = mosHTML :: selectList($ws, 'informationFrom', 'class="inputbox" size="1"', 'value', 'text');

    if ($informationFrom == null) {
        //show fetching information
        HTML_booklibrary :: refetchBoosks($option, $books, $wslist);
    } else {
        //fetching information	
        $infos = array();
        $id = array_pop($bid);

        while ($id != null) {

            $book = new mosBookLibrary($database);
            $book->load($id);
            $book->informationFrom = $informationFrom;

            $book_tmp = $book;

            $book = mosBooklibraryWS :: fetchInfos($book);

            if (is_string($book)) {
                //there was an error while fetching!
                array_push($infos, array($book_tmp->id, $book_tmp->bookid, $book_tmp->isbn, $book));
            } else {
                //storing pictures if neccesary	
                $retVal = null;
                if (intval($booklibrary_configuration['fetchImages']['boolean']) == 1) {
                    $retVal = mosBooklibraryOthers :: storeImageFile($book, null);
                }

                //fetching was OK!
                $book->date = date("Y-m-d H:i:s");

                if ($retVal != null) {
                    // error storing picture
                    array_push($infos, array($book->id, $book->bookid, $book->isbn, $retVal));
                } else if (!$book->check() || !$book->store()) {
                    //error while storing information!
                    array_push($infos, array($book->id, $book->bookid, $book->isbn, $book->getError()));
                } else {

                    array_push($infos, array($book->id, $book->bookid, $book->isbn, "OK"));
                }
                $book->checkin();
            }
            $id = array_pop($bid);
        }
        $infos = array_reverse($infos);
        HTML_booklibrary :: showInfoRefetchBooks($option, $infos, $wslist);
    }
}

function configure_list_set_value($parametr) {
    $str = "";
    if (isset($parametr) && $parametr != 0) {
        $str = implode(',', $parametr);
        return $str;
    }
    else
        return 0;
}

function configure_save_frontend($option) {
    global $my, $booklibrary_configuration;

    $booklibrary_configuration['ebooks']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'ebooks_registrationlevel', 0));
    $booklibrary_configuration['reviews']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'reviews_registrationlevel', 0));
    $booklibrary_configuration['approve_review']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'approve_review_registrationlevel', 0));
    $booklibrary_configuration['litpage']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'litpage_registrationlevel', 0));
    $booklibrary_configuration['litpage']['show'] = mosGetParam($_POST, 'litpage_show', 0);
    $booklibrary_configuration['lendrequest']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'lendrequest_registrationlevel', 0));
    $booklibrary_configuration['price']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'price_registrationlevel', 0));
    $booklibrary_configuration['review_added_email']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'review_added_email_registrationlevel', 0));
    $booklibrary_configuration['review_added_email']['show'] = mosGetParam($_POST, 'review_added_email_show', 0);
    $booklibrary_configuration['suggest_added_email']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'suggest_added_email_registrationlevel', 0));
    $booklibrary_configuration['suggest_added_email']['show'] = mosGetParam($_POST, 'suggest_added_email_show', 0);
    $booklibrary_configuration['lendrequest_email']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'lendrequest_email_registrationlevel', 0));
    $booklibrary_configuration['lendrequest_email']['show'] = mosGetParam($_POST, 'lendrequest_email_show', 0);
    $booklibrary_configuration['addbook_email']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'addbook_email_registrationlevel', 0));
    $booklibrary_configuration['addbook_email']['show'] = mosGetParam($_POST, 'addbook_email_show', 0);
    $booklibrary_configuration['advsearch']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'advsearch_registrationlevel', 0));
    $booklibrary_configuration['advsearch']['show'] = mosGetParam($_POST, 'advsearch_show', 0);
    $booklibrary_configuration['search_field']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'search_field_registrationlevel', 0));
    $booklibrary_configuration['search_field']['show'] = mosGetParam($_POST, 'search_field_show', 0);
    $booklibrary_configuration['addtocart_button']['allow']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'addtocart_button_registrationlevel', 0));
    $booklibrary_configuration['addtocart_button']['show'] = mosGetParam($_POST, 'addtocart_button_show', 0);
    $booklibrary_configuration['rss']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'rss_registrationlevel', 0));
    $booklibrary_configuration['rss']['show'] = mosGetParam($_POST, 'rss_show', 0);
    $booklibrary_configuration['mail_to']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'mail_to_registrationlevel', 0));
    $booklibrary_configuration['mail_to']['show'] = mosGetParam($_POST, 'mail_to_show', 0);
    $booklibrary_configuration['print_view']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'print_view_registrationlevel', 0));
    $booklibrary_configuration['print_view']['show'] = mosGetParam($_POST, 'print_view_show', 0);
    $booklibrary_configuration['print_pdf']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'print_pdf_registrationlevel', 0));
    $booklibrary_configuration['print_pdf']['show'] = mosGetParam($_POST, 'print_pdf_show', 0);
    $booklibrary_configuration['buy_now']['allow']['categories'] = configure_list_set_value(mosGetParam($_POST, 'buy_now_allow_categories', 0));
    $booklibrary_configuration['buy_now']['show'] = mosGetParam($_POST, 'buy_now_show', 0);
    $booklibrary_configuration['addbook_button']['allow']['categories'] = configure_list_set_value(mosGetParam($_POST, 'addbook_button_allow_categories', 0));
    $booklibrary_configuration['addbook_button']['allow']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'addbook_button_registrationlevel', 0));
    $booklibrary_configuration['addbook_button']['show'] = mosGetParam($_POST, 'addbook_button_show', 0);
    $booklibrary_configuration['cb_mybook']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'cb_mybook_registrationlevel', 0));
    $booklibrary_configuration['cb_edit']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'cb_edit_registrationlevel', 0));
    $booklibrary_configuration['cb_rent']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'cb_rent_registrationlevel', 0));
    $booklibrary_configuration['cb_history']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'cb_history_registrationlevel', 0));
    $booklibrary_configuration['cb']['show'] = mosGetParam($_POST, 'cb_show', 0);
    $booklibrary_configuration['cb_mybook']['show'] = mosGetParam($_POST, 'cb_show_mybook', 0);
    $booklibrary_configuration['cb_edit']['show'] = mosGetParam($_POST, 'cb_show_edit', 0);
    $booklibrary_configuration['cb_rent']['show'] = mosGetParam($_POST, 'cb_show_rent', 0);
    $booklibrary_configuration['cb_history']['show'] = mosGetParam($_POST, 'cb_show_history', 0);
    $booklibrary_configuration['add_suggest']['show'] = mosGetParam($_POST, 'add_suggest_show', 0);
    $booklibrary_configuration['my_books_button']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'my_books_button_registrationlevel', 0));
    $booklibrary_configuration['my_books_button']['show'] = mosGetParam($_POST, 'my_books_button', 0);
    $booklibrary_configuration['reviews']['show'] = mosGetParam($_POST, 'reviews_show', 0);
    $booklibrary_configuration['lendstatus']['show'] = mosGetParam($_POST, 'lendstatus_show', 0);
    $booklibrary_configuration['ebooks']['show'] = mosGetParam($_POST, 'ebooks_show', 0);
    $booklibrary_configuration['price']['show'] = mosGetParam($_POST, 'price_show', 0);
    $booklibrary_configuration['foto']['high'] = mosGetParam($_POST, 'foto_high');
    $booklibrary_configuration['foto']['width'] = mosGetParam($_POST, 'foto_width');
    $booklibrary_configuration['page']['items'] = mosGetParam($_POST, 'page_items');
    $booklibrary_configuration['license']['show'] = mosGetParam($_POST, 'license_show');
    $booklibrary_configuration['cat_pic']['show'] = mosGetParam($_POST, 'cat_pic_show');
    $booklibrary_configuration['subcategory']['show'] = mosGetParam($_POST, 'subcategory_show');
    $booklibrary_configuration['category']['default_sort'] = mosGetParam($_POST, 'category_default_sort');
    $booklibrary_configuration['owner']['show'] = mosGetParam($_POST, 'owner_show');
    $booklibrary_configuration['all_categories'] = mosGetParam($_POST, 'all_categories');
    $booklibrary_configuration['view_type'] = mosGetParam($_POST, 'view_type');
    $booklibrary_configuration['view_book'] = mosGetParam($_POST, 'view_book');
    $booklibrary_configuration['search_lay'] = mosGetParam($_POST, 'search_lay');
    $booklibrary_configuration['books'] = mosGetParam($_POST, 'books');
    $booklibrary_configuration['ownerslist_page'] = mosGetParam($_POST, 'ownerslist_page');
    $booklibrary_configuration['approve_on_add']['show'] = mosGetParam($_POST, 'approve_on_add');
    $booklibrary_configuration['approve_on_add']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'approve_on_add_registrationlevel', 0));
    $booklibrary_configuration['publish_on_add']['show'] = mosGetParam($_POST, 'publish_on_add');
    $booklibrary_configuration['publish_on_add']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'publish_on_add_registrationlevel', 0));
    $booklibrary_configuration['ownerslist']['show'] = mosGetParam($_POST, 'ownerslist_show');
    $booklibrary_configuration['ownerslist']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'ownerslist_registrationlevel', 0));
    $booklibrary_configuration['allowed_exts'] = mosGetParam($_POST, 'allowed_exts', "");
    $booklibrary_configuration['allowed_exts_img'] = mosGetParam($_POST, 'allowed_exts_img', "");
    $booklibrary_configuration['price_format'] = $_POST['patern'];
    $booklibrary_configuration['date_format'] = mosGetParam($_POST, 'date_format');
    $booklibrary_configuration['datetime_format'] = mosGetParam($_POST, 'datetime_format');
    $booklibrary_configuration['price_unit_show'] = $_POST['price_unit_show'];

    if (isset($_REQUEST['add_suggest_registrationlevel']))
        $booklibrary_configuration['add_suggest']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'add_suggest_registrationlevel', 0));
    else
        $booklibrary_configuration['add_suggest']['registrationlevel'] = 0;

    if (isset($_POST['LicenseField'])) {
        $booklibrary_configuration['license']['text'] = str_replace("\\", "", $_POST['LicenseField']);
    }
    mosBooklibraryOthers :: setParams();
    configure_frontend($option);
}

function configure_save_backend($option) {

    global $my, $booklibrary_configuration, $mosConfig_absolute_path;

    //**********   begin add merge description  *****************************
    $str = '';
    $supArr = mosGetParam($_POST, 'merge_description_registrationlevel', 0);
    $str = implode(',', $supArr);
    $booklibrary_configuration['merge_description']['registrationlevel'] = $str;
    $booklibrary_configuration['merge_description']['use'] = mosGetParam($_POST, 'merge_description_use', 0);
    //*********   end add merge description   *********
    //configure_backend_value('1');
    $booklibrary_configuration['review_added_email']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'review_added_email_registrationlevel', 0));
    // echo "<pre>" .print_r(mosGetParam($_POST, 'review_added_email_registrationlevel', 0), true). "</pre>"; exit;
    $booklibrary_configuration['review_added_email']['show'] = mosGetParam($_POST, 'review_added_email_show', 0);
    $booklibrary_configuration['review_email']['address'] = mosGetParam($_POST, 'review_email_address', ""); //back--1
    $booklibrary_configuration['suggest_email']['address'] = mosGetParam($_POST, 'suggest_email_address', "");
    $booklibrary_configuration['lendrequest_email']['address'] = mosGetParam($_POST, 'lendrequest_email_address', "");
    $booklibrary_configuration['addbook_email']['address'] = mosGetParam($_POST, 'addbook_email_address', "");
    $booklibrary_configuration['bookid']['auto-increment']['boolean'] = mosGetParam($_POST, 'bookid_auto_increment_boolean', 0);
    $booklibrary_configuration['fetchImages']['boolean'] = mosGetParam($_POST, 'fetchImages_boolean', 0);
    $booklibrary_configuration['ebooks']['allow'] = mosGetParam($_POST, 'ebooks_allow', 0);
    $booklibrary_configuration['ws']['amazon']['tag'] = mosGetParam($_POST, 'ws_amazon_tag', "gerdsaurer-20");
    $booklibrary_configuration['ws']['amazon']['devtag'] = mosGetParam($_POST, 'ws_amazon_devtag');
    $booklibrary_configuration['ws']['amazon']['secret_key'] = mosGetParam($_POST, 'ws_amazon_secret_key');
    $booklibrary_configuration['editbook']['check']['isbn'] = mosGetParam($_POST, 'editbook_check_isbn', 0); //back--7
    $booklibrary_configuration['editbook']['default']['host'] = mosGetParam($_POST, 'editbook_default_host', 0);
    $booklibrary_configuration['editbook']['default']['lang'] = mosGetParam($_POST, 'editbook_default_lang', 0);
    $booklibrary_configuration['suggest_added_email']['show'] = mosGetParam($_POST, 'suggest_added_email_show', 0);
    $booklibrary_configuration['proxy_server']['address'] = mosGetParam($_POST, 'proxy_server_adress', "");
    $booklibrary_configuration['port_proxy_server']['address'] = mosGetParam($_POST, 'port_proxy_server_adress', "");
    $booklibrary_configuration['login_proxy_server']['address'] = mosGetParam($_POST, 'login_proxy_server_adress', "");
    $booklibrary_configuration['password_proxy_server']['address'] = mosGetParam($_POST, 'password_proxy_server_adress', "");
    $booklibrary_configuration['suggest_added_email']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'suggest_added_email_registrationlevel', 0));
    $booklibrary_configuration['lendrequest_admin_decline_accept'] = mosGetParam($_POST, 'lendrequest_admin_decline_accept', "");
    $booklibrary_configuration['lendrequest_email']['show'] = mosGetParam($_POST, 'lendrequest_email_show', 0);
    $booklibrary_configuration['update'] = mosGetParam($_POST, 'update', "no");
    $booklibrary_configuration['lend_answer'] = mosGetParam($_POST, 'lend_answer', 0);
    //$booklibrary_configuration['lend_form'] = str_replace("\\", "", $_REQUEST['lend_form']);
    $booklibrary_configuration['lend_before_end_notify'] = mosGetParam($_POST, 'lend_before_end_notify', 0);
    $booklibrary_configuration['lend_before_end_notify_days'] = mosGetParam($_POST, 'lend_before_end_notify_days', "2");
    $booklibrary_configuration['lend_before_end_notify_email'] = mosGetParam($_POST, 'lend_before_end_notify_email', "");
    $booklibrary_configuration['lendrequest_email']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'lendrequest_email_registrationlevel', 0));
    $booklibrary_configuration['allowed_exts'] = mosGetParam($_POST, 'allowed_exts', "");
    $booklibrary_configuration['allowed_exts_img'] = mosGetParam($_POST, 'allowed_exts_img', "");
    $booklibrary_configuration['addbook_email']['show'] = mosGetParam($_POST, 'addbook_email_show', 0);
    $booklibrary_configuration['addbook_email']['registrationlevel'] = configure_list_set_value(mosGetParam($_POST, 'addbook_email_registrationlevel', 0));

    mosBooklibraryOthers :: setParams();

    configure_backend($option);
}

function configure_frontend($option) {
    global $my, $booklibrary_configuration, $acl, $database;
    global $mosConfig_absolute_path;

    $yesno[] = mosHTML :: makeOption('1', _BOOKLIBRARY_YES);
    $yesno[] = mosHTML :: makeOption('0', _BOOKLIBRARY_NO);

    $gtree[] = mosHTML :: makeOption('-2', 'Everyone');
    $gtree = get_group_children_tree_bl();

    $lists = array();

    $f = "";
    $s = explode(',', $booklibrary_configuration['reviews']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['reviews']['show'] = mosHTML :: RadioList($yesno, 'reviews_show', 'class="inputbox"', $booklibrary_configuration['reviews']['show'], 'value', 'text');
    $lists['reviews']['registrationlevel'] = mosHTML::selectList($gtree, 'reviews_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = "";
    $s = explode(',', $booklibrary_configuration['litpage']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['litpage']['show'] = mosHTML :: RadioList($yesno, 'litpage_show', 'class="inputbox"', $booklibrary_configuration['litpage']['show'], 'value', 'text');
    $lists['litpage']['registrationlevel'] = mosHTML::selectList($gtree, 'litpage_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = "";
    $s = explode(',', $booklibrary_configuration['lendrequest']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['lendstatus']['show'] = mosHTML :: RadioList($yesno, 'lendstatus_show', 'class="inputbox"', $booklibrary_configuration['lendstatus']['show'], 'value', 'text');

    $lists['lendrequest']['registrationlevel'] = mosHTML::selectList($gtree, 'lendrequest_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = "";
    $s = explode(',', $booklibrary_configuration['ebooks']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['ebooks']['registrationlevel'] = mosHTML::selectList($gtree, 'ebooks_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    $lists['ebooks']['show'] = mosHTML :: RadioList($yesno, 'ebooks_show', 'class="inputbox"', $booklibrary_configuration['ebooks']['show'], 'value', 'text');

    $f = "";
    $s = explode(',', $booklibrary_configuration['price']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['price']['show'] = mosHTML :: RadioList($yesno, 'price_show', 'class="inputbox"', $booklibrary_configuration['price']['show'], 'value', 'text');
    $lists['price']['registrationlevel'] = mosHTML::selectList($gtree, 'price_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    // _______________- community builder section -_______________
  
    // _______________- end community builder section -_______________
    //********   begin add send mail for admin  ******************
    $f = "";
    $s = explode(',', $booklibrary_configuration['review_added_email']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['review_added_email']['show'] = mosHTML :: RadioList($yesno, 'review_added_email_show', 'class="inputbox"', $booklibrary_configuration['review_added_email']['show'], 'value', 'text');
    $lists['review_added_email']['registrationlevel'] = mosHTML::selectList($gtree, 'review_added_email_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = "";
    $s = explode(',', $booklibrary_configuration['suggest_added_email']['registrationlevel'
            ]);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['suggest_added_email']['show'] = mosHTML :: RadioList($yesno, 'suggest_added_email_show', 'class="inputbox"', $booklibrary_configuration['suggest_added_email']['show'], 'value', 'text');
    $lists['suggest_added_email']['registrationlevel'] = mosHTML::selectList($gtree, 'suggest_added_email_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = "";
    $s = explode(',', $booklibrary_configuration['lendrequest_email']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['lendrequest_email']['show'] = mosHTML :: RadioList($yesno, 'lendrequest_email_show', 'class="inputbox"', $booklibrary_configuration['lendrequest_email']['show'], 'value', 'text');
    $lists['lendrequest_email']['registrationlevel'] = mosHTML::selectList($gtree, 'lendrequest_email_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    $f = "";
    $s = explode(',', $booklibrary_configuration['addbook_email']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['addbook_email']['show'] = mosHTML :: RadioList($yesno, 'addbook_email_show', 'class="inputbox"', $booklibrary_configuration['addbook_email']['show'], 'value', 'text');
    $lists['addbook_email']['registrationlevel'] = mosHTML::selectList($gtree, 'addbook_email_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    $f = "";
    $s = explode(',', $booklibrary_configuration['search_field']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['search_field']['show'] = mosHTML :: RadioList($yesno, 'search_field_show', 'class="inputbox"', $booklibrary_configuration['search_field']['show'], 'value', 'text');
    $lists['search_field']['registrationlevel'] = mosHTML::selectList($gtree, 'search_field_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    //********   end add send mail for admin   **********************
    //********** RSS ************
    
//********** END RSS ************
//********** SHOW MY BOOKS ************

//********** END SHOW MY BOOKS ************
//********** PUBLISH ADDED BOOK ************
    $f = "";
    $s = explode(',', $booklibrary_configuration['publish_on_add']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['publish_on_add']['registrationlevel'] = mosHTML::selectList($gtree, 'publish_on_add_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    $lists['publish_on_add']['show'] = mosHTML :: RadioList($yesno, 'publish_on_add', 'class="inputbox"', $booklibrary_configuration['publish_on_add']['show'], 'value', 'text');
//********** END PUBLISH ADDED BOOK ************
//----------------------------------------
//********** PDF  VIEW ************

    //********** END PRINT VIEW ************
    //********** MAIL TO ************
    $f = "";
    $s = explode(',', $booklibrary_configuration['mail_to']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['mail_to']['show'] = mosHTML :: RadioList($yesno, 'mail_to_show', 'class="inputbox"', $booklibrary_configuration['mail_to']['show'], 'value', 'text');
    $lists['mail_to']['registrationlevel'] = mosHTML::selectList($gtree, 'mail_to_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //********** END MAIL TO ************
    //-----------------------
//********   begin add button 'buy now'   ************************
    $ctree[] = mosHTML :: makeOption('-2', 'All Categories');

    $id = 0;
    $list = CAT_Utils::categoryArray();
    $cat = new mainBooklibraryCategories($database); //for 1.6
    $cat->load($id);

    $this_treename = '';
    $options = array();
    foreach ($list as $item) {
        if ($this_treename) {
            if ($item->id != $cat->id && strpos($item->treename, $this_treename) === false) {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
            }
        } else {
            if ($item->id != $cat->id) {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
            } else {
                $this_treename = "$item->treename/";
            }
        }
    }
    //$ctree = array_merge($ctree, $options);
    if (count($options) > 0)
        $ctree = array_merge($ctree, $options);

    $f = "";
    $s = explode(',', $booklibrary_configuration['buy_now']['allow']['categories']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['buy_now']['show'] = mosHTML :: RadioList($yesno, 'buy_now_show', 'class="inputbox"', $booklibrary_configuration['buy_now']['show'], 'value', 'text');

    $lists['buy_now']['allow']['categories'] = mosHTML::selectList($ctree, 'buy_now_allow_categories[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
//*************   end add button 'buy now'   ************************
//********   begin add button 'buy now vm'   ************************
    unset($ctree);
    unset($options);
    $ctree[] = mosHTML :: makeOption('-2', 'All Categories');

    $id = 0;
    $list = CAT_Utils::categoryArray();
    $cat = new mainBooklibraryCategories($database); //for 1.6
    $cat->load($id);

    $this_treename = '';
    foreach ($list as $item) {
        if ($this_treename) {
            if ($item->id != $cat->id && strpos($item->treename, $this_treename) === false) {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
            }
        } else {
            if ($item->id != $cat->id) {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
            } else {
                $this_treename = "$item->treename/";
            }
        }
    }
    $ctree = array_merge($ctree, $options);
  
//*************   end add button 'buy now vm'   ************************
//******   begin add for Manager Suggestion: button 'Suggest a book'   *****

    $f_r = '';
    $s = explode(',', $booklibrary_configuration['addbook_button']['allow']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f_r[] = mosHTML::makeOption($s[$i]);
    unset($f);
    $f = "";
    $s = explode(',', $booklibrary_configuration['addbook_button']['allow']['categories']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['addbook_button']['show'] = mosHTML :: RadioList($yesno, 'addbook_button_show', 'class="inputbox"', $booklibrary_configuration['addbook_button']['show'], 'value', 'text');
    $lists['addbook_button']['allow']['categories'] = mosHTML::selectList($ctree, 'addbook_button_allow_categories[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    $lists['addbook_button']['allow']['registrationlevel'] = mosHTML::selectList($gtree, 'addbook_button_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f_r);
    //print_r($ctree);exit;
//*************   end add button 'buy now'   ************************
   

//******   begin add for Manager Suggestion: button 'Suggest a book'   *****

//*******   end add for Manager Suggestion: button 'Suggest a book'   *******

    $query = 'show columns from #__booklibrary';
    $database->setQuery($query);
    $columns = $database->loadObjectList();

    $col_list[] = mosHTML::makeOption('title', 'title');
    $col_list[] = mosHTML::makeOption('authors', 'authors');
    $col_list[] = mosHTML::makeOption('rating', 'rating');
    $col_list[] = mosHTML::makeOption('hits', 'hits');


    $lists['category']['default_sort'] = mosHTML::selectList($col_list, 'category_default_sort', '', 'value', 'text', $booklibrary_configuration['category']['default_sort']);
    $lists['foto']['high'] = '<input type="text" name="foto_high" value="' . $booklibrary_configuration['foto']['high'] . '" class="inputbox" size="4" maxlength="4" title="" />';
    $lists['foto']['width'] = '<input type="text" name="foto_width" value="' . $booklibrary_configuration['foto']['width'] . '" class="inputbox" size="4" maxlength="4" title="" />';
    $lists['page']['items'] = '<input type="text" name="page_items" value="' . $booklibrary_configuration['page']['items'] . '" class="inputbox" size="3" maxlength="3" title="" />';
    $lists['license']['show'] = mosHTML :: RadioList($yesno, 'license_show', 'class="inputbox"', $booklibrary_configuration['license']['show'], 'value', 'text');
    $txt = $booklibrary_configuration['license']['text'];
    //add for show in category picture
    $lists['cat_pic']['show'] = mosHTML :: RadioList($yesno, 'cat_pic_show', 'class="inputbox"', $booklibrary_configuration['cat_pic']['show'], 'value', 'text');
    //add for show subcategory 
    $lists['subcategory']['show'] = mosHTML :: RadioList($yesno, 'subcategory_show', 'class="inputbox"', $booklibrary_configuration['subcategory']['show'], 'value', 'text');
    $lists['owner']['show'] = mosHTML :: RadioList($yesno, 'owner_show', 'class="inputbox"', $booklibrary_configuration['owner']['show'], 'value', 'text');

//******   begin approve_on_add  *****
    $f = "";
    $s = explode(',', $booklibrary_configuration['approve_on_add']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['approve_on_add']['show'] = mosHTML :: RadioList($yesno, 'approve_on_add', 'class="inputbox"', $booklibrary_configuration['approve_on_add']['show'], 'value', 'text');
    $lists['approve_on_add']['registrationlevel'] = mosHTML::selectList($gtree, 'approve_on_add_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
//*******   end approve_on_add   ******* 
//********** Owners list ************
    $f = "";
    $s = explode(',', $booklibrary_configuration['ownerslist']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['ownerslist']['show'] = mosHTML :: RadioList($yesno, 'ownerslist_show', 'class="inputbox"', $booklibrary_configuration['ownerslist']['show'], 'value', 'text');
    $lists['ownerslist']['registrationlevel'] = mosHTML::selectList($gtree, 'ownerslist_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);

    //***************************************************

    $component_path = JPath::clean(JPATH_SITE . '/components/com_booklibrary/views/all_categories/tmpl');
    $component_layouts = array();
    $options = array();
    if (is_dir($component_path)
            && ($component_layouts = JFolder::files($component_path, '^[^_]*\.php$', false, true))
    ) {
        foreach ($component_layouts as $i => $file) {
            $select_file_name = pathinfo($file);
            $select_file_name = $select_file_name['filename'];
            $all_categories[] = JHtml::_('select.option', $select_file_name, $select_file_name);
        }
    }

    $lists['all_categories'] = mosHTML::selectList($all_categories, 'all_categories', 'size="1" ', 'value', 'text', $booklibrary_configuration['all_categories']);

    $component_path = JPath::clean(JPATH_SITE . '/components/com_booklibrary/views/alone_category/tmpl');
    $component_layouts = array();
    $options = array();
    if (is_dir($component_path)
            && ($component_layouts = JFolder::files($component_path, '^[^_]*\.php$', false, true))
    ) {
        foreach ($component_layouts as $i => $file) {
            $select_file_name = pathinfo($file);
            $select_file_name = $select_file_name['filename'];
            $view_type[] = JHtml::_('select.option', $select_file_name, $select_file_name);
        }
    }

    $lists['view_type'] = mosHTML::selectList($view_type, 'view_type', 'size="1" ', 'value', 'text', $booklibrary_configuration['view_type']);

    // ---------Layouts for search-------------
    $component_path = JPath::clean(JPATH_SITE . '/components/com_booklibrary/views/show_search_book/tmpl');
    $component_layouts = array();
    $options = array();
    if (is_dir($component_path)
            && ($component_layouts = JFolder::files($component_path, '^[^_]*\.php$', false, true))
    ) {
        foreach ($component_layouts as $i => $file) {
            $select_file_name = pathinfo($file);
            $select_file_name = $select_file_name['filename'];
            $search_lay[] = JHtml::_('select.option', $select_file_name, $select_file_name);
        }
    }

    $lists['search_lay'] = mosHTML::selectList($search_lay, 'search_lay', 'size="1" ', 'value', 'text', $booklibrary_configuration['search_lay']);
    //************************

 
    //---------------------------------
    $money_ditlimer = array();
    $money_ditlimer[] = JHtml::_('select.option', ".", "Point (12.134.123,12)");
    $money_ditlimer[] = JHtml::_('select.option', ",", "Comma (12,134,123.12)");
    $money_ditlimer[] = JHtml::_('select.option', "space", "Space (12 134 123,12)");
    $money_ditlimer[] = JHtml::_('select.option', "other", "Youre ditlimer: ");

    $price_unit_show[] = mosHTML :: makeOption('1', _BOOKLIBRARY_PRICE_UNIT_SHOW_AFTER);
    $price_unit_show[] = mosHTML :: makeOption('0', _BOOKLIBRARY_PRICE_UNIT_SHOW_BEFORE);

    $selecter = '';
    switch ($booklibrary_configuration['price_format']) {

        case '.':
            $selecter = '.';
            break;

        case ',':
            $selecter = ',';
            break;

        case '&nbsp;':
            $selecter = 'space';
            break;

        default:
            $selecter = 'other';
    }
    // 1 - affter 0 - beffore
    $lists['price_unit_show'] = mosHTML :: RadioList($price_unit_show, 'price_unit_show', 'class="inputbox"', $booklibrary_configuration['price_unit_show'], 'value', 'text');
    $lists['money_ditlimer'] = mosHTML::selectList($money_ditlimer, 'money_select', 'size="1"
                                                   onchange="set_pricetype(this)"', 'value', 'text', $selecter);
    $lists['date_format'] = '<input type="text" name="date_format" value="'
            . $booklibrary_configuration['date_format']
            . '" class="inputbox"  title="" />';
    $lists['datetime_format'] = '<input type="text" name="datetime_format" value="'
            . $booklibrary_configuration['datetime_format']
            . '" class="inputbox" title="" />';
    //----------------------
    $lists['allowed_exts'] = '<input type="text" name="allowed_exts" value="'
            . $booklibrary_configuration['allowed_exts']
            . '" class="inputbox" size="50" maxlength="1500" title=""/>';
    $lists['allowed_exts_img'] = '<input type="text" name="allowed_exts_img" value="'
            . $booklibrary_configuration['allowed_exts_img']
            . '" class="inputbox" size="50" maxlength="1500" title=""/>';
    //----------------------    
    //******   begin approve_review  *****
    $f = "";
    $s = explode(',', $booklibrary_configuration['approve_review']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['approve_review']['show'] = mosHTML :: RadioList($yesno, 'approve_review', 'class="inputbox"', $booklibrary_configuration['approve_review']['show'], 'value', 'text');
    $lists['approve_review']['registrationlevel'] = mosHTML::selectList($gtree, 'approve_review_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    //*******   end approve_review   *******

    HTML_booklibrary :: showConfiguration_frontend($lists, $option, $txt);
}

function cutSlash($S) {
    $S2 = '';
    for ($i = 0; $i < strlen($S); $i++) {
        if ($S[$i] != '\\') {
            $S2.=$S[$i];
        } else {
            $i++;
            if ($S[$i] == '"') {
                $S2.='&#34';
            } else {
                $S2.=$S[$i];
            }
        }
    }

    return $S2;
}

function configure_backend($option) {

    global $my, $booklibrary_configuration, $acl;
    global $mosConfig_absolute_path;

    $yesno[] = mosHTML :: makeOption('1', _BOOKLIBRARY_YES);
    $yesno[] = mosHTML :: makeOption('0', _BOOKLIBRARY_NO);

    $gtree[] = mosHTML :: makeOption('-2', 'Everyone');
    $gtree = get_group_children_tree_bl();

    $lists = array();

    $f = array();
    $s = explode(',', $booklibrary_configuration['review_added_email']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['review_added_email']['registrationlevel'] = mosHTML::selectList($gtree, 'review_added_email_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    $lists['review_added_email']['show'] = mosHTML :: RadioList($yesno, 'review_added_email_show', 'class="inputbox"', $booklibrary_configuration['review_added_email']['show'], 'value', 'text');


    $f = array();
    $s = explode(',', $booklibrary_configuration['suggest_added_email']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['suggest_added_email']['show'] = mosHTML :: RadioList($yesno, 'suggest_added_email_show', 'class="inputbox"', $booklibrary_configuration['suggest_added_email']['show'], 'value', 'text', $booklibrary_configuration['suggest_added_email']['show']);
    $lists['suggest_added_email']['registrationlevel'] = mosHTML::selectList($gtree, 'suggest_added_email_registrationlevel[] ', 'size="4" multiple="multiple"', 'value', 'text', $f);
    $f = array();
    $s = explode(',', $booklibrary_configuration['lendrequest_email']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['lendrequest_email']['show'] = mosHTML :: RadioList($yesno, 'lendrequest_email_show', 'class="inputbox"', $booklibrary_configuration['lendrequest_email']['show'], 'value', 'text');
    $lists['lendrequest_email']['registrationlevel'] = mosHTML::selectList($gtree, 'lendrequest_email_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);
    $f = array();
    $s = explode(',', $booklibrary_configuration['addbook_email']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);
    $lists['addbook_email']['show'] = mosHTML :: RadioList($yesno, 'addbook_email_show', 'class="inputbox"', $booklibrary_configuration['addbook_email']['show'], 'value', 'text');
    $lists['addbook_email']['registrationlevel'] = mosHTML::selectList($gtree, 'addbook_email_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f);



    $lists['review_email']['address'] = '<input type="text" name="review_email_address" value="'
            . $booklibrary_configuration['review_email']['address']
            . '" class="inputbox" size="50" maxlength="50" title="" />'; //back--1
    $lists['suggest_email']['address'] = '<input type="text" name="suggest_email_address" value="'
            . $booklibrary_configuration['suggest_email']['address']
            . '" class="inputbox" size="50" maxlength="50" title="" />';
    $lists['lendrequest_email']['address'] = '<input type="text" name="lendrequest_email_address" value="'
            . $booklibrary_configuration['lendrequest_email']['address']
            . '" class="inputbox" size="50" maxlength="50" title="" />';
    $lists['addbook_email']['address'] = '<input type="text" name="addbook_email_address" value="'
            . $booklibrary_configuration['addbook_email']['address']
            . '" class="inputbox" size="50" maxlength="50" title="" />';
   
    $lists['bookid']['auto-increment']['boolean'] = mosHTML :: RadioList(
                    $yesno, 'bookid_auto_increment_boolean', 'class="inputbox"', $booklibrary_configuration['bookid']['auto-increment']['boolean'], 'value', 'text'
    );

    //********   begin add merge description   ************************
    $f = array();
    $s = explode(',', $booklibrary_configuration['merge_description']['registrationlevel']);
    for ($i = 0; $i < count($s); $i++)
        $f[] = mosHTML::makeOption($s[$i]);

    $lists['merge_description']['use'] = mosHTML :: RadioList(
                    $yesno, 'merge_description_use', 'class="inputbox"', $booklibrary_configuration['merge_description']['use'], 'value', 'text'
    );

    $lists['merge_description']['registrationlevel'] = mosHTML::selectList(
                    $gtree, 'merge_description_registrationlevel[]', 'size="4" multiple="multiple"', 'value', 'text', $f
    );
    //********   end add merge description  **********************

    $lists['fetchImages']['boolean'] = mosHTML :: RadioList($yesno, 'fetchImages_boolean', 'class="inputbox"', $booklibrary_configuration['fetchImages']['boolean'], 'value', 'text');
    $lists['fetchImages']['location'] = '<input disabled="disabled" type="text" name="fetchImages_location" value="' . $booklibrary_configuration['fetchImages']['location'] . '" class="inputbox" size="50" maxlength="50" title="" />';
    $lists['ebooks']['allow'] = mosHTML :: RadioList($yesno, 'ebooks_allow', 'class="inputbox"', $booklibrary_configuration['ebooks']['allow'], 'value', 'text');
    $lists['ebooks']['location'] = '<input disabled="disabled" type="text" name="ebooks_location" value="' . $booklibrary_configuration['ebooks']['location'] . '" class="inputbox" size="50" maxlength="50" title="" />';
    $lists['ws']['amazon']['tag'] = '<input type="text" name="ws_amazon_tag" value="' . $booklibrary_configuration['ws']['amazon']['tag'] . '" class="inputbox" size="50" maxlength="50" title="" />';
    $lists['ws']['amazon']['devtag'] = '<input type="text" name="ws_amazon_devtag" value="' . $booklibrary_configuration['ws']['amazon']['devtag'] . '" class="inputbox" size="50" maxlength="50" title="" />';
    $lists['ws']['amazon']['secret_key'] = '<input type="text" name="ws_amazon_secret_key" value="' . $booklibrary_configuration['ws']['amazon']['secret_key'] . '" class="inputbox" size="50" maxlength="50" title="" />';
    @$lists['editbook']['check']['isbn'] = mosHTML :: RadioList($yesno, 'editbook_check_isbn', 'class="inputbox"', $booklibrary_configuration['editbook']['check']['isbn'], 'value', 'text'); //back--7
    /////////////////////////////////ANTON CODE////////////////////////////////////
/////////////Proxy Relase
    $lists['proxy_server']['address'] = '<input type="text" name="proxy_server_adress" value="' . $booklibrary_configuration['proxy_server']['address'] . '"class="inputbox" size="50" maxlength="50" title="" />';
    $lists['port_proxy_server']['address'] = '<input type="text" name="port_proxy_server_adress" value="' . $booklibrary_configuration['port_proxy_server']['address'] . '"class="inputbox" size="50" maxlength="50" title="" />';
    $lists['login_proxy_server']['address'] = '<input type="text" name="login_proxy_server_adress" value="' . $booklibrary_configuration['login_proxy_server']['address'] . '"class="inputbox" size="50" maxlength="50" title="" />';
    $lists['password_proxy_server']['address'] = '<input type="text" name="password_proxy_server_adress" value="' . $booklibrary_configuration['password_proxy_server']['address'] . '"class="inputbox" size="50" maxlength="50" title="" />';

/////////////////////////////////ANTON CODE////////////////////////////////////
    $lists['update'] = mosHTML :: RadioList($yesno, 'update', 'class="inputbox"', $booklibrary_configuration['update'], 'value', 'text');
    //get language List
    $retVal1 = mosBooklibraryOthers :: getLanguageArray();
    $lang = null;
    for ($i = 0, $n = count($retVal1); $i < $n; $i++) {

        $help = $retVal1[$i];
        $lang[] = mosHTML :: makeOption($help[0], $help[1]);
    }
    $lists['editbook']['default']['lang'] = mosHTML :: selectList(
                    $lang, 'editbook_default_lang', 'class="inputbox" size="1"', 'value', 'text', $booklibrary_configuration['editbook']['default']['lang']
    );

    //get host List
    $retVal = mosBooklibraryWS :: getArray();
    $ws = null;

    for ($i = 0, $n = count($retVal); $i < $n; $i++) {
        $help = $retVal[$i];
        $ws[] = mosHTML :: makeOption($help[0], $help[1]);
    }

    $lists['editbook']['default']['host'] = mosHTML :: selectList($ws, 'editbook_default_host', 'class="inputbox" size="1"', 'value', 'text', intval($booklibrary_configuration['editbook']['default']['host']));
    $lists['lend_answer'] = mosHTML :: RadioList($yesno, 'lend_answer', 'class="inputbox"', $booklibrary_configuration['lend_answer'], 'value', 'text');
    $lists['lend_form'] = $booklibrary_configuration['lend_form'];
    $lists['ebooks']['registrationlevel'] = mosHTML::selectList($gtree, 'ebooks_registrationlevel', 'size="4"', 'value', 'text', $my->id); //(rus)--i tam i tam nado navernoe!
    $lists['lend_before_end_notify'] = mosHTML :: RadioList($yesno, 'lend_before_end_notify', 'class="inputbox"', $booklibrary_configuration['lend_before_end_notify'], 'value', 'text');
    $lists['lend_before_end_notify_days'] = '<input type="text" name="lend_before_end_notify_days" value="' . $booklibrary_configuration['lend_before_end_notify_days'] . '" class="inputbox" size="2" maxlength="2" title="" />';
    $lists['lend_before_end_notify_email'] = '<input type="text" name="lend_before_end_notify_email" value="' . $booklibrary_configuration['lend_before_end_notify_email'] . '" class="inputbox" size="50" maxlength="50" title="" />';

    HTML_booklibrary :: showConfiguration_backend($lists, $option);
}

//*****************************   end  moe   *********************


function lend($option, $bid) {

    global $database, $my;

    if (!is_array($bid) || count($bid) < 1) {
        echo "<script> alert('Select an item to lend'); window.history.go(-1);</script>\n";
        exit;
    }

    $bids = implode(',', $bid);

    $select = "SELECT a.*, l.id as lendid, l.lend_from as lend_from, " .
            "l.lend_return as lend_return, l.lend_until as lend_until, l.fk_userid as fk_userid, " .
            "l.user_name as user_name, l.user_email as user_email, u.name AS name, u.email as email" .
            "\nFROM #__booklibrary AS a" .
            "\nLEFT JOIN #__booklibrary_lend AS l ON l.id = a.fk_lendid" .
            "\nLEFT JOIN #__users AS u ON u.id = l.fk_userid" .
            "\nWHERE a.id in (" . $bids . ")";

    $database->setQuery($select);

    if (!$database->query()) {
        echo "<script> alert('" . addslashes($database->getErrorMsg()) . "'); window.history.go(-1); </script>\n";
        exit();
    }

    $books = $database->loadObjectList();

    // get list of categories

    $userlist[] = mosHTML :: makeOption('-1', '----------');
    $database->setQuery("SELECT id AS value, name AS text from #__users ORDER BY name");
    $userlist = array_merge($userlist, $database->loadObjectList());
    $usermenu = mosHTML :: selectList($userlist, 'userid', 'class="inputbox" size="1"', 'value', 'text', '-1');

    HTML_booklibrary :: showLendBooks($option, $books, $usermenu, "lend");
}

function edit_lend($option, $bid) {
    global $database, $my;
    if (!is_array($bid) || count($bid) < 1) {
        echo "<script> alert('Select an item to lend'); window.history.go(-1);</script>\n";
        exit;
    }
    $bids = implode(',', $bid);

    //for databases without subselect
    $select = "SELECT a.*, cc.name AS category, l.id as lendid, l.lend_from as lend_from, " .
            "l.lend_return as lend_return, l.lend_until as lend_until, " .
            "l.user_name as user_name, l.user_email as user_email " .
            "\nFROM #__booklibrary AS a" .
            "\nLEFT JOIN #__booklibrary_categories as hc on hc.bookid = a.id" .
            "\nLEFT JOIN #__booklibrary_main_categories AS cc ON cc.id = hc.catid" .
            "\nLEFT JOIN #__booklibrary_lend AS l ON l.id = a.fk_lendid" .
            "\nWHERE a.id in (" . $bids . ")";

    $database->setQuery($select);

    if (!$database->query()) {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    $books = $database->loadObjectList();
    $count = count($books);
    for ($i = 0; $i < 1; $i++) {
        if ((($books[$i]->lend_from) == '') && (($books[$i]->lend_return) == '')) {
            ?>
            <script type = "text/JavaScript" language = "JavaScript">
                alert('You edit book that were not lent out');
                window.history.go(-1);
            </script>
            <?php

            exit;
        }
    }
    // get list of users
    $userlist[] = mosHTML :: makeOption('-1', '----------');
    $database->setQuery("SELECT id AS value, name AS text from #__users ORDER BY name");
    $userlist = array_merge($userlist, $database->loadObjectList());
    $usermenu = mosHTML :: selectList($userlist, 'userid', 'class="inputbox" size="1"', 'value', 'text', '-1');

    HTML_booklibrary :: showLendBooks($option, $books, $usermenu, "edit_lend");
}

function lend_return($option, $bid) {
    global $database, $my;
    if (!is_array($bid) || count($bid) < 1) {
        echo "<script> alert('Select an item to lend'); window.history.go(-1);</script>\n";
        exit;
    }
    $bids = implode(',', $bid);


    //for databases without subselect
    $select = "SELECT a.*, l.id as lendid, l.lend_from as lend_from, " .
            "l.lend_return as lend_return, l.lend_until as lend_until, l.fk_userid as fk_userid, " .
            "l.user_name as user_name, l.user_email as user_email, u.name AS name, u.email as email" .
            "\nFROM #__booklibrary AS a" .
            "\nLEFT  JOIN #__booklibrary_lend AS l ON l.id = a.fk_lendid" .
            "\nLEFT JOIN #__users AS u ON u.id = l.fk_userid" .
            "\nWHERE a.id in (" . $bids . ")";

    $database->setQuery($select);

    if (!$database->query()) {
        echo "<script> alert('" . addslashes($database->getErrorMsg()) . "'); window.history.go(-1); </script>\n";
        exit();
    }

    $books = $database->loadObjectList();
    $count = count($books);

    for ($i = 0; $i < 1; $i++) {
        if ((($books[$i]->lend_from) == '') && (($books[$i]->lend_return) == '')) {
            ?>
            <script type = "text/JavaScript" language = "JavaScript">
                alert('You cannot return books that were not lent out');
                window.history.go(-1);
            </script>
            <?php

            exit;
        }
    }
    // get list of users
    $userlist[] = mosHTML :: makeOption('-1', '----------');
    $database->setQuery("SELECT id AS value, name AS text from #__users ORDER BY name");
    $userlist = array_merge($userlist, $database->loadObjectList());
    $usermenu = mosHTML :: selectList($userlist, 'userid', 'class="inputbox" size="1"', 'value', 'text', '-1');

    HTML_booklibrary :: showLendBooks($option, $books, $usermenu, "lend_return");
}

function saveLend($option, $bids, $task = "") {

    global $database;

    $checkh = mosGetParam($_POST, 'checkbook');

    if ($checkh != "on") {
        echo "<script> alert('Select an item to lend'); window.history.go(-1);</script>\n";
        exit;
    }

    $data = JFactory::getDBO();
    $bookid = mosGetParam($_POST, 'bookid');
    $id = mosGetParam($_POST, 'id');

    $query = "SELECT * FROM #__booklibrary_lend where fk_bookid= " . $id . " AND lend_return is NULL ";
    $data->setQuery($query);
    $lendTerm = $data->loadObjectList();

    if (!is_array($bids) || count($bids) < 1) {
        echo "<script> alert('Select an item to lend'); window.history.go(-1);</script>\n";
        exit;
    }

    $lend = new mosBookLibrary_lend($database);
    if ($task == "edit_lend")
        $lend->load($bids[0]);

    $lend_from = mosGetParam($_POST, 'lend_from');
    $lend_until = mosGetParam($_POST, 'lend_until');

    if ($lend_from > $lend_until) {
        echo "<script> alert('" . $lend_from . " more then " . $lend_until . "'); window.history.go(-1); </script>\n";
        exit();
    }

    $lend_from = substr($lend_from, 0, 10);
    $lend_until = substr($lend_until, 0, 10);

    if (isset($lendTerm[0])) {

        for ($e = 0, $m = count($lendTerm); $e < $m; $e++) {
            if ($task == "edit_lend" && $bids[0] == $lendTerm[$e]->id)
                continue;

            $lendTerm[$e]->lend_from = substr($lendTerm[$e]->lend_from, 0, 10);
            $lendTerm[$e]->lend_until = substr($lendTerm[$e]->lend_until, 0, 10);
            //lend check
            if (( $lend_from >= $lendTerm[$e]->lend_from &&
                    $lend_from <= $lendTerm[$e]->lend_until) ||
                    ($lend_from <= $lendTerm[$e]->lend_from
                    && $lend_until >= $lendTerm[$e]->lend_until)
                    || ( $lend_until >= $lendTerm[$e]->lend_from && $lend_until <= $lendTerm[$e]->lend_until)) {
                echo "<script> alert('Sorry , this object already lend out from " .
                $lendTerm[$e]->lend_from . " to " . $lendTerm[$e]->lend_until . "'); window.history.go(-1); </script>\n";
                exit();
            }
        }
    }

    if (mosGetParam($_POST, 'lend_from') != "") {
        $lend->lend_from = mosGetParam($_POST, 'lend_from');
    } else {
        $lend->lend_from = null;
    }

    if (mosGetParam($_POST, 'lend_until') != "") {
        $lend->lend_until = mosGetParam($_POST, 'lend_until');
    } else {
        $lend->lend_until = null;
    }

    $lend->fk_bookid = $id;
    $userid = mosGetParam($_POST, 'userid');

    if ($userid == "-1") {
        $lend->user_name = mosGetParam($_POST, 'user_name', '');
        $lend->user_email = mosGetParam($_POST, 'user_email', '');
    } else {
        $lend->getLendTo(intval($userid));
    }

    if (!$lend->check($lend)) {
        echo "<script> alert('" . $lend->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    if (!$lend->store()) {
        echo "<script> alert('" . $lend->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    $lend->checkin();
    $book = new mosBookLibrary($database);
    $book->load($id);
    $book->fk_lendid = $lend->id;
    $book->store();

    $book->checkin();

    mosRedirect("index.php?option=$option");
}

function saveLend_return($option, $lids) {

    global $database, $my;

    $id = mosGetParam($_POST, 'id');

    if (!is_array($lids) || count($lids) < 1) {
        echo "<script> alert('Select an item to return'); window.history.go(-1);</script>\n";
        exit;
    }

    for ($i = 0, $n = count($lids); $i < $n; $i++) {
        $lend = new mosBookLibrary_lend($database);
        $lend->load($lids[$i]);
        $lend->lend_return = date("Y-m-d H:i:s");

        if (!$lend->check($lend)) {
            echo "<script> alert('" . addslashes($lend->getError()) . "'); window.history.go(-1); </script>\n";
            exit();
        }

        if (!$lend->store()) {
            echo "<script> alert('" . addslashes($lend->getError()) . "'); window.history.go(-1); </script>\n";
            exit();
        }

        $lend->checkin();
        //$book = new mosBookLibrary($database);
        //$book->load($lend->fk_bookid);
        //$book->fk_lendid = 0;

        $is_update_book_lend = true;

        if ($is_update_book_lend) {
            $book = new mosBooklibrary($database);
            $book->load($id);

            $query = "SELECT * FROM #__booklibrary_lend WHERE fk_bookid=" . $id . " AND lend_return IS NULL ";
            $database->setQuery($query);
            $check_lends = $database->loadObjectList();

            if (isset($check_lends[0]->id)) {
                $book->fk_lendid = $check_lends[0]->id;
                $is_update_book_lend = false;
            } else {
                //$book->load($lend->fk_bookid);
                $book->fk_lendid = 0;
            }

            $book->store();
            $book->checkin();
        }
    }

    mosRedirect("index.php?option=$option");
}


function import($option) {
    global $database, $my;

    $file = file($_FILES['import_file']['tmp_name']);
    $catid = mosGetParam($_POST, 'import_catid');
    $type = mosGetParam($_POST, 'import_type');

//***********************   begin add for XML format   ***************************************

    switch ($type) {
        //CSV=='1' XML=='2'
        case '1':
            $retVal = mosBooklibraryImportExport :: importBooksCSV($file, $catid);
            HTML_booklibrary:: showImportResult($retVal, $option);
            break;

        default:
            $retVal = mosBooklibraryImportExport :: importBooksXML($_FILES['import_file']['tmp_name'], $catid);
            HTML_booklibrary:: showImportResult($retVal, $option);
            break;
    }
}

function export($option) {
    global $database, $my, $mainframe;

    $catid = mosGetParam($_POST, 'export_catid', 0);
    //$lend = mosGetParam($_POST, 'export_lend', null);
    //$pub = mosGetParam($_POST, 'export_pub', null);
    $type = mosGetParam($_POST, 'export_type', 0);
    $where = array();
    $wherecatid = '';

    if ($catid > 0) {
        array_push($where, "ac.catid='$catid'");
        $wherecatid = " AND c.id ='$catid'";
    }

    $selectstring = "SELECT a.id FROM #__booklibrary AS a
                        \nLEFT JOIN #__booklibrary_categories AS ac ON a.id = ac.bookid" .
            (count($where) ? "\nWHERE " . implode(' AND ', $where) : "") .
            "\nGROUP BY ac.bookid" .
            "\nORDER BY a.ordering";
    $database->setQuery($selectstring);
    //$booksids = $database->loadResultArray();

    if (version_compare(JVERSION, '3.0', 'lt')) {
        $booksids = $database->loadResultArray();
    } else {
        $booksids = $database->loadColumn();
    }

    echo $database->getErrorMsg();

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return;
    }

    $books = array();
    $order = array("\r\n", "\n", "\r");

    foreach ($booksids as $bid) {
        $tmp = new mosBooklibrary($database);
        $tmp->load(intval($bid));
        $books[] = $tmp;
    }
    //parsing in title and commenr symbol '|'
    foreach ($books as $key => $book) {
        $books[$key]->title = str_replace('|', '-', $book->title);
        $books[$key]->title = str_replace('\n', ' ', $book->title);
        $books[$key]->title = str_replace('\r', ' ', $book->title);
        $books[$key]->comment = str_replace('|', '-', $book->comment);
        $books[$key]->comment = str_replace('\n', ' ', $book->comment);
        $books[$key]->comment = str_replace('\r', ' ', $book->comment);
        $books[$key]->featured_clicks = str_replace('|', '-', $books[$key]->featured_clicks);
        $books[$key]->featured_clicks = str_replace($order, ' ', $books[$key]->featured_clicks);
        $books[$key]->featured_shows = str_replace('|', '-', $books[$key]->featured_shows);
        $books[$key]->featured_shows = str_replace($order, ' ', $books[$key]->featured_shows);
        if ($catid > 0)
            $books[$key]->categs = array($catid);
    }

    $cats = '';

    if ($type == '4') {
        $selectstring = "SELECT c.* FROM #__booklibrary_main_categories AS c" .
                "\nWHERE c.section='com_booklibrary' " . $wherecatid .
                "\nORDER BY c.parent_id";
        $database->setQuery($selectstring);
        $cats = $database->loadAssocList();
    }

    $retVal = mosBooklibraryImportExport :: exportBooksXML($books, $cats);
    $type2 = 'xml';

    switch ($type) {
        case '1':
            $type2 = 'csv';
            break;
        case '2':
            $type2 = 'xml';
            break;
    }

    $InformationArray = mosBooklibraryImportExport :: storeExportFile($retVal, $type2);

    HTML_booklibrary :: showExportResult($InformationArray, $option);
}

function importExportBooks($option) {
    global $database;

    $q = "SELECT id AS value, title AS text
                        FROM #__booklibrary_main_categories
                        WHERE section = '" . $option . "'
                        ORDER BY ordering"; // for J 1.6
    // get list of categories
    $categories[] = mosHTML :: makeOption('0', _BOOKLIBRARY_LABEL_SELECT_CATEGORIES);
    $database->setQuery($q); // for J 1.6
    $categories = array_merge($categories, $database->loadObjectList());

    if (count($categories) < 1) {
        mosRedirect("index.php?option=com_booklibrary&section=categories", _BOOKLIBRARY_ADMIN_IMPEXP_ADD);
    }

    $impclist = mosHTML :: selectList($categories, 'import_catid', 'class="inputbox" 
                                      size="1" id="import_catid"', 'value', 'text', 0);

    $expclist = mosHTML :: selectList($categories, 'export_catid', 'class="inputbox" 
                                      size="1" id="export_catid"', 'value', 'text', 0);

    $params = array();
    $params['import']['category'] = $impclist;
    $params['export']['category'] = $expclist;

    $importtypes[0] = mosHTML :: makeOption('0', _BOOKLIBRARY_ADMIN_PLEASE_SEL);
    $importtypes[1] = mosHTML :: makeOption('1', _BOOKLIBRARY_ADMIN_FORMAT_CSV);
    $importtypes[2] = mosHTML :: makeOption('2', _BOOKLIBRARY_ADMIN_FORMAT_XML);
    //$importtypes[3] = mosHTML :: makeOption('3', _BOOKLIBRARY_ADMIN_ENTIRE_RECOVER);
    $importtypes[4] = mosHTML :: makeOption('4', _BOOKLIBRARY_ADMIN_FULL_IMPORT);

    $params['import']['type'] = mosHTML :: selectList($importtypes, 'import_type', 'id="import_type" class="inputbox" size="1" onchange = "impch();"', 'value', 'text', 0);

    $exporttypes[0] = mosHTML :: makeOption('0', _BOOKLIBRARY_ADMIN_PLEASE_SEL);
    $exporttypes[1] = mosHTML :: makeOption('1', _BOOKLIBRARY_ADMIN_FORMAT_CSV);
    $exporttypes[2] = mosHTML :: makeOption('2', _BOOKLIBRARY_ADMIN_FORMAT_XML);
    //$exporttypes[3] = mosHTML :: makeOption('3', _BOOKLIBRARY_ADMIN_ENTIRE_BU);
    $exporttypes[4] = mosHTML :: makeOption('4', _BOOKLIBRARY_ADMIN_FULL_EXPORT);

    $params['export']['type'] = mosHTML :: selectList($exporttypes, 'export_type', 'id="export_type" class="inputbox" size="1" onchange="expch();"', 'value', 'text', 0);

    HTML_booklibrary :: showImportExportBooks($params, $option);
}

// LANGUAGE MANAGER

function showLanguageManager($option) {
    global $database, $mainframe, $mosConfig_list_limit, $menutype, $mosConfig_absolute_path;

    
    loadConstBook();

    $section = "com_booklibrary";

    $search['const'] = mosGetParam($_POST, 'search_const', '');
    $search['const_value'] = mosGetParam($_POST, 'search_const_value', '');
    $search['languages'] = mosGetParam($_POST, 'search_languages', '');
    $search['sys_type'] = mosGetParam($_POST, 'search_sys_type', '');

    $where_query = array();

    if ($search['const'] != '')
        $where_query[] = "c.const LIKE '%" . $search['const'] . "%'";

    if ($search['const_value'] != '')
        $where_query[] = "cl.value_const LIKE '%" . $search['const_value'] . "%'";

    if ($search['languages'] != '')
        $where_query[] = "cl.fk_languagesid = " . $search['languages'] . " ";

    if ($search['sys_type'] != '')
        $where_query[] = "c.sys_type LIKE '%" . $search['sys_type'] . "%'";

    $where = "";
    $i = 0;
    if (count($where_query) > 0)
        $where = "WHERE ";

    foreach ($where_query as $item) {

        if ($i == 0)
            $where .= "( $item ) ";
        else
            $where .= "AND ( $item ) ";
        $i++;
    }

    $query = "SELECT cl.id, cl.value_const, c.sys_type, l.title, c.const ";
    $query .= "FROM #__booklibrary_const_languages as cl ";
    $query .= "LEFT JOIN #__booklibrary_languages AS l ON cl.fk_languagesid=l.id ";
    $query .= "LEFT JOIN #__booklibrary_const AS c ON cl.fk_constid=c.id $where";

    $database->setQuery($query);
    $const_languages = $database->loadObjectList();

    $sectionid = $mainframe->getUserStateFromRequest("sectionid{$section}{$section}", 'sectionid', 0);
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$section}limitstart", 'limitstart', 0);
    $levellimit = $mainframe->getUserStateFromRequest("view{$option}limit$menutype", 'levellimit', 10);

    $total = count($const_languages);

    $pageNav = new JPagination($total, $limitstart, $limit); // for J 1.6

    $const_languages = array_slice($const_languages, $pageNav->limitstart, $pageNav->limit);

    $query = "SELECT sys_type FROM #__booklibrary_const GROUP BY sys_type";
    $database->setQuery($query);
    $sys_types = $database->loadObjectList();

    $sys_type_row[] = mosHTML::makeOption('', '--Select sys type--');

    foreach ($sys_types as $sys_type) {
        $sys_type_row[] = mosHTML::makeOption($sys_type->sys_type, $sys_type->sys_type);
    }

    $search['sys_type'] = mosHTML :: selectList(
                    $sys_type_row, 'search_sys_type', 'class="inputbox input-medium" size="1" 
        onchange="document.adminForm.submit();"', 'value', 'text', $search['sys_type']
    );

    $query = "SELECT id, title FROM #__booklibrary_languages";
    $database->setQuery($query);
    $languages = $database->loadObjectList();

    $languages_row[] = mosHTML::makeOption('', '--Select language--');
    foreach ($languages as $language) {
        $languages_row[] = mosHTML::makeOption($language->id, $language->title);
    }

    $search['languages'] = mosHTML :: selectList(
                    $languages_row, 'search_languages', 'class="inputbox input-medium" size="1"
        onchange="document.adminForm.submit();"', 'value', 'text', $search['languages']
    );

    HTML_booklibrary :: showLanguageManager($const_languages, $pageNav, $search);
}

function editLanguageManager($section = '', $uid = 0) {
    global $database, $my, $acl, $booklibrary_configuration;
    global $mosConfig_absolute_path, $mosConfig_live_site;

    $row = new mosBooklibrary_language($database); // for 1.6
    // load the row from the db table
    $row->load($uid);

    $query = "SELECT * FROM #__booklibrary_const WHERE id = " . $row->fk_constid;
    $database->setQuery($query);
    $const = $database->loadObject();

    $lists['const'] = $const->const;
    $lists['sys_type'] = $const->sys_type;

    $query = "SELECT title FROM #__booklibrary_languages WHERE id = " . $row->fk_languagesid;
    $database->setQuery($query);
    $language = $database->loadResult();

    $lists['languages'] = $language;

    HTML_booklibrary::editLanguageManager($row, $lists);
}

function saveLanguageManager() {
    global $database, $mosConfig_absolute_path;

    $row = new mosBooklibrary_language($database); // for 1.6
    $post = JRequest::get('post', JREQUEST_ALLOWHTML);

    if (!$row->bind($post)) {
        echo "<script> alert(\"" . $row->getError() . "\"); window.history.go(-1); </script>\n";
        exit();
    }

    if (!$row->check()) {
        echo "<script> alert(\"" . $row->getError() . "\"); window.history.go(-1); </script>\n";
        exit();
    }

    if (!$row->store()) {
        echo "<script> alert(\"" . $row->getError() . "\"); window.history.go(-1); </script>\n";
        exit();
    }

    mosRedirect('index.php?option=com_booklibrary&section=language_manager');
}

function cancelLanguageManager() {
    global $database, $mosConfig_absolute_path;

    mosRedirect('index.php?option=com_booklibrary&section=language_manager');
}
