<?php

/**
 *
 * @package BookLibraryManager
 * @copyright 2012 Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru);Rob de Cleen(rob@decleen.com)
 * Homepage: http://www.ordasoft.com
 * @version: 3.0 Free
 *
 * */
defined('_JEXEC') or die;

function booklibraryBuildRoute(&$query) {
//print_r($query['task']);exit;
    $segments = array();
    $db = JFactory::getDBO();
    $JSite = new JSite();
    $menu = $JSite->getMenu();
    if (isset($query['Itemid'])) {

        if (!isset($menu->getItem($query['Itemid'])->component)) {
            $a = 'com_booklibrary';
            $b = 'view';
        }
        else
            $a = $menu->getItem($query['Itemid'])->component;

        if (!isset($query['view']) && ($a == 'com_booklibrary') && !isset($query['task'])) {
            if (version_compare(JVERSION, "1.6.0", "lt")) {
                $query['view'] = $menu->getItem($query['Itemid'])->query['task'];
            } else if (version_compare(JVERSION, "1.6.0", "ge") && version_compare(JVERSION, "2.5.100", "lt")) {
                $query['view'] = $menu->getItem($query['Itemid'])->query['view'];
            } else if (version_compare(JVERSION, "1.6.0", "ge") && version_compare(JVERSION, "3.5.0", "lt")) {
                if (!isset($b))
                    $query['view'] = $menu->getItem($query['Itemid'])->query['view'];
                else
                    $query['view'] = $b;
            }
        }
    }

    if (isset($query['option']) && $query['option'] == 'com_booklibrary') { //check component
        $segments[0] = (isset($query['Itemid'])) && ($query['Itemid'] != 0) ? $query['Itemid'] : '0';
        if ((isset($query['view'])) && (!isset($query['task']))) {
            $query['task'] = $query['view'];
        }

        if (isset($query['task'])) {
            switch ($query['task']) {
                case 'new_url':
                    $segments[1] = 'buy_now';
                    break;
                case 'new_url_for_vm':
                    $segments[1] = 'buy_now_for_rem';
                    break;
                case 'view_user_books':
                case 'owner_books':
                    $segments[1] = $query['task'];
                    if (isset($query['name'])) {
                        //$segments[] = JFilterOutput::stringURLSafe($query['name']);
                        $segments[] = $query['name'];
                        unset($query['name']);
                    }
                    break;
                case 'lend_book' :
                    $segments[1] = $query['task'];
                    break;
                case 'categories' :
                    $segments[1] = "all_category";
                    break;
                default:
                    $segments[1] = $query['task'];
                    break;
            }
        }
        unset($query['task']);
        unset($query['view']);

        if (isset($query['catid'])) {
            if ($query['catid'] > 0) {
                /* $sql_query = "SELECT blc.id, blc.name, bc.catid, blc.parent_id ".
                  " FROM #__booklibrary_main_categories AS blc".
                  " LEFT JOIN #__booklibrary_categories AS bc ON bc.catid=blc.id ".
                  " LEFT JOIN #__booklibrary AS b ON bc.bookid = b.bookid ".		//!!!!!!!!!!!!!!!!!!1
                  " WHERE blc.section = 'com_booklibrary' AND blc.id = ".intval($query['catid']);
                 */
                $sql_query = "SELECT blc.name" .
                        " FROM #__booklibrary_main_categories AS blc" .
                        " WHERE blc.id = " . intval($query['catid']);

                $db->setQuery($sql_query);
                $row = null;
                $row = $db->loadObject();
                if (isset($row)) {
                    $cattitle = array();
                    $segments[] = $query['catid'];
                    $segments[] = $row->name;
                    unset($query['catid']);
                }
            } else {
                $temp = $query['catid']; //is catid set??
                unset($query['catid']);
            }
        }

        if (!empty($query['lang'])) {
            unset($query['lang']);
        }
        if (isset($query['Itemid'])) {
            unset($query['Itemid']);
        }
        if (!empty($query['Itemid'])) {
            $query['Itemid'] = "";
            unset($query['Itemid']);
        }

        if (isset($query['name'])) {
            $segments[] = JFilterOutput::stringURLSafe($query['name']);
            unset($query['name']);
        }

        if (isset($query['user'])) {
            $segments[] = $query['user'];
            unset($query['user']);
        }


        if (isset($query['id'])) {
            $sql_query = "SELECT bc.catid AS catid, b.title"
                    . "\n FROM #__booklibrary AS b"
                    . "\n LEFT JOIN #__booklibrary_categories AS bc ON bc.bookid=b.id"
                    . "\n LEFT JOIN #__booklibrary_main_categories AS blc ON blc.id=bc.catid"
                    . "\n WHERE b.id = " . intval($query['id']);

            $db->setQuery($sql_query);
            $row = null;
            $row = $db->loadObject();
            if (isset($row)) {
                $temp_title = JFilterOutput::stringURLSafe($row->title);
                if (isset($temp)) {
                    /*    $sql_query = "SELECT name FROM #__booklibrary_main_categories WHERE id = ".$row->catid;
                      $db->setQuery($sql_query);
                      $row = $db->loadObject();
                      if (isset($row)){
                      $segments[] = $row->name;
                      } */
                    $segments[] = $row->catid;
                }
                $segments[] = $query['id']; //for back up in parseroute
                $segments[] = $temp_title;
                unset($query['id']);
            }
        }

        if (isset($query['start'])) {
            $segments[] = $query['start'];
            if (isset($query['limitstart'])) {
                $segments[] = $query['limitstart'];
                unset($query['limitstart']);
            } else {
                $segments[] = $query['start'];
            }
            unset($query['start']);
        } else if (isset($query['limitstart'])) {
            $segments[] = $query['limitstart'];
            unset($query['limitstart']);
        }

        if (isset($query['viewtype'])) {
            $segments[] = 'viewtype' . ":" . $query['viewtype'];
            unset($query['viewtype']);
        }

        if (isset($query['searchtext'])) {
            $segments[] = $query['searchtext'];
            unset($query['searchtext']);
        }

        if (isset($query['searchtype'])) {
            $segments[] = $query['searchtype'];
            unset($query['searchtype']);
        }
    }
    unset($query); //print_r($segments);exit;
    return $segments;
}

/**
 * Parse the segments of a URL.
 * */
function booklibraryParseRoute($segments) {
    $db = JFactory::getDBO();
    $vars = array();

    $count = count($segments);
    $vars['option'] = 'com_booklibrary';

    $JSite = new JSite();
    $menu = $JSite->getMenu();
    $menu->setActive($segments[0]);
    /* if($segments[0] != 'rent_requests_cb_books')
      $vars['Itemid'] = $segments[0]; */
    switch ($segments[0]) {
        case 'rent_history_books':
            break;
        case 'rent_requests_cb_books':
            break;
        case 'show_my_books':
            break;
        default:
            $vars['Itemid'] = $segments[0];
            break;
    }
    //if ($count > 1)

    /*     if ((@$segments[1] == "alone_category" || @$segments[1] == "showCategory") && isset($segments[2]) && !isset($segments[4])){
      if(@$vars['task'] == "alone_category"){
      $vars['task'] = "alone_category";
      } else {
      $vars['task'] = "showCategory";
      }

      $sql_query = "SELECT id FROM #__booklibrary_main_categories WHERE name='".$segments[3]."'";
      $db->setQuery($sql_query);
      $row = null;
      $row = $db->loadObject();
      $vars['catid'] = $row->id;

      if (isset($segments[4])){
      $viewtype = explode( ':', $segments[3] );
      if ($viewtype[0] == "viewtype"){
      $vars['viewtype'] = (int) $viewtype[1];
      }
      }

      if (isset($segments[3]) && !isset($vars['viewtype'])){
      $vars['start'] = $segments[3];
      } else{
      unset($vars['start']);
      }

      if (isset($segments[4]) && !isset($vars['viewtype'])){
      $vars['limitstart'] = $segments[4];
      } else{
      unset($vars['limistart']);
      }

      if (isset($segments[5])){
      $viewtype = explode( ':', $segments[5] );
      if ($viewtype[0] == "viewtype"){
      $vars['viewtype'] = (int) $viewtype[1];
      }
      }
      if (isset($segments[6])){
      $vars['tab'] = $segments[6];
      }
      // } elseif (@$segments[1] == "view" && isset($segments[4])){
      //	$var['id'] = */
    if ((@$segments[1] == "showCategory") && isset($segments[3]) && (intval($segments[3]) || $segments[3] == '0')) {
        $vars['task'] = "showCategory";
        $vars['start'] = $segments[2];
        $vars['limitstart'] = $segments[3];
    } elseif (@$segments[1] == "lend_book") {

        $vars['task'] = "lend_book";
    } elseif ((@$segments[1] == "showCategory") && isset($segments[2])) {
        $vars['task'] = "showCategory";
        $vars['catid'] = @$segments[2];
        //echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
// 	       }
// 	      else{
// 		$vars['start'] = $segments[2];
// 		$vars['limitstart'] = $segments[3];
// 		//echo "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
// 		$bool = true;
// 		}
        //$vars['id'] = (int) $segments[4];
        //$vars['name'] = $segments[3];

        /*    $sql_query = "SELECT id FROM #__booklibrary_main_categories WHERE name='".$segments[3]."'";
          $db->setQuery($sql_query);
          $row = null;
          $row = $db->loadObject();
          $vars['catid'] = $row->id; */
//             if (!isset($bool)){
        if (isset($segments[4])) {
            $viewtype = explode(':', $segments[3]);
            if ($viewtype[0] == "viewtype") {
                $vars['viewtype'] = (int) $viewtype[1];
            }
        }

        if (isset($segments[3]) && !isset($vars['viewtype'])) {
            $vars['start'] = $segments[3];
        } else {
            unset($vars['start']);
        }

        if (isset($segments[4]) && !isset($vars['viewtype'])) {
            $vars['limitstart'] = $segments[4];
        } else {
            unset($vars['limitstart']);
        }

        if (isset($segments[5])) {
            $viewtype = explode(':', $segments[5]);
            if ($viewtype[0] == "viewtype") {
                $vars['viewtype'] = (int) $viewtype[1];
            }
        }
    } elseif (@$segments[1] == "show_my_books") {
        $vars['task'] = "show_my_books";
        if(isset($segments[2]) && $segments[2] > 0) $vars['limitstart'] = $segments[2];
    } elseif (@$segments[1] == "view" && isset($segments[4])) {
        $vars['task'] = "view";
        if (intval($segments[3]))
            $vars['id'] = (int) $segments[3];
        else/* if (is_integer($segments[4])) */
            $vars['id'] = (int) $segments[4];
        $vars['catid'] = $segments[2];
    } elseif (@$segments[1] == "books") {
        $vars['task'] = $segments[1];

        if (isset($segments[2]) && !isset($vars['viewtype'])) {
            $vars['start'] = $segments[2];
        } else {
            unset($vars['start']);
        }

        if (isset($segments[3]) && !isset($vars['viewtype'])) {
            $vars['limitstart'] = $segments[3];
        } else {
            unset($vars['limitstart']);
        }

        //$vars['name'] = $segments[4];

        /*   $sql_query = "SELECT id FROM #__booklibrary_main_categories WHERE name='".$segments[3]."'";
          $db->setQuery($sql_query);
          $row = null;
          $row = $db->loadObject();
          $vars['catid'] = $row->id; */

        /*   } elseif (@$segments[1] == "show_add") {							//!!!!!!!!!!!!!!!!!!!!!!!!!!!
          $vars['task'] = "show_add";

          } elseif (@$segments[1] == "owner_books") {
          $vars['task'] = "owner_books";
          } elseif (@$segments[1] == "my_books") {
          $vars['task'] = "my_books"; */
    } elseif (@$segments[0] == "show_my_books") {
        $vars['task'] = 'show_my_books';
    } elseif (@$segments[0] == "rent_history_books") {
        $vars['task'] = 'rent_history_books';
        $vars['name'] = $segments[1];
        $vars['number'] = $segments[2];
    } elseif (@$segments[0] == "rent_requests_cb_books") {
        $vars['task'] = 'rent_requests_cb_books';
    } elseif (@$segments[1] == "edit_book") {
        $vars['task'] = $segments[1];
        $vars['id'] = $segments[2];
        if (isset($segments[3]))
            $vars['start'] = $segments[3];
    }elseif (@$segments[1] == "owners_list") {
        $vars['task'] = $segments[1];
    } elseif (@$segments[1] == "view_user_books") {
        $vars['task'] = $segments[1];
        //$vars['name'] = "Super User";//$segments[2];
        if (isset($segments[2])) {
            if (/* eregi */preg_match("|:|", $segments[2])) {
                $viewtype = explode(':', $segments[2]);
                $vars['name'] = null;
                for ($a = 0; $a < count($viewtype); $a++) {
                    $vars['name'] .= $viewtype[$a];
                    if ($a < count($viewtype) - 1)
                        $vars['name'] .= " ";
                }
            }
            else {
                $vars['name'] = $segments[2];
            }
        }
        if (isset($segments[3]) && !isset($vars['viewtype'])) {
            $vars['start'] = $segments[3];
        } else {
            unset($vars['start']);
        }

        if (isset($segments[4]) && !isset($vars['viewtype'])) {
            $vars['limitstart'] = $segments[4];
        } else {
            unset($vars['limitstart']);
        }
    } elseif (@$segments[1] == "lend_return_book") {

        $vars['task'] = "lend_return_book";
    } elseif (@$segments[1] == "Search") {
        $vars['task'] = "search";
        if (isset($segments[4])) {
            $vars['searchtext'] = $segments[4];
        }
        if (isset($segments[5])) {
            $vars['searchtype'] = $segments[5];
        }
        if (isset($segments[2])) {
            $vars['start'] = $segments[2];
        } else {
            unset($vars['start']);
        }
        if (isset($segments[3])) {
            $vars['limitstart'] = $segments[3];
        } else {
            unset($vars['limistart']);
        }
    } elseif (@$segments[1] == "show_search") {  
        $vars['task'] = "show_search";
        if (isset($segments[4])) {
            $vars['searchtext'] = $segments[4];
        }
        if (isset($segments[5])) {
            $vars['searchtype'] = $segments[5];
        }
        if (isset($segments[2])) {
            $vars['start'] = $segments[2];
        } else {
            unset($vars['start']);
        }
        if (isset($segments[3])) {
            $vars['limitstart'] = $segments[3];
        } else {
            unset($vars['limistart']);
        }
    } elseif (@$segments[1] == "search") {
        $vars['task'] = "search";
        if (isset($segments[4])) {
            $vars['searchtext'] = $segments[4];
        }
        if (isset($segments[5])) {
            $vars['searchtype'] = $segments[5];
        }
        if (isset($segments[2])) {
            $vars['start'] = $segments[2];
        } else {
            unset($vars['start']);
        }
        if (isset($segments[3])) {
            $vars['limitstart'] = $segments[3];
        } else {
            unset($vars['limistart']);
        }
    } elseif (@$segments[1] == "show_rss_categories") {
        $vars['task'] = "show_rss_categories";
    } elseif (@$segments[1] == "buy_now") {
        $vars['task'] = "new_url";
        if (isset($segments[2])) {
            $vars['id'] = $segments[2];
        }
    } elseif (@$segments[1] == "buy_now_for_vm") {
        $vars['task'] = "new_url_for_vm";
        if (isset($segments[2])) {
            $vars['id'] = $segments[2];
        }
    } elseif (@$segments[1] == "view_user_books") {
        $vars['task'] = "view_user_books";
        if (isset($segments[2])) {
            $vars['name'] = $segments[2];
        }
    } elseif (@$segments[1] == "owner_books") {
        $vars['task'] = "owner_books";
        if (isset($segments[2])) {
            $vars['name'] = $segments[2];
        }
    } elseif (@$segments[1] == "lend_history") {
        $vars['task'] = "lend_history";
        $vars['name'] = $segments[2];
        $vars['user'] = $segments[3];
    } elseif (@$segments[1] == "lend_requests") {
        $vars['task'] = "lend_requests";
        if (isset($segments[2])) {
            $vars['start'] = $segments[2];
        } else {
            unset($vars['start']);
        }
        if (isset($segments[3])) {
            $vars['limitstart'] = $segments[3];
        } else {
            unset($vars['limistart']);
        }
    } elseif (@$segments[1] == "mdownload") {
        $vars['task'] = "mdownload";
        if (isset($segments[2])) {
            $vars['id'] = $segments[2];
        }
    } else {
        $vars['task'] = @$segments[1];
    }
    // print_r($segments); 
    // print_r($vars); 
//     exit;

    return $vars;
}
