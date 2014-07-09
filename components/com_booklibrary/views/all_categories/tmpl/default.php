<?php 
global $doc; // for 1.6
global $hide_js, $Itemid, $acl, $mosConfig_live_site, $my, $mainframe;
//$doc->addStyleSheet( $mosConfig_live_site.'/administrator/components/com_booklibrary/includes/booklibrary.css' );
$doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css');
$doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/custom.css'); // for 1.6
?><?php positions_bl($params->get('allcategories01')); ?>
<div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>">
    <?php echo $currentcat->header; ?>
</div><?php positions_bl($params->get('allcategories02')); ?>
<table  class="basictable bl_bl_all_categories_top_table_with_logo" border="0" cellpadding="4" cellspacing="0" width="100%">
    <tr>
        <td>
            <div class="my_alert">
                <?php echo $currentcat->descrip; ?>
            </div>	
        </td>     
        <td width="120" align="center">
            <img src="<?php echo $mosConfig_live_site; ?>/components/com_booklibrary/images/book.png" align="right" alt="Books"/>
        </td>
    </tr>
</table>
<?php positions_bl($params->get('allcategories03')); ?>
<?php
if ($params->get('show_search')) {
    ?>
    <div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>">
        <table  class="basictable bl_bl_all_categories_table_with_search" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="60%">&nbsp;
                </td>
                <td width="40%" nowrap align="right">
                    <?php
                    if ($params->get('search_fieldshow'))
                        echo HTML_booklibrary::displaySimpleSearch();
                    ?>
                    <?php
                    $link = 'index.php?option=com_booklibrary&task=show_search&catid=' .
                            $catid . '&Itemid=' . $Itemid;
                    ;
                    ?>
                     <!--a href="<?php echo sefRelToAbs($link); ?>" >
                     <img src="./components/com_booklibrary/images/search.gif" 
                         alt="Search" border="0" />
    <?php echo _BOOKLIBRARY_LABEL_SEARCH; ?>
                     </a-->
                </td>
            </tr>
        </table>
    </div>
    <?php
}
?>

<form class="bl_table_list_all_categories" id="adminForm" action="index.php" method="post" name="adminForm">
    <?php
    HTML_booklibrary::listCategories($params, $categories, $catid, $tabclass, $currentcat);
    positions_bl($params->get('allcategories07'));
    ?>
    <table  class="basictable bl_bl_all_categories_some_intresting_table_with_button" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="50%">&nbsp;

            </td>
            <td width="50%">
<?php mosHTML::BackButton($params, $hide_js); ?>
            </td>
        </tr>
    </table>
</form>


<!-- Add item Begin -->
<?php global $booklibrary_configuration; ?>
<?php
if ($booklibrary_configuration['addbook_button']['show'] == 1
        && $booklibrary_configuration['addbook_button']['allow']['categories'] == -2
        && (checkAccessBL($GLOBALS['add_book_button'], 'RECURSE', userGID_BL($my->id), $acl))) {
    ?> 
   
<?php } positions_bl($params->get('singlecategory10')); ?>

<div style="text-align: center;"><a href="http://ordasoft.com" style="font-size: 10px;">Powered by OrdaSoft!</a></div>
