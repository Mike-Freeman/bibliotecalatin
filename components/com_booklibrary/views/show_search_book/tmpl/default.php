<?php
global $doc, $hide_js, $Itemid, $mainframe, $mosConfig_live_site;
$doc->addStyleSheet($mosConfig_live_site . '/components/com_booklibrary/includes/booklibrary.css'); // for 1.6
positions_bl($params->get('showsearch01'));

// for J 1.6
$script_content = '';

$script_content .= "	function trim(string){	return string.replace(/(^\s+)|(\s+$)/g, '');	} \n";
$script_content .= "	function searchsubmit() \n";
$script_content .= "	{ \n";
$script_content .= "		var form = document.com_booklibsearchForm; \n";
$script_content .= "		var flag = true; \n";
$script_content .= "// 				var t_lprice = trim(document.getElementById('lprice').value); \n";
$script_content .= "// 				var t_uprice = trim(document.getElementById('uprice').value); \n";
$script_content .= "// 				var t_ldate = trim(document.getElementById('ldate').value); \n";
$script_content .= "// 				var t_udate = trim(document.getElementById('udate').value); \n";
$script_content .= "// 				if(!((t_lprice!='' && t_uprice!='') || (t_uprice == t_lprice))) \n";
$script_content .= "// 				{ \n";
$script_content .= "// 					alert('If you want search by price - interval must be set') \n";
$script_content .= "// 					flag=false; \n";
$script_content .= "// 				} \n";
$script_content .= "// 				if(!((t_ldate!='' && t_udate!='') || (t_ldate == t_udate))) \n";
$script_content .= "// 				{ \n";
$script_content .= "// 					alert('If you want search by release date - interval must be set') \n";
$script_content .= "// 					flag=false; \n";
$script_content .= "// 				} \n";

$script_content .= "				if(flag) \n";
$script_content .= "					form.submit(); \n";
$script_content .= "			} \n";

$doc->addScriptDeclaration($script_content);
// --
?>
<div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>">
    <?php echo $currentcat->header; ?>
</div><?php positions_bl($params->get('showsearch02')); ?>
<table  class="bl_bl_search_books_table_with_logo basictable" border="0" cellpadding="4" cellspacing="0" width="100%">
    <tr>				
        <?php
        if ($currentcat->img != null && $currentcat->align == 'left') {
            ?>
            <td>
                <img src="<?php echo $currentcat->img; ?>" align="<?php echo $currentcat->align; ?>" />
            </td>
            <?php
        }
        ?>
        <td width="100%">
            <?php echo $currentcat->descrip; ?>
        </td>
        <?php
        if ($currentcat->img != null && $currentcat->align == 'right') {
            ?>
            <td>
                <img src="<?php echo $currentcat->img; ?>" align="<?php echo $currentcat->align; ?>"  alt = "?"/>
            </td>
            <?php
        }
        ?>
    </tr>
</table>





<?php positions_bl($params->get('showsearch03')); ?>


<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $params->get('pageclass_sfx'); ?>">

    <?php
    $path = "index.php?option=" . $option . "&amp;task=search&amp;Itemid=" . $Itemid;
    ?>

    <form id="adminForm" action="<?php echo sefRelToAbs($path); ?>" method="get" name="com_booklibsearchForm">
        <table  class="bl_advanced_search_table my_table basictable" border="0" cellpadding="4" cellspacing="0" width="100%">
            <tr>
                <td align="center" colspan="2" >
                    <?php echo _BOOKLIBRARY_LABEL_SEARCH_KEYWORD; ?>&nbsp;:
                    <input class="inputbox" type="text" name="searchtext" size="20" maxlength="20"/>
                </td>
            </tr>

            <?php /* for Upgrade */ ?>
            <!--tr>
              <td colspan="2" align="center">
                <table>
                  <tr>
                    <td><?php echo _BOOKLIBRARY_SHOW_SEARCH_FOR_PRICE_BETWEEN; ?>:&nbsp;</td>
                    <td nowrap>
            <?php echo _BOOKLIBRARY_SHOW_LABEL_FROM; ?>
                          <input type="text" id="lprice" name="lprice" value="" size="6"/> 
            <?php echo _BOOKLIBRARY_SHOW_LABEL_TO; ?> 
                          <input type="text" id="uprice" name="uprice" value="" size="6"/>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td colspan="2" align="center">
                  <table>
                    <tr>
                      <td><?php echo _BOOKLIBRARY_SHOW_SEARCH_FOR_RELEASE_BETWEEN; ?>:&nbsp;</td>
                      <td nowrap>
            <?php echo _BOOKLIBRARY_SHOW_LABEL_FROM; ?>
                            <input type="text" name="ldate" id="ldate" value="" size="9"/>
                            <input type="reset" style="font-size:small;width:16px;height:20px;text-align:center;"
                              value="..." onClick="return showCalendar('ldate', 'y-mm-dd');" />
            <?php echo _BOOKLIBRARY_SHOW_LABEL_TO; ?>
                            <input type="text" name="udate" id="udate" value="" size="9"/>
                            <input type="reset" style="font-size:small;width:16px;height:20px;text-align:center;"
                              value="..." onClick="return showCalendar('udate', 'y-mm-dd');" />
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr-->

            <?php /* for Upgrade */ /*  echo JHtml::_('calendar',date("Y-m-d"), 'lend_from','lend_from','%Y-%m-%d' ); */ /* for 1.6 */ ?>			

            <tr>
                <td style="text-align:right;display:inline-block;width:116px;">Search in:</td>
                <td style="text-align:left;display:inline-block;width:70px;height:21px;padding-left:0px;">
                    <?php echo _BOOKLIBRARY_SHOW_SEARCH_FOR_TITLE; ?>:
                    <input type="checkbox" name="title" checked="checked" />&nbsp;&nbsp;
                </td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align:right;display:inline-block;width:106px;"></td>
                <td style="text-align:right;display:inline-block;width:75px;height:21px;padding-left:0px;">
                    <?php echo _BOOKLIBRARY_SHOW_SEARCH_FOR_ISBN ?>:
                    <input type="checkbox" name="isbn" checked="checked" />&nbsp;&nbsp;
                </td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align:right;display:inline-block;width:118px;"></td>	
                <td style="text-align:right;display:inline-block;width:75px;height:21px;padding-left:0px;">
                    <?php echo _BOOKLIBRARY_SHOW_SEARCH_FOR_AUTORS; ?>:
                    <input type="checkbox" name="author" checked="checked" />&nbsp;&nbsp;
                </td>	
                <td></td>
            </tr>
            <tr>
                <td style="text-align:right;display:inline-block;width:116px;"></td>
                <td style="text-align:right;display:inline-block;width:100px;height:21px;padding-left:0px;">			  
                    <?php echo _BOOKLIBRARY_SHOW_SEARCH_FOR_DESCRIPTION ?>:
                    <input type="checkbox" name="description" checked="checked" />&nbsp;&nbsp;
                    <div style="visibility:hidden;">
                        <?php /* echo _BOOKLIBRARY_SHOW_SEARCH_FOR_PUBLISHER;  */ ?>:
                        <input type="checkbox" name="publisher" checked="checked"/> </div>
                </td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align:right;display:inline-block;width:114px;">
                    <?php echo _BOOKLIBRARY_SHOW_SEARCH_PRICE_FROM; ?>:
                </td>	
                <td style="text-align:right;display:inline-block;width:116px;padding-left:0px;">
                    <input type="text" name="pricefrom" size="6"/>
                </td>
                <td></td>
            </tr>
            <tr>		  
                <td style="text-align:right;display:inline-block;width:114px;">
                    <?php echo _BOOKLIBRARY_SHOW_SEARCH_PRICE_TO; ?>:
                </td>
                <td style="text-align:right;display:inline-block;width:116px;padding-left:0px;">
                    <input type="text" name="priceto" size="6"/>
                </td>
                <td></td>
            </tr>
            <tr>      
                <td style="text-align:right;display:inline-block;width:114px;">
                    <?php echo _BOOKLIBRARY_LABEL_CATEGORY; ?>:
                </td>
                <td style="text-align:right;display:inline-block;width:114px;padding-left:2px;">
                    <?php echo $clist; ?>
                </td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align:right;display:inline-block;width:114px;"></td>
                <td style="text-align:right;display:inline-block;width:114px;padding-left:3px;">
                    <div class="my_btn my_btn-primary bl_advanced_search">
                        <input type="button" onclick="searchsubmit();" 
                               value="<?php echo _BOOKLIBRARY_LABEL_SEARCH_BUTTON; ?>" class="button bl_advanced_search" />
                    </div>	
                    <input type="hidden" name="option" value="<?php echo $option; ?>">
                    <input type="hidden" name="task" value="search">
                    <input type="hidden" name="Itemid" value="<?php echo $Itemid ?>">
                </td> 
                <td></td>
            </tr>
        </table>      
        <br />       
        <?php
        mosHTML::BackButton($params, $hide_js);
        ?>
    </form>
    <?php positions_bl($params->get('showsearch04')); ?>
<div style="text-align: center;"><a href="http://ordasoft.com" style="font-size: 10px;">Powered by OrdaSoft!</a></div>