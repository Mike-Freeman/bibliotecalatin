<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
*
* @package Booklibrary
* @copyright 2011 Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru);Rob de Cleen(rob@decleen.com)
* Homepage: http://www.joomlawebserver.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @version: 2.1
**/
if(defined("DS")!=true){
    define('DS', DIRECTORY_SEPARATOR);
    }
if (version_compare(JVERSION, "1.6.0", "lt"))
{ 

if (!JComponentHelper::isEnabled('com_booklibrary', true)) {
	return JError::raiseError(JText::_('Booklibrary Error'), JText::_('booklibrary is not installed on your system'));
}

require_once ( JPATH_BASE .DS.'components'.DS.'com_booklibrary'.DS.'functions.php' );






  function plgSearchBooklibAreas() {
		static $areas = array(
			'booklibrary' => 'BookLibrary'
			);
			return $areas;
	}

  
 
  
 function onContentSearch_booklibrary ($text ,$phrase='', $ordering='', $areas=null) {

      if (is_array( $areas )) {
        if (!array_intersect( $areas, array_keys( plgSearchBooklibAreas() ) )) {
          return array();
          } 
        } 

if( !function_exists( 'sefreltoabs')) {
  function sefRelToAbs( $value ) {
    //Need check!!!

    // Replace all &amp; with & as the router doesn't understand &amp;
    $url = str_replace('&amp;', '&', $value);
    if(substr(strtolower($url),0,9) != "index.php") return $url;
    $uri    = JURI::getInstance();
    $prefix = $uri->toString(array('scheme', 'host', 'port'));
    return $prefix.JRoute::_($url);
  }
}  

  
    $database	= JFactory::getDbo();
     $db		= JFactory::getDbo();
		$mainframe = JFactory::getApplication();

 		$my	= JFactory::getUser();
 	//	$groups	= implode(',', $my->getAuthorisedViewLevels());
 		
		$component			=	'com_booklibrary';
		$paramsC			= JComponentHelper::getParams($component) ;
 		
		$display_access_category 	= $paramsC->get( 'display_access_category', 1 );
 
     $s = getWhereUsergroupsString("c");
   

  
  
 	$database->setQuery("SELECT id  FROM #__menu WHERE   link LIKE'%option=com_booklibrary%' AND params LIKE '%back_button%'  ");;
 	$ItemId_tmp = $database->loadResult();

	$order = '';
  switch($ordering)
  {
      case 'newest':
      $order = 'ORDER BY bl.id DESC';
      break;
      case 'oldest':
      $order = 'ORDER BY bl.id';
      break;
      case 'popular':
      $order = 'ORDER BY bl.hits';
      break;
      case 'alpha':
      $order = 'ORDER BY title';
      break;
      case 'category':
      $order = 'ORDER BY category';
      break;
  }

	$text = trim($text);
  if ($text == '') return array();
   
   switch($phrase)
   {
		case 'exact':
        $text = preg_replace ('/\s/',' ',trim( $text ));
        $query = "SELECT bl.title AS title,"
                ." bl.date AS created,"
                ." bl.comment AS text,"            
                ." CONCAT( 'index.php?option=com_booklibrary"
                   ."&task=view&id=', bl.id,'&Itemid=', $ItemId_tmp'&catid=',bc.catid) AS href,"
                ." '2' AS browsernav,"
          ." 'Booklibrary' AS section,"
          ." c.title AS category, bc.catid AS catid"
                ." FROM #__booklibrary AS bl, #__booklibrary_main_categories AS c, #__booklibrary_categories AS bc"
                ." WHERE c.id=bc.catid AND bc.bookid=bl.id"
                ." AND ({$s}) AND c.published='1'"
                ." AND (bl.title  LIKE '%$text%'"
                ." OR bl.isbn LIKE  '%$text%'"
                ." OR bl.authors  LIKE '%$text%'"
                ." OR bl.manufacturer  LIKE '%$text%'"
                ." OR bl.comment  LIKE '%$text%')"
                ." AND bl.published = '1'"
                ." GROUP BY bl.id"
                ." $order";
			break;
		case 'all':
		case 'any':
		default:
        $text =  preg_replace ('/\s\s+/',' ',trim( $text ));

        $words = explode( ' ', $text );

        $wheres = array();

          foreach ($words as $word) {
            $word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
            $wheres2 	= array();
            $wheres2[] 	= "bl.title LIKE $word";
            $wheres2[] 	= " bl.isbn LIKE $word";
            $wheres2[] 	= " bl.authors LIKE $word";
            $wheres2[] 	= " bl.manufacturer LIKE $word";
            $wheres2[] 	= " bl.comment LIKE $word";

            $wheres[] 	= implode( ' OR ', $wheres2 );
          }
          $where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';

        $query = "SELECT bl.title AS title,"
                ." bl.date AS created,"
                ." bl.comment AS text,"            
                ." CONCAT( 'index.php?option=com_booklibrary"
                   ."&task=view&id=', bl.id,'&Itemid=', $ItemId_tmp'&catid=',bc.catid) AS href,"
                ." '2' AS browsernav,"
                 ." 'Booklibrary' AS section,"
                 ." c.title AS category"
                ." FROM #__booklibrary AS bl,#__booklibrary_main_categories AS c, #__booklibrary_categories AS bc"
                  ." WHERE c.id=bc.catid AND bc.bookid=bl.id AND $where "
                ." AND bl.published = '1' "
                ." AND ({$s}) AND c.published='1' "
                ." GROUP BY bl.id"
                ." $order";
			break;
   }
	
   $database->setQuery( $query );
   $rows = $database->loadObjectList();
	
   for($i=0;$i<count($rows);$i++) {
       $rows[$i]->Itemid=$ItemId_tmp;
   }    
   return $rows;  
      
  }
  


}

if (version_compare(JVERSION, "1.6.0", "lt"))
{ 
  $mainframe->registerEvent( 'onSearch',  'onContentSearch_booklibrary' );

$mainframe->registerEvent( 'onSearchAreas', 'plgSearchBooklibAreas' );	
} 
else
{
//===========================================JooMla 2.5.3===========================================

jimport('joomla.plugin.plugin');

if (!JComponentHelper::isEnabled('com_booklibrary', true)) {
	return JError::raiseError(JText::_('Booklibrary Error'), JText::_('booklibrary is not installed on your system'));
}

require_once ( JPATH_BASE .DS.'components'.DS.'com_booklibrary'.DS.'functions.php' );

class plgSearchBooklibrarySearch extends JPlugin {

public function __construct(& $subject, $config)
	{

		parent::__construct($subject, $config);
		$this->loadLanguage();

	}


  function onContentSearchAreas() {
		static $areas = array(
			'booklibrary' => 'BookLibrary'
			);
			return $areas;
	}

  
  public function onSearchAreas () {
   
    static $areas = array(
        'booklibrary' => 'BookLibrary'
      );
    return $areas;
  }
  
  public function onContentSearch ($text ,$phrase='', $ordering='', $areas=null) {

      if (is_array( $areas )) {
        if (!array_intersect( $areas, array_keys( $this->onSearchAreas() ) )) {
          return array();
          } 
        } //$this->onSearchAreas()

if( !function_exists( 'sefreltoabs')) {
  function sefRelToAbs( $value ) {
    //Need check!!!

    // Replace all &amp; with & as the router doesn't understand &amp;
    $url = str_replace('&amp;', '&', $value);
    if(substr(strtolower($url),0,9) != "index.php") return $url;
    $uri    = JURI::getInstance();
    $prefix = $uri->toString(array('scheme', 'host', 'port'));
    return $prefix.JRoute::_($url);
  }
}  

  
    $database	= JFactory::getDbo();
     $db		= JFactory::getDbo();
		$mainframe = JFactory::getApplication();

 		$my	= JFactory::getUser();
 		$groups	= implode(',', $my->getAuthorisedViewLevels());
 		
		$component			=	'com_booklibrary';
		$paramsC			= JComponentHelper::getParams($component) ;
 		
		$display_access_category 	= $paramsC->get( 'display_access_category', 1 );
 
     $s = getWhereUsergroupsString("c");
   

  
  
 	$database->setQuery("SELECT id  FROM #__menu WHERE   link LIKE'%option=com_booklibrary%' AND params LIKE '%back_button%'  ");;
 	$ItemId_tmp = $database->loadResult();

	$order = '';
  switch($ordering)
  {
      case 'newest':
      $order = 'ORDER BY bl.id DESC';
      break;
      case 'oldest':
      $order = 'ORDER BY bl.id';
      break;
      case 'popular':
      $order = 'ORDER BY bl.hits';
      break;
      case 'alpha':
      $order = 'ORDER BY title';
      break;
      case 'category':
      $order = 'ORDER BY category';
      break;
  }

	$text = trim($text);
  if ($text == '') return array();
   
   switch($phrase)
   {
		case 'exact':
        $text = preg_replace ('/\s/',' ',trim( $text ));
        $query = "SELECT bl.title AS title,"
                ." bl.date AS created,"
                ." bl.comment AS text,"            
                ." CONCAT( 'index.php?option=com_booklibrary"
                   ."&task=view&id=', bl.id,'&Itemid=', $ItemId_tmp,'&catid=',bc.catid) AS href,"
                ." '2' AS browsernav,"
          ." 'Booklibrary' AS section,"
          ." c.title AS category, bc.catid AS catid"
                ." FROM #__booklibrary AS bl, #__booklibrary_main_categories AS c, #__booklibrary_categories AS bc"
                ." WHERE c.id=bc.catid AND bc.bookid=bl.id"
                ." AND ({$s}) AND c.published='1'"
                ." AND (bl.title  LIKE '%$text%'"
                ." OR bl.isbn LIKE  '%$text%'"
                ." OR bl.authors  LIKE '%$text%'"
                ." OR bl.manufacturer  LIKE '%$text%'"
                ." OR bl.comment  LIKE '%$text%')"
                ." AND bl.published = '1'"
                ." GROUP BY bl.id"
                ." $order";
			break;
		case 'all':
		case 'any':
		default:
        $text =  preg_replace ('/\s\s+/',' ',trim( $text ));

        $words = explode( ' ', $text );

        $wheres = array();

          foreach ($words as $word) {
            $word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
            $wheres2 	= array();
            $wheres2[] 	= "bl.title LIKE $word";
            $wheres2[] 	= " bl.isbn LIKE $word";
            $wheres2[] 	= " bl.authors LIKE $word";
            $wheres2[] 	= " bl.manufacturer LIKE $word";
            $wheres2[] 	= " bl.comment LIKE $word";

            $wheres[] 	= implode( ' OR ', $wheres2 );
          }
          $where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';

        $query = "SELECT bl.title AS title,"
                ." bl.date AS created,"
                ." bl.comment AS text,"            
                ." CONCAT( 'index.php?option=com_booklibrary"
                   ."&task=view&id=', bl.id,'&Itemid=', $ItemId_tmp,'&catid=',bc.catid) AS href,"
                ." '2' AS browsernav,"
                 ." 'Booklibrary' AS section,"
                 ." c.title AS category"
                ." FROM #__booklibrary AS bl,#__booklibrary_main_categories AS c, #__booklibrary_categories AS bc"
                  ." WHERE c.id=bc.catid AND bc.bookid=bl.id AND $where "
                ." AND bl.published = '1' "
                ." AND ({$s}) AND c.published='1' "
                ." GROUP BY bl.id"
                ." $order";
			break;
   }

   $database->setQuery( $query );
   $rows = $database->loadObjectList();
	
   for($i=0;$i<count($rows);$i++) {
       $rows[$i]->Itemid=$ItemId_tmp;
   }    
   return $rows;  
      
  }
  

}
}

?>
