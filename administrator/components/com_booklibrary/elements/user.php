<?php 
defined('_JEXEC') or die('Restricted access');
//return;
/**
*
* @package BookLibrary
* @copyright Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru); Rob de Cleen(rob@decleen.com); 
* Homepage: http://www.ordasoft.com
* @version: 3.0 Free
*
**/

if (version_compare(JVERSION, "1.6.0", "lt")){
    class JElementUser extends JElement{
        function fetchElement($name, $value, &$node, $control_name){
            $db = JFactory::getDBO();
            $query = "SELECT u.name AS user
                      FROM `#__users` AS u
                      LEFT JOIN `#__booklibrary` AS b ON b.owneremail=u.email
                      WHERE b.published = 1
                      GROUP BY u.name
                      ORDER BY u.name";
            $db->setQuery($query);
            $showownerbooks = $db->loadObjectList();
            return JHTML::_('select.genericlist', $showownerbooks, ''.$control_name.'['.$name.']', 'class="inputbox"', 'user', 'user', $value, $control_name.$name);
        }
    }
} else if (version_compare(JVERSION, "1.6.0", "ge") ){
    class JFormFieldUser extends JFormField{
        protected function getInput(){
            
            $db = JFactory::getDBO();
            $query = "SELECT u.name AS user, u.username AS username, u.name AS title, u.email
                      FROM #__users AS u, #__booklibrary AS b
                      WHERE b.owneremail=u.email AND b.published=1
                      GROUP BY u.name
                      ORDER BY u.name";
            $db->setQuery( $query );
            $showownerbooks = $db->loadObjectList();
            return JHtml::_('select.genericlist', $showownerbooks, $this->name, 'class="inputbox"', 'user', 'user', $this->value, $this->name);
        }
    }
} else {echo "Sanity test. Error version check!"; exit;}
