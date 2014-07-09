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
/**
 * Legacy function, use <jdoc:include type="module" /> instead
 *
 * @deprecated    As of version 1.5
 */
if (!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);

if (isset($GLOBALS['mosConfig_absolute_path']))
    $mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path'];
else
    $mosConfig_absolute_path = JPATH_SITE; //JURI::base(true); // if file is calling from the module

include_once ($mosConfig_absolute_path . DS . 'components' . DS . 'com_booklibrary' . DS . 'booklibrary.main.categories.class.php');
require_once ($mosConfig_absolute_path . DS . 'components' . DS . 'com_booklibrary' . DS . 'includes' . DS . 'parameters.php');
if (version_compare(JVERSION, '3.0', 'lt')) {
    require_once (JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'menu.php' );
}
require_once ($mosConfig_absolute_path . DS . 'components' . DS . 'com_booklibrary' . DS . 'includes' . DS . 'menu.php' );
jimport('joomla.html.pagination');

if (!isset($GLOBALS['booklibrary_configuration'])) {
    require_once (JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_booklibrary' . DS . 'admin.booklibrary.class.conf.php' );
    $GLOBALS['booklibrary_configuration'] = $booklibrary_configuration;
}


if (!function_exists('mosLoadModule')) {

    function mosLoadModule($name, $style = -1) {
        ?><jdoc:include type="module" name="<?php echo $name ?>" style="<?php echo $style ?>" /><?php
    }

}

/**
 * Legacy function, using <jdoc:include type="modules" /> instead
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosMail')) {

    function mosMail($from, $fromname, $recipient, $subject, $body, $mode = 0, $cc = NULL, $bcc = NULL, $attachment = NULL, $replyto = NULL, $replytoname = NULL) {
        //return JUTility::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname );
        if (version_compare(JVERSION, '3.0.0', 'lt')) {
            return JUTility::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
        } else {
            $a = JMail::getInstance();
            $a->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
        }
    }

}

if (!function_exists("formatMoney")) {

    function formatMoney($number, $fractional = false, $pattern = ".") {


        if ($fractional) {
            $number = sprintf('%.2f', $number);
        }
        if ($pattern == ".")
            $number = str_replace(".", ",", $number);

        while (true) {
            $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1' . $pattern . '$2', $number);
            //echo $replaced."<br>";
            if ($replaced != $number) {
                $number = $replaced;
            } else {
                break;
            }
        }
        // $number = preg_replace('/\^/', $number, $pattern);
        return $number;
    }

}

if (!function_exists('mosLoadAdminModules')) {

    function mosLoadAdminModules($position = 'left', $style = 0) {

        // Select the module chrome function
        if (is_numeric($style)) {
            switch ($style) {
                case 2:
                    $style = 'xhtml';
                    break;

                case 0 :
                default :
                    $style = 'raw';
                    break;
            }
        }
        ?><jdoc:include type="modules" name="<?php echo $position ?>" style="<?php echo $style ?>" /><?php
    }

}

/**
 * Legacy function, using <jdoc:include type="module" /> instead
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosLoadAdminModule')) {

    function mosLoadAdminModule($name, $style = 0) {
        ?><jdoc:include type="module" name="<?php echo $name ?>" style="<?php echo $style ?>" /><?php
    }

}

/**
 * Legacy function, always use {@link JRequest::getVar()} instead
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosStripslashes')) {

    function mosStripslashes(&$value) {
        $ret = '';
        if (is_string($value)) {
            $ret = stripslashes($value);
        } else {
            if (is_array($value)) {
                $ret = array();
                foreach ($value as $key => $val) {
                    $ret[$key] = mosStripslashes($val);
                }
            } else {
                $ret = $value;
            }
        }
        return $ret;
    }

}

/**
 * Legacy function, use {@link JFolder::files()} or {@link JFolder::folders()} instead
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosReadDirectory')) {

    function mosReadDirectory($path, $filter = '.', $recurse = false, $fullpath = false) {
        $arr = array(null);

        // Get the files and folders
        jimport('joomla.filesystem.folder');
        $files = JFolder::files($path, $filter, $recurse, $fullpath);
        $folders = JFolder::folders($path, $filter, $recurse, $fullpath);
        // Merge files and folders into one array
        $arr = array_merge($files, $folders);
        // Sort them all
        asort($arr);
        return $arr;
    }

}

/**
 * Legacy function, use {@link JApplication::redirect() JApplication->redirect()} instead
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosRedirect')) {

    function mosRedirect($url, $msg = '') {
        //global $mainframe;
        $mainframe = JFactory::getApplication(); // for J 1.6
        $mainframe->redirect($url, $msg);
    }

}

/**
 * Legacy function, use {@link JArrayHelper::getValue()} instead
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosGetParam')) {

    function mosGetParam(&$arr, $name, $def = null, $mask = 0) {
        // Static input filters for specific settings
        static $noHtmlFilter = null;
        static $safeHtmlFilter = null;

        $var = JArrayHelper::getValue($arr, $name, $def, '');

        // If the no trim flag is not set, trim the variable
        if (!($mask & 1) && is_string($var)) {
            $var = trim($var);
        }

        // Now we handle input filtering
        if ($mask & 2) {
            // If the allow html flag is set, apply a safe html filter to the variable
            if (is_null($safeHtmlFilter)) {
                $safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);
            }
            $var = $safeHtmlFilter->clean($var, 'none');
        } elseif ($mask & 4) {
            // If the allow raw flag is set, do not modify the variable
            $var = $var;
        } else {
            // Since no allow flags were set, we will apply the most strict filter to the variable
            if (is_null($noHtmlFilter)) {
                $noHtmlFilter = JFilterInput::getInstance(/* $tags, $attr, $tag_method, $attr_method, $xss_auto */);
            }
            $var = $noHtmlFilter->clean($var, 'none');
        }
        return $var;
    }

}

/**
 * Legacy function, use {@link JEditor::save()} or {@link JEditor::getContent()} instead
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('getEditorContents')) {

    function getEditorContents($editorArea, $hiddenField) {
        jimport('joomla.html.editor');
        $editor = JFactory::getEditor();
        echo $editor->save($hiddenField);
    }

}

/**
 * Legacy function, use {@link JFilterOutput::objectHTMLSafe()} instead
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosMakeHtmlSafe')) {

    function mosMakeHtmlSafe(&$mixed, $quote_style = ENT_QUOTES, $exclude_keys = '') {
        JFilterOutput::objectHTMLSafe($mixed, $quote_style, $exclude_keys);
    }

}

/**
 * Legacy utility function to provide ToolTips
 *
 * @deprecated As of version 1.5
 */
if (!function_exists('mosToolTip')) {

    function mosToolTip($tooltip, $title = '', $width = '', $image = 'tooltip.png', $text = '', $href = '', $link = 1) {
        // Initialize the toolips if required
        static $init;
        if (!$init) {
            JHTML::_('behavior.tooltip');
            $init = true;
        }
        return JHTML::_('tooltip', $tooltip, $title, $image, $text, $href, $link);
    }

}

/**
 * Legacy function to replaces &amp; with & for xhtml compliance
 *
 * @deprecated  As of version 1.5
 */
if (!function_exists('mosTreeRecurse')) {

    function mosTreeRecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1) {
        jimport('joomla.html.html');
        return JHtml::_('menu.treerecurse', $id, $indent, $list, $children, $maxlevel, $level, $type);
    }

}

class blLittleThings {

    static function getGroupsByUser($uid, $recurse) {
        if (version_compare(JVERSION, "1.6.0", "lt")) {
            
        } else if (version_compare(JVERSION, "1.6.0", "ge") && version_compare(JVERSION, "3.5.0", "lt")) {
            $database = JFactory::getDBO();
            // Custom algorythm
            $usergroups = array();
            if ($recurse == 'RECURSE') {
                // [1]: Recurse getting the usergroups
                $id_group = array();
                $q1 = "SELECT group_id FROM `#__user_usergroup_map` WHERE user_id={$uid}";
                $database->setQuery($q1);
                $rows1 = $database->loadObjectList();
                foreach ($rows1 as $v)
                    $id_group[] = $v->group_id;
                for ($k = 0; $k < count($id_group); $k++) {
                    $q = "SELECT g2.id FROM `#__usergroups` g1 LEFT JOIN `#__usergroups` g2 ON g1.lft > g2.lft AND g1.lft < g2.rgt WHERE g1.id={$id_group[$k]} ORDER BY g2.lft";
                    $database->setQuery($q);
                    $rows = $database->loadObjectList();
                    foreach ($rows as $r)
                        $usergroups[] = $r->id;
                }
                $usergroups = array_unique($usergroups);
            }
            // [2]: Non-Recurse getting usergroups
            $q = "SELECT * FROM #__user_usergroup_map WHERE user_id = {$uid}";
            $database->setQuery($q);
            $rows = $database->loadObjectList();
            foreach ($rows as $k => $v)
                $usergroups[] = $rows[$k]->group_id;

            // If user is unregistered, Joomla contains it into standard group (Public by default).
            // So, groupId for anonymous users is 1 (by default).
            // But custom algorythm doesnt do this: if user is not autorised, he will NOT connected to any group.
            // And groupId will be 0. 
            if (count($rows) == 0)
                $usergroups[] = -2;
            return $usergroups;
        } else {
            echo "Sanity test. Error version check!";
            exit;
        }
    }

    static function getWhereUsergroupsCondition($table_alias = "c") {
        if (version_compare(JVERSION, "1.6.0", "lt")) {
            global $my;
            if (!isset($my)) { // echo "User is logged out"; 
                if ($my = JFactory::getUser())
                    $gid = $my->gid; else
                    $gid = 0;
            } else
                $gid = $my->gid;
            $usergroups_sh = array($gid, -2);
            $s = '';
            for ($i = 0; $i < count($usergroups_sh); $i++) {
                $g = $usergroups_sh[$i];
                $s .= " $table_alias.params LIKE '%,{$g}' or $table_alias.params = '{$g}' or $table_alias.params LIKE '{$g},%' or $table_alias.params LIKE '%,{$g},%' ";
                if (($i + 1) < count($usergroups_sh))
                    $s .= ' or ';
            }
            return $s;
        } else if (version_compare(JVERSION, "1.6.0", "ge") && version_compare(JVERSION, "3.5.0", "lt")) {
            $my = JFactory::getUser();
            if (isset($my->id) AND $my->id != 0)
                $usergroups_sh = blLittleThings::getGroupsByUser($my->id, '');
            else
                $usergroups_sh = array();
            $usergroups_sh[] = -2;

            $s = '';
            for ($i = 0; $i < count($usergroups_sh); $i++) {
                $g = $usergroups_sh[$i];
                $s .= " $table_alias.params LIKE '%,{$g}' or $table_alias.params = '{$g}' or $table_alias.params LIKE '{$g},%' or $table_alias.params LIKE '%,{$g},%' ";
                if (($i + 1) < count($usergroups_sh))
                    $s .= ' or ';
            }
            return $s;
        } else {
            echo "Sanity test. Error version check!";
            exit;
        }
    }

}

if (!function_exists('checkAccessBL')) {

    function checkAccessBL($accessgroupid, $recurse, $usersgroupid, $acl) {
        if (!is_array($usersgroupid)) {
            $usersgroupid = $usersgroupid * 1;
            if (!(is_int($usersgroupid)))
                return false;
            else {
                if (is_int($usersgroupid) AND isset($usersgroupid) AND $usersgroupid > 0) {
                    $t = $usersgroupid;
                    $usersgroupid = (array) $usersgroupid; // force to array
                    $usersgroupid[] = $t;
                } elseif (is_int($usersgroupid) AND isset($usersgroupid) AND $usersgroupid == 0) {
                    $usersgroupid = (array) $usersgroupid; // force to array
                    $usersgroupid[] = 0;
                }
            }
        }

        //parse usergroups
        $tempArr = array();
        $tempArr = explode(',', $accessgroupid);

        for ($i = 0; $i < count($tempArr); $i++) {
            if (( (!is_array($usersgroupid) && $tempArr[$i] == $usersgroupid )
                    OR ( is_array($usersgroupid) && in_array($tempArr[$i], $usersgroupid) ) )
                    || $tempArr[$i] == -2) {
                //allow access
                return true;
            } else {
                if ($recurse == 'RECURSE') {
                    if (is_array($usersgroupid)) {
                        for ($j = 0; $j < count($usersgroupid); $j++)
                            if (in_array($usersgroupid[$j], $tempArr))
                                return 1;
                    } else {
                        if (in_array($usersgroupid, $tempArr))
                            return 1;
                    }
                }
            }
        } // end for
        //deny access
        return 0;
    }

// End of checkAccessBL ()
}

// [author]: Wonderer
// [description]: Analogue of 
//    $usergroups = $acl->get_group_parents($my->gid,'ARO','RECURSE');
//    for 1.6 
// [call]: $usergroups = getGroupsByUser ($my->id,'RECURSE');   $usergroups = getGroupsByUser ($my->id,'');
// [date]: 03 June 2011
if (!function_exists('getGroupsByUser')) {

    function getGroupsByUser($uid, $recurse) {
        if (version_compare(JVERSION, "1.6.0", "lt")) {
            
        } else if (version_compare(JVERSION, "1.6.0", "ge") && version_compare(JVERSION, "3.5.0", "lt")) {
            $database = JFactory::getDBO();
            // Custom algorythm
            $usergroups = array();
            if ($recurse == 'RECURSE') {
                // [1]: Recurse getting the usergroups
                $id_group = array();
                $q1 = "SELECT group_id FROM `#__user_usergroup_map` WHERE user_id={$uid}";
                $database->setQuery($q1);
                $rows1 = $database->loadObjectList();
                foreach ($rows1 as $v)
                    $id_group[] = $v->group_id;
                for ($k = 0; $k < count($id_group); $k++) {
                    $q = "SELECT g2.id FROM `#__usergroups` g1 LEFT JOIN `#__usergroups` g2 ON g1.lft > g2.lft AND g1.lft < g2.rgt WHERE g1.id={$id_group[$k]} ORDER BY g2.lft";
                    $database->setQuery($q);
                    $rows = $database->loadObjectList();
                    foreach ($rows as $r)
                        $usergroups[] = $r->id;
                }
                $usergroups = array_unique($usergroups);
            }
            // [2]: Non-Recurse getting usergroups
            $q = "SELECT * FROM #__user_usergroup_map WHERE user_id = {$uid}";
            $database->setQuery($q);
            $rows = $database->loadObjectList();
            foreach ($rows as $k => $v)
                $usergroups[] = $rows[$k]->group_id;

            // If user is unregistered, Joomla contains it into standard group (Public by default).
            // So, groupId for anonymous users is 1 (by default).
            // But custom algorythm doesnt do this: if user is not autorised, he will NOT connected to any group.
            // And groupId will be 0. 
            if (count($rows) == 0)
                $usergroups[] = -2;
            return $usergroups;
        } else {
            echo "Sanity test. Error version check!";
            exit;
        }
    }

}


// [author]: Wonderer
// [description]: 
//  Returns a string for WHERE condition
// instead of using $usergroups.
// Now we replace an old (c.params IN ('.$usergroups.') construction 
// with ({$s}).
// [call]: $s = getWhereUsergroupsString ( "alias_name" );  
// (alias_name) its a `#__booklibrary_main_categories` table alias.
// (alias_name) depends of the particular query, as usual its "c", "cc" or something like this.
// [date]: 13 June 2011
if (!function_exists('getWhereUsergroupsString')) {

    function getWhereUsergroupsString($table_alias) {
        global $my;

        if (isset($my->id) AND $my->id != 0) {

            $usergroups_sh = getGroupsByUser($my->id, '');

            //$usergroups_sh = '-2'.$usergroups_sh;   
        } else {
            $usergroups_sh = array();
        }
        $usergroups_sh[] = -2;


        $s = '';
        for ($i = 0; $i < count($usergroups_sh); $i++) {
            $g = $usergroups_sh[$i];
            $s .= " $table_alias.params LIKE '%,{$g}' or $table_alias.params = '{$g}' or $table_alias.params LIKE '{$g},%' or $table_alias.params LIKE '%,{$g},%' ";
            if (($i + 1) < count($usergroups_sh))
                $s .= ' or ';
        }
        return $s;
    }

}


if (!function_exists('addSubmenuBookLibrary')) {

    function addSubmenuBookLibrary($vName) {
        if (!defined('_BOOKLIBRARY_HEADER_NUMBER')) loadConstBook();
        JSubMenuHelper::addEntry(
                JText::_(_BOOKLIBRARY_HEADER_NUMBER), 'index.php?option=com_booklibrary', $vName == 'Books'
        );

        JSubMenuHelper::addEntry(
                JText::_(_BOOKLIBRARY_CATEGORIES_NAME), 'index.php?option=com_booklibrary&section=categories', $vName == 'Categories'
        );
        
        JSubMenuHelper::addEntry(
                JText::_(_BOOKLIBRARY_ADMIN_SHOW_LEND_REQUESTS), 'index.php?option=com_booklibrary&task=lend_requests', $vName == 'Lend Requests'
        );
        JSubMenuHelper::addEntry(
                JText::_(_BOOKLIBRARY_ADMIN_SHOW_LANGUAGE_MANAGER), 'index.php?option=com_booklibrary&section=language_manager', $vName == 'Language Manager'
        );
        JSubMenuHelper::addEntry(
                JText::_(_BOOKLIBRARY_ADMIN_SHOW_IMPORT_EXPORT), 'index.php?option=com_booklibrary&task=show_import_export', $vName == 'Import/Export'
        );
        JSubMenuHelper::addEntry(
                JText::_(_BOOKLIBRARY_ADMIN_SHOW_SETTINGS_FRONTEND), 'index.php?option=com_booklibrary&task=config_frontend', $vName == 'Settings Frontend'
        );
        JSubMenuHelper::addEntry(
                JText::_(_BOOKLIBRARY_ADMIN_SHOW_SETTINGS_BACKEND), 'index.php?option=com_booklibrary&task=config_backend', $vName == 'Settings Backend'
        );
        JSubMenuHelper::addEntry(
                JText::_(_BOOKLIBRARY_ADMIN_ABOUT_ABOUT), 'index.php?option=com_booklibrary&task=about', $vName == 'About'
        );
    }

}


if (!function_exists('loadConstBook')) {

    function loadConstBook() {
         
          global $database, $mosConfig_absolute_path;
          $is_exception = false;
          
          $database->setQuery("SELECT * FROM #__booklibrary_languages");
          $langs = $database->loadObjectList();
          
          $component_path = JPath::clean($mosConfig_absolute_path . '/components/com_booklibrary/language/');
          $component_layouts = array();

          if (is_dir($component_path)
              && ($component_layouts = JFolder::files($component_path, '^[^_]*\.php$', false, true))
          ) {
          
              //check and add constants file in DB
             foreach ($component_layouts as $i => $file) {
                $file_name = pathinfo($file);
                $file_name = $file_name['filename'];
            
             if ($file_name === 'constant') {
                   $database->setQuery("SELECT id FROM #__booklibrary_const");
                   $idConst = $database->loadResult();
                   if(empty($idConst)) {
                      require($mosConfig_absolute_path . "/components/com_booklibrary/language/$file_name.php");
                      foreach ( $constMas as $mas ) {
                          $database->setQuery("INSERT INTO #__booklibrary_const (const, sys_type) VALUES ('".$mas["const"]."','".$mas["sys_type"]."')");
                          $database->query(); 
                      }
                   }
                }
             }
        
            //check and add new text files in DB
            foreach ($component_layouts as $i => $file) {
                $isLang = 0;
                $file_name = pathinfo($file);
                $file_name = $file_name['filename'];
                $LangLocal = '';
                $isLang = 1;

             if ($file_name != 'constant') {
               
                    require($mosConfig_absolute_path . "/components/com_booklibrary/language/$file_name.php");

                    foreach ($langs as $lang) {
                        if ($lang->lang_code == $LangLocal['lang_code'] || $lang->title == $LangLocal['title']) {
                            $isLang = 0;
                            break;
                        } else {
                            $isLang = 1;
                        }
                    }
                    
                    try{
                      
                    if ($isLang ===1 ) {

                        $sql= "INSERT INTO #__booklibrary_languages (lang_code,title) VALUES ('" . $LangLocal['lang_code'] . "','" . $LangLocal['title'] . "')";
                        $database->setQuery("INSERT INTO #__booklibrary_languages (lang_code,title) VALUES ('" . $LangLocal['lang_code'] . "','" . $LangLocal['title'] . "')");
                        $database->query();
                       
                        echo $database->getErrorMsg();
                        $idLang = $database->insertid();

                        foreach ($constLang as $item) {
                            $sql = "SELECT id FROM #__booklibrary_const WHERE const = '" . $item['const'] . "'" ;
                            $database->setQuery($sql);
                            $idConst = $database->loadResult();
                            if(!array_key_exists ( 'value_const'  , $item ) ){
                              echo "<br />:loadConstBook, This value constant ".$item['const']." for this lang file ". $LangLocal['title']." not exist";
                            } else {
                              $database->setQuery("INSERT INTO #__booklibrary_const_languages (fk_constid,fk_languagesid,value_const) VALUES ($idConst, $idLang, " . $database->quote($item['value_const']) . ")");
                              $database->query();
                            }
                        }
                    }
                    } catch (Exception $e) {
                        $is_exception = true;
                        echo 'Send exception, please write to admin for language check: ',  $e->getMessage(), "\n";
                    }
                    
                  
                }
           }
           
           if($is_exception) language_check();
           
          //if text constant missing recover they in DB
          if (!defined('_BOOKLIBRARY_HEADER_NUMBER')) {

               $query = "SELECT c.const, cl.value_const ";
               $query .= "FROM #__booklibrary_const_languages as cl ";
               $query .= "LEFT JOIN #__booklibrary_languages AS l ON cl.fk_languagesid=l.id ";
               $query .= "LEFT JOIN #__booklibrary_const AS c ON cl.fk_constid=c.id ";
               $query .= "WHERE l.lang_code = 'en-GB'";
               $database->setQuery($query);
               $langConst = $database->loadObjectList();

               foreach ($langConst as $item) {
                  define($item->const, $item->value_const);
               }
          }
       }
        //if some language file missing recover it
        $component_path = JPath::clean($mosConfig_absolute_path . '/components/com_booklibrary/language/');
        $component_layouts = array();
        if (is_dir($component_path) && ($component_layouts = JFolder::files($component_path, '^[^_]*\.php$', false, true))) {
            foreach ($component_layouts as $i => $file) {
                $isLang = 0;
                $file_name = pathinfo($file);
                $file_name = $file_name['filename'];
                if ($file_name != 'constant') {
                    require($mosConfig_absolute_path . "/components/com_booklibrary/language/$file_name.php");
                    //$fileMas[] = $LangLocal;
                    $fileMas[] = $LangLocal['title'];
                    
                }
            }
        }
        
        $database->setQuery("SELECT title FROM #__booklibrary_languages");
        if (version_compare(JVERSION, '3.0', 'lt')) {
            $langs = $database->loadResultArray();
        } else {
            $langs = $database->loadColumn();
        }
    
        if (count($langs) > count($fileMas)) {
            $results = array_diff($langs, $fileMas);
            foreach ($results as $result) {
    
                $database->setQuery("SELECT lang_code FROM #__booklibrary_languages WHERE title = '$result'");
                $lang_code = $database->loadResult();
                $langfile = "<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );";
                $langfile .= "\n\n/**\n*\n* @package  BookLibrary\n* @copyright 2012 Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru); Rob de Cleen(rob@decleen.com);\n* Homepage: http://www.ordasoft.com\n* @version: 3.0 Pro\n*\n* */\n\n";
                $langfile .= "\$LangLocal = array('lang_code'=>'$lang_code', 'title'=>'$result');\n";
                $langfile .= "\$constLang = array();\n";
    
                $query = "SELECT c.const, cl.value_const ";
                $query .= "FROM #__booklibrary_const_languages as cl ";
                $query .= "LEFT JOIN #__booklibrary_languages AS l ON cl.fk_languagesid=l.id ";
                $query .= "LEFT JOIN #__booklibrary_const AS c ON cl.fk_constid=c.id ";
                $query .= "WHERE l.title = '$result'";
    
                $database->setQuery($query);
                $constlanguages = $database->loadObjectList();
    
                foreach ($constlanguages as $constlanguage) {
                    $langfile .= "\$constLang[] = array('const'=>'" . $constlanguage->const . "', 'value_const'=>'" . mysql_real_escape_string($constlanguage->value_const) . "');\n";
                }
    
                // Write out new initialization file
                $fd = fopen($mosConfig_absolute_path . "/components/com_booklibrary/language/$result.php", "w") or die("Cannot create language file.");
                fwrite($fd, $langfile);
                fclose($fd);
            }
        }

        //language_check();

    } 
}
if (!function_exists('language_check')) {
    function language_check($component_db_name = 'booklibrary' ) {
         global $database;

               
         $database->setQuery("SELECT * FROM #__".$component_db_name."_languages");
         $langIds = $database->loadObjectList();
         
         print_r("These constants exit in Languages files but not exist in file constants:<br />");
         foreach ($langIds as $langId){
         print_r("<br />Languages: ".$langId->title."<br />");
                $query = " SELECT  l1.*  FROM    #__".$component_db_name."_const_languages as l1 ";
                $query .= " WHERE   l1.fk_languagesid = ".$langId->id." and NOT EXISTS ";
                $query .= " ( SELECT  lc.*  FROM #__".$component_db_name."_const as lc ";
                $query .= " WHERE   lc.id = l1.`fk_constid` ) ";
                $database->setQuery($query);
                $badLangConsts = $database->loadObjectList();
                print_r($badLangConsts);
         }

         print_r("<br />These constants exit in file constants but not exist in Languages files:<br />");
         foreach ($langIds as $langId){

                print_r("<br />Languages: ".$langId->title."<br />");
                $query = " SELECT  lc.*  FROM    #__".$component_db_name."_const as lc ";
                $query .= " WHERE  NOT EXISTS ";
                $query .= " ( SELECT  l1.*  FROM #__".$component_db_name."_const_languages as l1 ";
                $query .= " WHERE lc.id = l1.`fk_constid` and l1.fk_languagesid = ".$langId->id.") ";
                $database->setQuery($query);
                $badLangConsts = $database->loadObjectList();
                print_r($badLangConsts);
         }
    }
}
if (!function_exists('remove_langs')) {
    function remove_langs($component_db_name = 'booklibrary' ) {
         global $database;

               
        $query = " TRUNCATE TABLE #__".$component_db_name."_languages; ";
        $database->setQuery($query);
        $database->query();
        
        $query = " TRUNCATE TABLE #__".$component_db_name."_const; ";
        $database->setQuery($query);
        $database->query();
        
        $query = " TRUNCATE TABLE #__".$component_db_name."_const_languages ;";
        $database->setQuery($query);
        $database->query();
        
    }
}
if (!function_exists('data_transformer')) {
    function data_transformer($date, $date_format = "from") {
        if ($date_format == "from") {
            global $booklibrary_configuration;
            $mask = str_replace("%", "", $booklibrary_configuration['date_format'] . " " . $booklibrary_configuration['datetime_format']);
        } elseif ($date_format == "to") {
            $mask = "Y-m-d H:i:s";
        }
        $unix_time = strtotime($date);
        if ($unix_time > 1000 && $unix_time < 2147483647) {
            $data_transform = date($mask, strtotime($date));
            return $data_transform;
        } else {
            return $mydate;
        }
    }
}
class getLayoutPathBook {

    static function getLayoutPathCom($components, $type, $layout = 'default') {
        $template = JFactory::getApplication()->getTemplate();
        $defaultLayout = $layout;

        if (strpos($layout, ':') !== false) {
            // Get the template and file name from the string
            $temp = explode(':', $layout);
            $template = ($temp[0] == '_') ? $template : $temp[0];
            $layout = $temp[1];
            $defaultLayout = ($temp[1]) ? $temp[1] : 'default';
        }

        // Build the template and base path for the layout
        $tPath = JPATH_THEMES . '/' . $template . '/html/' . $components . '/' . $layout . '.php';

        $cPath = JPATH_BASE . '/components/' . $components . '/views/' . $type . '/tmpl/' . $layout . '.php';

        // If the template has a layout override use it
        if (file_exists($tPath)) {
            return $tPath;
        } else {
            return $cPath;
        }
    }

}



