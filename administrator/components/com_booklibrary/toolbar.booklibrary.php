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
if (stristr($_SERVER['PHP_SELF'], 'administrator')) {
    @define('_VM_IS_BACKEND', '1');
}
defined('_VM_TOOLBAR_LOADED') or define('_VM_TOOLBAR_LOADED', 1);

include_once( /* dirname(__FILE__) */JPATH_COMPONENT_SITE . '/compat.joomla1.5.php' );

$path = JPATH_SITE . "/administrator/components/com_booklibrary/";
// ensure this file is being included by a parent file

require_once( $path . 'toolbar.booklibrary.php' );
require_once( $path . 'toolbar.booklibrary.html.php' );

$section = mosGetParam($_REQUEST, 'section', 'courses');


if (version_compare(JVERSION, "3.0.0", "ge"))
    if (isset($_REQUEST['task'])) {
        $task = $_REQUEST['task'];
    } else {
        $task = '';
    }

if (isset($section) && $section == 'categories') {

    switch ($task) {

        case "add": // use 'add' instead 'new' in J 1.6
            menucat::NEW_CATEGORY();
            break;
        case "edit":
            menucat::EDIT_CATEGORY();
            break;
        default:
            menucat::SHOW_CATEGORIES();
            addSubmenuBookLibrary("Categories");
            break;
    }
} elseif ($section == 'language_manager') {
    switch ($task) {

        case "copy":
            menulanguagemanager::EDIT_LANGUAGEMANAGER();
            /* vmLittleThings */addSubmenuBookLibrary("Language Manager");
            break;
        case "edit":
            menulanguagemanager::EDIT_LANGUAGEMANAGER();
            /* vmLittleThings */addSubmenuBookLibrary("Language Manager");
            break;
        default:
            menulanguagemanager::MENU_LANGUAGEMANAGER();
            /* vmLittleThings */addSubmenuBookLibrary("Language Manager");
            break;
    }
} else {

    switch ($task) {

        case "add": // use 'add' instead 'new' in J 1.6
            menubooklibrary::MENU_SAVE_BACKEND();
            break;

        case "edit":
            menubooklibrary::MENU_EDIT();
            break;

        case "refetchInfos":
            menubooklibrary::MENU_REFETCH_INFOS();
            break;
/////////////////////////st5/////////////
        case "Addproduct":

            menubooklibrary::MENU_ADD_TOVAR();
            break;

        case "show_import_export":
            menubooklibrary::MENU_IMPORT_EXPORT();
            addSubmenuBookLibrary("Import/Export");
            break;

        case "lend":
            menubooklibrary::MENU_LEND();
            break;

        case "lend_return":
            menubooklibrary::MENU_LEND_RETURN();
            break;

        case "lend_requests":
            menubooklibrary::MENU_LENDREQUESTS();
            addSubmenuBookLibrary("Lend Requests");
            break;

        case "edit_lend":
            menubooklibrary::MENU_EDIT_LEND();
            addSubmenuBookLibrary("Lend Requests");
            break;

        case "import":
            menubooklibrary::MENU_CANCEL();
            break;

        case "export":
            menubooklibrary::MENU_CANCEL();
            break;

        case "config_frontend":
            menubooklibrary::MENU_CONFIG_FRONTEND();
            addSubmenuBookLibrary("Settings Frontend");
            break;

        case "config_backend":
            menubooklibrary::MENU_CONFIG_BACKEND();
            addSubmenuBookLibrary("Settings Backend");
            break;


        case "config_save_frontend":
            menubooklibrary::MENU_CONFIG_FRONTEND();
            addSubmenuBookLibrary("Settings Frontend");
            break;

        case "config_save_backend":
            menubooklibrary::MENU_CONFIG_BACKEND();
            addSubmenuBookLibrary("Settings Backend");
            break;

        case "about":
            menubooklibrary::MENU_ABOUT();
            addSubmenuBookLibrary("About");
            break;

        case "delete_review":
            menubooklibrary::MENU_DELETE_REVIEW();
            break;

        case "edit_review":
            menubooklibrary::MENU_EDIT_REVIEW();
            break;

        case "update_review":
            menubooklibrary::MENU_EDIT();
            break;

        case "cancel_review_edit":
            menubooklibrary::MENU_EDIT();
            break;

//**************   begin for manage reviews   *********************

//**************   end for manage reviews   ***********************
//**************   begin for manage suggestion    *****************
       
//**************   end for manage suggestion    *******************

        default:
            menubooklibrary::MENU_DEFAULT();
            addSubmenuBookLibrary("Books");
            break;
    }
} //else
