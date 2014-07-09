 
<?php
/**
 * FileName: category.php
 * Date:July 2011
 * License: GNU General Public License 
 * JOS Version #: 1.6.x
 * Development OrdaSoft(http://ordasoft.com)
 */
defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION, "1.6.0", "lt")){
    class JElementAllcategorylayout extends JElement{
        var $_name = 'allcategorylayout';
        function fetchElement($name, $value, &$node, $control_name){
            $component_path = JPath::clean(JPATH_SITE. '/components/com_booklibrary/views/all_categories/tmpl');
            $component_layouts = array();
            $layouts = array();
            if (is_dir($component_path) && ($component_layouts = JFolder::files($component_path, '^[^_]*\.php$', false, true))){
		$layout = new stdClass;
		$layout->layout = "";
		$layout->title  = "Use Global";
		$layouts[] = $layout;
                foreach ($component_layouts as $i => $file){
                    $select_file_name = pathinfo($file);
                    $select_file_name = $select_file_name['filename'];
                    $layout = new stdClass;
                    $layout->layout = $select_file_name;
                    $layout->title  = $select_file_name;
                    $layouts[] = $layout;
                }
            }
            return JHTML::_('select.genericlist', $layouts, ''.$control_name.'['.$name.']', 'class="inputbox"','layout' , 'title', $value, $control_name.$name);
        }
    }
} else if (version_compare(JVERSION, "1.6.0", "ge") && version_compare(JVERSION, "3.5.0", "lt")){
    class JFormFieldAllcategorylayout extends JFormField{
        protected $type = 'allcategorylayout';
        protected function getInput(){
            $component_path = JPath::clean(JPATH_SITE. '/components/com_booklibrary/views/all_categories/tmpl');
            $component_layouts = array();
            $options = array();
            if (is_dir($component_path) && ($component_layouts = JFolder::files($component_path, '^[^_]*\.php$', false, true))){
		$options[] = JHtml::_('select.option', '', 'Use Global');
                foreach ($component_layouts as $i => $file){
                    $select_file_name = pathinfo($file);
                    $select_file_name = $select_file_name['filename'];
                    $options[] = JHtml::_('select.option', $select_file_name, $select_file_name);
                }
            }
            return  JHtml::_('select.genericlist', $options, $this->name, 'class="inputbox"', 'value', 'text', $this->value, $this->id);
        }
    }
} else {echo "Sanity test. Error version check!"; exit;}
