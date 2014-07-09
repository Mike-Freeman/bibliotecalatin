<?php

defined('_JEXEC') or die('Restricted access');

/**
 *
 * @package BookLibrary
 * @copyright Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru); Rob de Cleen(rob@decleen.com); 
 * Homepage: http://www.ordasoft.com
 * @version: 3.0 Free
 *
 * */
if (version_compare(JVERSION, "1.6.0", "lt")) {

    class JElementCategory extends JElement {

        var $_name = 'category';

        function fetchElement($name, $value, &$node, $control_name) {
            $db = JFactory::getDBO();
            $query = 'SELECT title, id as catid FROM #__booklibrary_main_categories WHERE published = 1';
            $db->setQuery($query);
            $categories = $db->loadObjectList();
            return JHTML::_('select.genericlist', $categories, '' . $control_name . '[' . $name . ']', 'class="inputbox"', 'catid', 'title', $value, $control_name . $name);
        }

    }

} else if (version_compare(JVERSION, "1.6.0", "ge") && version_compare(JVERSION, "2.5.100", "lt")) {

    class JFormFieldCategory extends JFormField {

        protected $type = 'category';

        protected function getInput() {
            $db = JFactory::getDBO();
            // Initialize variables.
            $html = array();
            $attr = '';

            // Initialize some field attributes.
            $attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
            // To avoid user's confusion, readonly="true" should imply disabled="true".
            if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
                $attr .= ' disabled="disabled"';
            }
            $attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
            $attr .= $this->multiple ? ' multiple="multiple"' : '';
            // Initialize JavaScript field attributes.
            $attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

            $query = 'SELECT title, id AS catid FROM #__booklibrary_main_categories WHERE published = 1'; // for 1.6
            $db->setQuery($query);
            $categories = $db->loadObjectList();

            $options = array();
            foreach ($categories as $item) {
                $options[] = JHtml::_('select.option', $item->catid, $item->title);
            }

            // Create a read-only list (no name) with a hidden input to store the value.
            if ((string) $this->element['readonly'] == 'true') {
                $html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
                $html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
            }
            // Create a regular list.
            else {
                $html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
            }
            return implode($html);
        }

    }

} else {
    echo "Sanity test. Error version check!";
    exit;
}
