<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

/**
*
* @package BookLibrary
* @copyright Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru); Rob de Cleen(rob@decleen.com); 
* Homepage: http://www.ordasoft.com
* @version: 3.0 Free
*
**/

$mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path'];



jimport('joomla.html.toolbar');
require_once($mosConfig_absolute_path."/administrator/includes/toolbar.php");//JToolBarHelper extends JToolBar

if(!class_exists('JToolBarHelper_ext')){
    class JToolBarHelper_ext extends JToolBarHelper
    {
    function NewSave($task='',$page,$icon='',$iconOver='',$alt='',$listSelect=true,$formName="adminForm") 
    {
      $bar =JToolBar::getInstance('toolbar');
        if(empty($task)){$image_name = uniqid( "img_" );}
      else {$image_name  = $task;}
      
      if ($icon && $iconOver) 
      {
        $bar->appendButton('NewSave',"<a class=\"my_btn ten toolbar\"onmouseout=\"MM_swapImgRestore();\" onmouseover=\"MM_swapImage('$image_name','','$iconOver',1);\"><img name=\"$image_name\" title = \"$alt\" src=\"$icon\" alt=\"$alt\" border=\"0\" align=\"middle\"/>&nbsp;<br/>$alt</a>" );
                
        } 
        else {
            // The button is just a link then!
            $bar->appendButton('Custom', "<a class=\"my_btn nine toolbar\" title = \"$alt\" href=\"$href\">&nbsp;$alt<a>" );
        }	
    }

    static function NewCustom_I($task='',$page,$icon='',$iconOver='',$text='',$alt='',$listSelect=true,$formName="adminForm") 
    {
    global $VM_LANG;

    $bar =JToolBar::getInstance('toolbar');



            if ($listSelect) {
                if( empty( $func ))
                    $href = "javascript:if (document.adminForm.import_type.value == 0){ alert('$text');}
                                                        else{submitbutton('$task')}";
                else
                    $href = "javascript:submitbutton('$task')";
                    } else {
                            $href = "javascript:submitbutton('$task')";
                    }
                    if( empty( $task )) {
                            $image_name = uniqid( "img_" );
                    }
                    else {
                    $image_name  = $task;
                    }
                    if ($icon && $iconOver) {
    $bar->appendButton('Custom', "<a class=\"my_btn bl_import_export_toolbar_import_buttom toolbar\" href=\"$href\" onmouseout=\"MM_swapImgRestore();\"  onmouseover=\"MM_swapImage('$image_name','','$iconOver',1);\">
    <img name=\"$image_name\"title = \"$alt\" src=\"$icon\" alt=\"$alt\" border=\"0\" align=\"left\" width=\"14px\" height=\"14px\" />
    &nbsp;$alt</a>");
                
            } 
            else {
                // The button is just a link then!
                $bar->appendButton('Custom', "<a class=\"my_btn seven toolbar\" title = \"$alt\" href=\"$href\">&nbsp;$alt</a>");
            }	
    }


    static function NewCustom_E($task='',$page,$icon='',$iconOver='',$text='',$alt='',$listSelect=true,$formName="adminForm") 
    {
    global $VM_LANG;

    $bar =JToolBar::getInstance('toolbar');



            if ($listSelect) {
                if( empty( $func ))
                    $href = "javascript:if (document.adminForm.export_type.value == 0){ alert('$text');}
                                                        else{submitbutton('$task')}";
                else
                    $href = "javascript:submitbutton('$task')";
                    } else {
                            $href = "javascript:submitbutton('$task')";
                    }
                    if( empty( $task )) {
                            $image_name = uniqid( "img_" );
                    }
                    else {
                    $image_name  = $task;
                    }
                    if ($icon && $iconOver) {
    $bar->appendButton('Custom', "<a class=\"my_btn bl_import_export_toolbar_export_buttom toolbar\" href=\"$href\" onmouseout=\"MM_swapImgRestore();\"  onmouseover=\"MM_swapImage('$image_name','','$iconOver',1);\">
                            <img name=\"$image_name\" title = \"$alt\" src=\"$icon\" alt=\"$alt\" border=\"0\" align=\"left\" width=\"14px\" height=\"14px\"  />
                            &nbsp;$alt</a>");
                
            } 
            else {
                // The button is just a link then!
                $bar->appendButton('Custom', "<a class=\"my_btn five toolbar\" title = \"$alt\" href=\"$href\">&nbsp;$alt</a>");
            }	
    }




    static function NewCustom($task='',$page,$icon='', $iconOver='',$text='', $alt='', $listSelect=true, $formName="adminForm") 
    {
      global $VM_LANG;
            
      $bar =JToolBar::getInstance('toolbar');
      if ($listSelect) {
          if( empty( $func ))
              $href = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('$text');}else{vm_submitButton('$task','$formName', '$page')}";
          else
              $href = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('$text');}else{vm_submitListFunc('$task','$formName', '$func')}";
        } else {
                $href = "javascript:vm_submitButton('$task','$formName', '$page')";
        }
        if( empty( $task )) {
                $image_name = uniqid( "img_" );
        }
        else {
        $image_name  = $task;
        }
        if ($icon && $iconOver) {
          $bar->appendButton('Custom', "<a class=\"my_btn four toolbar\" href=\"$href\" onmouseout=\"MM_swapImgRestore();\"  onmouseover=\"MM_swapImage('$image_name','','$iconOver',1);\">
                            <img width=\"12\" height=\"12\" name=\"$image_name\" title = \"$alt\" src=\"$icon\" alt=\"$alt\" border=\"0\" align=\"middle\" />
                            &nbsp;$alt</a>");
                
            } 
            else {
                // The button is just a link then!
                $bar->appendButton('Custom', "<a class=\"my_btn three toolbar\" title = \"$alt\" href=\"$href\">&nbsp;$alt<a>" );
            }	
    }


    static function NewCustom_Add_VM($task='',$page,$icon='',$iconOver='',$text='',$alt='',$listSelect=true, $formName="adminForm")
    {
      global $VM_LANG;
            
      $textM = explode(";", $text);
        $bar =JToolBar::getInstance('toolbar');
        if ($listSelect) {
        $href = "javascript:if (document.adminForm.boxchecked.value == 0)
                    {alert('$textM[0]');}
                else
                    {if (document.adminForm.boxchecked.value > 1)
                    {alert('$textM[1]');}
                    else
                    {vm_submitButton('$task','$formName', '$page')}}";

      } else {
              $href = "javascript:vm_submitButton('$task','$formName', '$page')";
      }
      if( empty( $task )) {
              $image_name = uniqid( "img_" );
      }
      else {
      $image_name  = $task;
      }
      if ($icon && $iconOver) {
        $bar->appendButton('Custom', "<a class=\"my_btn two toolbar\" href=\"$href\" onmouseout=\"MM_swapImgRestore();\"  onmouseover=\"MM_swapImage('$image_name','','$iconOver',1);\">
                            <img width=\"12\" height=\"12\" name=\"$image_name\" title = \"$alt\" src=\"$icon\" alt=\"$alt\" border=\"0\" align=\"middle\" />
                            &nbsp;$alt</a>");
        } 
        else {
            // The button is just a link then!
            $bar->appendButton('Custom', "<a class=\"my_btn one toolbar\" title = \"$alt\" href=\"$href\">&nbsp;$alt<a>" );
        }	
    }

    }
}
