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
include_once( /* dirname(__FILE__) */JPATH_COMPONENT_SITE . '/compat.joomla1.5.php' );


//*** Get language files
global $mosConfig_absolute_path, $mosConfig_lang;

$mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path'];
require_once($mosConfig_absolute_path . "/administrator/components/com_booklibrary/menubar_ext.php");

/* if (file_exists($mosConfig_absolute_path."/components/com_booklibrary/language/$mosConfig_lang.php" )) {
  include_once($mosConfig_absolute_path."/components/com_booklibrary/language/{$mosConfig_lang}.php" );
  } else { */

//include_once($mosConfig_absolute_path."/components/com_booklibrary/language/english.php" );
//}


class menucat {

    static function NEW_CATEGORY() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::save();
        mosMenuBar_ext::cancel();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function EDIT_CATEGORY() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::save();
        mosMenuBar_ext::cancel();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function SHOW_CATEGORIES() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::publishList();
        mosMenuBar_ext::unpublishList();
        mosMenuBar_ext::addNew();
        mosMenuBar_ext::editList();
        mosMenuBar_ext::deleteList();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    function DEFAULT_CATEGORIES() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::publishList();
        mosMenuBar_ext::unpublishList();
        mosMenuBar_ext::addNew('new', 'Add');
        mosMenuBar_ext::editList();
        mosMenuBar_ext::deleteList();
        mosMenuBar_ext::endTable();
    }

}

class menulanguagemanager {

    static function EDIT_LANGUAGEMANAGER() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::save();
        mosMenuBar_ext::cancel();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function MENU_LANGUAGEMANAGER() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::editList();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

}

class menubooklibrary {

    function MENU_ADD_TOVAR() {
        menubooklibrary::MENU_DEFAULT();
    }

    function MENU_NEW() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::save();
        mosMenuBar_ext::cancel();
        mosMenuBar_ext::apply('apply', 'apply');
        //mosMenuBar::help(./components/com_booklibrary/help/1.html);
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function MENU_EDIT() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::save();
        mosMenuBar_ext::apply('apply', 'apply');
        //*******************  begin add for review edit  **********************
        mosMenuBar_ext::editList('edit_review', _BOOKLIBRARY_TOOLBAR_ADMIN_EDIT_REVIEW);
        mosMenuBar_ext::deleteList('', 'delete_review', _BOOKLIBRARY_TOOLBAR_ADMIN_DELETE_REVIEW);
        //*******************  end add for review edit  ************************

        mosMenuBar_ext::cancel();
        //mosMenuBar::help();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    function MENU_DELETE_REVIEW() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::save();
        mosMenuBar_ext::spacer();

        //*******************  begin add for review edit  **********************
        mosMenuBar_ext::editList('edit_review', _BOOKLIBRARY_TOOLBAR_ADMIN_EDIT_REVIEW);
        mosMenuBar_ext::deleteList('', 'delete_review', _BOOKLIBRARY_TOOLBAR_ADMIN_DELETE_REVIEW);
        //*******************  end add for review edit  ************************

        mosMenuBar_ext::spacer();
        mosMenuBar_ext::cancel();
        //mosMenuBar::help();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    function MENU_EDIT_REVIEW() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::save('update_review');
        mosMenuBar_ext::cancel('cancel_review_edit');
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function MENU_CANCEL() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::back();  //old valid  mosMenuBar::cancel();
        //mosMenuBar::help();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function MENU_CONFIG_FRONTEND() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::save('config_save_frontend');
        //mosMenuBar_ext::cancel();
        //mosMenuBar::help();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function MENU_CONFIG_BACKEND() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::save('config_save_backend');
        //mosMenuBar_ext::cancel();
        //mosMenuBar::help();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

//**************   begin for manage reviews   *********************


//**************   end for manage reviews   ***********************
//**************   begin for manage suggestion    *****************
  
//**************   end for manage suggestion    *******************

    function MENU_MUSIC() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::publishList();
        mosMenuBar_ext::unpublishList();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::NewCustom('refetchInfos', 'adminForm', '../administrator/components/com_booklibrary/images/dm_refetchInfos.png', '../administrator/components/com_booklibrary/images/dm_refetchInfos_32.png', _BOOKLIBRARY_TOOLBAR_REFETCH_INFORMATION, _BOOKLIBRARY_TOOLBAR_ADMIN_REFRESH, true, 'adminForm');

        mosMenuBar_ext::NewCustom('lend', 'adminForm', "../administrator/components/com_booklibrary/images/dm_lend.png", "../administrator/components/com_booklibrary/images/dm_lend_32.png", _BOOKLIBRARY_TOOLBAR_LEND_BOOKS, _BOOKLIBRARY_TOOLBAR_ADMIN_LEND, true, 'adminForm');

        mosMenuBar_ext::NewCustom('lend_return', 'adminForm', "../administrator/components/com_booklibrary/images/dm_lend_return.png", "../administrator/components/com_booklibrary/images/dm_lend_return_32.png", _BOOKLIBRARY_TOOLBAR_RETURN_BOOKS, _BOOKLIBRARY_TOOLBAR_ADMIN_RETURN, true, 'adminForm');

        mosMenuBar_ext::addNew("new_music");
        mosMenuBar_ext::deleteList();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function MENU_DEFAULT() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::publishList();
        mosMenuBar_ext::unpublishList();

        mosMenuBar_ext::spacer();
        mosMenuBar_ext::NewCustom('refetchInfos', 'adminForm', '../administrator/components/com_booklibrary/images/dm_refetchInfos.png', '../administrator/components/com_booklibrary/images/dm_refetchInfos_32.png', _BOOKLIBRARY_TOOLBAR_REFETCH_INFORMATION, _BOOKLIBRARY_TOOLBAR_ADMIN_REFRESH, true, 'adminForm');

        mosMenuBar_ext::NewCustom('lend', 'adminForm', "../administrator/components/com_booklibrary/images/dm_lend.png", "../administrator/components/com_booklibrary/images/dm_lend_32.png", _BOOKLIBRARY_TOOLBAR_LEND_BOOKS, _BOOKLIBRARY_TOOLBAR_ADMIN_LEND, true, 'adminForm');

        mosMenuBar_ext::NewCustom('lend_return', 'adminForm', "../administrator/components/com_booklibrary/images/dm_lend_return.png", "../administrator/components/com_booklibrary/images/dm_lend_return_32.png", _BOOKLIBRARY_TOOLBAR_RETURN_BOOKS, _BOOKLIBRARY_TOOLBAR_ADMIN_RETURN, true, 'adminForm');
        mosMenuBar_ext::editList('edit_lend', _BOOKLIBRARY_TOOLBAR_ADMIN_EDIT_LEND);
        mosMenuBar_ext::addNew();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::deleteList();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function MENU_SAVE_BACKEND() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::save();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::back();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function MENU_LEND() {
        mosMenuBar_ext::startTable();

        mosMenuBar_ext::NewCustom('lend', 'adminForm', "../administrator/components/com_booklibrary/images/dm_lend.png", "../administrator/components/com_booklibrary/images/dm_lend_32.png", _BOOKLIBRARY_TOOLBAR_LEND_BOOKS, _BOOKLIBRARY_TOOLBAR_ADMIN_LEND, true, 'adminForm');

        mosMenuBar_ext::cancel();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function MENU_EDIT_LEND() {
        mosMenuBar_ext::startTable();

        mosMenuBar_ext::NewCustom('edit_lend', 'adminForm', "../administrator/components/com_booklibrary/images/dm_lend.png", "../administrator/components/com_booklibrary/images/dm_lend_32.png", _BOOKLIBRARY_TOOLBAR_LEND_BOOKS, _BOOKLIBRARY_TOOLBAR_ADMIN_LEND, true, 'adminForm');

        mosMenuBar_ext::cancel();
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function MENU_LENDREQUESTS() {
        global $mosConfig_absolute_path;
        mosMenuBar_ext::startTable();

        mosMenuBar_ext::NewCustom('accept_lend_requests', 'adminForm', '../administrator/components/com_booklibrary/images/dm_accept.png', '../administrator/components/com_booklibrary/images/dm_accept_32.png', _BOOKLIBRARY_TOOLBAR_ACCEPT_REQUEST, _BOOKLIBRARY_TOOLBAR_ADMIN_ACCEPT, true, 'adminForm');

        mosMenuBar_ext::NewCustom('decline_lend_requests', 'adminForm', '../administrator/components/com_booklibrary/images/dm_decline.png', '../administrator/components/com_booklibrary/images/dm_decline_32.png', _BOOKLIBRARY_TOOLBAR_EXPORT, _BOOKLIBRARY_TOOLBAR_ADMIN_DECLINE, true, 'adminForm');


        //mosMenuBar_ext::cancel();
        //mosMenuBar::help(./components/com_booklibrary/help/1.html);
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function MENU_LEND_RETURN() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::NewCustom('lend_return', 'adminForm', "../administrator/components/com_booklibrary/images/dm_lend_return.png", "../administrator/components/com_booklibrary/images/dm_lend_return_32.png", _BOOKLIBRARY_TOOLBAR_RETURN_BOOKS, _BOOKLIBRARY_TOOLBAR_ADMIN_RETURN, true, 'adminForm');
        mosMenuBar_ext::cancel();
        //mosMenuBar::help(./components/com_booklibrary/help/1.html);
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    function MENU_REFETCH_INFOS() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::NewCustom('refetchInfos', 'adminForm', '../administrator/components/com_booklibrary/images/dm_refetchInfos.png', '../administrator/components/com_booklibrary/images/dm_refetchInfos_32.png', _BOOKLIBRARY_TOOLBAR_REFETCH_INFORMATION, _BOOKLIBRARY_TOOLBAR_ADMIN_REFRESH, true, 'adminForm');
        mosMenuBar_ext::cancel();
        //mosMenuBar::help(./components/com_booklibrary/help/1.html);
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function MENU_IMPORT_EXPORT() {
        mosMenuBar_ext::startTable();
        mosMenuBar_ext::NewCustom_I('import', 'adminForm', '../administrator/components/com_booklibrary/images/dm_import.png', '../administrator/components/com_booklibrary/images/dm_import_32.png', _BOOKLIBRARY_TOOLBAR_IMPORT, _BOOKLIBRARY_TOOLBAR_ADMIN_IMPORT, true, 'adminForm');

        mosMenuBar_ext::NewCustom_E('export', 'adminForm', '../administrator/components/com_booklibrary/images/dm_export.png', '../administrator/components/com_booklibrary/images/dm_export_32.png', _BOOKLIBRARY_TOOLBAR_EXPORT, _BOOKLIBRARY_TOOLBAR_ADMIN_EXPORT, true, 'adminForm');


        //mosMenuBar_ext::back();  
        mosMenuBar_ext::spacer();
        mosMenuBar_ext::endTable();
    }

    static function MENU_ABOUT() {
        mosMenuBar_ext::startTable();
        //mosMenuBar_ext::back();		
        mosMenuBar_ext::endTable();
    }

}
