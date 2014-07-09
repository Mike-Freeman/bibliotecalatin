<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
* This file provides compatibility for BookLibrary on Joomla! 1.0.x and Joomla! 1.5
*
*/


// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Utility class for drawing admin menu HTML elements
 *
 * @static
 * @package 	Joomla.Legacy
 * @subpackage	1.5
 * @since	1.0
 * @deprecated	As of version 1.5
 */
if ( !class_exists('mosAdminMenus')) {
  class mosAdminMenus
  {
  
  ////////////////////////////////////////////////////////////////////////////////////////////
	  /**
    * Legacy function, use {@link JHTML::_('list.users', )} instead
    *
    * @deprecated	As of version 1.5
    */
	  function UserSelect( $name, $active, $nouser=0, $javascript=NULL, $order='name', $reg=1 )
	  {
		  return JHTML::_('list.users', $name, $active, $nouser, $javascript, $order, $reg);
	  }
  function SpecificOrdering( &$row, $id, $query, $neworder=0 )
	  {
		  return JHTML::_('list.specificordering', $row, $id, $query, $neworder);
	  }
  
  function Access( &$row )
	  {
		  return JHTML::_('list.accesslevel', $row);
	  }
  
	  /**
    * Legacy function, use {@link JHTML::_('list.positions', )} instead
    *
    * @deprecated	As of version 1.5
    */
	  function Positions( $name, $active=NULL, $javascript=NULL, $none=1, $center=1, $left=1, $right=1, $id=false )
	  {
		  return JHTML::_('list.positions', $name, $active, $javascript, $none, $center, $left, $right, $id);
	  }
  
  function SelectSection( $name, $active=NULL, $javascript=NULL, $order='ordering' )
	  {
		  //return JHTML::_('list.section', $name, $active, $javascript, $order);
		  return JHtml::_('list.category', $name, $active, $javascript, $order);
		  ///return JHtmlList::category ('list.section', $name);
	  }
  ///////////////////////////////////////////////////////////////////////////////////////////////////
  }
}	







