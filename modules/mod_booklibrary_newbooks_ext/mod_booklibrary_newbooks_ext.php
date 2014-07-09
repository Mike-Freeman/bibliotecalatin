<?php
/**
 * New Books Extended module for BookLibrary
 * @version 3.0 FREE
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * mod_booklibrary_newbooks_ext
 * copyright Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru), 2011;
 */
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
$database = JFactory::getDBO();
$my = JFactory::getUser();
$GLOBALS['database'] = $database;
$GLOBALS['my'] = $my;
$acl = JFactory::getACL();
$GLOBALS['acl'] = $acl;
?>

<link rel="stylesheet" type="text/css" href="components/com_booklibrary/includes/booklibrary.css">
<?php
if (!function_exists('sefreltoabs')) {

    function sefRelToAbs($value) {
        //Need check!!!
        // Replace all &amp; with & as the router doesn't understand &amp;
        $url = str_replace('&amp;', '&', $value);

        if (substr(strtolower($url), 0, 9) != "index.php")
            return $url;

        $uri = JURI::getInstance();
        $prefix = $uri->toString(array('scheme', 'host', 'port'));

        return $prefix . JRoute::_($url);
    }

}

$ItemId_tmp_from_params = $params->get('ItemId');
$database->setQuery("SELECT id  FROM #__menu WHERE menutype like '%menu%' AND link LIKE '%index.php?option=com_booklibrary%' ");
$ItemId_tmp_from_db = $database->loadResult();

if ($ItemId_tmp_from_params != '') {
    $ItemId_tmp = $ItemId_tmp_from_params;
} else {
    $ItemId_tmp = $ItemId_tmp_from_db;
}

$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$count = intval($params->get('count', 1));
$description = intval($params->get('description', 1));
$g_words = $params->get('words', '');
$showtitle = $params->get('showtitle', '');
$showauthor = $params->get('showauthor', '');
$showcover = $params->get('showcover', 1);
$displaytype = $params->get('displaytype', 0);
$coversize = $params->get('coversize', '127');
$sortnewby = $params->get('sortnewby', 0);

switch ($sortnewby) {

    case 0:
        $sql_orderby_query = "date"; // Last Edited
        break;
    case 1:
        $sql_orderby_query = "id"; // Last Added
        break;
}

require_once ( JPATH_SITE . "/components/com_booklibrary/functions.php" );
$s = getWhereUsergroupsString("c");

$selectstring = "SELECT a.*,bc.catid FROM #__booklibrary AS a
            \nLEFT JOIN #__booklibrary_categories AS bc ON bc.bookid=a.id
            \nLEFT JOIN #__booklibrary_main_categories AS c ON c.id=bc.catid
            \nWHERE a.published=1 AND ({$s}) AND c.published='1' " .
        "\nGROUP BY a.id
            \nORDER BY " . $sql_orderby_query . " DESC LIMIT 0,$count;";

$database->setQuery($selectstring);
$rows = $database->loadObjectList();
?> 
<?php if ($moduleclass_sfx != '') { ?>
    <div  class="<?php echo $moduleclass_sfx; ?>"> <?php } ?>
    <table cellpadding="1" cellspacing="1" class="basictable" width="100%">
        <?php
        if ($displaytype == 1) { // Display Horizontal         
            ?>
            <tr valign="top">           
                <?php
            }
            ?>

            <?php
            foreach ($rows as $i => $row) {
                $comment = $row->comment;
                $prevwords = count(explode(" ", $comment));
                if (trim($g_words == ""))
                    $words = $prevwords;
                else
                    $words = intval($g_words);
                $text = implode(" ", array_slice(explode(" ", $comment), 0, $words));
                if (count(explode(" ", $text)) < $prevwords) {
                    $text .= "";
                }

                $link1 = "index.php?option=com_booklibrary&amp;task=view&amp;Itemid=" . $ItemId_tmp . "&amp;id=" . $row->id . "&amp;catid=" . $row->catid;

                //for local images
                $imageURL = $row->imageURL;
                if ($imageURL != '' && substr($imageURL, 0, 4) != "http") {
                    $imageURL = JURI::base() . $row->imageURL;
                    ;
                }
                if ($imageURL == '') {
                    $imageURL = "./components/com_booklibrary/images/no-img_eng.gif";
                }

                if ($displaytype == 1) { // Display Horizontal         
                    
                ?>
                <td valign="top" align="center">
                    <p><strong><?php

                if ($showcover == 1) {
                        ?>
                    <noscript>Javascript is required to use Book Library <a href="http://ordasoft.com/Book-Library/booklibrary-versions-feature-comparison.html">Book Library - create book library, ebook, book collection  </a>, 

                    <a href="http://ordasoft.com/location-map.html">Book library book sowftware for Joomla</a></noscript>
                    
                        <a href="<?php echo sefRelToAbs($link1); ?>" target="_self">
                            <img src="<?php echo $imageURL; ?>"  hspace="15" vspace="2" border="0" height="<?php echo $coversize; ?>" /></a>
                        <br>
                    <?php
                } //End Show Image If 

                if ($showtitle == "1") {
                    echo $row->title;
                } else {
                    print "&nbsp;";
                }
                ?></strong><br/><?php
                if ($showauthor == "1") {
                    echo "By&nbsp;".$row->authors;
                } else {
                    print "&nbsp;";
                }
                ?><p><?php
                        //for 1.6      
                        echo switchDescription($text, $description);
                        // --
                        ?></p>
                    <p><!--a class="readon" href="<?php echo sefRelToAbs($link1); ?>" target="_self">Read Reviews...</a></p-->
                </td> 		
                <?php

                if(($i+1) % 4 == 0){
                ?>
                    </tr><tr>
                <? }

            } else {
//Display Vertical     
                ?>
                <tr valign="top">
                    <td>
                        <a href="<?php echo sefRelToAbs($link1); ?>" target="_self">
                        <?php if ($showcover == 1) { ?>             
                                <img src="<?php echo $imageURL; ?>"  hspace="2" vspace="2" border="0" height="<?php echo $coversize; ?>" /></a>
                        <?php } //End Show Image If?>
                        <?php
                        if ($showtitle == "1") {
                            echo $row->title;
                        } else {
                            echo "&nbsp;";
                        }
                        ?>  

                        <?php
                        if ($showauthor == "1") {
                            echo "<br />" . $row->authors;
                        } else {
                            echo "&nbsp;";
                        }
                        ?>
                        <br />
                        <p><?php echo switchDescription($text, $description);      //for 1.6  ?></p>
                        <p><a class="readon" href="<?php echo sefRelToAbs($link1); ?>" target="_self">Read Reviews...</a></p>
                    </td>            
                </tr>    
                <tr> <td>&nbsp; </td>
                </tr>
                <?php
            } //End Display If
        }
        ?>
        <?php
        if ($displaytype == 1) { // Display Horizontal         
            ?>
            </tr>           
            <?php
        }
        ?>

    </table>
<?php if ($moduleclass_sfx != '') { ?>
    </div> <?php } ?>
<div style="text-align: center;"><a href="http://ordasoft.com" style="font-size: 10px;">Powered by OrdaSoft!</a></div>
<?php

function switchDescription($text, $description) {
// for 1.6
    switch ($description) {
        case 1:
            $text = substr($text, 0, 100);
            $text .= '...';
            break;

        case 2:
            $text = substr($text, 0, 500);
            $text .= '...';
            break;

        case 3:
            break;

        case 4:
            $text = '';
            break;
    }
    return $text;
}
?>
