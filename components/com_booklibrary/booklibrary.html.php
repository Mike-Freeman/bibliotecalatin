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
//require_once($mosConfig_absolute_path . "/libraries/joomla/plugin/helper.php");
jimport( 'joomla.plugin.helper' );
require_once ($mosConfig_absolute_path . "/administrator/includes/toolbar.php");
if (version_compare(JVERSION, "3.0.0", "lt"))
    require_once ($mosConfig_absolute_path . "/libraries/joomla/html/toolbar.php");

require_once ($mosConfig_absolute_path . "/components/com_booklibrary/booklibrary.php");
require_once($mosConfig_absolute_path . "/administrator/components/com_booklibrary/menubar_ext.php");

$mosConfig_live_site = JURI::root(true); //for 1.6
$GLOBALS['mosConfig_live_site'] = $mosConfig_live_site;
$GLOBALS['mosConfig_absolute_path'] = $mosConfig_absolute_path; //for 1.6
// for J 1.6
$mainframe = JFactory::getApplication();
$GLOBALS['mainframe'] = $mainframe;

$templateDir = JPATH_THEMES . DS . JFactory::getApplication()->getTemplate() . DS;
$GLOBALS['templateDir'] = $templateDir;

$doc = JFactory::getDocument();
$GLOBALS['doc'] = $doc;
// --

$g_item_count = 0;

class HTML_booklibrary {

    static function showAddBook($tpl_list, $option, $Itemid, $ratinglist, $book) {

        global $database, $doc, $booklibrary_configuration;
        global $my, $mosConfig_live_site, $mainframe, $Itemid, $books;
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js'); // for 1.6
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
        ?>
        <script language="javascript" type="text/javascript">
                                    
            function trim(string){	return string.replace(/(^\s+)|(\s+$)/g, "");	}
            function submitbutton(pressbutton) { 
                var form = document.adminForm;
                if (pressbutton == 'save') {		
                                				
                    if (trim(form.bookid.value) == '') {
                        alert( "<?php echo _BOOKLIBRARY_ADMIN_INFOTEXT_JS_EDIT_BOOKID_CHECK; ?>" );
                        return;
                    } else if ( trim(form.isbn.value) == '') {
                        alert( "<?php echo _BOOKLIBRARY_ADMIN_INFOTEXT_JS_EDIT_ISBN_CHECK; ?>" );
                        return;
                    } else if (form.catid.value == '0') {
                        alert( "<?php echo _BOOKLIBRARY_ADMIN_INFOTEXT_JS_EDIT_CATEGORY; ?>");
                        return;
                    } else {
                        form.submit();
                    }
                } else {
                    submitform( pressbutton );
                }
            }
        </script>
        <?php
        if ($option == 'com_comprofiler') {
            $form_action = "index.php?option=" . $option . "&task=save_book_fe&is_show_data=1&tab=getmybooksTab&Itemid=" . $Itemid;
        }
        else
            $form_action = "index.php?option=com_booklibrary&task=save_book_fe&Itemid=" . $Itemid;
        ?>
        <form class="m" id="adminForm" action="<?php echo sefRelToAbs($form_action); ?>" method="post" name="adminForm" enctype="multipart/form-data">
            <table  class="basictable bl_bl_single_category_add_book" cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">  
                <tr>
                    <td colspan="2" style="color:red;">
                        <?php if (!isset($my->email)) { ?><?php
                echo _BOOKLIBRARY_LOGIN_ERROR;
            }
            ?>
                    </td>
                </tr>
        <?php if (isset($book->id)) { ?><input type="hidden" name="id" value="<?php echo $book->id; ?>"/><?php } ?>
                <tr>
                    <td width="15%" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_BOOKID; ?>:</strong></td>
                    <td width="85%" align="left">
                        <input class="inputbox" type="text" name="bookid" size="20" maxlength="20" value="<?php
        if ($book->bookid == '') {
            echo $tpl_list['auto_bookID'];
        } else {
            echo $book->bookid;
        }
        ?>" />
        <?php ?>
                    </td>
                </tr>
                <tr>
                    <td width="20%" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_ISBN; ?>:</strong></td>
                    <td align="left"><input class="inputbox" type="text" name="isbn" size="20" maxlength="20" value="<?php echo $book->isbn; ?>"/></td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_LANGUAGE; ?>:</strong></td>
                    <td align="left"><?php echo $tpl_list['langlist']; ?></td>
                </tr>     
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_LANGUAGEDESCRIPTION; ?>:</strong></td>
                    <td align="left"><?php echo $langlistshow; ?></td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_CATEGORY; ?>:</strong></td>
                    <td align="left"><?php echo $tpl_list['clist']; ?></td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_FETCH_INFO; ?>:</strong></td>
                    <td align="left" nowrap><?php echo $tpl_list['wlist']; ?></td>
                </tr>
                <tr>	
                    <td align="left" nowrap>
                        <img src="components/com_booklibrary/images/amazon/com-logo.gif" alt="amazon.com" border="0"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="background:#fff !important">
                        <strong><?php echo _BOOKLIBRARY_LABEL_COMMENT; ?>:</strong>
                    </td>
                    <td align="left" style="background:#fff !important">
                    <!--<textarea align= "top" name="comment" id="comment" cols="60" rows="10" style="width:400;height:100;" value="<?php //if ( isset($_REQUEST["comment"]) ) {echo $_REQUEST["comment"];} ?>"/></textarea>-->
        <?php editorArea('editor1', $book->comment, 'comment', 500, 250, '70', '10', false); ?>
                    </td>
                </tr>
        <?php if ($booklibrary_configuration['ebooks']['allow']) { ?>
                    <tr>
                        <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_EBOOK_UPLOAD; ?>:</strong></td>
                        <td align="left">
                            <input class="inputbox" type="file" name="ebook_file" value="<?php echo $book->ebook_file; ?>" size="50" maxlength="250" onClick="document.adminForm.ebook_Url.value ='';"/>    <!-- //+ -->
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_EBOOK_UPLOAD_URL; ?>:</strong></td>
                        <td align="left">
                            <input class="inputbox" type="text" name="ebook_Url" value="" size="50" maxlength="250"/>
                        </td>
                    </tr>
        <?php } ?>
                <tr>
                    <td colspan="2"><hr size="2" width="100%"/></td>
                </tr>
                <tr>
                    <td valign="top" align="right">&nbsp;</td>
                    <td align="left">
                        <span class="with_max_width">
        <?php echo _BOOKLIBRARY_ADMIN_TEXT_WSINFO_TEXT1; ?>
                            <strong><?php echo _BOOKLIBRARY_LABEL_FETCH_INFO; ?>-><?php echo _BOOKLIBRARY_WS_NO; ?></strong>
                        </span>	
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_TITLE; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" id="titleid" type="text" name="title" size="50" value="<?php echo $book->title; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_AUTHORS; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" type="text" name="authors" size="50" value="<?php echo $book->authors; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_MANUFACTURER; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" type="text" name="manufacturer" size="50" value="<?php echo $book->manufacturer; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_PUB_DATE; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" type="text" name="release_Date" size="30" value="<?php echo date(str_replace("%", "", $booklibrary_configuration['date_format']), strtotime($book->release_Date)); ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_PRICE; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" type="text" name="price" size="15" value="<?php echo $book->price; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_PRICEUNIT; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" type="text" name="priceunit" size="6" value="<?php echo $book->priceunit; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_EDITION; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" type="text" name="edition" size="45" value="<?php echo $book->edition; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_NUMPAGES; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" type="text" name="numberOfPages" size="6" value="<?php echo $book->numberOfPages; ?>" />
                    </td>
                </tr>
        <?php //if($params->get( 'show_rating' ) == '1'){     ?>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_RATING; ?>:</strong></td>
                    <td align="left"><?php echo $ratinglist; ?></td>       
                </tr>
        <?php //}    ?>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_FEATURE; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" type="text" name="featured_clicks" size="30" value="<?php echo $book->featured_clicks; ?>" />				
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_SHOWS; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" type="text" name="featured_shows" size="30" value="<?php echo $book->featured_shows; ?>" />				
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_PICTURE_URL; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" type="text" name="imageURL" size="50" value="<?php echo $book->imageURL; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_PICTURE_URL_UPLOAD; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" type="file" name="picture_file" value="<?php echo $book->picture_file; ?>" size="50" maxlength="250" />
                        <br />
                        <span class="with_max_width"><?php echo _BOOKLIBRARY_LABEL_PICTURE_URL_DESC; ?></span>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_URL; ?>:</strong></td>
                    <td align="left">
                        <input class="inputbox" type="text" name="URL" size="50" value="<?php echo $book->URL; ?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right"><strong><?php echo _BOOKLIBRARY_LABEL_BOOKOWNER; ?>:</strong></td>
                    <td align="left">
                        <?php
                        if ($my->username == '') {
                            echo 'anonymous';
                        } else {
                            echo $my->username;
                        }
                        ?>
                        <input type="hidden" name="owneremail" size="50" value="<?php
                        if ($my->email): echo $my->email;
                        else: echo 'anonymous';
                        endif;
                        ?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>

                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input class="my_btn my_btn-success" type="button" onclick="submitbutton('save');" value="[Save]">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
            <input type="hidden" name="task" value="save_book_fe" />
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
        </form>
        <?php
    }

    function displayLicense($id) {
        global $mosConfig_live_site;

        $session = JFactory::getSession();
        $pas = $session->get("ssmid", "default");
        $sid_1 = $session->getId();
        $book = $session->get("obj_book", "default");

        if (!($session->get("ssmid", "default")) ||
                $pas == "" ||
                $pas != $sid_1 ||
                $_COOKIE['ssd'] != $sid_1 ||
                !array_key_exists("HTTP_REFERER", $_SERVER) ||
                $_SERVER["HTTP_REFERER"] == "" ||
                strpos($_SERVER["HTTP_REFERER"], $_SERVER['SERVER_NAME']) === false) {
            echo '<H3 align="center">Link failure</H3>';
            exit;
        }
        echo '<style type="text/css"><!--#frm {width: 95%;height: 200px;border-width: thin;}--></style>';
        echo '<form class="n" id="adminForm" name="dlform" method="POST" action="' . sefRelToAbs($mosConfig_live_site . '/index.php?option=com_booklibrary&task=mdownload&id=' . $id) . ' ">';
        echo '<h2 align = "center" style="text-align: center;">' . _BOOKLIBRARY_LICENSE_AGREEMENT_TITLE . '</h2>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo '<iframe src="' . $mosConfig_live_site . '/components/com_booklibrary/mylicense.php" width="95%" height="230" name="frm" id="frm" SCROLLING="auto" noresize>';
        echo '</iframe>';
        echo '<input type="hidden" name="id" value="' . $id . '" />';
        echo '<input type="hidden" name="task" value="downitsf" />';
        echo '<input type="hidden" name="ssidPost" value="' . $session->getId() . '" >';
        echo '<div align="right" style="text-align:right;>';
        echo '<br /> <font size="3"><strong>' . _BOOKLIBRARY_LICENSE_AGREEMENT_ACCEPT . '</strong></font> <input type="radio" name="choice" checked="checked" onclick="document.getElementById(\'DBB\').disabled=true;" />';
        echo _BOOKLIBRARY_NO;
        echo '<input type="radio" name="choice" onclick="document.getElementById(\'DBB\').removeAttribute(
\'disabled\');" >';
        echo _BOOKLIBRARY_YES . '&nbsp;&nbsp;&nbsp;';
        echo '<input type="submit" id="DBB" name="downbutton" disabled="disabled" value="download" />&nbsp;&nbsp;&nbsp;&nbsp;';
        echo '<br /><br /><br /><br />';
        echo '</div>';
        echo '</form>';
    }

    function sort_head($title, $fieldname, $sort_arr, $fb = true) {
        global $mosConfig_live_site, $Itemid;
        global $templateDir; //for 1.6
        global $mosConfig_absolute_path; //for 1.6

        $att_str = '';
        $ext_str = '';
        if ($fb) {
            if (array_key_exists('task', $_REQUEST)) {
                $ext_str .= '&task=' . $_REQUEST['task'];
                if ($_REQUEST['task'] == 'search')
                    $att_str = '&searchtext=&searchtype=simplesearch';
            } else if (array_key_exists('view', $_REQUEST)) {
                $ext_str .= '&task=' . $_REQUEST['view'];
                if ($_REQUEST['view'] == 'search')
                    $att_str = '&searchtext=&searchtype=simplesearch';
            }

            if (array_key_exists('catid', $_REQUEST)) {
                $ext_str .= '&catid=' . $_REQUEST['catid'];
            }

            if (array_key_exists('limitstart', $_REQUEST)) {
                $ext_str .= '&limitstart=' . $_REQUEST['limitstart'];
            }

            if (array_key_exists('sp', $_REQUEST)) {
                $ext_str .= '&sp=' . $_REQUEST['sp'];
            }
        }

        $img_str = "";
        if ($sort_arr['field'] == $fieldname) {
            if ($sort_arr['direction'] == '') {

                $templateDir = substr($templateDir, strpos($templateDir, "templat")); //for 1.6
                $path = "$mosConfig_absolute_path/$templateDir" . "images/plus.png"; //for 1.6
                $p = JURI::base() . "components/com_booklibrary/" . "images/uparrow-1.png"; //for 1.6
                $img_str = "<img src='$p' width=\"12\" height=\"12\" border=\"0\" alt='Sorted up' style='margin:-2px 4px 0 0;' />";
                $str = "<a href='" . $mosConfig_live_site . "/index.php?option=com_booklibrary$ext_str&sortup=$fieldname&Itemid=$Itemid" . $att_str . "'>" .
                        $img_str .
                        $title .
                        "</a>";
            } else {
                $templateDir = substr($templateDir, strpos($templateDir, "templat")); //for 1.6
                $path = "$mosConfig_absolute_path/$templateDir" . "images/minus.png"; //for 1.6
                $p = JURI::base() . "components/com_booklibrary/" . "images/downarrow-1.png"; //for 1.6
                $img_str = "<img src='$p' width=\"12\" height=\"12\" border=\"0\" alt='Sorted down' style='margin:-2px 4px 0 0;' />";
                $str = "<a href='" . $mosConfig_live_site . "/index.php?option=com_booklibrary$ext_str&sortdown=$fieldname&Itemid=$Itemid" . $att_str . "'>" .
                        $img_str .
                        $title .
                        "</a>";
            }
        } else {
            $str = $str = "<a href='" . $mosConfig_live_site . "/index.php?option=com_booklibrary$ext_str&sortdown=$fieldname&Itemid=$Itemid" . $att_str . "'>" .
                    $title .
                    "</a>";
        }
        return $str;
    }

    static function showLendRequest(& $books, & $currentcat, & $params, & $tabclass, & $catid, & $sub_categories, $is_exist_sub_categories, $sort_arr) {
        HTML_booklibrary::displayBooks($books, $currentcat, $params, $tabclass, $catid, $sub_categories, $is_exist_sub_categories, $sort_arr);
        // add the formular for send to :-)
    }

    static function displayBooks($rows, $currentcat, &$params, $tabclass, $catid, $categories, $is_exist_sub_categories, $sort_arr, $list_str = array(), &$pageNav = null, $layout = "list", $cat_name = "") {
        global $mosConfig_absolute_path;

        if ($layout == null)
            $layout = "list";
        $type = 'alone_category';
        require getLayoutPathBook::getLayoutPathCom('com_booklibrary', $type, $layout);
        //require($mosConfig_absolute_path . "/components/com_booklibrary/views/showCategory/tmpl/" . $layout . ".php");
    }

    /**
     * Displays simple search
     * Lend Status 
     */
    //----------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------

    static function displaySimpleSearch($align = 'right') {
        global $Itemid, $mosConfig_live_site;
        ?>
        <!-- <?php
        $path = "index.php?option=" . $option . "&amp;task=search&amp;Itemid=" . $Itemid;
        ?> --> 


        <form class="my_form-search bl_bl_all_categories_top_search" id="adminForm" name="simlesearchform" action="<?php echo sefRelToAbs($path); ?>"  method="get">
            <div class="my_input-append" style="text-align: <?php echo $align ?>;float:right;width:240px;">
                <input class="my_search-query" type="text" name="searchtext" value="" />
                <button type="submit" class="my_btn">
                    <a href="javascript:document.simlesearchform.submit();" >
                        <img src="<?php echo $mosConfig_live_site; ?>/components/com_booklibrary/images/search.gif" alt="Search" title="Search" />
                    </a>
                </button>	
                <input type="hidden" name="option" value="com_booklibrary">
                <input type="hidden" name="task" value="search">
                <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>">
                <input type="hidden" name="searchtype" value="simplesearch">
            </div>
        </form>
        <?php
    }

    //--------------------------------------------------
    static function displayMediaBook(&$book, &$params) {
        global $booklibrary_configuration, $mosConfig_live_site, $Itemid;
        $printItem = JRequest::getVar('printItem');
        JPluginHelper::importPlugin('content');
        $dispatcher = JDispatcher::getInstance();
        ?>

        <tr>    
            <td align="right">
        <?php if ($params->get('show_input_print_pdf') && $printItem != 'pdf') { ?>

                    <a onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;" rel="nofollow"
                       href="<?php echo $mosConfig_live_site; ?>/index.php?option=com_booklibrary&amp;task=view&amp;id=<?php //echo $id;   ?>&amp;catid=<?php //echo $catid;   ?>&amp;itemid=<?php echo $Itemid; ?>&amp;printItem=pdf" title="PDF"  rel="nofollow">
                        <img src="./components/com_booklibrary/images/pdf_button.png" alt="PDF"  />
                    </a>
        <?php } ?>
            </td>
            <td align="right">
        <?php if ($params->get('show_input_print_view') && $printItem != 'pdf') { ?>
                    <a onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;" rel="nofollow"
                       href="<?php echo $mosConfig_live_site; ?>/index.php?option=com_booklibrary&amp;task=view&amp;id=<?php //echo $id;   ?>&amp;catid=<?php //echo $catid;   ?>&amp;itemid=<?php echo $Itemid; ?>&amp;printItem=print&amp;tmpl=component" title="Print"  rel="nofollow">
                        <img src="./components/com_booklibrary/images/printButton.png" alt="Print"  />
                    </a>
        <?php } ?>
            </td>
            <td align="right">
                <?php if ($params->get('show_input_mail_to') && $printItem != 'pdf') { ?>
                    <a href="<?php echo $mosConfig_live_site; ?>/index.php?option=com_mailto&amp;tmpl=component&amp;link=<?php
            $url = JFactory::getURI();
            echo base64_encode($url->toString());
            ?>"
                       title="E-mail"
                       onclick="window.open(this.href,'win2','width=400,height=350,menubar=yes,resizable=yes'); return false;">
                        <img src="./components/com_booklibrary/images/emailButton.png" alt="E-mail"  />
                    </a>	
        <?php } ?> 
            </td>
        </tr>    

        <?php if (trim($book->title)) { ?>

            <tr>
                <td nowrap="nowrap" width="20%" align="right">       
                    <strong><?php echo _BOOKLIBRARY_LABEL_TITLE; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
            <?php echo $book->title; ?>
                </td>
            </tr>
            <?php
        }
        ?>

        <tr>
            <td nowrap="nowrap" width="20%" align="right">
                <strong><?php echo _BOOKLIBRARY_LABEL_BOOKID; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
            </td>
            <td>
        <?php echo $book->bookid; ?>
            </td>
        </tr>
        <?php if (trim($book->authors)) { ?>

            <tr>
                <td nowrap="nowrap" align="right">
                    <strong><?php echo _BOOKLIBRARY_LABEL_AUTHORS; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
            <?php echo $book->authors; ?>
                </td>
            </tr>
            <?php
        }
        if (trim($book->isbn)) {
            ?>


            <tr>

                <td nowrap="nowrap" align="right">
                    <strong><?php echo _BOOKLIBRARY_LABEL_ISBN; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
            <?php echo $book->isbn; ?>
                </td>
            </tr>   
            <?php
        }
        if (trim($book->manufacturer)) {
            ?>



            <tr>
                <td nowrap="nowrap" align="right">
                    <strong><?php echo _BOOKLIBRARY_LABEL_MANUFACTURER; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
            <?php echo $book->manufacturer; ?>
                </td>
            </tr>

            <?php
        }
        if (trim($book->edition)) {
            ?>



            <tr>
                <td nowrap="nowrap" align="right">
                    <strong><?php echo _BOOKLIBRARY_LABEL_EDITION; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
            <?php echo $book->edition; ?>
                </td>
            </tr>

            <?php
        }

        if ($book->numberOfPages != '') {
            ?>
            <tr>
                <td nowrap="nowrap" align="right">
                    <strong><?php echo _BOOKLIBRARY_LABEL_NUMPAGES; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
            <?php echo $book->numberOfPages; ?>
                </td>
            </tr>
            <?php
        }


        if (trim($book->language)) {
            ?>
            <tr>
                <td nowrap="nowrap" align="right">
                    <strong><?php echo _BOOKLIBRARY_LABEL_LANGUAGE; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
                    <?php
                    echo $book->language; //old ENGLISH strtoupper($book->language);
                    ?>
                </td>
            </tr>



            <?php
        }

        if (trim($book->featured_clicks)) {
            ?>		<tr>
                <td nowrap="nowrap" align="right" class="title_td">
                    <strong><?php echo _BOOKLIBRARY_LABEL_FEATURE; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
            <?php echo $book->featured_clicks; ?>
                </td>
            </tr>
            <?php
        }
        if (trim($book->featured_shows)) {
            ?>		<tr>
                <td nowrap="nowrap" align="right" class="title_td">
                    <strong><?php echo _BOOKLIBRARY_LABEL_SHOWS; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
            <?php echo $book->featured_shows; ?>
                </td>
            </tr>
            <?php
        }
        if ($params->get('show_price') == '1') {
            ?>
            <tr>
                <td nowrap="nowrap" align="right">
                    <strong><?php echo _BOOKLIBRARY_LABEL_PRICE; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
            <?php echo $book->price; ?>&nbsp;&nbsp; <?php echo $book->priceunit; ?>

                </td>
            </tr>
            <?php
        }
        if (trim($book->language)) {
            ?>
            <tr>
                <td nowrap="nowrap" align="right">
                    <strong><?php echo _BOOKLIBRARY_LABEL_RATING; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
                    <img src="<?php echo JURI::root(); ?>/components/com_booklibrary/images/rating-<?php echo $book->rating; ?>.gif" alt="<?php echo ($book->rating) / 2; ?>" border="0" />&nbsp;
                </td>
            </tr>

            <?php
        }
        if (trim($booklibrary_configuration['foto']['high']) || trim($book->imageURL)) {
            ?>



            <tr>
                <td nowrap="nowrap" align="right" valign="top">
                    <strong><?php echo _BOOKLIBRARY_LABEL_PICTURE; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
                    <?php
                    //for local images
                    $imageURL = $book->imageURL;
                    if ($imageURL != '' && substr($imageURL, 0, 4) != "http") {
                        $imageURL = $mosConfig_live_site . '/' . $media_type_class->imageURL;
                        ;
                    }

                    if ($imageURL != '') {
                        echo '<img src="' . $imageURL . '" alt="cover" border="0" height="' . $booklibrary_configuration['foto']['high'] . '" width="' . $booklibrary_configuration['foto']['width'] . '"/>';
                    } else {
                        //echo '<img src="'.$mosConfig_live_site.'/components/com_booklibrary/images/' . _BOOKLIBRARY_NO_PICTURE.'" alt="no-img_eng.gif" border="0" />';
                    }
                }
                ?>

                <!--************   begin add button 'buy now'   ************************-->

                <?php
                //show button 'buy now'
                if ($params->get('show_input_buy_now') && $book->informationFrom == 1) {
                    if ($book->URL != '') {
                        ?>

                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        <a href="<?php echo sefRelToAbs($mosConfig_live_site . "/index.php?option=com_booklibrary&amp;task=new_url&amp;id=" . $book->id); ?>" target="blank">
                            <img src="<?php echo JURI::root(); ?>/components/com_booklibrary/images/amazon/buy_now.png" alt="Button Buy now" border="0" height="27" width="82" />
                        </a>

                        <?php
                    }
                }
                ?>
                <!--************************   end add button 'buy now'   ******************-->

<!--************   begin add button 'buy now vm'   ************************-->

<!--************************   end add button 'buy now vm'   ******************-->


            </td>
        </tr>
        <!--************   begin add to cart'   ************************-->
        <?php if ($params->get('show_add_to_cart') && substr($book->URL, 0, 17) == 'http://www.amazon') { ?>
            <tr>
                <td align="right">
                    <strong><?php echo _BOOKLIBRARY_LABEL_QUANTITY; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td align="left">
                    <div id ="add_to_cart">
                        <form class="d" id="adminForm" action="<?php echo $mosConfig_live_site; ?>/index.php" method="get">
                            <input type="hidden" name="option" value="com_booklibrary"/>
                            <input type="text" name="quantity" value="1" size="2"/>
                            <input type="hidden" name="task" value="add_to_cart"/>
                            <input type="hidden" name="id" value="<?php echo $book->id; ?>"/>
                            <input type="submit" name="submit" value="<?php echo _BOOKLIBRARY_LABEL_ADDTOCART; ?>"/>
                            <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
                        </form>
                    </div>
                </td>
            </tr>
        <?php } ?>
        <!--************   end add to cart'   ************************-->


        <?php
        if ($params->get('show_emedisrequest') && $book->ebookURL != null) {

            $session = JFactory::getSession();
            $sid = $session->getId();
            $session->set("ssmid", $sid);
            setcookie('ssd', $sid, mktime() . time() + 60 * 60 * 24, "/");
            ?>
            <tr>
                <td align="right" >
                    <strong><?php echo _BOOKLIBRARY_LABEL_EMEDIA; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td align ="left">  
                    <a href="<?php echo sefRelToAbs('index.php?option=com_booklibrary&task=mdownload&id=' . $book->id); ?>" target="blank">
            <?php echo _BOOKLIBRARY_LABEL_EMEDIA_DOWNLOAD; ?>
                    </a>
                </td>
            </tr> 
            <?php
        }
        ?>
        <?php
        //lend out?? 
        if ($params->get('show_lendstatus') && $params->get('show_lendrequest')) {
            $lend = $book->getAllLends();
            $num = count($lend);
            if ($num > 0) {
                ?>

                <tr>
                    <td align="right" valign="top">
                        <strong><?php echo _BOOKLIBRARY_LABEL_LEND_FROM_UNTIL; ?>:</strong>
                    </td>
                    <td></td>
                </tr>

                <?php
                for ($e = 0, $m = count($lend); $e < $m; $e++) {
                    print(" <tr><td align=\"left\">&nbsp;</td><td> ");
                    $date = substr($lend[$e]->lend_from, 0, 10) . "      " . substr($lend[$e]->lend_until, 0, 10);
                    print_r($date);
                    print(" </td></tr>");
                }
            }
        }



        if (trim($book->comment)) {
            ?>       	   
            <tr>
                <td align="right" valign="top">
                    <strong><?php echo _BOOKLIBRARY_LABEL_COMMENT; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
                    <?php
                    $err_state = ini_get('display_errors');
                    ini_set('display_errors', 'Off');
                    $idesc = $book->comment; //load the desc into a variable
                    $plug_row->text = $idesc; // load the var into plugin_row object
                    $results = $dispatcher->trigger('onPrepareContent', array(&$plug_row, &$plug_params), true); //run mambot onPrepareContent on plug_row object
                    $idesc = $plug_row->text; //get new content from plug_row object to value
                    echo $idesc; //echo new content out

                    ini_set('display_errors', $err_state);
                    ?>
                </td>
            </tr>
        <?php } ?>
        <?php if ($book->owneremail != '' && $booklibrary_configuration['owner']['show']) { ?>
            <tr>
                <td align="right">
                    <strong><?php echo _BOOKLIBRARY_LABEL_BOOKOWNER; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </td>
                <td>
            <?php echo $book->ownername; ?>
                </td>
            </tr>
        <?php } ?>


        <?php
    }

//-----------------------------------------------------------------------------
    static function displayBook(& $book, & $tabclass, & $params, & $currentcat, & $cat, & $rating, $book_lang, $id, $catid, $layout/* = "default" */) {
        global $mosConfig_absolute_path;

        if ($layout == '')
            $layout = "default";
        $type = 'view_book';
        require getLayoutPathBook::getLayoutPathCom('com_booklibrary', $type, $layout);
        //require($mosConfig_absolute_path . "/components/com_booklibrary/views/view_book/tmpl/" . $layout . ".php");
    }

    /**
     * Display links to categories
     */
    static function showCategories(&$params, &$categories, &$catid, &$tabclass, &$currentcat, $layout = "default") {
        global $mosConfig_absolute_path;
        $type = 'all_categories';
        require getLayoutPathBook::getLayoutPathCom('com_booklibrary', $type, $layout);
        //require($mosConfig_absolute_path . "/components/com_booklibrary/views/categories/tmpl/" . $layout . ".php");
    }

    static function showRentHistory($option, $rows, $pageNav, $params) {
        global $my, $Itemid, $mosConfig_live_site, $mainframe, $booklibrary_configuration;
        $session = JFactory::getSession();
        $acl = JFactory::getACL();
        $arr = $session->get("array", "default");
        $doc = JFactory::getDocument();
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/custom.css'); // for 1.6
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/custom.css');
        if ($option == 'com_booklibrary') {
            $user = Jfactory::getuser();
            $db = Jfactory::getDBO();


            $query = "SELECT * FROM #__booklibrary_lend_request AS b WHERE b.status=0";
            $db->setQuery($query);
            $current_user_rent_request_array = $db->loadObjectList();
            $check_for_show_rent_request = 0;
            if (isset($current_user_rent_request_array))
                    $check_for_show_rent_request = 1;
                  
            if ($booklibrary_configuration['cb_mybook']['show'] == '1' && checkAccessBL($booklibrary_configuration['cb_mybook']['registrationlevel'], 'NORECURSE', userGID_BL($my->id), $acl))
                echo "<span class='books_button'><a class='my_btn my_btn-primary' href='" . JRoute::_('index.php?option=' . $option . '&task=show_my_books&layout=mybooks') . "'>" . _BOOKLIBRARY_LABEL_CBBOOKS_TT . "</a></span>";
            
            if(($booklibrary_configuration['cb_edit']['show'])=='1' && checkAccessBL($booklibrary_configuration['cb_mybook']['registrationlevel'], 'NORECURSE', userGID_BL($my->id), $acl))
                echo " <span class='books_button'><a class='my_btn my_btn-primary' href='" . JRoute::_('index.php?option='.$option.'&task=showmybooks&layout=mybooks') . "'>" . _BOOKLIBRARY_LABEL_CBEDIT . "</a></span>";
            
            if (($booklibrary_configuration['cb_history']['show'])) {
                $params->def('show_history', 1);
                $i = checkAccessBL($booklibrary_configuration['cb_history']['registrationlevel'], 'NORECURSE', userGID_BL($my->id), $acl);
                if ($i) {
                    $params->def('show_history_registrationlevel', 1);
                }
                if ($params->get('show_history')) {
                    if ($params->get('show_history_registrationlevel')) {
                        if ($check_for_show_rent_request != 0) {
                            echo " <span class='books_button'>
										<a class='my_btn my_btn-primary' href='" . JRoute::_('index.php?option=com_booklibrary&task=rent_history_books&name=' .
                                    $user->name . '&user=' . $user->id . '&is_show_data=1') . "'>" .
                            _BOOKLIBRARY_MY_LEND_HISTORY . "</a></span>";
                        }
                        //echo "<div style=\" border:1px solid black; padding: 10px; text-align:center; \">you dont have rent_history_lable</div>";
                    }
                }
            }
            
            if (($booklibrary_configuration['cb_rent']['show'])) {
                $params->def('show_rent', 1);
                $i = checkAccessBL($booklibrary_configuration['cb_rent']['registrationlevel'], 'NORECURSE', userGID_BL($my->id), $acl);
                if ($i) {
                    $params->def('show_rent_registrationlevel', 1);
                }

                if ($params->get('show_rent')) {
                    if ($params->get('show_rent_registrationlevel')) {
                        if ($check_for_show_rent_request != 0) {

                            echo " <span class='books_button'><a class='my_btn my_btn-primary' href='" . JRoute::_('index.php?option=' . $option . '&task=rent_requests_cb_books&is_show_data=1') . "'>" . _BOOKLIBRARY_LEND_REQUESTS . "</a></span>";
                        }
                    }
                }
            }
            ?>
            <style type="text/css">
                .row0 {background-color:#F9F9F9;}
            </style><br/><br/><br/><?php } ?>
        <?php
        //---------------------------------------------------------------------------- view
//echo " <span class='books_button'><a class='my_btn my_btn-primary' href='" . JRoute::_('index.php?option=com_booklibrary&task=show_my_books&layout=mybooks') . "'>" . _BOOKLIBRARY_LABEL_CBBOOKS_TT . "</a></span>";
        ?>

        <?php if ($rows) { ?>
            <form class="g" action="index.php" method="get" name="adminForm" id="adminForm">
                <table cellpadding="4" cellspacing="0" border="0" width="100%" class="basictable my_table my_table-bordered
                       my_table-hover bl_all_categories_show_my_book_lend_history">
                    <tr>
                        <th align = "center" width="30">#</th>         
                        <th align = "center" class="title" width="5%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_BOOKID; ?></th>
                        <th align = "center" class="title" width="40%" nowrap="nowrap"><?php echo _BOOKLIBRARY_LABEL_TITLE; ?></th>
                        <th align = "center" class="title" width="20%" nowrap="nowrap"><?php echo _BOOKLIBRARY_ORDER_LEND_FROM; ?></th>
                        <th align = "center" class="title" width="20%" nowrap="nowrap"><?php echo _BOOKLIBRARY_ORDER_LEND_UNTIL; ?></th>
                        <th align = "center" class="title" width="20%" nowrap="nowrap"><?php echo _BOOKLIBRARY_SHOW_LEND_RETURN; ?></th>
                    </tr>


                    <?php
                    $numb = 0;
                    //echo "<br/><pre>". print_r($rows,true)."</pre>";exit;
                    for ($i = 0, $n = count($rows); $i < $n; $i++) {
                        $row = $rows[$i];
                        $book = $row->id;
                        $title = $row->title;
                        $numb++
                        ?>
                        <tr>	
                            <td align="center"><?php echo $numb; ?></td>
                            <td align="center"><?php echo $row->bookid; ?></td>
                            <td align="center"><?php echo $row->title; ?></td>
                            <td align="center"><?php echo $row->lend_from; ?></td>
                            <td align="center"><?php echo $row->lend_until; ?></td>
                            <td align="center"><?php echo $row->lend_return; ?></td>
                        </tr>
            <?php } ?>

                    <tr class="for_paginator">
                        <td colspan="6" align="center" id="pagenavig">
                            <?php
                            $paginations = $arr;
                            if ($paginations && ($pageNav->total > $pageNav->limit))
                                echo $pageNav->getPagesLinks();
                            ?>
                        </td>
                    </tr>
                </table>
            </form> <?php } //else echo "<div style=\"text-align:center;\">HISTORY IS EMPTY</div>"?>
        <?php
    }


    static function listCategories(&$params, $cat_all, $catid, $tabclass, $currentcat) {
        global $Itemid, $mosConfig_live_site;
        ?>
        <table  class="bl_bl_single_category_list_categories_table my_table my_table-bordered" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="2" height="20" width="83%" class="sectiontableheader<?php echo $params->get('pageclass_sfx'); ?>">
        <?php echo _BOOKLIBRARY_LABEL_CATEGORY; ?>
                </td>
                <td height="20" width="10%" class="sectiontableheader<?php echo $params->get('pageclass_sfx'); ?>">
                <?php echo _BOOKLIBRARY_LABEL_BOOKS; ?> 
                </td>    
            </tr>
            <tr>
                <td colspan="4">
                    <?php
                    HTML_booklibrary::showInsertSubCategory($catid, $cat_all, $params, $tabclass, $Itemid, 0);
                    ?>
                </td>
            </tr>
        </table>
        <?php
    }

    /*

     * function for show subcategory
     */

    static function showInsertSubCategory($id, $cat_all, $params, $tabclass, $Itemid, $deep) {
        global $g_item_count, $booklibrary_configuration, $mosConfig_live_site;

        $deep++;
        for ($i = 0; $i < count($cat_all); $i++) {
            if (($id == $cat_all[$i]->parent_id) && ($cat_all[$i]->display == 1)) {
                $g_item_count++;

                $link = 'index.php?option=com_booklibrary&task=showCategory&catid=' . $cat_all[$i]->id . '&Itemid=' . $Itemid;
                ?>
                <table  class="basictable bl_bl_all_categories_list_categories_in_table" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr class="<?php echo $tabclass[($g_item_count % 2)]; ?>">
                        <td width="1%" style="vertical-align:text-top;">
                            <?php
                            if ($deep != 1) {
                                $jj = $deep;
                                while ($jj--) {
                                    echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                                }
                                echo "&nbsp;"; //|_";
                                ?>
                                <img class="arrow_symbol" src="./components/com_booklibrary/images/arrow.png"/>
                                <?php
                            }
                            ?>
                        </td>
                        <td width="9%">
                            <?php if (($params->get('show_cat_pic')) && ($cat_all[$i]->image != "")) { ?>
                                <img src="./images/stories/<?php echo $cat_all[$i]->image; ?>" alt="picture for subcategory" height="48" width="48" />&nbsp;
                            <?php } else {
                                ?>
                                <img src="<?php echo $mosConfig_live_site; ?>/components/com_booklibrary/images/folder.png" alt="picture for subcategory" height="48" width="48" />&nbsp;
                <?php } ?>
                        </td>
                        <td class="bl_single_category_list_subcategories_link" width="74%">	
                            <a href="<?php echo sefRelToAbs($link); ?>" class="category<?php echo $params->get('pageclass_sfx'); ?>">
                <?php echo $cat_all[$i]->title; ?>
                            </a>					
                        </td>
                        <td  align="left" width="10%" style="text-align:center;">				
                        <?php if ($cat_all[$i]->books == '') echo "0";else echo $cat_all[$i]->books; ?>
                        </td>
                   </tr>
                </table>
                <?php
                if ($GLOBALS['subcategory_show']) {

                    HTML_booklibrary::showInsertSubCategory($cat_all[$i]->id, $cat_all, $params, $tabclass, $Itemid, $deep);
                }
            }//end if ($id == $cat_all[$i]->parent_id)
        }//end for(...)	
    }

//end function showInsertSubCategory($id, $cat_all)

    static function showSearchBooks($params, $currentcat, $clist, $option) {
        global $mosConfig_absolute_path, $booklibrary_configuration;

        $layout = $booklibrary_configuration['search_lay'];
        $type = 'show_search_book';
        require getLayoutPathBook::getLayoutPathCom('com_booklibrary', $type, $layout);
        //require($mosConfig_absolute_path . "/components/com_booklibrary/views/show_search_book/tmpl/" . $layout . ".php");
    }

    function showLendRequestThanks($params, $currentcat) {
        global $hide_js, $Itemid;
        ?>
        <div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>">
        <?php echo $currentcat->header; ?>
        </div>
        <table  class="basictable your_lend_request_was_stored" border="0" cellpadding="4" cellspacing="0" width="100%">
            <tr>				
                <?php
                if ($currentcat->img != null) {
                    ?>
                    <td>

                        <img src="<?php echo $currentcat->img; ?>" alt="?" />
                    </td>
                    <?php
                }
                ?>
                <td width="70%">
        <?php echo $currentcat->descrip; ?>
                </td>

            </tr>
        </table>
        <form class="j" id="adminForm" action="<?php echo sefRelToAbs("index.php?option=com_booklibrary&Itemid=" . $Itemid); ?>" method="post" name="userForm">
            <table  class="basictable your_lend_request_was_stored_buttons" border="0" cellpadding="4" cellspacing="0" width="100%">
                <tr>
                    <td>
                        <input class="my_btn my_btn-success" type="submit" name="submit" value="<?php echo _BOOKLIBRARY_LABEL_OK; ?>" class="button" />
                    </td>
                </tr>

            </table>			
        </form>
        <?php
    }

    static function show_cart($amazon_cart) {
        global $mosConfig_live_site, $booklibrary_configuration, $Itemid;
        $i = 1;
        $form_name = "userForm" . $i;
        ?>
        <style>
            .featureTitle{
                font-size: 16pt;
                margin: 10px;
            }
        </style>
        <link href="<?php echo $mosConfig_live_site; ?>/components/com_booklibrary/includes/booklibrary.css" rel="stylesheet">
        <div class="white-bg">
            <form class="bl_bl_view_book_add_to_cart_page" id="adminForm" method="post" action="index.php">
                <div class="cart">
                    <div class="featureTitle"><?php echo _BOOKLIBRARY_LABEL_CART; ?></div>
                    <div class="checkout">
                        <input type='hidden' name='task' value="cart_event" />
                        <input type='hidden' name='option' value="com_booklibrary" />
        <?php if ($amazon_cart != ""): ?>
                            <input class="my_btn my_btn-success" type='submit' name="check_out" value="<?php echo _BOOKLIBRARY_LABEL_CHECKOUT; ?>" />
                            <input class="my_btn my_btn-danger" type='submit' name="clean_cart" value="<?php echo _BOOKLIBRARY_LABEL_CLEARCART; ?>" />
        <?php endif; ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <?php $total_price = 0; ?>
        <?php if ($amazon_cart != ""): ?>
                    <div>
                        <table class="basictable my_table my_table-bordered bl_bl_view_book_add_to_cart_page_table" >
                            <tr>
                                <th width="35%"><?php echo _BOOKLIBRARY_LABEL_TITLE; ?>:</th>
                                <th><?php echo _BOOKLIBRARY_LABEL_AUTHORS; ?>:</th>
                                <th><?php echo _BOOKLIBRARY_LABEL_ISBN; ?>:</th>
                                <th><?php echo _BOOKLIBRARY_LABEL_PRICE; ?>:</th>
                                <th></th>
                                <th><?php echo _BOOKLIBRARY_LABEL_QUANTITY; ?>:</th>
                                <th></th>
                                <th><?php echo _BOOKLIBRARY_LABEL_SUBTOTAL; ?>:</th>
                            </tr>

            <?php foreach ($amazon_cart as $key => $item): ?>

                                <tr>
                                    <td><?php echo $item->title; ?></td>
                                    <td><?php echo $item->authors; ?></td>
                                    <td><?php echo $item->isbn; ?></td>
                                    <td><?php echo $item->price; ?></td>
                                    <td>X</td>
                                    <td>
                                        <input type="hidden" name="id[]" value ="<?php echo $item->id; ?>"/>
                                        <input type="text" size="2" name="quantity[]" value="<?php echo $item->quantity; ?>"/>
                                    </td>
                                    <td>=</td>
                                    <td>
                                        <?php
                                        $sub_price = (float) $item->price * $item->quantity;
                                        $total_price+=$sub_price;
                                        ?>
                <?php echo '$', $sub_price; ?>
                                    </td>
                                </tr>


            <?php endforeach; ?>

                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                                <td><strong><?php echo _BOOKLIBRARY_LABEL_TOTAL; ?>:</strong></td>
                                <td></td>

                                <td><hr/>
            <?php echo '$', $total_price; ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <?php else: ?>
                    <div>
                    <?php echo _BOOKLIBRARY_LABEL_CART_EMPTY; ?>
                    </div>
        <?php endif; ?>
                <input class="my_btn my_btn-info" type='submit' name="continue_shop" value="<?php echo _BOOKLIBRARY_LABEL_CONTINUE; ?>" />
                <input type='hidden' name="Itemid" value="<?php echo $Itemid; ?>" />

            </form>

        </div>
        <?php
    }


//OwnersList

    static function showOwnersList(&$params, &$ownerslist, &$pageNav) {
        global $mosConfig_absolute_path;
        $layout = $params->get('ownerslistlayout', 'default');
        $type = 'owners_list';
        require getLayoutPathBook::getLayoutPathCom('com_booklibrary', $type, $layout);
        //require($mosConfig_absolute_path . "/components/com_booklibrary/views/owners_list/tmpl/" . $layout . ".php");
    }

    static function showMyBooks(&$books, &$params, &$pageNav) {
        global $mosConfig_absolute_path;
        $layout = $params->get('mybookslayout', 'default');
        $type = 'show_my_books';
        require getLayoutPathBook::getLayoutPathCom('com_booklibrary', $type, $layout);
        //require($mosConfig_absolute_path . "/components/com_booklibrary/views/show_my_books/tmpl/" . $layout . ".php");
    }

    static function showRequestRentBooksCB($option, $rent_requests, & $pageNav, $params) {

        global $my, $mosConfig_live_site, $mainframe, $Itemid, $booklibrary_configuration;
        $session = JFactory::getSession();
        $acl = JFactory::getACL();
        $arr = $session->get("array", "default");
        $doc = JFactory::getDocument();
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/custom.css');
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/custom.css'); // for 1.6
        if ($option == 'com_booklibrary') {
            $user = JFactory::getuser();
            $db = JFactory::getDBO();


            $query = "SELECT * FROM #__booklibrary_lend_request AS b WHERE b.status=0";
            $db->setQuery($query);
            $current_user_rent_request_array = $db->loadObjectList();
            $check_for_show_rent_request = 0;
            if (isset($current_user_rent_request_array))
                $check_for_show_rent_request = 1;

            if ($booklibrary_configuration['cb_mybook']['show'] == '1' && checkAccessBL($booklibrary_configuration['cb_mybook']['registrationlevel'], 'NORECURSE', userGID_BL($my->id), $acl))
                echo "<span class='books_button'><a class='my_btn my_btn-primary' href='" . JRoute::_('index.php?option=' . $option . '&task=show_my_books&layout=mybooks') . "'>" . _BOOKLIBRARY_LABEL_CBBOOKS_TT . "</a></span>";

            if (($booklibrary_configuration['cb_edit']['show']) == '1' && checkAccessBL($booklibrary_configuration['cb_mybook']['registrationlevel'], 'NORECURSE', userGID_BL($my->id), $acl))
                echo " <span class='books_button'><a class='my_btn my_btn-primary' href='" . JRoute::_('index.php?option=' . $option . '&task=showmybooks&layout=mybooks') . "'>" . _BOOKLIBRARY_LABEL_CBEDIT . "</a></span>";

            if (($booklibrary_configuration['cb_history']['show'])) {
                $params->def('show_history', 1);
                $i = checkAccessBL($booklibrary_configuration['cb_history']['registrationlevel'], 'NORECURSE', userGID_BL($my->id), $acl);
                if ($i) {
                    $params->def('show_history_registrationlevel', 1);
                }
                if ($params->get('show_history')) {
                    if ($params->get('show_history_registrationlevel')) {
                        if ($check_for_show_rent_request != 0) {
                            echo " <span class='books_button'>
										<a class='my_btn my_btn-primary' href='" . JRoute::_('index.php?option=com_booklibrary&task=rent_history_books&name=' .
                                    $user->name . '&user=' . $user->id . '&is_show_data=1') . "'>" .
                            _BOOKLIBRARY_MY_LEND_HISTORY . "</a></span>";
                        }
                        //echo "<div style=\" border:1px solid black; padding: 10px; text-align:center; \">you dont have rent_history_lable</div>";
                    }
                }
            }

            if (($booklibrary_configuration['cb_rent']['show'])) {
                $params->def('show_rent', 1);
                $i = checkAccessBL($booklibrary_configuration['cb_rent']['registrationlevel'], 'NORECURSE', userGID_BL($my->id), $acl);
                if ($i) {
                    $params->def('show_rent_registrationlevel', 1);
                }

                if ($params->get('show_rent')) {
                    if ($params->get('show_rent_registrationlevel')) {
                        if ($check_for_show_rent_request != 0) {

                            echo " <span class='books_button'><a class='my_btn my_btn-primary' href='" . JRoute::_('index.php?option=' . $option . '&task=rent_requests_cb_books&is_show_data=1') . "'>" . _BOOKLIBRARY_LEND_REQUESTS . "</a></span>";
                        }
                    }
                }
            }
            ?>
            <style type="text/css">
                .row0 {background-color:#F9F9F9;}
            </style><br/><br/><br/>
        <?php } ?>
        <script>
            function vm_buttonClickCB()  {
                document.getElementById('adminFormTaskInput').value = 'decline_rent_requests_cb_book';
                document.getElementById('adminForm').submit();
            }
        </script>
        <script type="text/javascript">
            function checkAll(all) 
            { var c = document.getElementsByName('bid[]');
                for(var i=0;i<c.length;i++)
                {if(all.checked!=true)
                    {c[i].checked=false;}
                    else {c[i].checked=true;}
                }
            }
            function buttonClick(button)
            {if(button.name=='addbook')
                {submitform('add_book_fe');return;}
                var c = document.getElementsByName('bid[]');
                for(var i=0;i<c.length;i++)
                {if(c[i].checked)
                    {var checkedbooks = true;break;}
                }
                if(!checkedbooks){ alert("<?php echo _BOOKLIBRARY_ERROR_DEL; ?>");return;}
                if(button.name=='delete'){ resultat = confirm("<?php echo _BOOKLIBRARY_LABEL_EBOOK_DELETE; ?>");
                    if (resultat) { submitform('delete');}
                    return;}
                if(button.name=='unpublish'){ submitform('unpublish');return;}
                if(button.name=='publish'){ submitform('publish');return;}
            }
            function submitform(submit){
                var button=document.getElementsByName('submitbutton');
                button[0].value=submit;document.forms["adminForm1"].submit();
            }
        </script>
        <form class="bl_all_categories_show_my_book_lend_requests_form" action="index.php" method="get" name="adminForm" id="adminForm">
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="bl_all_categories_show_my_book_lend_requests my_table my_table-bordered basictable my_table-hover">
                <tr>
                    <th align = "center" width="5%">
                        <input type="checkbox" name="toggle" value="" onClick="checkAll(this<?php //echo count($rent_requests);    ?>);" />
                    </th>
                    <th align = "center" width="5%">#</th>
                    <th align = "center" class="title" width="10%" nowrap="nowrap">
                        <?php echo _BOOKLIBRARY_ORDER_LEND_FROM; ?></th>
                    <th align = "center" class="title" width="10%" nowrap="nowrap">
                        <?php echo _BOOKLIBRARY_ORDER_LEND_UNTIL; ?></th>
                    <th align = "center" class="title" width="5%" nowrap="nowrap">
                        <?php echo _BOOKLIBRARY_LABEL_BOOKID; ?></th>
                    <th align = "center" class="title" width="70%" nowrap="nowrap">
                        <?php echo _BOOKLIBRARY_LABEL_TITLE; ?></th>
                    <th align = "center" class="title" width="15%" nowrap="nowrap">
                        <?php echo _BOOKLIBRARY_LABEL_LEND_USER; ?></th>
                    <th align = "center" class="title" width="10%" nowrap="nowrap">
                        <?php echo _BOOKLIBRARY_LABEL_LEND_EMAIL; ?></th>
                    <th align = "center" class="title" width="20%" nowrap="nowrap">
                <?php echo _BOOKLIBRARY_LABEL_LEND_ADRES; ?></th>
                </tr>

                <?php
                for ($i = 0, $n = count($rent_requests); $i < $n; $i++) {
                    $row = $rent_requests[$i];
                    ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td width="20" align="center"><?php echo mosHTML::idBox($i, $row->id, 0, 'bid'); ?></td>
                        <td align = "center"><?php echo $row->id; ?></td>
                        <td align = "center"><?php echo $row->lend_from; ?></td>
                        <td align = "center"><?php echo $row->lend_until; ?></td>
                        <td align = "center"><?php
                $data = JFactory::getDBO();
                $query = "SELECT bookid FROM #__booklibrary where id = " . $row->fk_bookid . " ";
                $data->setQuery($query);
                $bookid = $data->loadObjectList();
                echo $bookid[0]->bookid;
                ?>
                        </td>
                        <td align = "center"><?php echo $row->title; ?></td>
                        <td align = "center"><?php echo $row->user_name; ?></td>
                        <td align = "center">
                            <a href='mailto:"<?php echo $row->user_email; ?>'><?php echo $row->user_email; ?></a>
                        </td>
                        <td align= "center"><?php echo $row->user_mailing; ?></td>
                    </tr>
                        <?php } ?>

                <tr class="for_paginator">
                    <td colspan = "9" align="center" id="pagenavig">
                        <?php
                        $paginations = $arr;
                        if ($paginations && ($pageNav->total > $pageNav->limit )) {
                            echo $pageNav->getPagesLinks();
                        }
                        ?>
                    </td>
                </tr>
            </table>
        <?php if ($option == 'com_booklibrary') { ?>
                <input type="hidden" name="option" value="com_booklibrary" />
        <?php } else { ?>
                <input type="hidden" name="option" value="com_comprofiler" />
                <input type="hidden" name="tab" value="getmybooksTab" /><?php } ?>
            <input type="hidden" name="is_show_data" value="1" />
            <input type="hidden" id="adminFormTaskInput" name="task" value="accept_rent_requests_cb_book" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />  
            <input class="my_btn my_btn-success" type="submit" name="submitButton" value="accept request"/>
            <input class="my_btn my_btn-danger" type="button" name="declineButton" value="decline request" onclick="vm_buttonClickCB()"/>

        </form>
        <?php
    }

    static function showLendBooks($option, $rows, & $userlist, $type) {
        global $my, $mosConfig_live_site, $mainframe, $doc, $Itemid;
        $doc->addScript($mosConfig_live_site . '/components/com_booklibrary/includes/functions.js');
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
        $doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/custom.css');
        //$doc->addScript($mosConfig_live_site . '/includes/js/mambojavascript.js');
        //$doc->addStyleSheet($mosConfig_live_site . '/includes/js/calendar/calendar-mos.css');
        //$doc->addScript($mosConfig_live_site . '/includes/js/calendar/calendar.js');
        //$doc->addScript($mosConfig_live_site . '/includes/js/calendar/lang/calendar-en-GB.js');
        // $doc->addScript($mosConfig_live_site . '/includes/js/overlib_mini.js');
        //print_r($_REQUEST);exit;
        ?>
        <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
        <form class="bl_all_categories_show_my_book_lent_out_form" action="index.php" method="get" name="adminForm" id="adminForm">
        <?php
        if ($type == "lend" || $type == "edit_lend") {
            ?>
                <table cellpadding="4" class="basictable my_table bl_all_categories_show_my_book_lent_out_table_user_data"
                       cellspacing="0" width="100%">
                    <tr>
                        <td align="center" nowrap="nowrap">
                            <label><?php echo _BOOKLIBRARY_LABEL_LEND_TO . ':'; ?></label>
                        </td>
                        <td align="center" nowrap="nowrap"><?php echo $userlist; ?></td>
                    </tr>
                    <tr>
                        <td align="center" nowrap="nowrap">
                            <label><?php echo _BOOKLIBRARY_LABEL_LEND_USER . ':'; ?></label>
                        </td>
                        <td><input type="text" name="user_name" class="inputbox" /></td>
                    </tr>
                    <tr>
                        <td align="left" nowrap="nowrap">
                            <label><?php echo _BOOKLIBRARY_LABEL_LEND_EMAIL . ':'; ?><label>
                                    </td>
                                    <td><input type="text" name="user_email" class="inputbox" /></td>
                                    </tr>
            <?php global $booklibrary_configuration; ?>
                                    <script language="JavaScript">
                                        window.onload = function ()
                                        {
                                            var today = new Date();
                                            var date = today.toLocaleFormat("<?php echo $booklibrary_configuration['date_format'] ?>");
                                            document.getElementById('lend_from').value = date;
                                            document.getElementById('lend_until').value = date;
                                        };
                                    </script>

                                    <tr>
                                        <td align="left" nowrap="nowrap">
                                            <label><?php
            $date_format = str_replace('%', '', $booklibrary_configuration['date_format']);
            echo "lend from:";
            ?></label>
                                        </td>
                                        <td align="left" nowrap="nowrap">
            <?php echo JHtml::_('calendar', date("Y-m-d"), 'lend_from', 'lend_from', $booklibrary_configuration['date_format'], array('size' => '17')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" nowrap="nowrap">
                                            <label><?php echo _BOOKLIBRARY_LABEL_LEND_TIME . ':'; ?></label>
                                        </td>
                                        <td align="left" nowrap="nowrap">
                                    <?php echo JHtml::_('calendar', date("Y-m-d"), 'lend_until', 'lend_until', $booklibrary_configuration['date_format'], array('size' => '17')); ?>
                                        </td>
                                    </tr>
                                    </table>
                                <?php } else { ?>
                                    &nbsp;
                                    <?php
                                }
                                $all = JFactory::getDBO();
                                $query = "SELECT * FROM #__booklibrary_lend";
                                $all->setQuery($query);
                                $num = $all->loadObjectList();
                                ?>
                                <table cellpadding="4" cellspacing="0" border="0" width="100%" class="basictable my_table 
                                       my_table-bordered bl_all_categories_show_my_book_lent_out_table_user_table">
                                    <tr>
                                        <th width="20" align="center">
        <?php if ($type != 'lend') { ?>
                                                <input type="checkbox" name="toggle" value="" onClick="vm_checkAll(this<?php //echo count($num);  ?>);" />
                                            <?php } ?>
                                        </th>
                                        <th align = "center" width="30">#</th>
                                        <th align = "center" class="title" width="5%" nowrap="nowrap">
                                            <?php echo _BOOKLIBRARY_LABEL_BOOKID; ?>
                                        </th>
                                        <th align = "center" class="title" width="35%" nowrap="nowrap">
                                            <?php echo _BOOKLIBRARY_LABEL_TITLE; ?>
                                        </th>
                                        <th align = "center" class="title" width="15%" nowrap="nowrap">
                                            <?php echo _BOOKLIBRARY_LABEL_LEND_FROM; ?>
                                        </th>
                                        <th align = "center" class="title" width="20%" nowrap="nowrap">
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
                                            //lent was called for a correct Book
                                        } else if ($row->lend_from == null && $type == "lend") {
                                            //lent return was called on a Book which was not lent out
                                        } else if ($row->lend_from == null && $type == "lend_return") {
                                            ?>
                                            &nbsp;
                                            <?php
                                            //lent return was called correctly
                                        } else if ($row->lend_from != null && $type == "lend_return") {
                                            
                                        } else {
                                            ?>
                                            &nbsp;
            <?php } ?>

                                        <input class="inputbox" type="hidden" name="bookid" id="bookid" size="0" maxlength="0" value="<?php echo $row->bookid; ?>" />
                                        <input class="inputbox" type="hidden" name="id" id="id" size="0" maxlength="0" value="<?php echo $row->id; ?>" />
                                        <input class="inputbox" type="hidden" name="title" id="title" size="0" maxlength="0" value="<?php echo $row->title; ?>" />

                                            <?php $my_count = 0; ?>

                                        <tr class="this">
                                            <?php
                                            $book = $row->id;
                                            $title = $row->title;
                                            $data = JFactory::getDBO();
                                            $query = "SELECT l.*,u.name FROM #__booklibrary_lend as l LEFT JOIN #__users AS u ON l.fk_userid = u.id WHERE fk_bookid =" . $book . " ORDER BY lend_return ";
                                            $data->setQuery($query);
                                            $alllend = $data->loadObjectList();
                                            if ($type == "lend") {
                                                ?>
                                                <td align="center">  
                                                    <input class="inputbox" type="checkbox" name="checkbook" id="checkBook" size="0" maxlength="0" value="on" />
                                                </td>  
                                                <td align="center"></td>
                                                <td align="center"><?php echo $row->bookid; ?></td>
                                                <td align="center"><?php echo $title; ?></td>
                                                <td align="center"></td>
                                                <td align="center"></td>
                                                <td align="center"></td>
                                                <td align="center"></td> 
                                            </tr>

                                            <tr class="lent_history">
                                                <td colspan="8">Lent history</td>
                                                <?php $my_count = 1; ?>
                                            </tr>

                                            <tr class="those">
                                            <?php } else if ($type == "edit_lend") { ?>
                                                <td>
                                                    <input type="hidden" name="checkbook" id="checkbook" value="on" />
                                                </td>
                                                <?php
                                            } $num = 1;
                                            for ($i = 0, $n = count($alllend); $i < $n; $i++) {
                                                if (!isset($alllend[$i]->lend_return) && $type != "lend") {
                                                    ?>
                                                    <td class="that0" align="center">
                                                        <input type="checkbox" id="cb<?php echo $i; ?>" name="bid[]" value="<?php echo $alllend[$i]->id; ?>"/> <!--onClick="Joomla.isChecked(this.checked);"-->
                                                    </td>
                <?php } else { ?>
                                                    <td  class="that1" align="center"></td>
                <?php } ?>
                                                <td  class="that2" align="center"><?php echo $num; ?></td>
                                                <td  class="that3" align="center"><?php echo $row->bookid; ?></td>
                                                <td  class="that4" align="center"><?php echo $row->title; ?></td>
                                                <td  class="that5" align="center"><?php echo $alllend[$i]->lend_from; ?></td>
                                                <td  class="that6" align="center"><?php echo $alllend[$i]->lend_until; ?></td>
                                                <td  class="that7" align="center"><?php echo $alllend[$i]->lend_return; ?></td>
                                            <?php if ($alllend[$i]->fk_userid != null) { ?>
                                                    <td  class="that8" align="center"><?php echo $alllend[$i]->name; ?></td>
                                                </tr>

                <?php } else {
                    ?>
                                                <td  class="that9" align="center"><?php echo $alllend[$i]->user_name . ":" . $alllend[$i]->user_email; ?></td>
                                                </tr>

                    <?php
                    if ($my_count == 0) {
                        $my_count++;
                        ?>
                                                    <tr class="lent_history">
                                                        <td colspan="8">Lent history</td>
                                                    </tr>
                                                <?php } ?>

                                                <?php
                                            }
                                            $num++;
                                        }
                                    }
                                    ?>
                                </table>
                                <?php if ($option == 'com_booklibrary') { ?>
                                    <input type="hidden" name="option" value="com_booklibrary" /> 
                                <?php } else { ?>
                                    <input type="hidden" name="option" value="com_comprofiler" />
        <?php } ?>
                                <input type="hidden" id="adminFormTaskInput" name="task" value="lend_return_book" />
        <?php if ($option == 'com_comprofiler') { ?>
                                    <input type="hidden" name="tab" value="getmybooksTab" /><?php } ?>
                                <input type="hidden" name="is_show_data" value="1" />
                                <input type="hidden" name="boxchecked" value="1" />
                                <input type="hidden" name="save" value="1" />
                                <input type="hidden" name="Itemid" value="<?php echo $Itemid ?>" />
                                <?php if ($type == "lend") { ?>
                                    <input class="my_btn my_btn-success" type="button" name="lend" value="<?php echo "lend"; ?>" onclick="vm_buttonClickRent1()"/>
                                    <!--<input type="button" name="lend" value="<?php //echo "lend"; ?>" onclick="rem_buttonClickRent(this)"/> -->
                                <?php } ?>
                                <?php if ($type == "lend_return") { ?>
                                    <input class="my_btn my_btn-warning" type="button" name="lend_return" value="<?php echo "return from lend"; ?>" onclick="vm_buttonClickRentOut1()"/>
                                <?php } ?>
                                </form>
                                <?php
                            }

                        }
                        ?>
