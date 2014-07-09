<?php
if (!defined('_VALID_MOS') && !defined('_JEXEC'))
    die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');
/**
 *
 * @package BookLibrary
 * @copyright Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru); Rob de Cleen(rob@decleen.com); 
 * Homepage: http://www.ordasoft.com
 * @version: 3.0 Free
 *
 * */
require_once ($mosConfig_absolute_path . "/libraries/joomla/factory.php");
require_once ( JPATH_BASE . DS . 'includes' . DS . 'defines.php' );
require_once ( JPATH_BASE . DS . 'includes' . DS . 'framework.php' );
// for J 1.6
$mainframe = JFactory::getApplication();
$templateDir = 'templates/' . $mainframe->getTemplate();
$GLOBALS['mainframe'] = $mainframe;
$GLOBALS['templateDir'] = $templateDir;
$mosConfig_live_site = JURI::root(true);
$GLOBALS['mosConfig_live_site'] = $mosConfig_live_site;
$doc = JFactory::getDocument();
$GLOBALS['doc'] = $doc;
// --
// ensure this file is being included by a parent file
$bid = mosGetParam($_POST, 'bid', array(0));
require_once ($mosConfig_absolute_path . "/administrator/components/com_booklibrary/admin.booklibrary.class.ws.php");
require_once ($mosConfig_absolute_path . "/administrator/components/com_booklibrary/admin.booklibrary.class.others.php");
require_once ($mosConfig_absolute_path . "/administrator/components/com_booklibrary/admin.booklibrary.class.conf.php");

class HTML_Categories {

    static function show($rows, $myid, &$pageNav, &$lists, $type) {
        global $my, $mainframe, $mosConfig_live_site, $templateDir, $doc;
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        $html = "<div class='book_manager_caption' ><img src='./components/com_booklibrary/images/cfg.png' alt ='Config' />" . _BOOKLIBRARY_CATEGORIES_MANAGER . "</div>";

        $app = JFactory::getApplication();

        $app->JComponentTitle = $html;

        $section = "com_booklibrary";
        $section_name = "BookLibrary";
        ?>
        <form id="adminForm" action="index.php" method="post" name="adminForm">

            <table class="admin1" cellpadding="4" cellspacing="0" border="0" width="100%">
                <tr>
                    <!--<td width="30%">
                    <img src="./components/com_booklibrary/images/cfg.png" align="right" alt="Config" />
                    </td>-->
                    <!--<td width="70%" class="book_manager_caption" valign='bottom' >
                    </td>-->
        <?php if (version_compare(JVERSION, "3.0.0", "ge")) { ?>
                    <table width="100%">
                        <tr>
                            <td>
                                <div class="btn-group pull-right hidden-phone">
                                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
            <?php echo $pageNav->getLimitBox(); ?>
                                </div>
                            </td>                
                        </tr>
                    </table>
        <?php } ?>
                </tr>
            </table>

            <table class="adminlist my_table my_table-bordered my_table-hover bl_admin_categories_main_table">
                <tr class="cat-header">
                    <th width="20" style="text-align:center;">#</th>
                    <th width="20" style="text-align:center;">
                        <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this<?php //echo count($rows); ?>);" />
                    </th>
                    <th align = "center" class="title"><?php echo _BOOKLIBRARY_HEADER_CATEGORY; ?></th>
                    <th align = "center" width="5%"><?php echo _BOOKLIBRARY_HEADER_NUMBER; ?></th>
                    <th align = "center" width="10%"><?php echo _BOOKLIBRARY_HEADER_PUBLISHED; ?></th>
                    <?php if ($section <> 'content') { ?>
                        <th align = "center" colspan="2"><?php echo _BOOKLIBRARY_HEADER_REORDER; ?></th>
                    <?php } ?>
                    <th align = "center" width="10%"><?php echo _BOOKLIBRARY_HEADER_ACCESS; ?></th>
                    <?php if ($section == 'content') { ?>
                        <th width="12%" align="left">Section</th>
        <?php } ?>
                    <th align = "center" width="12%">ID</th>
                    <th align = "center" width="12%"><?php echo _BOOKLIBRARY_HEADER_CHECKED_OUT; ?></th>
                </tr>
                <?php
                $k = 0;
                $i = 0;
                $n = count($rows);
                foreach ($rows as $row) {
                    $img = $row->published ? 'ok.png' : 'remove.png';
                    $task = $row->published ? 'unpublish' : 'publish';
                    $alt = $row->published ? 'Published' : 'Unpublished';
                    if (!$row->access) {
                        $color_access = 'style="color: green;"';
                        $task_access = 'accessregistered';
                    } else if ($row->access == 1) {
                        $color_access = 'style="color: red;"';
                        $task_access = 'accessspecial';
                    } else {
                        $color_access = 'style="color: black;"';
                        $task_access = 'accesspublic';
                    }
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td width="20" align="center"><?php echo $pageNav->getRowOffset($i); ?></td>
                        <td width="20" style="text-align:center">
                            <?php echo mosHTML::idBox($i, $row->id, ($row->checked_out_contact_category && $row->checked_out_contact_category != $my->id), 'bid'); ?>
                        </td>
                        <td width="35%">
                                <?php if ($row->checked_out_contact_category && ($row->checked_out_contact_category != $my->id)) { ?>
                                <?php echo $row->treename . ' ( ' . $row->title . ' )'; ?>
                                &nbsp;[ <i>Checked Out</i> ]
            <?php } else { ?>
                                <a href="#edit" onClick="return listItemTask('cb<?php echo $i; ?>','edit')">
                <?php echo $row->treename . ' ( ' . $row->title . ' )'; ?>
                                </a> 
                                <?php } ?>
                        </td>
                        <td align="center" style="text-align:center"><?php echo $row->cc; ?></td>
                        <td align="center" style="text-align:center">
                            <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task; ?>')">
            <?php if (version_compare(JVERSION, "1.6.0"/* "3.0.0" */, "lt")) { ?>
                                    <img src="<?php echo $mosConfig_live_site . "/components/com_booklibrary/images/" . $img; ?>" border="0" alt="<?php echo $alt; ?>" />
            <?php } else { ?>
                                    <img src="<?php echo $mosConfig_live_site . "/components/com_booklibrary/images/" . $img; ?>" border="0" alt="<?php echo $alt; ?>" />
            <?php } ?>	
                            </a>
                        </td>
                        <td style="text-align:center;vertical-align:middle;"><?php echo catOrderUpIcon($row->ordering - 1, $i); ?></td>
                        <td style="text-align:center;vertical-align:middle;"><?php echo catOrderDownIcon($row->ordering - 1, $row->all_fields_in_list, $i); ?></td>
                        <td align="center"><?php echo $row->groups; ?></td>
                        <td align="center"><?php echo $row->id; ?></td>
                        <td align="center"><?php echo $row->checked_out_contact_category ? $row->editor : ""; ?></td>
            <?php $k = 1 - $k; ?>
                    </tr>
            <?php $k = 1 - $k;
            $i++;
        } ?>
                <tr class="for_paginator">
                    <td colspan = "11"><?php echo $pageNav->getListFooter(); ?></td>
                </tr>
            </table>

            <input type="hidden" name="option" value="com_booklibrary" />
            <input type="hidden" name="section" value="categories" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="chosen" value="" />
            <input type="hidden" name="act" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="type" value="<?php echo $type; ?>" />
        </form>
    <?php
    }

    /**
     * Writes the edit form for new and existing categories
     * 
     * @param mosCategory $ The category object
     * @param string $ 
     * @param array $ 
     */
    static function edit(&$row, $section, &$lists, $redirect) {
        global $my, $mosConfig_live_site, $mainframe, $option, $doc; // for J 1.6
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');

        $aa = $row->id ? _BOOKLIBRARY_HEADER_EDIT : _BOOKLIBRARY_HEADER_ADD;
        $a = $aa . " " . _BOOKLIBRARY_CATEGORY . " " . $row->name;

        $html = '<div class="book_manager_caption"><img src="./components/com_booklibrary/images/cfg.png"/>' . $a . '</div>';
        $app = JFactory::getApplication();
        $app->JComponentTitle = $html;

        if ($row->image == "") {
            $row->image = 'blank.png';
        }
        mosMakeHtmlSafe($row, ENT_QUOTES, 'description');
        ?>
        <script language="javascript" type="text/javascript">
            Joomla.submitbutton = function(pressbutton) {

                var form = document.adminForm;
                if (pressbutton == 'cancel') {
                    submitform( pressbutton );
                    return;
                }        		
                /* [inserted by]: Wonderer */
                alias = document.getElementById('alias'); alias = trim(alias.value);		
                cat_name = document.getElementById('cat_name'); cat_name = trim(cat_name.value);
                title = document.getElementById('title'); title.value = cat_name;
                        
                if ( alias == '' ) { alert( "<?php echo _BOOKLIBRARY_ADMIN_INFOTEXT_CAT_CHECK_ERR_ALIAS; ?>" );return;}
                if ( cat_name == '' ) { alert( "<?php echo _BOOKLIBRARY_ADMIN_INFOTEXT_CAT_CHECK_ERR_NAME; ?>" );return;}
                if ( title == '' ) { alert( "<?php echo _BOOKLIBRARY_ADMIN_INFOTEXT_CAT_CHECK_ERR_TITLE; ?>" );return;}
                // --
                if ( form.name.value == "" ) {
                    alert('<?php echo _BOOKLIBRARY_DML_CAT_MUST_SELECT_NAME; ?>');
                } else {
        <?php getEditorContents('editor1', 'description'); ?>
                            submitform(pressbutton);
                        }
                    }
        			
                    function trim(string)
                    {
                        return string.replace(/(^\s+)|(\s+$)/g, "");
                    }
        </script>

        <?php
        global $database;
        //my !!!!! -------- langdescription   ----------------------------------------------------------------------------------------------
        $lg = JFactory::getLanguage();
        $installed = $lg->getKnownLanguages();
        $languages_row[] = mosHTML::makeOption('*', 'All');
        foreach ($installed as $installang) {
            $langname = $installang['name'];
            $languages_row[] = mosHTML::makeOption($langname, $langname);
        }
        $langlistshow = mosHTML :: selectList($languages_row, 'langshow', 'class="inputbox" size="1"', 'value', 'text', $row->langshow);
        $lists['langshow'] = $langlistshow;
        //end of my langdecription ---------  !!!!   ---------------------------------------------------------------------------------------
        ?>

        <form id="adminForm" action="index.php" method="post" name="adminForm">

            <table width="100%" class="my_table bl_admin_categories_category">
                <tr>
                    <td valign="top">

                        <table class="adminform my_table bl_admin_edit_category" width="100%">
                            <tr>
                                <th colspan="3"><h1><?php echo _BOOKLIBRARY_CATEGORIES__DETAILS; ?></h1></th>
                </tr>
                <tr>
                    <td width="20%"><?php echo "ID"; ?>:</td>
                    <td colspan="2" width="80%">
                        <input readonly="readonly" class="text_area id" type="text" name="catid" id="catid" value="<?php echo $row->id; ?>" size="50" maxlength="50" title="" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo _BOOKLIBRARY_CATEGORIES_HEADER_NAME; ?>:</td>
                    <td colspan="2">
                        <input class="text_area" type="hidden" name="title" id="title" value="<?php echo $row->title; ?>" maxlength="50" title="" />
                        <input class="text_area" type="text" name="name" id="cat_name" value="<?php echo $row->name; ?>" size="50" maxlength="255" title="" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo _BOOKLIBRARY_CATEGORIES_HEADER_ALIAS; ?>:</td>
                    <td colspan="2">
                        <input class="text_area" type="text" name="alias" id="alias" value="<?php echo $row->alias; ?>" size="50" maxlength="255" title="A short name to appear in menus" />
                    </td>
                </tr>




                <tr>
                    <td><?php echo _BOOKLIBRARY_LABEL_LANGUAGEDESCRIPTION; ?>:</td>
                    <td colspan="2"><?php echo $lists['langshow']; ?></td>
                </tr>

                <tr>
                    <td><?php echo _BOOKLIBRARY_CONFIG_VIEW_TYPE; ?>:</td>
                    <td colspan="2"><?php echo $lists['alone_category']; ?></td>
                </tr>
                <tr>
                    <td><?php echo _BOOKLIBRARY_VIEW_BOOK; ?>:</td>
                    <td colspan="2"><?php echo $lists['view_book']; ?></td>
                </tr>
                <tr>
                    <td align="right"><?php echo _BOOKLIBRARY_CATEGORIES__PARENTITEM; ?>:</td>
                    <td colspan="2"><?php echo $lists['parent']; ?></td>
                </tr>
                <tr>
                    <td><?php echo _BOOKLIBRARY_CATEGORIES_HEADER_IMAGE; ?>:</td>
                    <td><?php echo $lists['image']; ?></td>
                    <td rowspan="4" width="50%">
                        <script language="javascript" type="text/javascript">
                            if (document.forms[0].image.options.value!='')
                            {
                                jsimg='../images/stories/' + getSelectedValue( 'adminForm', 'image' );
                            } 
                            else 
                            {
                                jsimg='../images/M_images/blank.png';
                            }
                            document.write('<img src=' + jsimg + ' name="imagelib" width="80" height="80" border="2" alt="<?php echo _BOOKLIBRARY_CATEGORIES__IMAGEPREVIEW; ?>" />');
                        </script>
                    </td>
                </tr>
                <tr>
                    <td><?php echo _BOOKLIBRARY_CATEGORIES_HEADER_IMAGEPOS; ?>:</td>
                    <td><?php echo $lists['image_position']; ?></td>
                </tr>
                <tr>
                    <td><?php echo _BOOKLIBRARY_CATEGORIES_HEADER_ORDER; ?>:</td>
                    <td><?php echo $lists['ordering']; ?></td>
                </tr>
                <tr>
                    <td align = "center"><?php echo _BOOKLIBRARY_HEADER_ACCESS; ?>:</td>
                    <td><input value="Everyone" disabled /></td>
                </tr>
                <tr>
                    <td><?php echo _BOOKLIBRARY_HEADER_PUBLISHED; ?>:</td>
                    <td colspan="2"><?php echo $lists['published']; ?></td>
                </tr>
                <tr>
                    <td valign="top"><?php echo _BOOKLIBRARY_CATEGORIES__DETAILS; ?>:</td>
                    <td colspan="2">
        <?php
        // parameters : areaname, content, hidden field, width, height, rows, cols
        editorArea('editor1', $row->description, 'description', '500', '200', '50', '5');
        ?>
                    </td>
                </tr>
            </table>
        </td>
        </tr>
        </table>

        <input type="hidden" name="option" value="com_booklibrary" />
        <input type="hidden" name="section" value="categories" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
        <input type="hidden" name="sectionid" value="com_booklibrary" />
        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
        </form>
    <?php
    }

}

/**
 * Booklibrary Import Export Class
 * Handles the import and export of data from the booklibrary.
 */
class HTML_booklibrary {

    static function sort_head_let($sortby) {
        global $mosConfig_live_site, $templateDir;
        $img_str = "";
        switch ($sortby) {
            case 'lend_out':
                $title = "";
                break;
            case 'lend_out_desc':
                $title = "";
                break;
            case 'lend_until':
                $img_str = "<img src=\"" . $templateDir . "/images/admin/uparrow-1.png\" width=\"12\" height=\"12\" border=\"0\" alt='Sorted up' />";
                $title = _BOOKLIBRARY_ORDER_LEND_UNTIL;
                break;
            case 'lend_until_desc':
                $img_str = "<img src=\"" . $templateDir . "/images/admin/downarrow-1.png\" width=\"12\" height=\"12\" border=\"0\" alt='Sorted up' />";
                $title = _BOOKLIBRARY_ORDER_LEND_UNTIL;
                break;
            case 'lend_from':
                $img_str = "<img src=\"" . $templateDir . "/images/admin/uparrow-1.png\" width=\"12\" height=\"12\" border=\"0\" alt='Sorted up' />";
                $title = _BOOKLIBRARY_ORDER_LEND_FROM;
                break;
            case 'lend_from_desc':
                $img_str = "<img src=\"" . $templateDir . "/images/admin/downarrow-1.png\" width=\"12\" height=\"12\" border=\"0\" alt='Sorted up' />";
                $title = _BOOKLIBRARY_ORDER_LEND_FROM;
                break;
            default :
                $value = "lend_until";
                $title = _BOOKLIBRARY_LABEL_LEND;
                break;
        }
        $enums = Array('none', 'lend_until', 'lend_until_desc', 'lend_from', 'lend_from_desc');
        for ($i = 0; $i < count($enums); $i++) {
            if ($enums[$i] == $sortby) {
                if (($i + 1) != count($enums)) {
                    $value = $enums[$i + 1];
                } else {
                    $value = $enums[0];
                }

                break;
            }
        }

        $str = "<a href='" . $mosConfig_live_site . "/administrator/index.php?option=com_booklibrary&amp;sortlet=$value'>" .
                $img_str . $title . "</a>";
        return $str;
    }

    static function sort_head($title, $fieldname, $sort_arr) {
        global $mosConfig_live_site, $templateDir; // for J 1.6

        $img_str = "";
        if ($sort_arr['field'] == $fieldname) {
            if ($sort_arr['direction'] == '') {
                $img_str = "<img src=\"{$templateDir}/images/admin/uparrow-1.png\" width=\"12\" height=\"12\" border=\"0\" alt='Sorted up' />";
            } else {
                $img_str = "<img src=\"{$templateDir}/images/admin/downarrow-1.png\" width=\"12\" height=\"12\" border=\"0\" alt='Sorted up' />";
            }
        }
        $str = "<a href='" . $mosConfig_live_site . "/administrator/index.php?option=com_booklibrary&amp;sort=$fieldname'>" .
                $img_str . $title . "</a>";
        return $str;
    }

    function edit_review($option, $book_id, $review) {
        global $my, $mosConfig_live_site, $mainframe, $doc; // for J 1.6
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        ?>

        <form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
            <table cellpadding="4" cellspacing="5" border="0" width="100%" class="adminform admin5">
                <tr>
                    <td colspan="2"><?php echo _BOOKLIBRARY_LABEL_REVIEW_TITLE; ?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input class="inputbox" type="text" name="title" size="80" value="<?php echo $review[0]->title ?>" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo _BOOKLIBRARY_LABEL_REVIEW_COMMENT; ?></td>
                    <td align="left"><?php echo _BOOKLIBRARY_LABEL_REVIEW_RATING; ?></td>
                </tr>
                <tr>
                    <td>
                        <?php editorArea('editor1', $review[0]->comment, 'comment', '410', '200', '60', '10'); ?>
                    </td>
                    <td width="102" align='left'>
                        <?php $k = 0;
                        while ($k < 11) { ?>
                            <input type="radio" name="rating" value="<?php echo $k; ?>" 
            <?php if ($k == $review[0]->rating) echo 'checked="checked"'; ?> alt="Rating" />
                            <img src="../components/com_booklibrary/images/rating-<?php echo $k; ?>.gif" 
                                 alt="<?php echo ($k) / 2; ?>" border="0" /><br />
            <?php $k++;
        } ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="update_review" />
            <input type="hidden" name="book_id" value="<?php echo $book_id; ?>" />
            <input type="hidden" name="review_id" value="<?php echo $review[0]->id; ?>" />
        </form>
    <?php
    }

    //*************   begin for manage reviews   ********************
    static function edit_manage_review($option, & $review) {
        global $my, $mosConfig_live_site, $mainframe, $doc; // for J 1.6
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $html = "<div class='book_manager_caption' ><img src='./components/com_booklibrary/images/cfg.png' alt ='Config' />" . _BOOKLIBRARY_HEADER_EDIT . "</div>";

        $app = JFactory::getApplication();
        $app->JComponentTitle = $html;
        ?>

        <form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
            <table cellpadding="4" cellspacing="5" border="0" width="100%" class="adminform bl_admin_user_review my_table">
                <tr>
                    <td><?php echo _BOOKLIBRARY_LABEL_REVIEW_TITLE; ?></td>
                    <td>
                        <input class="inputbox" type="text" name="title" size="80" value="<?php echo $review[0]->title ?>" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo _BOOKLIBRARY_LABEL_REVIEW_COMMENT; ?></td>
                    <td>
        <?php editorArea('editor1', $review[0]->comment, 'comment', '410', '200', '60', '10'); ?>
                    </td>
                </tr>
                <tr>
                    <td align="left"><?php echo _BOOKLIBRARY_LABEL_REVIEW_RATING; ?></td>
                    <td align='left'>
        <?php $k = 0;
        while ($k < 11) { ?>
                            <input type="radio" name="rating" value="<?php echo $k; ?>" 
            <?php if ($k == $review[0]->rating) echo 'checked="checked"'; ?> alt="Rating" />
                            <img src="../components/com_booklibrary/images/rating-<?php echo $k; ?>.gif" 
                                 alt="<?php echo ($k) / 2; ?>" border="0" /><br />
            <?php $k++;
        } ?>
                    </td>
                </tr>
            </table>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="update_edit_manage_review" />
            <input type="hidden" name="review_id" value="<?php echo $review[0]->id; ?>" />
        </form>
    <?php
    }

    //***************   end for manage reviews   ********************

    static function showRequestLendBooks($option, $lend_requests, & $pageNav) {
        global $my, $mosConfig_live_site, $mainframe, $doc;
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        $doc->addScript($mosConfig_live_site . '/media/system/js/core.js');

        $html = "<div class='book_manager_caption' ><img src='./components/com_booklibrary/images/cfg.png' alt ='Config' /> " . _BOOKLIBRARY_ADMIN_REQUEST_LEND . "</div>";

        $app = JFactory::getApplication();
        $app->JComponentTitle = $html;
        ?>

        <form id="adminForm" action="index.php" method="post" name="adminForm">
        <?php if (version_compare(JVERSION, "3.0.0", "ge")) { ?>
                <table class="admin8" width="100%">
                    <tr>
                        <td>
                            <div class="btn-group pull-right hidden-phone">
                                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
            <?php echo $pageNav->getLimitBox(); ?>
                            </div>
                        </td>                
                    </tr>
                </table>
        <?php } ?>

            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist bl_admin_lend_requests_table
                   my_table my_table-bordered my_table-hover">
                <tr>
                    <th align = "center" width="20">
                        <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this<?php //echo count( $lend_requests ); ?>);" />
                    </th>
                    <th align = "center" width="30">id</th>
                    <th align = "center" class="title" width="10%" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_LEND_FROM; ?>
                    </th>
                    <th align = "center" class="title" width="10%" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_LEND_UNTIL; ?>
                    </th>
                    <th align = "center" class="title" width="5%" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_BOOKID; ?>
                    </th>
                    <th align = "center" class="title" width="10%" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_ISBN; ?>
                    </th>
                    <th align = "center" class="title" width="15%" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_TITLE; ?>
                    </th>
                    <th align = "center" class="title" width="15%" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_LEND_USER; ?>
                    </th>
                    <th align = "center" class="title" width="15%" nowrap="nowrap">
                <?php echo _BOOKLIBRARY_LABEL_LEND_EMAIL; ?>
                    </th>
                    <th align = "center" class="title" width="20%" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_LEND_ADRES; ?>
                    </th>
                </tr>
                        <?php
                        for ($i = 0, $n = count($lend_requests); $i < $n; $i++) {
                            $row = $lend_requests[$i];
                            //print_r($row->fk_lendid);//exit;
                            ?>

                    <tr class="row<?php echo $i % 2; ?>">
                        <td width="20">
                            <?php
                            //print_r($row);exit;
                            echo mosHTML::idBox($i, $row->id, ($row->fk_lendid = 0), 'bid[]');
                            ?>
                        </td>
                        <td align = "center"><?php echo $row->id; ?></td>
                        <td align = "center">
                            <?php echo $row->lend_from; ?>
                        </td>
                        <td align = "center">
                            <?php echo $row->lend_until; ?>
                        </td>
                        <td align = "center"><?php echo $row->fk_bookid; ?></td>
                        <td align = "center">
            <?php echo $row->isbn; ?>
                        </td>
                        <td align = "center">
                    <?php echo $row->title; ?>
                        </td>
                        <td align = "center">					
            <?php echo $row->user_name; ?>
                        </td>
                        <td align = "center">
                            <a href=mailto:"<?php echo $row->user_email; ?>"><?php echo $row->user_email; ?></a>
                        </td>
                        <td align = "center"><?php echo $row->user_mailing; ?></td>
                    </tr>
        <?php } ?>
                <tr class="for_paginator">
                    <td colspan = "11"><?php echo $pageNav->getListFooter(); ?></td>
                </tr>
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="lend_requests" />
            <input type="hidden" name="boxchecked" value="0" />
        </form>
    <?php
    }

    static function showBooks($option, $rows_book, & $clist, & $lendlist, & $userlist, & $publist, & $search, & $pageNav, & $sort_arr, $search_for_list, $order_let) {
        global $my, $mosConfig_live_site, $session;
        global $mainframe, $templateDir, $doc; // for J 1.6
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $html = "<div class='book_manager_caption' ><img src='./components/com_booklibrary/images/cfg.png' alt ='Config' />" . _BOOKLIBRARY_SHOW . "</div>";
        $app = JFactory::getApplication();
        $app->JComponentTitle = $html;
        $script_content = ''; // for J 1.6
        $script_content .= "function before_print_check() \n";
        $script_content .= " { \n";
        $script_content .= "  var add = document.getElementsByName('bid[]'); \n";
        $script_content .= "  var count=0;    \n";
        $script_content .= "  for(var i=0;i<add.length;i++) \n";
        $script_content .= "  { \n";
        $script_content .= "      if(add[i].checked) \n";
        $script_content .= "        { \n";
        $script_content .= "         count++; \n";
        $script_content .= "         break; \n";
        $script_content .= "       } \n";
        $script_content .= "   } \n";
        $script_content .= "  if(count == 0) \n";
        $script_content .= "    {\n";
        $script_content .= "     alert('Please choose some books'); \n";
        $script_content .= "     exit; \n";
        $script_content .= "    } \n";
        $script_content .= "  else \n";
        $script_content .= "   {\n";
        $script_content .= "    document.adminForm.target = '_blank' ; \n";
        $script_content .= "    document.adminForm.task.value='print_books'; \n";
        $script_content .= "    document.adminForm.submit(); \n";
        $script_content .= "  }\n";
        $script_content .= "}\n";
        $doc->addScriptDeclaration($script_content);
        ?>

        <form id="adminForm" action="index.php" method="post" name="adminForm" class="bl_admin_books">
            <table class="admin10" cellpadding="4" cellspacing="0" border="0" width="100%">
                <tr>
                    <!--<td width="30%">
                            <img src="./components/com_booklibrary/images/cfg.png" align="right" alt="Config" />
                        </td>-->

                <?php if (version_compare(JVERSION, "3.0.0", "ge")) { ?>
                    <table class="admin11" width="100%">
                        <tr>
                            <td>
                                <div class="btn-group pull-right hidden-phone">
                                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
            <?php echo $pageNav->getLimitBox(); ?>
                                </div>
                            </td>                
                        </tr>
                    </table>
        <?php } ?>

                <td width="68%" class="book_manager_caption" valign='bottom' ></td>
                <!--********   begin add for button print in Manager Books   *******************-->
               
                <!--******   end add for button print in Manager Books   *******************-->
                </tr>
            </table>

            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist my_table bl_admin_books_top_table_with_search_ans_orders">
                <tr>
                    <td><label><?php echo _BOOKLIBRARY_SHOW_SEARCH; ?></label></td>
                    <td>
                        <input type="text" name="search" value="<?php echo $search; ?>" class="inputbox" />
                    </td>
                    <td>
                        <label><?php echo _BOOKLIBRARY_SHOW_SEARCH_FOR; ?></label>
                    </td>
                    <td>
            <?php echo $search_for_list; ?>
                        <input class="my_btn" type="submit" name="go" value="<?php echo _BOOKLIBRARY_SHOW_SEARCH_GO; ?>" >
                    </td>
                </tr>	
                <tr>
                    <td><?php echo $publist; ?></td>
                    <td><?php echo $lendlist; ?></td>
                    <td><?php echo $userlist; ?></td>
                    <td><?php echo $clist; ?></td>
                </tr>
            </table>

        <?php /* if(version_compare(JVERSION, '3.0', 'lt'))
          echo 'onClick="checkAll('.count( $rows_book ).');"';
          else
          echo 'onClick="Joomla.checkAll(this);"'; */ ?>

            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="my_table 
                   my_table-bordered my_table-hover adminlist bl_admin_books_main_table">
                <tr>
                    <th width="4%">
                        <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
                    </th>
                    <th width="4%">id</th>
                    <th width="8%" align = "center" class="title" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_BOOKID; ?>
                    </th>
                    <th width="12%" align = "center"  class="title" nowrap="nowrap">
        <?php echo HTML_booklibrary::sort_head(_BOOKLIBRARY_LABEL_ISBN, 'isbn', $sort_arr); ?>
                    </th>
                    <th width="12%" align = "center" class="title" nowrap="nowrap">
        <?php echo HTML_booklibrary::sort_head(_BOOKLIBRARY_LABEL_TITLE, 'title', $sort_arr); ?>
                    </th>
                    <!--<th align = "center" class="title" width="5%" nowrap="nowrap" colspan="2">
        <?php echo _BOOKLIBRARY_LABEL_LINE; ?>
                    </th>-->
                    <th width="12%" align = "center" class="title" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_FETCH_INFO; ?>
                    </th>
                    <th width="12%" align = "center" class="title" nowrap="nowrap">
        <?php echo HTML_booklibrary::sort_head(_BOOKLIBRARY_LABEL_CATEGORY, 'category', $sort_arr); ?>
                    </th>
                    <th width="8%" align = "center" class="title" nowrap="nowrap">
        <?php echo HTML_booklibrary::sort_head_let($order_let); ?>
                    </th>
                    <th width="8%" align = "center" class="title" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_HITS; ?>
                    </th>
                    <th width="12%" align = "center" class="title" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_TYPE_USER; ?>
                    </th>	  
                    <th width="8%" align = "center"  class="title" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_PUBLIC; ?>
                    </th>
                    <th width="8%" align = "center" class="title" nowrap="nowrap">
                <?php echo _BOOKLIBRARY_LABEL_APPROVED; ?>
                    </th>
                    <th width="8%" align = "center" class="title" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_CONTROL; ?>
                    </th>
                    
                </tr>

        <?php for ($i = 0, $n = count($rows_book); $i < $n; $i++) {
            $row = $rows_book[$i];
            ?>

                    <tr class="row<?php echo $i % 2; ?>">
                        <td align="left">
            <?php if ($row->checked_out && $row->checked_out != $my->id) { ?>
                                &nbsp;
            <?php
            } else {
                echo mosHTML::idBox($i, $row->id, ($row->checked_out && $row->checked_out != $my->id), 'bid');
            }
            ?>
                        </td>
                        <td align = "center" ><?php echo $row->id; ?></td>
                        <td align = "center"><?php echo $row->bookid; ?></td>
                        <td align="center">
                            <a href="#edit" onClick="return listItemTask('cb<?php echo $i; ?>','edit')">
                            <?php echo $row->isbn; ?>
                            </a>
                        </td>
                        <td align="left">
                            <a href="#edit" onClick="return listItemTask('cb<?php echo $i; ?>','edit')">
            <?php echo $row->title; ?>
                            </a>
                        </td>
                        <td align="center">
            <?php echo mosBooklibraryWS :: getWsNameById($row->informationFrom); ?>
                        </td>
                        <td align = "center"><?php echo $row->category; ?></td>
                        <td align = "center" style="text-align:center;">
                        <?php if ($row->lend_from == null) { ?>
                                <a class="my_btn my_btn-success" href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','lend')">
                                    <img style="vertical-align:middle;" src="<?php echo $mosConfig_live_site; ?>/components/com_booklibrary/images/next.png"/>
                                </a>
                        <?php } else { ?>
                                <a class="my_btn my_btn-warning" href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','lend')">
                                    <img style="vertical-align:middle;" src="<?php echo $mosConfig_live_site; ?>/components/com_booklibrary/images/back.png"/>
                                </a>	
                                <?php } ?>
                        </td>
                        <td align = "center"><?php echo $row->hits; ?></td>
                                <?php //print_r($row);exit;  ?>
                        <td align="center"><?php echo $row->owneremail; ?></td>
            <?php
            $task = $row->published ? 'unpublish' : 'publish';
            $alt = $row->published ? 'Unpublish' : 'Publish';
            $img = $row->published ? 'ok.png' : 'remove.png';
            $task1 = $row->approved ? 'unapprove' : 'approve';
            $alt1 = $row->approved ? 'Unapproved' : 'Approved';
            $img1 = $row->approved ? 'ok.png' : 'remove.png';
            ?>
                        <td align="center">
                            <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task; ?>')">
                        <?php if (version_compare(JVERSION, "1.6.0"/* "3.0.0" */, "lt")) { ?>
                                    <img src="<?php echo $mosConfig_live_site . "/components/com_booklibrary/images/" . $img; ?>" border="0" alt="<?php echo $alt; ?>" />
                        <?php } else { ?>
                                    <img src="<?php echo $mosConfig_live_site . "/components/com_booklibrary/images/" . $img; ?>" border="0" alt="<?php echo $alt; ?>" />
                        <?php } ?>	
                            </a>
                        </td>
                        <td align="center">
                            <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task1; ?>')">
            <?php if (version_compare(JVERSION, "1.6.0"/* "3.0.0" */, "lt")) { ?>
                                    <img src="<?php echo $mosConfig_live_site . "/components/com_booklibrary/images/" . $img1; ?>" border="0" alt="<?php echo $alt1; ?>" />
                    <?php } else { ?>
                                    <img src="<?php echo $mosConfig_live_site . "/components/com_booklibrary/images/" . $img1; ?>" border="0" alt="<?php echo $alt1; ?>" />
                    <?php } ?>	
                        </td>
            <?php if ($row->checked_out) { ?>
                            <td align="center"><?php echo $row->owner_name; ?></td>
            <?php } else { ?>
                            <td align="center">&nbsp;</td>
            <?php } ?>
                    
                    </tr>
            <?php
        }//end for
        ?>
                <tr class="for_paginator">
                    <td colspan = "14"><?php echo $pageNav->getListFooter(); ?></td>
                </tr>
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
        </form>

    <?php
    }

    static function showPrintBooks($rows) {
        global $my, $mosConfig_live_site, $mainframe, $doc; // for J 1.6
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        ?>
        <html>
            <head>
                <title></title>
            </head>
            <body>
                <form id="adminForm" name="Print" action="<?php echo $mosConfig_live_site; ?>/administrator/index.php?option=com_booklibrary&task=print_item" method="post" target="_top">
                    <div align="left">
        <?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT_FONT_SIZE; ?>:
                        <select name="font_size" title="Select size font!">
                            <option value="1">1
                            <option value="2">2
                            <option value="3">3
                            <option value="4">4
                            <option value="5">5
                            <option value="6">6
                            <option value="7">7
                            <option value="8" selected >8
                            <option value="9">9
                            <option value="10">10
                            <option value="11">11
                            <option value="12">12
                            <option value="13">13
                            <option value="14">14
                            <option value="15">15
                            <option value="16">16
                            <option value="17">17
                            <option value="18">18
                        </select>
                        <br />
        <?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT_FORMAT; ?>:
                        <br />
                        <input type="hidden" name="format_w_h" value="verticall" title="Checked format!" checked />

                        <select name="format" title="Select size font!">
                            <option value="A5">A5&nbsp;(148x210&nbsp;mm)
                            <option value="A4" selected >A4&nbsp;(210x297&nbsp;mm)
                            <option value="A3">A3&nbsp;(297x420&nbsp;mm)
                            <option value="Letter">Letter&nbsp;(8,5x11&nbsp;inch)
                            <option value="Legal">Legal&nbsp;(8,5x14&nbsp;inch)
                            <option value="Tabloid">Tabloid&nbsp;(11x17&nbsp;inch)
                            <option value="Executive">Executive&nbsp;(7,5x10&nbsp;inch)
                        </select>
                        <p>
        <?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT_SELECT; ?>
                            <br />
                            <input type="submit" value="Next" title="Next step for print!"/>
                        </p>
                    </div>

                    <table cellpadding="4" cellspacing="0" border="1px" style="width:180mm" class="adminlist admin16">
                        <tr bgcolor="#d0d0d0">
                            <td width="5%" nowrap="nowrap" align="center"><?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT; ?>
                                <input type="checkbox" name="print_id" value="1" title="Select for print!" checked />
                            </td>
                            <td width="5%" nowrap="nowrap" align="center"><?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT; ?>
                                <input type="checkbox" name="print_bookid" value="1" title="Select for print!" checked />
                            </td>
                            <td width="5%" nowrap="nowrap" align="center"><?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT; ?>
                                <input type="checkbox" name="print_isbn" value="1" title="Select for print!" checked />
                            </td>
                            <td width="20%" nowrap="nowrap" align="center"><?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT; ?>
                                <input type="checkbox" name="print_title" value="1" title="Select for print!" checked />
                            </td>
                            <td width="10%" nowrap="nowrap" align="center"><?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT; ?>
                                <input type="checkbox" name="print_category" value="1" title="Select for print!" checked />
                            </td>
                            <td width="10%" nowrap="nowrap" align="center"><?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT; ?>
                                <input type="checkbox" name="print_lend_from" value="1" title="Select for print!" checked/>
                            </td>
                            <td width="10%" nowrap="nowrap" align="center"><?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT; ?>
                                <input type="checkbox" name="print_lend_until" value="1" title="Select for print!" checked/>
                            </td>
                            <td width="10%" nowrap="nowrap" align="center"><?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT; ?>
                                <input type="checkbox" name="print_user_name" value="1" title="Select for print!" checked />
                            </td>
                            <td width="10%" nowrap="nowrap" align="center"><?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT; ?>
                                <input type="checkbox" name="print_user_email" value="1" title="Select for print!" checked/>
                            </td>
                            <td width="10%" nowrap="nowrap" align="center"><?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT; ?>
                                <input type="checkbox" name="print_user_mailing" value="1" title="Select for print!" checked />
                            </td>
                            <td width="5%" nowrap="nowrap" align="center"><?php echo _BOOKLIBRARY_TOOLBAR_ADMIN_PRINT; ?>
                                <input type="checkbox" name="print_hits" value="1" title="Select for print!" checked />
                            </td>
                        </tr>
                        <tr bgcolor="#d0d0d0">
                            <th width="5%">id</th>
                            <th width="5%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_BOOKID; ?></th>
                            <th width="5%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_ISBN; ?></th>
                            <th width="20%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_TITLE; ?></th>
                            <th width="10%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_CATEGORY; ?></th>
                            <th width="10%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_LEND_FROM; ?></th>
                            <th width="10%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_LEND_UNTIL; ?></th>
                            <th width="10%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_LEND_USER; ?></th>
                            <th width="10%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_LEND_EMAIL; ?></th>
                            <th width="10%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_LEND_ADRES; ?></th>
                            <th width="5%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_HITS; ?></th>
                        </tr>

        <?php for ($i = 0; $i < count($rows); $i++) { ?>
                            <tr bgcolor="#<?php if (($i % 2) != 1) {
                echo "efefef";
            } else {
                echo "ffffff";
            } ?>" >
                                <td width="5%"><?php echo wordwrap($rows[$i]->id, 6, "<br />\n", 1); ?></td>
                                <td width="5%" nowrap="nowrap"><?php echo wordwrap($rows[$i]->bookid, 6, "<br />\n", 1); ?></td>
                                <td width="5%" nowrap="nowrap"><?php echo $rows[$i]->isbn; ?></td>
                                <td width="20%" nowrap="nowrap"><?php echo wordwrap($rows[$i]->title, 20, "<br />\n", 1); ?></td>
                                <td width="10%" nowrap="nowrap"><?php echo wordwrap($rows[$i]->category, 10, "<br />\n", 1); ?></td>
                                <td width="10%" nowrap="nowrap">
            <?php if (isset($rows[$i]->lend_from)) {
                for ($j = 0; $j < 10; $j++) {
                    echo $rows[$i]->lend_from[$j];
                }
            } else {
                echo "--";
            } ?>
                                </td>
                                <td width="10%" nowrap="nowrap">
            <?php if (isset($rows[$i]->lend_until)) {
                for ($j = 0; $j < 10; $j++) {
                    echo $rows[$i]->lend_until[$j];
                }
            } else {
                echo "--";
            } ?>
                                </td>
                                <td width="10%" nowrap="nowrap">
            <?php if (isset($rows[$i]->user_name) && ($rows[$i]->user_name != "")) {
                echo wordwrap($rows[$i]->user_name, 10, "<br />\n", 1);
            } else {
                echo "--";
            } ?>
                                </td>
                                <td width="10%" nowrap="nowrap">
            <?php if (isset($rows[$i]->user_email) && ($rows[$i]->user_email != "")) {
                echo wordwrap($rows[$i]->user_email, 10, "<br />\n", 1);
            } else {
                echo "--";
            } ?>
                                </td>
                                <td width="10%" nowrap="nowrap">
                    <?php if (isset($rows[$i]->user_mailing) && ($rows[$i]->user_mailing != "")) {
                        echo wordwrap($rows[$i]->user_mailing, 10, "<br />\n", 1);
                    } else {
                        echo "--";
                    } ?>
                                </td>
                                <td width="5%" nowrap="nowrap"><?php echo wordwrap($rows[$i]->hits, 6, "<br />\n", 1); ?></td>
                            </tr>
                <?php } ?>
                    </table>		
                </form>
            </body>
        </html>

                <?php
                @ session_start();
                $_SESSION['rows'] = $rows;
                exit();
            }

//end showPrintBooks($rows)
            //*********************************************************************************

            static function showPrintItem($rows) {
                global $my, $mosConfig_live_site, $mainframe, $doc; // for J 1.6
                $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
                $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
                ?>
        <html> 
            <head>  
                <title></title>
                <style type="text/css">
                    .print_font_nik {background-color:#ffffff;font-size:<?php echo $_REQUEST['font_size']; ?>pt;
                                     font-color:#000000;font-family:Arial,Times,Helvetica,Zapf-Chancery,Western,or Courier;}
                    </style>
                    <script language="JavaScript" type="text/javascript">
                        function print_item_no_button() 
                        {
                            var el  = document.getElementById('print_button_off'); 
                            el.style.display = 'none';window.print();
                        }
                    </script>
                </head>
                <body>
                    <div align="left" id="print_button_off">
                    <form id="adminForm" name="back_print" action="<?php echo $mosConfig_live_site; ?>/administrator/index.php?option=com_booklibrary&task=print_books" method="post" target="_top">
                        <input type="submit" value="Back" title="Back windows!" />
                    </form>
                    <p align="left">
                        <a href="#" onClick="javascript:print_item_no_button();" title="Print">
                            <img src="<?php echo $mosConfig_live_site; ?>/administrator/components/com_booklibrary/images/print.png"  alt="Print" name="Print" align="center" border="0" />
                        </a>
                    </p>	
                </div>
                        <?php
                        $kol = 0;
                        if (isset($_REQUEST['print_id']))
                            $kol++;
                        if (isset($_REQUEST['print_bookid']))
                            $kol++;
                        if (isset($_REQUEST['print_isbn']))
                            $kol++;
                        if (isset($_REQUEST['print_title']))
                            $kol++;
                        if (isset($_REQUEST['print_category']))
                            $kol++;
                        if (isset($_REQUEST['print_lend_from']))
                            $kol++;
                        if (isset($_REQUEST['print_lend_until']))
                            $kol++;
                        if (isset($_REQUEST['print_user_name']))
                            $kol++;
                        if (isset($_REQUEST['print_user_email']))
                            $kol++;
                        if (isset($_REQUEST['print_user_mailing']))
                            $kol++;
                        if (isset($_REQUEST['print_hits']))
                            $kol++;
                        if (($kol < 11) && (isset($_REQUEST['print_title']) == 0) && ($kol != 0))
                            $k = (int) (100 / $kol);
                        if (($kol < 11) && (isset($_REQUEST['print_title'])) && ($kol != 0)) {
                            $k = (int) (100 / $kol);
                            if ($kol == 10) {
                                $k_tit = $k + 9;
                                $k -= 1;
                            }
                            if ($kol == 9) {
                                $k_tit = $k + 8;
                                $k -= 1;
                            }
                            if ($kol == 8) {
                                $k_tit = $k + 15;
                                $k -= 2;
                            }
                            if ($kol == 7) {
                                $k_tit = $k + 16;
                                $k -= 3;
                            }
                            if ($kol == 6) {
                                $k_tit = $k + 20;
                                $k -= 4;
                            }
                            if ($kol == 5) {
                                $k_tit = $k + 20;
                                $k -= 5;
                            }
                            if ($kol == 4) {
                                $k_tit = $k + 15;
                                $k -= 5;
                            }
                            if ($kol == 3) {
                                $k_tit = $k + 10;
                                $k -= 5;
                            }
                            if ($kol == 2) {
                                $k_tit = $k;
                            }
                        }//end if
                        if ($kol != 0) {
                            if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'A5')) {
                                $width_tabl = 118;
                            }//138;}
                            if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'A4')) {
                                $width_tabl = 180;
                            }//200;}
                            if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'A3')) {
                                $width_tabl = 267;
                            }//287;}
                            if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'Letter')) {
                                $width_tabl = 185;
                            }//205;}
                            if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'Legal')) {
                                $width_tabl = 185;
                            }//205;}
                            if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'Tabloid')) {
                                $width_tabl = 249;
                            }//269;}
                            if (($_REQUEST['format_w_h'] == 'verticall') && ($_REQUEST['format'] == 'Executive')) {
                                $width_tabl = 160;
                            }//180;}
                            if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'A5')) {
                                $width_tabl = 200;
                            }
                            if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'A4')) {
                                $width_tabl = 287;
                            }
                            if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'A3')) {
                                $width_tabl = 410;
                            }
                            if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'Letter')) {
                                $width_tabl = 269;
                            }
                            if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'Legal')) {
                                $width_tabl = 343;
                            }
                            if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'Tabloid')) {
                                $width_tabl = 421;
                            }
                            if (($_REQUEST['format_w_h'] == 'horizontall') && ($_REQUEST['format'] == 'Executive')) {
                                $width_tabl = 244;
                            }
                            ?>

                    <table cellpadding="4" cellspacing="0" border="1px" style="width:<?php echo $width_tabl; ?>mm" class="print_font_nik admin17">
                        <tr bgcolor="#d0d0d0">
                    <?php if (isset($_REQUEST['print_id'])) { ?>
                                <th width="<?php if (isset($k)) {
                    echo $k . "%";
                } else {
                    echo "5%";
                } ?>">#</th>
            <?php } ?>
            <?php if (isset($_REQUEST['print_bookid'])) { ?>
                                <th width="<?php if (isset($k)) {
                    echo $k . "%";
                } else {
                    echo "5%";
                } ?>" nowrap="nowrap">
                <?php echo _BOOKLIBRARY_LABEL_BOOKID; ?>
                                </th>
            <?php } ?>
            <?php if (isset($_REQUEST['print_isbn'])) { ?>
                                <th width="<?php if (isset($k)) {
                    echo $k . "%";
                } else {
                    echo "5%";
                } ?>" nowrap="nowrap">
                <?php echo _BOOKLIBRARY_LABEL_ISBN; ?>
                                </th>
            <?php } ?>
            <?php if (isset($_REQUEST['print_title'])) { ?>
                                <th width="<?php if (isset($k_tit)) {
                    echo $k_tit . "%";
                } else if (($kol == 1) && (isset($_REQUEST['print_title']))) {
                    echo "100%";
                } else {
                    echo "20%";
                } ?>" nowrap="nowrap">
                <?php echo _BOOKLIBRARY_LABEL_TITLE; ?>
                                </th>
            <?php } ?>
                                    <?php if (isset($_REQUEST['print_category'])) { ?>
                                <th width="<?php if (isset($k)) {
                                            echo $k . "%";
                                        } else {
                                            echo "10%";
                                        } ?>" nowrap="nowrap">
                        <?php echo _BOOKLIBRARY_LABEL_CATEGORY; ?>
                                </th>
            <?php } ?>
            <?php if (isset($_REQUEST['print_lend_from'])) { ?>
                                <th width="<?php if (isset($k)) {
                    echo $k . "%";
                } else {
                    echo "10%";
                } ?>" nowrap="nowrap">
                <?php echo _BOOKLIBRARY_LABEL_LEND_FROM; ?>
                                </th>
            <?php } ?>
            <?php if (isset($_REQUEST['print_lend_until'])) { ?>
                                <th width="<?php if (isset($k)) {
                    echo $k . "%";
                } else {
                    echo "10%";
                } ?>" nowrap="nowrap">
                <?php echo _BOOKLIBRARY_LABEL_LEND_UNTIL; ?>
                                </th>
                            <?php } ?>
            <?php if (isset($_REQUEST['print_user_name'])) { ?>
                                <th width="<?php if (isset($k)) {
                    echo $k . "%";
                } else {
                    echo "10%";
                } ?>" nowrap="nowrap">
                <?php echo _BOOKLIBRARY_LABEL_LEND_USER; ?>
                                </th>
                    <?php } ?>
                    <?php if (isset($_REQUEST['print_user_email'])) { ?>
                                <th width="<?php if (isset($k)) {
                    echo $k . "%";
                } else {
                    echo "10%";
                } ?>" nowrap="nowrap">
                                <?php echo _BOOKLIBRARY_LABEL_LEND_EMAIL; ?>
                                </th>
                            <?php } ?>
                            <?php if (isset($_REQUEST['print_user_mailing'])) { ?>
                                <th width="<?php if (isset($k)) {
                    echo $k . "%";
                } else {
                    echo "10%";
                } ?>" nowrap="nowrap">
                                    <?php echo _BOOKLIBRARY_LABEL_LEND_ADRES; ?>
                                </th>
            <?php } ?>
            <?php if (isset($_REQUEST['print_hits'])) { ?>
                                <th width="<?php if (isset($k)) {
                    echo $k . "%";
                } else {
                    echo "5%";
                } ?>" nowrap="nowrap">
                                <?php echo _BOOKLIBRARY_LABEL_HITS; ?>
                                </th>
            <?php } ?>
                        </tr>
                    <?php for ($i = 0; $i < count($rows); $i++) { ?>
                            <tr bgcolor="#<?php if (($i % 2) != 1) {
                    echo "efefef";
                } else {
                    echo "ffffff";
                } ?>" >
                <?php if (isset($_REQUEST['print_id'])) { ?>
                                    <td width="<?php if (isset($k)) {
                        echo $k . "%";
                    } else {
                        echo "5%";
                    } ?>">
                    <?php if (isset($k)) {
                        $symbol = $k;
                    } else {
                        $symbol = 6;
                    } echo wordwrap($rows[$i]->id, $symbol, "<br />\n", 1); ?>
                                    </td>
                <?php } ?>
                <?php if (isset($_REQUEST['print_bookid'])) { ?>
                                    <td width="<?php if (isset($k)) {
                        echo $k . "%";
                    } else {
                        echo "5%";
                    } ?>" nowrap="nowrap">
                    <?php if (isset($k)) {
                        $symbol = $k;
                    } else {
                        $symbol = 6;
                    } echo wordwrap($rows[$i]->bookid, $symbol, "<br />\n", 1); ?>
                                    </td>
                <?php } ?>
                <?php if (isset($_REQUEST['print_isbn'])) { ?>
                                    <td width="<?php if (isset($k)) {
                        echo $k . "%";
                    } else {
                        echo "5%";
                    } ?>" nowrap="nowrap">
                    <?php echo $rows[$i]->isbn; ?>
                                    </td>
                <?php } ?>
                <?php if (isset($_REQUEST['print_title'])) { ?>
                                    <td width="<?php if (isset($k_tit)) {
                        echo $k_tit . "%";
                    } else if (($kol == 1) && (isset($_REQUEST['print_title']))) {
                        echo "100%";
                    } else {
                        echo "20%";
                    } ?>" nowrap="nowrap">
                    <?php
                    if (isset($k_tit)) {
                        $symbol = $k_tit;
                    } else if (($kol == 1) && (isset($_REQUEST['print_title']))) {
                        $symbol = $k;
                    } else {
                        $symbol = 20;
                    }
                    echo wordwrap($rows[$i]->title, $symbol, "<br />\n", 1);
                    ?>
                                    </td>
                <?php } ?>
                <?php if (isset($_REQUEST['print_category'])) { ?>
                                    <td width="<?php if (isset($k)) {
                        echo $k . "%";
                    } else {
                        echo "10%";
                    } ?>" nowrap="nowrap">
                    <?php if (isset($k)) {
                        $symbol = $k;
                    } else {
                        $symbol = 10;
                    } echo wordwrap($rows[$i]->category, $symbol, "<br />\n", 1); ?>
                                    </td>
                                <?php } ?>
                <?php if (isset($_REQUEST['print_lend_from'])) { ?>
                                    <td width="<?php if (isset($k)) {
                        echo $k . "%";
                    } else {
                        echo "10%";
                    } ?>" nowrap="nowrap">
                                    <?php if (isset($rows[$i]->lend_from)) {
                                        for ($j = 0; $j < 10; $j++) {
                                            echo $rows[$i]->lend_from[$j];
                                        }
                                    } else {
                                        echo "--";
                                    } ?>
                                    </td>
                <?php } ?>
                <?php if (isset($_REQUEST['print_lend_until'])) { ?>
                                    <td width="<?php if (isset($k)) {
                        echo $k . "%";
                    } else {
                        echo "10%";
                    } ?>" nowrap="nowrap">
                    <?php if (isset($rows[$i]->lend_until)) {
                        for ($j = 0; $j < 10; $j++) {
                            echo $rows[$i]->lend_until[$j];
                        }
                    } else {
                        echo "--";
                    } ?>
                                    </td>
                <?php } ?>
                <?php if (isset($_REQUEST['print_user_name'])) { ?>
                                    <td width="<?php if (isset($k)) {
                        echo $k . "%";
                    } else {
                        echo "10%";
                    } ?>" nowrap="nowrap">
                    <?php
                    if (isset($rows[$i]->user_name) && ($rows[$i]->user_name != "")) {
                        if (isset($k)) {
                            $symbol = $k;
                        } else {
                            $symbol = 10;
                        }
                        echo wordwrap($rows[$i]->user_name, $symbol, "<br />\n", 1);
                    } else {
                        echo "--";
                    }
                    ?>
                                    </td>
                <?php } ?>
                <?php if (isset($_REQUEST['print_user_email'])) { ?>
                                    <td width="<?php if (isset($k)) {
                        echo $k . "%";
                    } else {
                        echo "10%";
                    } ?>" nowrap="nowrap">
                    <?php
                    if (isset($rows[$i]->user_email) && ($rows[$i]->user_email != "")) {
                        if (isset($k)) {
                            $symbol = $k;
                        } else {
                            $symbol = 10;
                        }
                        echo wordwrap($rows[$i]->user_email, $symbol, "<br />\n", 1);
                    } else {
                        echo "--";
                    }
                    ?>
                                    </td>
                <?php } ?>
                <?php if (isset($_REQUEST['print_user_mailing'])) { ?>
                                    <td width="<?php if (isset($k)) {
                        echo $k . "%";
                    } else {
                        echo "10%";
                    } ?>" nowrap="nowrap">
                    <?php
                    if (isset($rows[$i]->user_mailing) && ($rows[$i]->user_mailing != "")) {
                        if (isset($k)) {
                            $symbol = $k;
                        } else {
                            $symbol = 10;
                        }
                        echo wordwrap($rows[$i]->user_mailing, $symbol, "<br />\n", 1);
                    } else {
                        echo "--";
                    }
                    ?>
                                    </td>
                <?php } ?>
                <?php if (isset($_REQUEST['print_hits'])) { ?>
                                    <td width="<?php if (isset($k)) {
                        echo $k . "%";
                    } else {
                        echo "5%";
                    } ?>" nowrap="nowrap">
                    <?php if (isset($k)) {
                        $symbol = $k;
                    } else {
                        $symbol = 6;
                    } echo wordwrap($rows[$i]->hits, $symbol, "<br />\n", 1); ?>
                                    </td>
                <?php } ?>
                            </tr>
            <?php }/* end for */ ?>
                    </table>
            <?php
        }//end if($kol != 0)
        ?>
            </body>
        </html>
        <?php
        exit();
    }

//end function showPrintItem()
    //*************************************************************************************************************
    //*********************************   end add for button print in Manager Books   *****************************
    //*************************************************************************************************************
    //*************************************************************************************************************
    //*********************************   begin for manage suggestion    ******************************************
    //*************************************************************************************************************


    //end showViewSuggestion($option, $suggestion)
    //*************************************************************************************************************
    //*********************************   end for manage suggestion    ********************************************
    //*************************************************************************************************************

    /**
     * Writes the edit form for new and existing records
     *
     * A new record is defined when <var>$row</var> is passed with the <var>id</var>
     * property set to 0.
     * @param mosBookLibrary The book object
     * @param string The html for the categories select list
     * @param string The html for the ordering select list
     */
    static function editBook($option, & $row, & $clist, & $wslist, & $langlist, & $langlistshow, & $rating, & $delete_ebook, & $reviews) { 
        global $books, $database, $booklibrary_configuration, $my, $mosConfig_live_site, $mainframe, $doc; // for J 1.6
//    $query = "SELECT lang_code, title FROM #__languages";
        //  $database->setQuery($query);
        // $languages = $database->loadObjectList();
        //my !!!!! -------- langdescription   ------------------------------------------------------------------------------------------
        $lg = JFactory::getLanguage();
        $installed = $lg->getKnownLanguages();
        $languages_row[] = mosHTML::makeOption('*', 'All');
        foreach ($installed as $installang) {
            $langname = $installang['name'];
            $languages_row[] = mosHTML::makeOption($langname, $langname);
        }
        @$langlistshow = mosHTML :: selectList($languages_row, 'langshow', 'class="inputbox" size="1"', 'value', 'text', $row->langshow);
        //end of my langdecription ---------  !!!!   ---------------------------------------------------------------------------------------
//echo "<br /><pre>" . print_r($languages_row, true). "</pre>"; exit;
//---- end of my langdescrition
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        $aa = $row->id ? _BOOKLIBRARY_HEADER_EDIT : _BOOKLIBRARY_HEADER_ADD;
        $html = "<div class='book_manager_caption' ><img src='./components/com_booklibrary/images/cfg.png' alt ='' />" . $aa . "</div>";
        $app = JFactory::getApplication();
        $app->JComponentTitle = $html;
        ?>

        <script language="javascript" type="text/javascript">
            function trim(string){return string.replace(/(^\s+)|(\s+$)/g, "");}
            Joomla.submitbutton = function(task) { // for J 1.6
                var bookid = document.getElementById("bookid").value;// for J 1.6
                var isbn = document.getElementById("isbn").value; // OR document.adminForm.isbn.value	
                var catid = document.getElementById("catid").value;// for J 1.6
                var titleid = document.getElementById("titleid").value;
                if (task == 'save') {
                    if (trim(bookid) == '') {
                        alert( "<?php echo _BOOKLIBRARY_ADMIN_INFOTEXT_JS_EDIT_BOOKID_CHECK; ?>" );
                        return;
                    } else if ( trim(isbn) == '') {
                        alert( "<?php echo _BOOKLIBRARY_ADMIN_INFOTEXT_JS_EDIT_ISBN_CHECK; ?>" );
                        return;
                    } else if (catid == '0' || catid == '') {
                        alert( "<?php echo _BOOKLIBRARY_ADMIN_INFOTEXT_JS_EDIT_CATEGORY; ?>");
                        return;
                    } else { submitform( task );}} else {submitform( task );}}
        </script>
        <form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
            <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform bl_admin_books_book my_table">
                <tr>
                    <td width="15%" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_BOOKID; ?>:</strong>
                    </td>
                    <td width="85%" align="left">
                        <input class="inputbox" type="text" name="bookid" id="bookid" size="20" maxlength="20" value="<?php echo $row->bookid; ?>" />
                    </td>
                </tr>
                <tr>
                    <td width="20%" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_ISBN; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" type="text" name="isbn" id="isbn" size="20" maxlength="20" value="<?php echo $row->isbn; ?>" />				
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_LANGUAGE; ?>:</strong></td>
                    <td align="left"><?php echo $langlist; ?></td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_LANGUAGEDESCRIPTION; ?>:</strong></td>
                    <td align="left"><?php echo $langlistshow; ?></td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_CATEGORY; ?>:</strong></td>
                    <td align="left"><?php echo $clist; ?></td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_FETCH_INFO; ?>:</strong></td>
                    <td align="left">
        <?php echo $wslist; ?>
                        &nbsp;&nbsp;&nbsp;<img src="../components/com_booklibrary/images/amazon/com-logo.gif" alt="amazon.com" border="0"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_COMMENT; ?>:</strong></td>
                    <td align="left">
        <?php editorArea('editor1', $row->comment, 'comment', 500, 250, '70', '10'); ?>
                    </td>
                </tr>
                        <?php if ($booklibrary_configuration['ebooks']['allow']) { ?>	
                    <tr>
                        <td valign="top" align="right">
                            <strong><?php echo _BOOKLIBRARY_LABEL_EBOOK_UPLOAD; ?>:</strong>
                        </td>
                        <td align="left">
                            <input class="inputbox" type="file" name="ebook_file" value="" size="50" maxlength="250" onClick="document.adminForm.ebook_Url.value ='';"/>
                        </td>
                    </tr>		
                    <tr>
                        <td valign="top" align="right">
                            <strong><?php echo _BOOKLIBRARY_LABEL_EBOOK_UPLOAD_URL; ?>:</strong>
                        </td>
                        <td align="left">
                            <input class="inputbox" type="text" name="ebook_Url" value="<?php echo $row->ebookURL; ?>" size="50" maxlength="250"/>
                        </td>
                    </tr>		
        <?php } if (count($row->ebookURL) > 0) { ?>
                    <tr>
                        <td valign="top" align="right">
                            <strong><?php echo _BOOKLIBRARY_LABEL_EBOOK_DELETE; ?>:</strong>
                        </td>
                        <td align="left"><?php echo $delete_ebook; ?></td>
                    </tr>
        <?php } ?>		
                <tr>
                    <td colspan="2"><hr size="2" width="100%" /></td>
                </tr>
                <tr>
                    <td valign="top" align="right">&nbsp;</td>
                    <td align="left">
        <?php echo _BOOKLIBRARY_ADMIN_TEXT_WSINFO_TEXT1; ?>
                        <strong>
        <?php echo _BOOKLIBRARY_LABEL_FETCH_INFO; ?>
                            ->
        <?php echo _BOOKLIBRARY_WS_NO; ?>
                        </strong>
                    </td>
                </tr>	
                <tr>
                    <td valign="top" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_TITLE; ?>:</strong>
                    </td>
                    <td align="left">
                        <input class="inputbox" id="titleid" type="text" name="title" size="80" value="<?php echo $row->title; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_AUTHORS; ?>:</strong>
                    </td>
                    <td align="left">
                        <input class="inputbox" type="text" name="authors" size="80" value="<?php echo $row->authors; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_MANUFACTURER; ?>:</strong>
                    </td>
                    <td align="left">
                        <input class="inputbox" type="text" name="manufacturer" size="80" value="<?php echo $row->manufacturer; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_PUB_DATE; ?>:</strong>
                    </td>
                    <td align="left">
                        <input class="inputbox" type="text" name="release_Date" size="30" value="<?php echo $row->release_Date; ?>" />				
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_PRICE; ?>:</strong>
                    </td>
                    <td align="left">
                        <input class="inputbox" type="text" name="price" size="15" value="<?php echo $row->price; ?>" />
                        <input class="inputbox" type="text" name="priceunit" size="6" value="<?php echo $row->priceunit; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_EDITION; ?>:</strong>
                    </td>
                    <td align="left">
                        <input class="inputbox" type="text" name="edition" size="45" value="<?php echo $row->edition; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_NUMPAGES; ?>:</strong>
                    </td>
                    <td align="left">
                        <input class="inputbox" type="text" name="numberOfPages" size="6" value="<?php echo $row->numberOfPages; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_PICTURE_URL; ?>:</strong>
                    </td>
                    <!-- /////////////////////////////Begin AddPatch CODE///////////////////-->           
                    <td align="left">
                        <input class="inputbox" type="text" name="imageURL" size="80" value="<?php echo $row->imageURL; ?>" />				
                        <input class="inputbox" type="button" name="default_patch" value="Set Local Cover Path" onClick="build_patch()" />
                        <script type="text/javascript" language="javascript">
                            function build_patch()
                            {
                                var isbn = document.adminForm.isbn.value;var url = 'components/com_booklibrary/isbn_build.php';
        <?php $cover_path = $booklibrary_configuration['fetchImages']['location']; ?>	
                                    var cover_path = "<?php echo $cover_path; ?>";									
                                    if (window.XMLHttpRequest)
                                    {
                                        // Mozilla, Safari,...
                                        http_request = new XMLHttpRequest();
                                        if (http_request.overrideMimeType)
                                        {http_request.overrideMimeType('text/xml');} }
                                    else if (window.ActiveXObject)
                                    {
                                        // IE
                                        try
                                        {http_request = new ActiveXObject("Msxml2.XMLHTTP");}
                                        catch (e)
                                        { try
                                            {http_request = new ActiveXObject("Microsoft.XMLHTTP");}
                                            catch (e) {} }}
                                    if (!http_request)
                                    { alert('Giving up :( Cannot create an XMLHTTP instance');
                                        return false; }
                                    http_request.onreadystatechange = alertContents;
                                    http_request.open("POST", url, true);
                                    http_request.setRequestHeader('Content-Type' , 'application/x-www-form-urlencoded');
                                    http_request.send('isbn='+isbn+'&cover_path='+cover_path); }

                                function alertContents()
                                { if (http_request.readyState == 4)
                                    { if (http_request.status == 200)
                                        { var resp_text = http_request.responseText;if (resp_text == 1)
                                            { alert("<?php echo _BOOKLIBRARY_TOOLBAR_NEW_BOOK_INCORRECT_FOLDER . " " . $cover_path; ?>");}
                                            if (resp_text == false) 
                                            { alert(resp_text + "<?php echo _BOOKLIBRARY_TOOLBAR_NEW_BOOK_INCORRECT_FILE; ?>");}
                                            else { document.adminForm.imageURL.value = resp_text;} }
                                        else { alert('There was a problem with the request.');}
                                    } }
                        </script>				
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_PICTURE_URL_UPLOAD; ?>:</strong>
                    </td>
                    <td align="left">
                        <input class="inputbox" type="file" name="picture_file" value="" size="50" maxlength="250" />
                        <br /><?php echo _BOOKLIBRARY_LABEL_PICTURE_URL_DESC; ?>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_URL; ?>:</strong>
                    </td>
                    <td align="left">
                        <input class="inputbox" type="text" name="URL" size="80" value="<?php echo $row->URL; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_BOOKOWNER; ?>:</strong>
                    </td>
                    <td align="left">
        <?php echo $row->owner; ?>(<?php echo $row->owneremail; ?>)
                        <input type="hidden" name="owneremail" size="80" value="<?php echo $row->owneremail; ?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><hr size="2" width="100%" /></td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_FEATURE; ?>:</strong>
                    </td>
                    <td align="left">
                        <input class="inputbox" type="text" name="featured_clicks" size="30" value="<?php echo $row->featured_clicks; ?>" />				
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <strong><?php echo _BOOKLIBRARY_LABEL_SHOWS; ?>:</strong>
                    </td>
                    <td align="left">
                        <input class="inputbox" type="text" name="featured_shows" size="30" value="<?php echo $row->featured_shows; ?>" />				
                    </td>
                </tr>
                        <?php
                        //***********************************************************************************************************
                        //**********************************   begin change review   ************************************************
                        //***********************************************************************************************************
                        if ($reviews > false) { /* show, if review exist */
                            ?>
                    <tr>
                        <td colspan="7">
                            <hr width="100%" size="2" align="left"> <h3><?php echo _BOOKLIBRARY_LABEL_REVIEWS; ?>:</h3> 
                        </td>
                    </tr>

                    <table class="adminlist admin22">
                        <tr class="row0">
                            <td width="3%" valign="top" align="center"><div>id</div></td>
                            <td width="2%" valign="top" align="center"><div></div></td>
                            <td width="10%" valign="top" align="center">
                                <strong><?php echo _BOOKLIBRARY_LABEL_REVIEW_TITLE; ?>:</strong>
                            </td>
                            <td width="10%" valign="top" align="center">
                                <strong><?php echo _BOOKLIBRARY_LABEL_LEND_USER; ?>:</strong>
                            </td>
                            <td width="65%" valign="top" align="center">
                                <strong><?php echo _BOOKLIBRARY_LABEL_REVIEW_COMMENT; ?>:</strong>
                            </td>
                            <td width="5%" valign="top" align="center">
                                <strong><?php echo _BOOKLIBRARY_LABEL_PUB_DATE; ?>:</strong>
                            </td>
                            <td width="5%" valign="top" align="center">
                                <strong><?php echo _BOOKLIBRARY_LABEL_REVIEW_RATING; ?>:</strong>
                            </td>
                        </tr>

                <?php for ($i = 0, $nn = 1; $i < count($reviews); $i++, $nn++) /* if not one comment */ {
                    ?>
                            <tr class="row0">
                                <td valign="top" align="center">
                                    <div><?php echo $nn; ?></div>
                                </td>
                                <td valign="top" align="center">
                                    <div>
                                <?php echo "<input type='radio' id='cb" . $i . "' name='bid[]' value='" . $row->id . "," . $reviews[$i]->id . "' onclick='isChecked(this.checked);' />"; ?>
                                    </div>
                                </td>
                                <td valign="top" align="center">
                                    <div><?php print_r($reviews[$i]->title); ?></div>
                                </td>
                                <td valign="top" align="left">
                                    <div><?php print_r($reviews[$i]->name); ?></div>
                                </td>
                                <td valign="top" align="left">
                                    <div><?php print_r($reviews[$i]->comment); ?></div>
                                </td>
                                <td valign="top" align="left">
                                    <div><?php print_r($reviews[$i]->date); ?></div>
                                </td>
                                <td valign="top" align="left">
                                    <div><img src="../components/com_booklibrary/images/rating-<?php echo $reviews[$i]->rating; ?>.gif" alt="<?php echo ($reviews[$i]->rating) / 2; ?>" border="0" align="right"/>&nbsp;</div>
                                </td>
                            </tr>
            <?php }/* end for(...) */ ?>
                    </table>
                    <?php }/* end if(...) */ ?>
            </table>

            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="option" value="<?php echo $option; ?>" />		
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="task" value="" />				
        </form>
        <?php
        //***********************************************************************************************************
        //**********************************   end change review   **************************************************
        //***********************************************************************************************************
    }

    function showInfoRefetchBooks($option, $result, & $wslist) {
        global $my, $mosConfig_live_site, $mainframe, $doc; // for J 1.6
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        ?>

        <form id="adminForm" action="index.php" method="post" name="adminForm">
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist bl_admin_fetch_information my_table">
                <tr>
                    <td  colspan="6"><?php echo _BOOKLIBRARY_REFETCH; ?></td>
                </tr>
                <tr>
                    <td colspan="6">
                        <strong><?php echo _BOOKLIBRARY_LABEL_INFO_FETCH; ?></strong>
        <?php echo $wslist; ?>
                        &nbsp;&nbsp;&nbsp;
                        <img src="../components/com_booklibrary/images/amazon/com-logo.gif" alt="amazon.com" border="0" />
                    </td>
                </tr>
                <tr>
                    <th align = "center" class="title" width="20">
        <?php echo _BOOKLIBRARY_LABEL_INFO_REFETCH; ?>
                    </th>
                    <th align = "center" class="title" width="30">id</th>
                    <th align = "center"  class="title" width="15%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_BOOKID; ?></th>
                    <th align = "center" class="title" width="30%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_ISBN; ?></th>
                    <th align = "center"  class="title" width="30%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_RESULT; ?></th>
                </tr>
        <?php
        $i = 0;
        while ($i < count($result)) {
            $row = $result[$i];
            ?>

                    <tr class="row<?php echo $i % 2; ?>">
                        <td align = "center">
            <?php
            if ($row[3] != "OK") {
                echo "<input type='checkbox' id='cb'" . $i . "' name='bid[]' value='" . $row[0] . "' onclick='isChecked(this.checked);' />";
            } else {
                echo "&nbsp";
            }
            ?>
                        </td>
                        <td align = "center"><?php echo $row[0] ?></td>
                        <td align = "center"><?php echo $row[1] ?></td>
                        <td align = "center"><?php echo $row[2] ?></td>
                        <td align = "center"><?php echo $row[3] ?></td>				
                    </tr>
            <?php $i++;
        } ?>
                <input type="hidden" name="option" value="<?php echo $option; ?>" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
            </table>
        </form>
    <?php
    }

    function refetchBoosks($option, $rows, & $wslist) {
        global $my, $mosConfig_live_site, $mainframe, $doc; // for J 1.6
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        ?>

        <form id="adminForm" action="index.php" method="post" name="adminForm">

            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist bl_admin_refetch_information my_table">
                <tr>
                    <td  colspan="6"><?php echo _BOOKLIBRARY_REFETCH; ?></td>
                </tr>
                <tr>
                    <td  colspan="6">
                        <strong><?php echo _BOOKLIBRARY_LABEL_INFO_FETCH; ?></strong>
                        <?php echo $wslist; ?>
                        &nbsp;&nbsp;&nbsp;
                        <img src="../components/com_booklibrary/images/amazon/com-logo.gif" alt="amazon.com" border="0" />
                    </td>
                </tr>
                <tr>
                    <th  class="title" width="20">
                        <input type="checkbox" name="toggle" value="" checked="checked" onClick="Joomla.checkAll(this<?php // echo count( $rows ); ?>);" />
                    </th>
                    <th align = "center" class="title" width="30">id</th>
                    <th  align = "center" class="title" width="15%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_BOOKID; ?></th>
                    <th align = "center" class="title" width="30%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_ISBN; ?></th>
                    <th align = "center" class="title" width="30%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_TITLE; ?></th>
                    <th align = "center" class="title" width="25%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_FETCHED_FROM; ?></th>
                </tr>

        <?php
        $i = 0;
        while ($i < count($rows)) {
            $row = $rows[$i];
            ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td align = "center">
                            <input type="checkbox" checked="checked" id="cb<?php echo $i; ?>" name="bid[]" value="<?php echo $row->id; ?>" onClick="isChecked(this.checked);" />
                        </td>
                        <td align = "center"><?php echo $row->id ?></td>
                        <td align = "center"><?php echo $row->bookid ?></td>
                        <td align = "center"><?php echo $row->isbn ?></td>
                        <td align = "left"><?php echo $row->title ?></td>
                        <td align = "center">
            <?php echo mosBooklibraryWS :: getWsNameById($row->informationFrom); ?>
                        </td>
                    </tr>
            <?php $i++;
        } ?>
                <input type="hidden" name="option" value="<?php echo $option; ?>" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="1" />
            </table>
        </form>
            <?php
            }

            static function showImportExportBooks($params, $option) {
                global $my, $mosConfig_live_site, $mainframe, $doc; // for J 1.6
                $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
                $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
                $html = "<div class='book_manager_caption' ><img src='./components/com_booklibrary/images/cfg.png' alt ='Config' /> " . _BOOKLIBRARY_ADMIN_IMPEXP . "</div>";
                $app = JFactory::getApplication();
                $app->JComponentTitle = $html;
                ?>

        <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
        <script type="text/javascript" language="Javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/overlib_mini.js"></script>
        <script language="javascript" type="text/javascript">
            function impch()
            {	
                var a = document.getElementById('import_type').value;
                if(a == 4)
                    document.getElementById('import_catid').disabled=true;
                else
                    document.getElementById('import_catid').disabled=false;
            }
            function expch()
            {	
                var a = document.getElementById('export_type').value;
                if(a == 4)
                    document.getElementById('export_catid').disabled=true;
                else
                    document.getElementById('export_catid').disabled=false;
            }
            Joomla.submitbutton = function(pressbutton) { // for 1.6
                var form = document.adminForm;
                if (pressbutton == 'import') {
                    if (form.import_type.value == '0') {
                        alert("<?PHP echo _BOOKLIBRARY_SHOW_IMPEXP_ERR1; ?>" );
                        return;
                    }
                    if (form.import_file.value == '' ) {
                        alert("<?PHP echo _BOOKLIBRARY_SHOW_IMPEXP_ERR3; ?>");
                        return;
                    }
                    if (form.import_catid.value == '0' && form.import_type.value != '4' && form.import_type.value != '0') {
                        alert("<?PHP echo _BOOKLIBRARY_SHOW_IMPEXP_ERR2; ?>");
                        return;
                    }
                    if (form.import_catid.value != '0' && form.import_file.value == '') {
                        alert("<?PHP echo _BOOKLIBRARY_SHOW_IMPEXP_ERR3; ?>");
                        return;
                    }
                    if ((form.import_type.value == '2') && (form.import_catid.value != '0' && form.import_file.value != '')) {
                        alert("<?php echo _BOOKLIBRARY_SHOW_IMPEXP_ERR5; ?>");
                        submitform( pressbutton );
                    }
                    if ((form.import_type.value == '1') && (form.import_catid.value != '0' && form.import_file.value != '')) {
                        submitform( pressbutton );
                    }
        								
                    if (form.import_type.value == '4') {
                        resultat_1 = confirm("<?php echo _BOOKLIBRARY_SHOW_IMPEXP_CONF; ?>");
                        if (resultat_1) submitform( pressbutton );
                    }
                }
                if (pressbutton == 'export') {
                    if (form.export_type.value == '0') {
                        alert("<?PHP echo _BOOKLIBRARY_SHOW_IMPEXP_ERR4; ?>");
                        return;
                    }
                    if (form.export_type.value == '1') {
                        submitform( pressbutton );
                    }
                    if (form.export_type.value == '2') {
                        submitform( pressbutton );
                    }
                    if (form.export_type.value == '4') {
                        submitform( pressbutton );
                    }
                } }
        </script>

        <form class="bl_admin_import_export_form" id="adminForm" action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
        <?php
        if (version_compare(JVERSION, "3.0.0", "ge")) {
            $options = Array();
            echo JHtml::_('tabs.start', 'addBook', $options);
            echo JHtml::_('tabs.panel', _BOOKLIBRARY_ADMIN_IMP, 'panel_1_addBook');
        } else {
            $tabs = new mosTabs(1);
            $tabs->startPane("impexPane");
            $tabs->startTab(_BOOKLIBRARY_ADMIN_IMP, "impexPane");
        }
        ?>

            <table class="adminform bl_admin_import_export_import_table my_table" width="100%">
                <!--*****************************************************************************************************************-->
                <!--********************   begin add Warning in 'Import' for 'CSV', 'XML', 'MySQL tables import'   ******************-->
                <!--*****************************************************************************************************************-->
                <tr>
                    <td colspan="3">
        <?php echo _BOOKLIBRARY_SHOW_IMPORT_WARNING_MESSAG; ?>
                        <hr />
                    </td>		
                </tr>
                <!--*****************************************************************************************************************-->
                <!--********************   end add Warning in 'Import' for 'CSV', 'XML', 'MySQL tables import'   ********************-->
                <!--*****************************************************************************************************************-->
                <tr>
                    <td><?php echo _BOOKLIBRARY_SHOW_IMPEXP_LABEL_IMPORT_TYP; ?>:</td> <!-- Typ importu -->
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                        <td class="width_mostooltip" width="5%"><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_CAT_TT_HEAD, _BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_CAT, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                        <td class="width_mostooltip"><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_TYP_TT_HEAD, _BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_TYP, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>
                    <td><?php echo $params['import']['type']; ?></td>
                </tr>
                <tr>
                    <td width="5%"><?php echo _BOOKLIBRARY_SHOW_IMPEXP_LABEL_IMPORT_CATEGORY; ?>:</td> <!-- Kategoria -->
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                        <td class="width_mostooltip" width="5%"><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_CAT_TT_HEAD, _BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_CAT, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                        <td class="width_mostooltip" width="5%"><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_CAT_TT_HEAD, _BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_CAT, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>		
                    <td width="50%"><?php echo $params['import']['category']; ?></td>         
                </tr>      
                <tr>
                    <td><?php echo _BOOKLIBRARY_SHOW_IMPEXP_LABEL_IMPORT_FILE; ?>:</td>   <!-- Plik do importu -->      
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                        <td class="width_mostooltip" width="20"><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_FILE_TT_HEAD, _BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_FILE, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                        <td class="width_mostooltip" width="20"><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_FILE_TT_HEAD, _BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_IMPORT_FILE, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>
                    <td><input class="inputbox" type="file" name="import_file" value="" size="50" maxlength="250" /></td>         
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <!-- begin old poka ostavim
                <tr>
                    <td width="185">&nbsp;</td>
                    <td width="20">&nbsp;</td>
                    <td>
                            <?php //echo _BOOKLIBRARY_SHOW_IMPEXP_FORMAT;
                            ?>
                    </td>
                </tr>
                end old poka ostavim -->
            </table>

        <?php
        if (version_compare(JVERSION, "3.0.0", "ge")) {
            echo JHtml::_('tabs.panel', _BOOKLIBRARY_ADMIN_EXP, 'panel_2_addBook');
        } else {
            $tabs->endTab();
            $tabs->startTab(_BOOKLIBRARY_ADMIN_EXP, "impexPane");
        }
        ?>

            <table class="adminform bl_admin_import_export_export_table my_table" width="100%">
                <!--*****************************************************************************************************************-->
                <!--********************   begin add Warning in 'Export' for 'CSV', 'XML', 'MySQL tables import'   ******************-->
                <!--*****************************************************************************************************************-->
                <tr>
                    <td colspan="3">
        <?php echo _BOOKLIBRARY_SHOW_EXPORT_WARNING_MESSAG; ?>
                        <hr />
                    </td>		
                </tr>
                <!--*****************************************************************************************************************-->
                <!--********************   end add Warning in 'Export' for 'CSV', 'XML', 'MySQL tables import'   ********************-->
                <!--*****************************************************************************************************************-->
                <tr>
                    <td width="5%"><?php echo _BOOKLIBRARY_SHOW_IMPEXP_LABEL_EXPORT_TYP; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                        <td width="5%" style="text-align:left;"><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_EXPORT_TYP_TT_HEAD, _BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_EXPORT_TYP, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                        <td width="5%" style="text-align:left;"><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_EXPORT_TYP_TT_HEAD, _BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_EXPORT_TYP, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
                            <?php } ?>		
                    <td width="50%"><?php echo $params['export']['type']; ?></td>
                </tr>
                <tr>
                    <td><?php echo _BOOKLIBRARY_SHOW_IMPEXP_LABEL_EXPORT_CATEGORY; ?>:</td>
                                <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                        <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_EXPORT_CAT_TT_HEAD, _BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_EXPORT_CAT, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                                    ?></td>
        <?php } else { ?>
                        <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_EXPORT_CAT_TT_HEAD, _BOOKLIBRARY_ADMIN_SHOW_IMPEXP_LABEL_EXPORT_CAT, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
                                <?php } ?>	
                    <td><?php echo $params['export']['category']; ?></td>         
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
            </table>

        <?php
        if (version_compare(JVERSION, "3.0.0", "ge")) {
            echo JHtml::_('tabs.end');
        } else {
            $tabs->endTab();
            $tabs->endPane();
        }
        ?>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="" />
        </form>

    <?php
    }

    static function showLendBooks($option, $rows, & $userlist, $type) {
         global $my, $mosConfig_live_site, $mainframe, $doc, $app; // for J 1.6
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        //$doc->addStyleSheet($mosConfig_live_site.'/components/com_booklibrary/includes/js/calendar/calendar-mos.css');
        //$doc->addScript($mosConfig_live_site.'/components/com_booklibrary/includes/js/mambojavascript.js');
        //$doc->addScript($mosConfig_live_site.'/components/com_booklibrary/includes/js/calendar/calendar.js');
        //$doc->addScript($mosConfig_live_site.'/components/com_booklibrary/includes/js/calendar/lang/calendar-en-GB.js');
        //$doc->addScript($mosConfig_live_site.'/components/com_booklibrary/includes/js/overlib_mini.js');
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        switch ($type) {
            case 'lend':
                $a = _BOOKLIBRARY_SHOW_LEND_BOOKS;
                break;
            case 'lend_return':
                $a = _BOOKLIBRARY_SHOW_LEND_RETURN;
                break;
            case 'edit_lend':
                $a = _BOOKLIBRARY_SHOW_LEND_EDIT;
                break;
            default :
                $a = "&nbsp;";
                break;
        }

        $html = "<div class='book_manager_caption' ><img src='./components/com_booklibrary/images/cfg.png' alt ='Config' /> " . $a . "</div>";
        $app->JComponentTitle = $html;
        ?>
        <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
        <form action="index.php" method="post" name="adminForm" id="adminForm">

        <?php if ($type == "lend" or $type == "edit_lend") { ?>
                <table class="bl_admin_lent-top_table my_table" cellpadding="4" cellspacing="0" border="0" width="100%">
                    <tr>
                        <td align="center" nowrap="nowrap" width="10%">
                            <label><?php echo _BOOKLIBRARY_LABEL_LEND_TO . ':'; ?></label>
                        </td>
                        <td align="center" nowrap="nowrap" width="35%">
            <?php echo $userlist; ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" nowrap="nowrap" width="10%">
                            <label><?php echo _BOOKLIBRARY_LABEL_LEND_USER . ':'; ?></label>
                        </td>
                        <td colspan="2" width="35%">  
                            <input type="text" name="user_name" class="inputbox" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left" nowrap="nowrap">
                            <label><?php echo _BOOKLIBRARY_LABEL_LEND_EMAIL . ':'; ?></label>
                        </td>
                        <td colspan="4">    
                            <input type="text" name="user_email" class="inputbox" />
                        </td>
                    </tr>
                    <tr>	
                        <td align="left" nowrap="nowrap">
                            <label><?php echo "lend from:"; ?></label>
                        </td>
                        <td align="left" nowrap="nowrap">
            <?php echo JHtml::_('calendar', date("Y-m-d"), 'lend_from', 'lend_from', '%Y-%m-%d'); ?>
                        </td>
                        <td align="left" nowrap="nowrap">
                            <label><?php echo _BOOKLIBRARY_LABEL_LEND_TIME . ':'; ?></label>
                        </td>
                        <td align="left" nowrap="nowrap" colspan="2">
                                    <?php echo JHtml::_('calendar', date("Y-m-d"), 'lend_until', 'lend_until', '%Y-%m-%d'); ?>
                        </td>
                    </tr>	
                </table>
                                <?php } else { ?>
                &nbsp;
        <?php
        }
        $all = JFactory::getDBO();
        $query = "SELECT * FROM #__booklibrary_lend ";
        $all->setQuery($query);
        $num = $all->loadObjectList();
        ?>

            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist bl_admin_lent-bottom_table my_table">
                <tr>
                    <th width="20">
                        <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this<?php //echo count( $num );  ?>);" />
                    </th>
                    <th align = "center" width="30">id</th>
                    <th align = "center" class="title" width="5%" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_BOOKID; ?>
                    </th>
                    <th align = "center" class="title" width="15%" nowrap="nowrap">
                                <?php echo _BOOKLIBRARY_LABEL_ISBN; ?>
                    </th>
                    <th align = "center" class="title" width="25%" nowrap="nowrap">
                                <?php echo _BOOKLIBRARY_LABEL_TITLE; ?>
                    </th>
                    <th align = "center" class="title" width="15%" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_LEND_FROM; ?>
                    </th>
                    <th align = "center" class="title" width="15%" nowrap="nowrap">
                                <?php echo _BOOKLIBRARY_LABEL_LEND_UNTIL; ?>
                    </th>
                    <th align = "center" class="title" width="15%" nowrap="nowrap">
                                            <?php echo _BOOKLIBRARY_LABEL_LEND_RETURN; ?>
                    </th>
                    <th align = "center" class="title" width="20%" nowrap="nowrap">
                                <?php echo _BOOKLIBRARY_LABEL_LEND_TO; ?>
                    </th>
                </tr>
        <?php
        for ($i = 0, $n = count($rows); $i < $n; $i++) {
            $row = $rows[$i];
            //error for this row lend was called even if the book is already lent out
            if ($row->lend_from != null && $type == "lend") {
                ?>
                        &nbsp;
                                    <?php
                                    //lend was called for a correct book
                                } else if ($row->lend_from == null && $type == "lend") {
                                    //lend return was called on a book which was not lend out
                                } else if ($row->lend_from == null && $type == "lend_return") {
                                    ?>
                        &nbsp;
                                    <?php
                                    //lend return was called correctly
                                } else if ($row->lend_from != null && $type == "lend_return") {
                                    
                                } else {
                                    ?>
                        &nbsp;
                                    <?php } ?>

                    <input class="inputbox" type="hidden"  name="bookid" id="bookid" size="0" maxlength="0" value="<?php echo $row->bookid; ?>" />
                    <input class="inputbox"  type="hidden"  name="id" id="id" size="0" maxlength="0" value="<?php echo $row->id; ?>" />
                    <input class="inputbox"  type="hidden"  name="id2" id="id2" size="0" maxlength="0" value="<?php echo $row->id; ?>" />
                    <tr>
            <?php
            $book = $row->id;
            $title = $row->title;
            $data = JFactory::getDBO();
            $query = "SELECT * FROM #__booklibrary_lend WHERE fk_bookid=" . $book . " ORDER BY lend_return ";
            $data->setQuery($query);
            $alllend = $data->loadObjectList();
            if ($type == "lend") {
                ?>
                            <td align="center">
                                <input type="checkbox"  id="checkbook" name="checkbook" value="on"/>
                            </td>
                            <td align="center"></td>
                            <td align="center"><?php echo $row->bookid; ?></td>
                            <td align="center"><?php echo $row->isbn; ?></td>
                            <td align="center"><?php echo $row->title; ?></td>
                            <td align="center"></td>	
                            <td align="center"></td>	
                            <td align="center"></td>
                        </tr>
                                    <?php } else if ($type == "edit_lend") { ?>
                        <input type="hidden"  name="checkbook" id="checkbook" value="on" /></td>
            <?php
            } 
            
            for($i = 0;$i <count($alllend); $i++) {
                if(empty($alllend[$i]->user_name) && empty($alllend[$i]->user_email)) {
                    $query = "SELECT name FROM #__users WHERE id = ".$alllend[$i]->fk_userid;
                    $data->setQuery($query);
                    $alllend[$i]->user_name = $data->loadResult();
                }
            }
            
            $num = 1;
            for ($i = 0, $n = count($alllend); $i < $n; $i++) {
                if ($type != "lend") {
                    if (!trim($alllend[$i]->lend_return)) {
                        ?>	
                                <td align="center">
                                    <input type="checkbox"  id="cb<?php echo $i; ?>" name="bid[]" value="<?php echo $alllend[$i]->id; ?>"    onClick="isChecked(this.checked);" />
                                </td>
                                        <?php } else { ?>
                                <td align="center">
                                    <input type="hidden"  id="Test" name="Test"     onClick="isChecked(this.checked);" /></td>
                                        <?php }
                                    } else { ?>
                            <td align="center">
                                <input   TYPE="hidden"  id="cb<?php echo $i; ?>" name="bid[]" value="<?php echo $alllend[$i]->id; ?>" onClick="isChecked(this.checked);" />
                            </td>
                                        <?php } ?>
                        <td align="center"><?php echo $num; ?></td>
                        <td align="center"><?php echo $row->bookid; ?></td>
                        <td align="center"><?php echo $row->isbn; ?></td>
                        <td align="center"><?php echo $row->title; ?></td>
                        <td align="center"><?php echo $alllend[$i]->lend_from; ?></td>	
                        <td align="center"><?php echo $alllend[$i]->lend_until; ?></td>	
                        <td align="center"><?php echo $alllend[$i]->lend_return; ?></td>	
                        <td align="center"><?php echo $alllend[$i]->user_name; if(!empty($alllend[$i]->user_email)) echo ":  " . $alllend[$i]->user_email; ?></td>
                        </tr>
                <?php $num++;
            }
        }
        ?>
                <input class="inputbox"  type="hidden"  name="mas" id="mas" size="0" maxlength="0" value="<?php echo $alllend; ?>" />
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="1" />
            <input type="hidden" name="save" value="1" />
        </form>

    <?php
    }

    static function showConfiguration_frontend($lists, $option, $txt) {
        global $my, $mosConfig_live_site, $mainframe, $act, $task, $doc; // for J 1.6
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        $html = "<div class='book_manager_caption' ><img src='./components/com_booklibrary/images/cfg.png' alt ='Config' /> " . _BOOKLIBRARY_ADMIN_CONFIG_FRONTEND . "</div>";
        $app = JFactory::getApplication();
        $app->JComponentTitle = $html;
        ?>

        <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
        <script type="text/javascript" language="Javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/overlib_mini.js"></script>
        <script>
            window.onload=function()
            {
                if (document.getElementById('money_select').options[document.getElementById('money_select').selectedIndex].value == 'other') { 
                    document.getElementById('patt').type="text";
                    document.getElementById('patt').removeAttribute('readonly');
                }
            }
            function set_pricetype(sel) {
                var value = sel.options[sel.selectedIndex].value;
                if (value=="space") {
                    document.getElementById('patt').value="&nbsp;";
                    document.getElementById('patt').setAttribute('readonly', true); 
                    document.getElementById('patt').type="hidden";
                }
                else if (value!="other") {
                    document.getElementById('patt').value=value;
                    document.getElementById('patt').setAttribute('readonly', true); 
                    document.getElementById('patt').type="hidden";
                } else
                {
                    document.getElementById('patt').value="";
                    document.getElementById('patt').type="text";
                    document.getElementById('patt').removeAttribute('readonly');
                }
            }
        </script>

        <form action="index.php" method="post" name="adminForm" id="adminForm">

            <div class="my_tab_menu">
                <br id="my_tab2"/><br id="my_tab3"/>

                <a href="#my_tab1">
                    <!--<input type="checkbox" id="point_tab1"/>
                    <label for="point_tab1">BookLibrary Page Settings</label>-->
                    BookLibrary Page Settings
                </a>
               <div>
                    <table class="adminform my_table bl_admin_settings_frontend_tabs_bl_page_settings" width="100%">
                        <!--***************   begin add send mail for admin   *****************-->
                        <!--************   end add send mail for admin   ********************************-->
                        <h1>BookLibrary Page Settings</h1>
                        <tr>
                            <td width="22%"><?php echo _BOOKLIBRARY_ADMIN_CONFIG_REVIEWS_SHOW; ?>:</td>
                                <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td width="7%"><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_REVIEWS_SHOW_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_REVIEWS_SHOW_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                                    ?></td>
        <?php } else { ?>
                                <td width="7%"><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_REVIEWS_SHOW_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_REVIEWS_SHOW_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
                                <?php } ?>			
                            <td width="22%"><?php echo $lists['reviews']['show']; ?></td>
                            <td width="22%"><?php echo _BOOKLIBRARY_ADMIN_CONFIG_REVIEWS_REGISTRATIONLEVEL; ?>:</td>         
                                <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td width="7%"><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_REVIEWS_REGISTRATIONLEVEL_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_REVIEWS_REGISTRATIONLEVEL_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                                    ?></td>
        <?php } else { ?>
                                <td width="7%"><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_REVIEWS_REGISTRATIONLEVEL_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_REVIEWS_REGISTRATIONLEVEL_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>			
                            <td width="22%"><?php echo $lists['reviews']['registrationlevel']; ?></td>         
                        </tr>
                        
                        <tr>
                            <td colspan="6"><hr /></td>
                        </tr>
                        <tr>
                            <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_LENDSTATUS_SHOW; ?>:</td>
                                <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>	
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_LENDSTATUS_SHOW_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_LENDSTATUS_SHOW_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                                    ?></td>
                            <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_LENDSTATUS_SHOW_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_LENDSTATUS_SHOW_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
                                ?></td>
                                <?php } ?>				
                            <td><?php echo $lists['lendstatus']['show']; ?></td>
                            <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_LENDREQUEST_REGISTRATIONLEVEL; ?>:</td>
                                <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>	
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_LENDREQUEST_REGISTRATIONLEVEL_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_LENDREQUEST_REGISTRATIONLEVEL_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                                    ?></td>
        <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_LENDREQUEST_REGISTRATIONLEVEL_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_LENDREQUEST_REGISTRATIONLEVEL_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>			
                            <td><?php echo $lists['lendrequest']['registrationlevel']; ?></td>         
                        </tr>      
                        <!--***   begin add for Manager Suggestion: button 'Suggest a book'   ***-->
                     
               
                        <tr>
                            <td colspan="6"><hr /></td>
                        </tr>
                        <tr>
                            <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_BUYNOW_SHOW; ?>:</td>
                            <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_BUYNOW_SHOW_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_BUYNOW_SHOW_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                    ?></td>
                            <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_BUYNOW_SHOW_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_BUYNOW_SHOW_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
                    ?></td>
        <?php } ?>			
                            <td><?php echo $lists['buy_now']['show']; ?></td>
                            <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_BUYNOW_REGISTRATIONLEVEL; ?></td>
                                <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_BUYNOW_REGISTRATIONLEVEL_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_BUYNOW_REGISTRATIONLEVEL_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                                    ?></td>
                                <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_BUYNOW_REGISTRATIONLEVEL_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_BUYNOW_REGISTRATIONLEVEL_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
                                    ?></td>
        <?php } ?>			
                            <td><?php echo $lists['buy_now']['allow']['categories']; ?></td>
                        </tr>
                         <tr>
                            <td colspan="6"><hr /></td>
                        </tr>                        
                        <tr>
                            <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_SHOW; ?>:</td>
                            <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_SHOW_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_SHOW_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                                ?></td>
                                <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_SHOW_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_SHOW_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
                                    ?></td>
                                <?php } ?>			
                            <td><?php echo $lists['ebooks']['show']; ?></td>
                            <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_REGISTRATIONLEVEL; ?>:</td>
                            <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_REGISTRATIONLEVEL_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_REGISTRATIONLEVEL_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                                ?></td>
                                <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_REGISTRATIONLEVEL_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_REGISTRATIONLEVEL_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
                                    ?></td>
                                <?php } ?>		
                            <td><?php echo $lists['ebooks']['registrationlevel']; ?></td>
                        </tr>
                                      
                         <tr>
                            <td colspan="6"><hr /></td>
                        </tr>
                        <tr>
                            <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_FOTO_SIZE; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_FOTO_SIZE_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_FOTO_SIZE_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_FOTO_SIZE_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_FOTO_SIZE_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>	
                            <td><?php echo ($lists['foto']['high']) . ($lists['foto']['width']); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    
                        <tr>
                            <td colspan="6"><hr /></td>
                        </tr>
                        <tr>
                            <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_PAGE_ITEMS; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_PAGE_ITEMS_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_PAGE_ITEMS_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_PAGE_ITEMS_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_PAGE_ITEMS_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>		
                            <td><?php echo $lists['page']['items']; ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                            <!--*******	end add PageItems ************ -->
                            <!--********   begin add for show in category picture   **************-->
                        <tr>
                            <td colspan="6"><hr /></td>
                        </tr>
                        <tr>
                            <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_PICTURE_IN_CATEGORY; ?>:</td>
                                <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_PICTURE_IN_CATEGORY_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_PICTURE_IN_CATEGORY_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                        ?></td>
                                <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_PICTURE_IN_CATEGORY_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_PICTURE_IN_CATEGORY_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
                        ?></td>
        <?php } ?>		

                            <td><?php echo $lists['cat_pic']['show']; ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <!--***************   end add for show in category picture  *************-->
                        <!--********   begin add for show subcategory   **************-->
                        <tr>
                            <td colspan="6"><hr /></td>

                        </tr>
                        <tr>
                            <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_SUBCATEGORY_SHOW; ?>:</td>
                                    <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_SUBCATEGORY_SHOW_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_SUBCATEGORY_SHOW_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                                        ?></td>
        <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_SUBCATEGORY_SHOW_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_SUBCATEGORY_SHOW_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
                                <?php } ?>			
                            <td><?php echo $lists['subcategory']['show']; ?></td>
                            <!--***************   end add for show subcategory *************-->
                            <!--********   begin add for view type   **************-->
                            <td>Single Category Layout:</td>
                                    <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_CONFIG_VIEW_TYPE_TT_BODY, _BOOKLIBRARY_CONFIG_VIEW_TYPE_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                                        ?></td>
        <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_CONFIG_VIEW_TYPE_TT_BODY, _BOOKLIBRARY_CONFIG_VIEW_TYPE_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>			
                            <td><?php echo $lists['view_type']; ?></td>
                        </tr>
                        <tr>
                            <td colspan="6"><hr /></td>
                        </tr>
                        <tr>
                            <td>All Category Layout:</td>
                                    <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_CONFIG_VIEW_TYPE_TT_BODY, _BOOKLIBRARY_CONFIG_VIEW_TYPE_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                                        ?></td>
        <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_CONFIG_VIEW_TYPE_TT_BODY, _BOOKLIBRARY_CONFIG_VIEW_TYPE_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>			
                            <td><?php echo $lists['all_categories']; ?></td>
                            <td>Search Layout:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_CONFIG_VIEW_TYPE_TT_BODY, _BOOKLIBRARY_CONFIG_VIEW_TYPE_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_CONFIG_VIEW_TYPE_TT_BODY, _BOOKLIBRARY_CONFIG_VIEW_TYPE_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>		
                            <td><?php echo $lists['search_lay']; ?></td>
                        </tr> 
                        <!--***************   end add for view type *************-->
                        <!--********   begin ownerslist   **************-->
                        
                        <tr>
                            <td colspan="6"><hr /></td>
                        </tr>
                        <tr>
                            <td><h1>Price & Date Format Settings</h1></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?php echo _BOOKLIBRARY_PRICE_FORMAT; ?>:</td>
                                <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_PRICE_FORMAT, _BOOKLIBRARY_PRICE_FORMAT_INFO, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                        ?></td>
                                <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_PRICE_FORMAT, _BOOKLIBRARY_PRICE_FORMAT_INFO, '', '../../../components/com_booklibrary/images/circle-info.png');
                        ?></td>
                                <?php } ?>			
                            <td><?php echo $lists['money_ditlimer'] ?></td>
                            <td>
                                <input id="patt" type="hidden" readonly="true" value="<?php global $booklibrary_configuration;
                        echo $booklibrary_configuration['price_format'] ?>" name="patern" size="2">
                                    <?php echo _BOOKLIBRARY_PRICE_UNIT_SHOW; ?>:
                            </td>
                                <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_PRICE_UNIT_SHOW, _BOOKLIBRARY_PRICE_UNIT_SHOW_INFO, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                        ?></td>
                                <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_PRICE_UNIT_SHOW, _BOOKLIBRARY_PRICE_UNIT_SHOW_INFO, '', '../../../components/com_booklibrary/images/circle-info.png');
                        ?></td>
        <?php } ?>			
                            <td><?php echo $lists['price_unit_show'] ?></td>
                        </tr>
                        <tr>
                            <td colspan="6"></td>
                        </tr>
                        <tr>
                            <td><?php echo _BOOKLIBRARY_DATE_TIME_FORMAT; ?>:</td>
                                    <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_DATE_FORMAT, _BOOKLIBRARY_DATE, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                                        ?></td>
        <?php } else { ?>
                                <td><?php echo mosToolTip(_BOOKLIBRARY_DATE_FORMAT, _BOOKLIBRARY_DATE, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>			
                            <td><?php echo $lists['date_format'] ?> </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <!--<td><?php // echo mosToolTip(_BOOKLIBRARY_TIME_FORMAT, _BOOKLIBRARY_TIME);  ?></td>
                            <td><?php // echo $lists['datetime_format']  ?></td> -->
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="6"><hr /></td>
                        </tr>
                        <td colspan="6">
                            <h1>Format File Settings</h1>
                        </td>
                        <tr>
                            <td colspan="2"><?php echo _BOOKLIBRARY_ALLOWED_EXTS; ?>:</td>
                            <td><?php echo $lists['allowed_exts']; ?></td>
                            <td colspan="2" style="text-align:right"><?php echo _BOOKLIBRARY_ALLOWED_EXTS_IMG; ?>:</td>
                            <td><?php echo $lists['allowed_exts_img']; ?></td> 
                        </tr>
                        <tr> 
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <!--***************   end add for show subcategory *************-->
                    </table>
                </div>

                
            </div>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="config_save_frontend" />
        </form>

    <?php
    }

    static function showConfiguration_backend($lists, $option) {
        global $mosConfig_live_site, $act, $task, $mainframe, $doc; // for J 1.6
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $html = "<div class='book_manager_caption' ><img src='./components/com_booklibrary/images/cfg.png' alt ='Config' /> " . _BOOKLIBRARY_ADMIN_CONFIG_BACKEND . "</div>";
        $app = JFactory::getApplication();
        $app->JComponentTitle = $html;
        ?>
        <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
        <script type="text/javascript" language="Javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/overlib_mini.js"></script>

        <form class="bl_admin_settings_backend_form_with_accordion" action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
            <ul class="my_accordion_menu">                
                <li>
                    <input type="radio" name="odin" id="vkl7"/>
                    <label for="vkl7">Media Files</label>
                    <div>
                        <table class="adminform bl_admin_settings_backend my_table" width="100%">
                            <tr>
                                <td colspan="6">
                                    <h1>Media Files</h1>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_IMAGES_DOWNLOAD; ?>:</td>
            <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_IMAGES_DOWNLOAD_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_IMAGES_DOWNLOAD_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
                ?></td>
            <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_IMAGES_DOWNLOAD_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_IMAGES_DOWNLOAD_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
                ?></td>
            <?php } ?>				
                                <td><?php echo $lists['fetchImages']['boolean']; ?></td>
                                <td style="text-align:right;"><?php echo _BOOKLIBRARY_ADMIN_CONFIG_IMAGES_DOWNLOAD_LOCATION; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_IMAGES_DOWNLOAD_LOCATION_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_IMAGES_DOWNLOAD_LOCATION_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_IMAGES_DOWNLOAD_LOCATION_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_IMAGES_DOWNLOAD_LOCATION_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>				
                                <td><?php echo $lists['fetchImages']['location']; ?></td>
                            </tr>
                            <tr>
                                <td colspan="6"><hr /></td>
                            </tr>
                            <tr>
                                <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_DOWNLOAD; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_DOWNLOAD_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_DOWNLOAD_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_DOWNLOAD_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_DOWNLOAD_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
                <?php } ?>			
                                <td><?php echo $lists['ebooks']['allow']; ?></td>
                                <td style="text-align:right;"><?php echo _BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_DOWNLOAD_LOCATION; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_DOWNLOAD_LOCATION_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_DOWNLOAD_LOCATION_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_DOWNLOAD_LOCATION_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EBOOKS_DOWNLOAD_LOCATION_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>			
                                <td><?php echo $lists['ebooks']['location']; ?></td>
                            </tr>
                        </table>
                    </div>
                </li>
                <li>
                    <input type="radio" name="odin" id="vkl8"/>
                    <label for="vkl8">Amazon Settings</label>
                    <div>
                        <table class="adminform bl_admin_settings_backend my_table" width="100%">
                            <tr>
                                <td colspan="6">
                                    <h1>Amazon Settings</h1>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_TAG; ?>:</td>
            <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_TAG_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_TAG_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
            <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_TAG_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_TAG_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>		
                                <td><?php echo $lists['ws']['amazon']['tag']; ?></td>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_DEVTAG; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_DEVTAG_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_DEVTAG_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_DEVTAG_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_DEVTAG_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>		
                                <td><?php echo $lists['ws']['amazon']['devtag']; ?></td>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_SECRET_KEY; ?>:</td>
                <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_SECRET_KEY_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_SECRET_KEY_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_SECRET_KEY_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_WS_AMAZON_SECRET_KEY_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>		
                                <td><?php echo $lists['ws']['amazon']['secret_key']; ?></td>
                                <td colspan="3"></td>
                            </tr>
                            <td colspan="3"></td>
                            </tr>
                        </table>
                    </div>
                </li>
                
                <li>
                    <input type="radio" name="odin" id="vkl10"/>
                    <label for="vkl10">Common Settings</label>
                    <div>
                        <table class="adminform bl_admin_settings_backend my_table" width="100%">
                            <tr>
                                <td colspan="6">
                                    <h1>Common Settings</h1>
                                </td>
                            </tr>
                            <tr>
                                <td ><?php echo _BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_CHECK_ISBN; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_CHECK_ISBN_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_CHECK_ISBN_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_CHECK_ISBN_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_CHECK_ISBN_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
                        <?php } ?>		
                                <td><?php echo $lists['editbook']['check']['isbn']; ?></td>
                                <td colspan="3"></td>

                            </tr>
                             <tr>
                                <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_BOOKID_AUTO_INCREMENT; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_BOOKID_AUTO_INCREMENT_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_BOOKID_AUTO_INCREMENT_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_BOOKID_AUTO_INCREMENT_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_BOOKID_AUTO_INCREMENT_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
                        <?php } ?>			
                                <td><?php echo $lists['bookid']['auto-increment']['boolean']; ?></td>
                                <td colspan="3"></td>
                            </tr>

                            <tr>
                                <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_DESCRIPTION_MERGE_USE; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_DESCRIPTION_MERGE_USE_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_DESCRIPTION_MERGE_USE_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_DESCRIPTION_MERGE_USE_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_DESCRIPTION_MERGE_USE_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>		
                                <td><?php echo $lists['merge_description']['use']; ?></td>
                                <td style="text-align:right;"><?php echo _BOOKLIBRARY_ADMIN_CONFIG_DESCRIPTION_MERGE_REGISTRATIONLEVEL; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_DESCRIPTION_MERGE_REGISTRATIONLEVEL_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_DESCRIPTION_MERGE_REGISTRATIONLEVEL_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_DESCRIPTION_MERGE_REGISTRATIONLEVEL_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_DESCRIPTION_MERGE_REGISTRATIONLEVEL_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>		
                                <td><?php echo $lists['merge_description']['registrationlevel']; ?></td>
                            </tr>

                            <tr>
                                <td><?php echo _BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_DEFAULT_LANG; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_DEFAULT_LANG_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_DEFAULT_LANG_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_DEFAULT_LANG_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_DEFAULT_LANG_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>		
                                <td><?php echo $lists['editbook']['default']['lang']; ?></td>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <td style="text-align:right;"><?php echo _BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_DEFAULT_HOST; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_DEFAULT_HOST_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_DEFAULT_HOST_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_DEFAULT_HOST_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_EDITBOOK_DEFAULT_HOST_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>		
                                <td><?php echo $lists['editbook']['default']['host']; ?></td>
                                <td colspan="3"></td>
                            </tr>
                             <tr>
                                <td style="text-align:right;"><?php echo _BOOKLIBRARY_ADMIN_CONFIG_UPDATE; ?>:</td>
        <?php if (version_compare(JVERSION, "1.7.0", "ge")) { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_UPDATE_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_UPDATE_TT_HEAD, '', JURI::root() . '/components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } else { ?>
                                    <td><?php echo mosToolTip(_BOOKLIBRARY_ADMIN_CONFIG_UPDATE_TT_BODY, _BOOKLIBRARY_ADMIN_CONFIG_UPDATE_TT_HEAD, '', '../../../components/com_booklibrary/images/circle-info.png');
            ?></td>
        <?php } ?>                      
                                <td><?php echo $lists['update']; ?></td>
                                <td colspan="3"></td>
                            </tr>
                      </table>
                    </div>
                </li>


            </ul>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="config_save_backend" />
        </form>
    <?php
    }

    static function about() {
        global $mosConfig_live_site, $mainframe, $doc;
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $html = "<div class='book_manager_caption' ><img src='./components/com_booklibrary/images/cfg.png' alt ='Config' /> " . _BOOKLIBRARY_ADMIN_ABOUT . "</div>";
        $app = JFactory::getApplication();
        $app->JComponentTitle = $html;
        ?>

        <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
        <script type="text/javascript" language="Javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/overlib_mini.js"></script>

        <form class="bl_admin_about" action="index.php" method="post" name="adminForm" id="adminForm">
        <?php
        if (version_compare(JVERSION, "3.0.0", "ge")) {
            $options = Array();
            echo JHtml::_('tabs.start', 'aboutPane', $options);
            echo JHtml::_('tabs.panel', _BOOKLIBRARY_ADMIN_ABOUT_ABOUT, 'panel_1_id');
        } else {
            $tabs = new mosTabs(0);
            $tabs->startPane("aboutPane");
            $tabs->startTab(_BOOKLIBRARY_ADMIN_ABOUT_ABOUT, "display-page");
        }
        ?>

            <table class="adminform bl_admin_about_tab_about my_table">
                <tr>
                    <td width="80%">
                        <h3><?PHP echo _BOOKLIBRARY__HTML_ABOUT; ?></h3>
        <?PHP echo _BOOKLIBRARY__HTML_ABOUT_INTRO; ?>
                    </td>
                    <td width="20%">
                        <img src="../components/com_booklibrary/images/book.png" align="right" alt="Book" />
                    </td>	         
                </tr>
            </table>

        <?php
        //       $tabs->endTab();
        // //******************************   tab--2 about   **************************************
        //       $tabs->startTab(_BOOKLIBRARY_ADMIN_ABOUT_RELEASENOTE, "display-page");
        //       include_once("./components/com_booklibrary/doc/releasenote.php");
        //       $tabs->endTab();
        // //******************************   tab--3 about--changelog.txt   ***********************
        //       $tabs->startTab(_BOOKLIBRARY_ADMIN_ABOUT_CHANGELOG, "display-page");
        //       include_once("./components/com_booklibrary/doc/changelog.html");
        //       $tabs->endTab();
        // 
        //       $tabs->endPane();
        //***************************
        if (version_compare(JVERSION, "3.0.0", "ge")) {
            echo JHtml::_('tabs.panel', _BOOKLIBRARY_ADMIN_ABOUT_RELEASENOTE, 'panel_2_id');
        } else {
            $tabs->endTab();
            $tabs->startTab(_BOOKLIBRARY_ADMIN_ABOUT_RELEASENOTE, "display-page");
        }
        include_once("./components/com_booklibrary/doc/releasenote.php");
        if (version_compare(JVERSION, "3.0.0", "ge")) {
            echo JHtml::_('tabs.panel', _BOOKLIBRARY_ADMIN_ABOUT_CHANGELOG, 'panel_2_id');
        } else {
            $tabs->endTab();
            $tabs->startTab(_BOOKLIBRARY_ADMIN_ABOUT_CHANGELOG, "display-page");
        }
        include_once("./components/com_booklibrary/doc/changelog.html");
        if (version_compare(JVERSION, "3.0.0", "ge")) {
            echo JHtml::_('tabs.end');
        } else {
            $tabs->endTab();
            $tabs->endPane();
        }
        //***************************
        ?>
        </form>

    <?php
    }
  
    static function showImportResult($table, $option) {
        global $my, $mosConfig_live_site, $mainframe, $doc; // for J 1.6
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        $html = "<div class='book_manager_caption' ><img src='./components/com_booklibrary/images/cfg.png' alt ='Config' /> " . _BOOKLIBRARY_ADMIN_IMPEXP . "</div>";
        $app = JFactory::getApplication();
        $app->JComponentTitle = $html;
        ?>

        <form action="index.php" method="post" name="adminForm" id="adminForm">

            <table class="admin33" cellpadding='4' cellspacing='0' border='1' width='100%'>
                <tr>
                    <td>#</td>
                    <td><?php echo _BOOKLIBRARY_LABEL_BOOKID; ?></td>
                    <td><?php echo _BOOKLIBRARY_LABEL_ISBN; ?></td>
                    <td><?php echo _BOOKLIBRARY_LABEL_TITLE; ?></td>
                    <td><?php echo _BOOKLIBRARY_LABEL_AUTHORS; ?></td>
                    <td><?php echo _BOOKLIBRARY_LABEL_MANUFACTURER; ?></td>
                    <td><?php echo _BOOKLIBRARY_LABEL_STATUS; ?></td>
                </tr>

        <?php foreach ($table as $entry) { ?>
                    <tr>
                        <td><?php echo $entry[0]; ?></td>
                        <td><?php echo $entry[1]; ?></td>
                        <td><?php echo $entry[2]; ?></td>
                        <td><?php echo $entry[3]; ?></td>
                        <td><?php echo $entry[4]; ?></td>
                        <td><?php echo $entry[5]; ?></td>
                        <td><?php echo $entry[6]; ?></td>
                    </tr>
        <?php } ?>
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="cancel" />
        </form> 

    <?php
    }

    static function showExportResult($InformationArray, $option) {
        $html = "<div class='book_manager_caption' ><img src='./components/com_booklibrary/images/cfg.png' alt ='Config' /> " . _BOOKLIBRARY_SHOW_IMPEXP_RESULT . "</div>";
        $app = JFactory::getApplication();
        $app->JComponentTitle = $html;
        ?>

        <form action="index.php" method="post" name="adminForm" id="adminForm">
            <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
            <script type="text/javascript" language="Javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/overlib_mini.js"></script>
        <?php echo _BOOKLIBRARY_SHOW_IMPEXP_RESULT_DOWNLOAD; ?>  <br />
            <a href="<?php echo $InformationArray['urlBase'] . $InformationArray['out_file']; ?>" target="blank"><?php echo $InformationArray['urlBase'] . $InformationArray['out_file']; ?></a>
            <br />
        <?php echo _BOOKLIBRARY_SHOW_IMPEXP_RESULT_REMEMBER; ?>  <br />
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="cancel" />
        </form> 
    <?php
    }

    static function showLanguageManager($const_languages, $pageNav, $search) {
        global $my, $mosConfig_live_site, $mainframe, $templateDir;
        // for 1.6
        global $doc;
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        $html = "<div class='book_manager_caption' ><img src='./components/com_booklibrary/images/cfg.png' alt ='Config' /> " . _BOOKLIBRARY_ADMIN_LANGUAGE_MANAGER . "</div>";
        $app = JFactory::getApplication();
        $app->JComponentTitle = $html;
        ?>

        <form action="index.php" method="post" name="adminForm" id="adminForm">    

            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist language_manager bl_admin_language_manager_table
                   my_table my_table-hover my_table-bordered">

        <?php if (version_compare(JVERSION, "3.0.0", "ge")) { ?>
                    <tr>	
                        <td width="5%"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <div class="btn-group pull-right hidden-phone">
                                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
            <?php echo $pageNav->getLimitBox(); ?>
                            </div>
                        </td>
                    </tr>	
        <?php } ?>

                <tr>
                    <td style="text-align:right;" width="5%"><?php echo _BOOKLIBRARY_SHOW_SEARCH; ?></td>
                    <td width="35%">
                        <input type="text" name="search_const" value="<?php echo $search['const']; ?>" class="inputbox input-medium" onChange="document.adminForm.submit();" />
                    </td>
                    <td width="35%">
                        <input type="text" name="search_const_value" value="<?php echo $search['const_value']; ?>" class="inputbox input-medium" onChange="document.adminForm.submit();" />
                    </td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td></td>
                    <td width="35%">
        <?php echo $search['languages']; ?>
                    </td>
                    <td width="35%">
        <?php echo $search['sys_type']; ?>
                    </td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <th width="5%" align="center"></th>
                    <th align = "center" class="title" width="35%" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_ADMIN_LANGUAGE_MANAGER_CONST; ?>
                    </th>
                    <th align = "center" class="title" width="35%" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_ADMIN_LANGUAGE_MANAGER_VALUE_CONST; ?>
                    </th>
                    <th align = "center" class="title" width="35%" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_LABEL_LANGUAGE; ?>
                    </th>
                    <th align = "center" class="title" width="35%" nowrap="nowrap">
        <?php echo _BOOKLIBRARY_ADMIN_LANGUAGE_MANAGER_SYS_TYPE; ?>
                    </th>
                </tr>

        <?php $i = 0;
        foreach ($const_languages as $const_language) {
            ?>
                    <tr>
                        <td align="center">
            <?php echo mosHTML::idBox($i, $const_language->id, false, 'bid'); ?>
                        </td>
                        <td>
                            <a href="#edit" onClick="return listItemTask('cb<?php echo $i; ?>','edit')">
            <?php echo $const_language->const; ?>
                            </a>
                        </td>	
                        <td>
                            <a href="#edit" onClick="return listItemTask('cb<?php echo $i; ?>','edit')">
            <?php echo $const_language->value_const; ?>
                            </a>
                        </td>
                        <td align="center">
            <?php echo $const_language->title; ?>
                        </td>
                        <td align="center"><?php echo $const_language->sys_type; ?></td>
                    </tr>
            <?php $i++;
        } ?>
                <tr class="for_paginator">
                    <td colspan = "13"><?php echo $pageNav->getListFooter(); ?></td>
                </tr>
            </table>
            <input type="hidden" name="option" value="com_booklibrary" />
            <input type="hidden" name="section" value="language_manager" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" value="0" name="boxchecked">
        </form>

    <?php
    }

    static function editLanguageManager($row, $lists) {
        global $mosConfig_live_site;
        global $doc;
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        $html = "<div class='booklibrary_caption' ><img src='./components/com_booklbirary/images/building_icon.jpg' alt ='Config' /> " . _BOOKLIBRARY_ADMIN_LANGUAGE_MANAGER . "</div>";
        $app = JFactory::getApplication();
        $app->JComponentTitle = $html;
        ?>    

        <form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

            <table width="100%" class="my_table bl_admin_language_manager_edit_constant">
                <tr>
                    <th colspan="2">
                <h1><?php echo $row->id ? _BOOKLIBRARY_HEADER_EDIT : _BOOKLIBRARY_HEADER_ADD; ?> 
        <?php echo _BOOKLIBRARY_ADMIN_LANGUAGE_MANAGER_CONST; ?> 
                </h1>	
                </th>
                </tr>
                <tr>
                    <td width="10%">
                        <label><?php echo _BOOKLIBRARY_ADMIN_LANGUAGE_MANAGER_CONST; ?>:</label>
                    </td>
                    <td colspan="2">
        <?php echo $lists['const']; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php echo _BOOKLIBRARY_ADMIN_LANGUAGE_MANAGER_VALUE_CONST; ?>:</label>
                    </td>
                    <td colspan="2">
                        <textarea class="text_area" type="text" name="value_const"><?php echo $row->value_const; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php echo _BOOKLIBRARY_ADMIN_LANGUAGE_MANAGER_SYS_TYPE; ?>:</label>
                    </td>
                    <td colspan="2">
        <?php echo $lists['sys_type']; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php echo _BOOKLIBRARY_LABEL_LANGUAGE; ?>:</label>
                    </td>
                    <td colspan="2">
        <?php echo $lists['languages']; ?>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="const" value="<?php echo $lists['const']; ?>"/>
            <input type="hidden" name="option" value="com_booklibrary" />
            <input type="hidden" name="section" value="language_manager" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="sectionid" value="com_booklibrary" />
        </form>    

    <?php
    }

}
