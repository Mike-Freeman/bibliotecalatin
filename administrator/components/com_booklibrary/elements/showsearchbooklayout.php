<?php
defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION, "1.6.0", "lt")){
    class JElementShowSearchBooklayout extends JElement{
        var $_name = 'showsearchbooklayout';
        function fetchElement($name, $value, &$node, $control_name){
            $component_path = JPath::clean(JPATH_SITE. '/components/com_booklibrary/views/show_search_book/tmpl');
            $component_layouts = array();
            $layouts = array();
            if (is_dir($component_path) && ($component_layouts = JFolder::files($component_path, '^[^_]*\.php$', false, true))){
                foreach ($component_layouts as $i => $file){
                    $select_file_name = pathinfo($file);
                    $select_file_name = $select_file_name['filename'];
                    $layout = new stdClass;
                    $layout->layout = $select_file_name;
                    $layout->title = $select_file_name;
                    $layouts[] = $layout;
                }
            }
            return JHTML::_('select.genericlist', $layouts, ''.$control_name.'['.$name.']', 'class="inputbox"','layout' , 'title', $value, $control_name.$name);
        }
    }
} else if (version_compare(JVERSION, "1.6.0", "ge") && version_compare(JVERSION, "3.5.0", "lt")){
    class JFormFieldShowSearchBooklayout extends JFormField{
        protected function getInput(){
            $component_path = JPath::clean(JPATH_SITE. '/components/com_booklibrary/views/show_search_book/tmpl');
            $component_layouts = array();
            $layouts = array();
            if (is_dir($component_path) && ($component_layouts = JFolder::files($component_path, '^[^_]*\.php$', false, true))){
                foreach ($component_layouts as $i => $file){
                    $select_file_name = pathinfo($file);
                    $select_file_name = $select_file_name['filename'];
                    $layout = new stdClass;
                    $layout->layout = $select_file_name;
                    $layout->title = $select_file_name;
                    $layouts[] = $layout;
                }
            }
            return JHtml::_('select.genericlist', $layouts, $this->name, 'class="inputbox"', 'layout', 'title', $this->value, $this->name);
        }
    }
} else {echo "Sanity test. Error version check!"; exit;}
