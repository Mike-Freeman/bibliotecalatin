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

include_once( JPATH_ROOT."/components/com_booklibrary/compat.joomla1.5.php" );
require_once ($mosConfig_absolute_path."/components/com_booklibrary/booklibrary.class.php");

if(!defined( '_JLEGACY' )){
    $GLOBALS['path'] = $mosConfig_live_site."/components/com_booklibrary/images/";
} else{
    $GLOBALS['path'] = $mosConfig_live_site."/administrator/components/com_booklibrary/images/";
}
$path = $GLOBALS['path'];

class DMInstallHelper{ 
    static function getComponentId(){
        global $database;
        if (version_compare(JVERSION, "1.6.0", "lt")){
            $query = "SELECT id FROM #__components WHERE `option`='com_booklibrary'";
        } else if (version_compare(JVERSION, "1.6.0", "ge") && version_compare(JVERSION, "3.5.0", "lt")){
            $query = "SELECT extension_id FROM #__extensions WHERE `element`='com_booklibrary'";
        } else {echo "Sanity test. Error version check!"; exit;}
        $database->setQuery($query);
        $id = $database->loadResult();
        return $id;
    }
   
    static function getParentId(){
        $id = DMInstallHelper::getComponentId();
        global $database;
        if (version_compare(JVERSION, "1.6.0", "lt")){
            //
        } else if (version_compare(JVERSION, "1.6.0", "ge") && version_compare(JVERSION, "3.5.0", "lt")){
            $database->setQuery("SELECT id FROM #__menu WHERE title='BookLibrary' and level=1 and parent_id=1 and component_id=".$id);
            $parent_id =$database->loadResult();
            return $parent_id;
        } else {echo "Sanity test. Error version check!"; exit;}
    }
   

    static function setAdminMenuImages(){
      global $database,$path;

        $id = DMInstallHelper::getComponentId();
		  if (version_compare(JVERSION, "1.6.0", "lt")){

            // Main menu
            $database->setQuery("UPDATE #__components SET admin_menu_img = '".$path."dm_component_16.png' WHERE id=".$id);
            $database->query();

            // Submenus
        $submenus = array();
        $submenus[] = array( 'image' => 'class:module', 'title'=>'Books' );
        $submenus[] = array( 'image' => 'class:dm_credits', 'title'=>'Categories' );
        $submenus[] = array( 'image' => 'class:move', 'title'=>'Lend Requests' );
        $submenus[] = array( 'image' => 'class:download', 'title'=>'Import/Export' );
        $submenus[] = array( 'image' => 'class:config', 'title'=>'Settings Frontend' );
        $submenus[] = array( 'image' => 'class:config', 'title'=>'Settings Backend' );
        $submenus[] = array( 'image' => 'class:info', 'title'=>'About' );

            foreach($submenus as $submenu){
                $database->setQuery("UPDATE #__components SET admin_menu_img = '".$submenu['image']."' WHERE parent=$id AND name = '".$submenu['title']."';");
                $database->query();
            }
        } else if (version_compare(JVERSION, "1.6.0", "ge") && version_compare(JVERSION, "3.5.0", "lt")){
            $parent_id = DMInstallHelper::getParentId();

            // Main menu
            $database->setQuery("UPDATE #__menu SET img = 'class:component' WHERE title='BookLibrary' and level=1 and parent_id=1 and component_id=$id");
            $database->query();

            // Submenus
           $submenus = array();
            $submenus[] = array( 'img' => 'class:module', 'title'=>'Books' );
            $submenus[] = array( 'img' => 'class:dm_credits', 'title'=>'Categories' );
            $submenus[] = array( 'img' => 'class:move', 'title'=>'Lend Requests' );
            $submenus[] = array( 'img' => 'class:download', 'title'=>'Import/Export' );
            $submenus[] = array( 'img' => 'class:config', 'title'=>'Settings Frontend' );
            $submenus[] = array( 'img' => 'class:config', 'title'=>'Settings Backend' );
            $submenus[] = array( 'img' => 'class:info', 'title'=>'About' );

            foreach($submenus as $submenu){
                $database->setQuery("UPDATE #__menu SET img = '".$submenu['img']."' WHERE component_id=$id AND parent_id = '".$parent_id."' and level=2  AND title = '".$submenu['title']."';");
                $database->query();
            }



        }else {echo "Sanity test. Error version check!"; exit;}
    }
}
