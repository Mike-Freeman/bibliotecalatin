<?php
defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION, "1.6.0", "lt")){
    class JElementCategorylay extends JElement{
        var $_name = 'categorylay'; 
        function fetchElement($name, $value, &$node, $control_name){
            $db = JFactory::getDBO();
            $query = 'SELECT title, id as catid FROM #__booklibrary_main_categories WHERE published = 1';
            $db->setQuery($query);
            $categories = $db->loadObjectList();
            return JHTML::_('select.genericlist', $categories, ''.$control_name.'['.$name.']', 'class="inputbox"','catid' , 'title', $value, $control_name.$name);
        }
    }
} else if (version_compare(JVERSION, "1.6.0", "ge") && version_compare(JVERSION, "3.5.0", "lt")){
class JFormFieldCategorylay extends JFormField{
        protected $type = 'categorylay';
        protected function getInput(){
            $db = JFactory::getDBO();
            // Initialize variables.
            $html = array();
            $attr = '';
            // Initialize some field attributes.
            $attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
            // To avoid user's confusion, readonly="true" should imply disabled="true".
            if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true'){
                $attr .= ' disabled="disabled"';
            }
            $attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
            $attr .= $this->multiple ? ' multiple="multiple"' : '';
            // Initialize JavaScript field attributes.
            $attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

            $query = 'SELECT title, id AS catid FROM #__booklibrary_main_categories WHERE published = 1'; 
            $db->setQuery( $query );
            $categories = $db->loadObjectList();

            $options = array();

            foreach ($categories as $item) $options[] = JHtml::_('select.option', $item->catid, $item->title);

            // Create a read-only list (no name) with a hidden input to store the value.
            if ((string) $this->element['readonly'] == 'true'){
                $html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
                $html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
            }
            // Create a regular list.
            else{
                $html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
            }
            return implode($html);
        }
    }
} else {echo "Sanity test. Error version check!"; exit;}
