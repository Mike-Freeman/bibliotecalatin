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

global $mosConfig_absolute_path;
//include_once( dirname(__FILE__).'/compat.joomla1.5.php' );
include_once( /*dirname(__FILE__)*/JPATH_ROOT.'/components/com_booklibrary/compat.joomla1.5.php' );
require_once ($mosConfig_absolute_path."/administrator/components/com_booklibrary/admin.booklibrary.class.conf.php");
 $db = $GLOBALS['database'];
 
function copydir($src,$dst){
  $dir = opendir($src);
     while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            @copy($src ."/". $file,$dst ."/". $file);
         /*   if(!copy($src ."/". $file,$dst ."/". $file)){
              echo "copy error! Please copy folders
                ..path_to_joomla/components/com_booklibrary/covers and ..path_to_joomla/components/com_booklibrary/ebooks
                to ..path_to_joomla/tmp/com_booklibrary/ manualy if you want to save electronic documents and picture of books";
            } */
        }
     }
}
 
 if($booklibrary_configuration['update']):
   @mkdir($mosConfig_absolute_path."/tmp/com_booklibrary/");
   @mkdir($mosConfig_absolute_path."/tmp/com_booklibrary/covers/");
   @mkdir($mosConfig_absolute_path."/tmp/com_booklibrary/ebooks/");
     $dst=$mosConfig_absolute_path."/tmp/com_booklibrary/covers";
     $src=$mosConfig_absolute_path."/components/com_booklibrary/covers";
     copydir($src,$dst);
     $dst=$mosConfig_absolute_path."/tmp/com_booklibrary/ebooks";
     $src=$mosConfig_absolute_path."/components/com_booklibrary/ebooks";
     copydir($src,$dst);
   echo 'database saved';

 else:
   echo 'no update';
   $db->SetQuery("DROP TABLE #__booklibrary");
   $db->query();
   $db->SetQuery("DROP TABLE #__booklibrary_categories");
   $db->query();
   $db->SetQuery("DROP TABLE #__booklibrary_lend");
   $db->query();
   $db->SetQuery("DROP TABLE #__booklibrary_lend_request");
   $db->query();
   $db->SetQuery("DROP TABLE #__booklibrary_review");
   $db->query();
   $db->SetQuery("DROP TABLE #__booklibrary_main_categories");
   $db->query();
   $db->SetQuery("DROP TABLE #__booklibrary_mime_types");
   $db->query();
   $db->SetQuery("DROP TABLE #__booklibrary_version");
   $db->query();
   $db->SetQuery("DROP TABLE #__booklibrary_const");
   $db->query();
   $db->SetQuery("DROP TABLE #__booklibrary_const_languages");
   $db->query();
   $db->SetQuery("DROP TABLE #__booklibrary_languages");
   $db->query();
 endif;



 
function com_uninstall()
{
echo "Uninstalled! ";
}

