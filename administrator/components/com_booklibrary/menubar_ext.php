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

// Check to ensure this file is within the rest of the framework
//defined('JPATH_BASE') or die();

// Register legacy classes for autoloading
//JLoader::register('JToolbarHelper' , JPATH_ADMINISTRATOR.DS.'includes'.DS.'toolbar.php');

/**
 * Legacy class, use {@link JToolbarHelper} instead
 *
 * @deprecated	As of version 1.5
 * @package	Joomla.Legacy
 * @subpackage	1.5
 */
$mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path'];
require_once($mosConfig_absolute_path."/administrator/components/com_booklibrary/toolbar_ext.php");

jimport('joomla.application.component.view');
//class mosMenuBar_ext extends JToolbarHelper
if(!class_exists('mosMenuBar_ext')){
    class mosMenuBar_ext extends JToolBarHelper_ext
    {
        /**
        * @deprecated As of Version 1.5
        */
        static function startTable()
        {
            return;
        }

        /**
        * @deprecated As of Version 1.5
        */
        static function endTable()
        {
            return;
        }

        /**
         * Deprecated
         *
         * @deprecated As of Version 1.5
         */
        function saveedit()
        {
            parent::save('saveedit');
        }

    }
}
