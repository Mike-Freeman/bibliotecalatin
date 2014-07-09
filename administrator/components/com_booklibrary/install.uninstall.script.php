<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) {
	die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
}

/**
*
* @package BookLibrary
* @copyright 2012 Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru); Rob de Cleen(rob@decleen.com);
* Homepage: http://www.ordasoft.com
* @version: 3.0 Free
*
**/


class com_BookLibraryInstallerScript{
    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent){
        // $parent is the class calling this method
    }
 
    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent){
        // $parent is the class calling this method
        require_once(JPATH_SITE."/administrator/components/com_booklibrary/uninstall.booklibrary.php");
    }
 
    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent){
        // $parent is the class calling this method
    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent){
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent){
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        require_once(JPATH_SITE."/administrator/components/com_booklibrary/install.booklibrary.php");
	global $mosConfig_absolute_path;
	com_install2();
//	if(version_compare(JVERSION,'1.6.0','ge')) unlink($mosConfig_absolute_path.'/components/com_booklibrary/metadata.xml');
    }
}
