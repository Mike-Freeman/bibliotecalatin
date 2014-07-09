<?php
if (!defined('_VALID_MOS') && !defined('_JEXEC')) die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');
/**
 *
 * @package BookLibrary
 * @copyright Andrey Kvasnevskiy-OrdaSoft(akbet@mail.ru); Rob de Cleen(rob@decleen.com);
 * Homepage: http://www.ordasoft.com
 * @version: 3.0 Free
 *
 *
 */
$mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path'];
require_once ($mosConfig_absolute_path . "/components/com_booklibrary/booklibrary.main.categories.class.php");
function print_vars($obj) {
    $arr = get_object_vars($obj);
    while (list($prop, $val) = each($arr)) if (class_exists($val)) print_vars($val);
    else echo "\t $prop = $val\n<br />";
}
function print_methods($obj) {
    $arr = get_class_methods(get_class($obj));
    foreach($arr as $method) echo "\tfunction $method()\n <br />";
}
if (PHP_VERSION >= 5) {
    // Emulate the old xslt library functions
    function xslt_create() {
        return new XsltProcessor();
    }
    function xslt_process($xsltproc, $xml_arg, $xsl_arg, $xslcontainer = null, $args = null, $params = null) {
        // Create instances of the DomDocument class
        $xml = new DomDocument;
        $xsl = new DomDocument;
        // Load the xml document and the xsl template
        $xml->load($xml_arg);
        $xsl->load($xsl_arg);
        // Load the xsl template
        $xsltproc->importStyleSheet($xsl);
        // Set parameters when defined
        if ($params) {
            foreach($params as $param => $value) {
                $xsltproc->setParameter("", $param, $value);
            }
        }
        // Start the transformation
        $processed = $xsltproc->transformToXML($xml);
        // Put the result in a file when specified
        if ($xslcontainer) {
            return file_put_contents($xslcontainer, $processed);
        } else {
            return $processed;
        }
    }
    function xslt_free($xsltproc) {
        unset($xsltproc);
    }
}
class mosBooklibraryImportExport {
    /**
     * Imports the lines given to this method into the database and writes a
     * table containing the information of the imported books.
     * The imported books will be set to [not published]
     * Format: #;id;isbn;title;author;language
     * @param array lines - an array of lines read from the file
     * @param int catid - the id of the category the books should be added to
     */
   static function importBooksCSV($lines, $catid) {
        global $database;
        $retVal = array();
        $i = 0;
        foreach($lines as $line) {
            $tmp = array();
            if (trim($line) == "") continue;
            $line = explode('|', $line);
            $book = new mosBookLibrary($database);
            $book->bookid = trim($line[0]);
            $book->isbn = $line[1];
            $book->title = $line[2];
            $book->authors = $line[3];
            $book->manufacturer = $line[4];
            $book->price = $line[9];
            //$book->date = date("Y-m-d H:i:s");
            $book->date = $line[15];
            $book->language = $line[6]; //Language
            if (count($line) > 16) { //if new version csv
                $book->priceunit = $line[16];
                $book->owneremail = $line[17];
                $book->featured_clicks = $line[18];
                $book->featured_shows = $line[19];
                $book->numberOfPages = $line[20];
                $book->comment = $line[21]; //Book Description
                
            } // optimize!!!
            else { //if old version csv
                $book->comment = $line[16];
            }
            $tmp[0] = $i;
            $tmp[1] = $book->bookid;
            $tmp[2] = $line[1];
            $tmp[3] = $line[2];
            $tmp[4] = $line[3];
            $tmp[5] = $line[4];
            if (!$book->check()) {
                $tmp[6] = $book->getError();
                $retVal[$i] = $tmp;
                $i++;
                continue;
            }
            if (!$book->store()) {
                $tmp[6] = $book->getError();
                $retVal[$i] = $tmp;
                $i++;
                continue;
                $tmp[6] = $book->getError();
            } else {
                $tmp[6] = "OK";
            }
            $book->categs = array($catid);
            $book->saveCategs();
            $book->checkin();
            //$book->updateOrder( "ordering" );
            $retVal[$i] = $tmp;
            $i++;
        }
        //exit;
        return $retVal;
    }
    static function getXMLItemValue($item, $item_name) {
        $book_items = $item->getElementsByTagName($item_name);
        $book_item = $book_items->item(0);
        if (NULL != $book_item) return $book_item->nodeValue;
        else return "";
    }
    //***************************************************************************************************
    static function findCategory(&$categories, $new_category) {
        global $database;
        //    foreach( $categories as $category ){
        //      if($category->old_id == $new_category->old_id ) return  $category;
        //    }
        $new_parent_id = null;
        if ($new_category->old_parent_id != 0) { //ÐµÑÐ»Ð¸ ÑÑ‚Ð¾ ÑÑƒÐ±ÐºÐ°Ñ‚ÐµÑ€Ð¾Ð³Ð¸Ñ
            foreach($categories as $category) {
                if ($category->old_id == $new_category->old_parent_id) {
                    $new_parent_id = $category->id;
                    break;
                }
            }
        } else $new_parent_id = 0;
        //sanity test
        if ($new_parent_id === null) {
            //echo "error in import !"; exit;
            $new_parent_id = 0;
        }
        ///$row = new mosCategory($database);
        $row = new mainBooklibraryCategories($database); // for 1.6
        $row->section = 'com_booklibrary';
        $row->parent_id = $new_parent_id;
        $row->name = $new_category->name;
        $row->title = $new_category->title;
        $row->alias = $new_category->alias;
        $row->published = $new_category->published;
        $row->ordering = $new_category->ordering;
        $row->access = $new_category->access;
        $row->description = $new_category->description;
        $row->params = $new_category->params;
        $row->params2 = $new_category->params2; //!!!!!!!!!!
        if (!$row->check()) {
            echo "error in import2 !";
            exit;
            exit();
        }
        if (!$mess = $row->store()) {
            // echo "err-mess_".$mess."_";
            echo "error in import3 !: " . $mess;
            exit;
            // echo "error in import3 !"; exit;
            exit();
        }
        ///$row->updateOrder("extension='com_booklibrary' AND parent_id='$row->parent_id'");
        $row->updateOrder("section='com_booklibrary' AND parent_id='$row->parent_id'"); //for 1.6
        $new_category->id = $row->id;
        $categories[] = $new_category;
        return $new_category;
    }
    //***********************   begin add for import XML format   ***************************************
    //***************************************************************************************************
    static function importBooksXML($files_name_pars, $catid) {
        $files_name_pars = file($files_name_pars);
        $files_name_pars = implode('', $files_name_pars);
        //echo $files_name_pars;
        global $database;
        $retVal = array();
        $new_categories = array();
        $k = 0;
        $dom = new domDocument('1.0', 'utf-8');
        $dom->loadXML($files_name_pars);
        $version = $dom->getElementsByTagName('version');
        if ($version->item(0) != NULL) {
            $numversion = explode(' ', $version->item(0)->nodeValue);
            if (intval($numversion[0]) >= 2) {
                $categories_xml = $dom->getElementsByTagName('category');
                if ($categories_xml->item(0) != NULL) { //ÐµÑÐ»Ð¸ Ð² XML ÐµÑÑ‚ÑŒ ÑÐ¿Ð¸ÑÐ¾Ðº ÐºÐ°Ñ‚ÐµÑ€Ð¾Ð³Ð¸Ð¹ Ð´Ð»Ñ Ð¸Ð¼Ð¿Ð¾Ñ€Ñ‚Ð°
                    mosBooklibraryImportExport::remove_info(); //Ñ‡Ð¸ÑÑ‚Ð¸Ð¼ Ð±Ð°Ð·Ñƒ Ð´Ð°Ð½Ð½Ñ‹Ñ…
                    if ($catid === null) { //ÐµÑÐ»Ð¸ ÐºÐ°Ñ‚ÐµÐ³Ð¾Ñ€Ð¸Ñ Ð´Ð»Ñ Ð¸Ð¼Ð¿Ð¾Ñ€Ñ‚Ð° Ð½Ðµ Ð²Ñ‹Ð±Ñ€Ð°Ð½Ð°
                        for ($i = 0;$i < $categories_xml->length;$i++) {
                            $category = $categories_xml->item($i);
                            $new_category = new stdClass();
                            $new_category->old_id = mosBooklibraryImportExport::getXMLItemValue($category, 'id');
                            $new_category->old_parent_id = mosBooklibraryImportExport::getXMLItemValue($category, 'parent_id');
                            $new_category->name = mosBooklibraryImportExport::getXMLItemValue($category, 'name');
                            $new_category->title = mosBooklibraryImportExport::getXMLItemValue($category, 'title');
                            $new_category->alias = mosBooklibraryImportExport::getXMLItemValue($category, 'alias');
                            $new_category->published = mosBooklibraryImportExport::getXMLItemValue($category, 'published');
                            $new_category->ordering = mosBooklibraryImportExport::getXMLItemValue($category, 'ordering');
                            $new_category->access = mosBooklibraryImportExport::getXMLItemValue($category, 'access');
                            $new_category->description = mosBooklibraryImportExport::getXMLItemValue($category, 'description');
                            $new_category->params = mosBooklibraryImportExport::getXMLItemValue($category, 'params');
                            $new_category->params2 = mosBooklibraryImportExport::getXMLItemValue($category, 'params2');
                            $new_category = mosBooklibraryImportExport::findCategory($new_categories, $new_category);
                        }
                    }
                } //end if exist categories
                //exit;
                $books_xml = $dom->getElementsByTagName('book');
                foreach($books_xml as $i => $book_xml) {
                    //echo mosBooklibraryImportExport::getXMLItemValue($book_xml,'bookid');
                    $book = new mosBooklibrary($database);
                    //get BookID
                    $book->bookid = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'bookid');
                    //get ISBN
                    $book->isbn = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'isbn');
                    //get Title(book)
                    $book->title = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'title');
                    //get Authors
                    $book->authors = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'authors');
                    //get Manufacturer
                    $book->manufacturer = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'manufacturer');
                    //get releasedate
                    $book->release_Date = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'releaseDate');
                    //get language
                    $book->language = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'language');
                    $book->langshow = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'langshow');
                    //get hits
                    $book->hits = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'hits');
                    $book->user_name = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'user_name');
                    //get featured_clicks
                    $book->featured_clicks = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'featured_clicks');
                    //get featured_shows
                    $book->featured_shows = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'featured_shows');
                    //get rating
                    $book->rating = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'rating');
                    //get price
                    $book->price = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'price');
                    //get priceunit
                    $book->priceunit = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'priceunit');
                    //get URL
                    $book->URL = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'url');
                    //get imageURL
                    $book->imageURL = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'imageURL');
                    //get edition
                    $book->edition = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'edition');
                    //get ebookURL
                    $book->ebookURL = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'ebookURL');
                    //get informationFrom
                    $book->informationFrom = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'informationFrom');
                    //get date
                    $book->date = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'date');
                    //get published
                    $book->published = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'published');
                    //get comment
                    $book->comment = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'comment');
                    //get numberOfPages
                    $book->numberOfPages = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'numberOfPages');
                    //get Language
                    $book->language = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'language');
                    //get Comment for book (item Book Description)
                    $book->langshow = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'langshow');
                    $book->comment = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'comment');
                    //get email owner book
                    $book->owneremail = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'owneremail');
                    //get email owner book
                    $book->owner_id = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'owner_id');
                    //get email owner book
                    $book->vm_id_product = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'vm_id_product');
                    //get Categorie
                    if ($catid != "0" && $catid != "") { //ÐµÑÐ»Ð¸ Ð²Ñ‹Ð±Ñ€Ð°Ð½ Ð¸Ð¼Ð¿Ð¾Ñ€Ñ‚ Ð² ÐºÐ°Ñ‚ÐµÐ³Ð¾Ñ€Ð¸ÑŽ
                        $book->categs = array($catid);
                    } else {
                        $categ = $book_xml->getElementsByTagName('categ');
                        //var_dump($categ);
                        $arrcatid = array();
                        foreach($categ as $cattemp) { //ÑÐ¿Ð¸ÑÐ¾Ðº ÐºÐ°Ñ‚ÐµÐ³Ð¾Ñ€Ð¸Ð¹ ÐºÐ½Ð¸Ð³ Ð¸Ð· XML
                            $category_book_old_id = $cattemp->nodeValue; //n-Ð°Ñ ÐºÐ°Ñ‚ÐµÐ³Ð¾Ñ€Ð¸Ñ ÐºÐ½Ð¸Ð³Ð¸ Ð¸Ð· XML
                            foreach($new_categories as $new_category) { //Ð¸Ñ‰ÐµÐ¼ Ð² ÑÐ¿Ð¸ÑÐºÐµ ÑÐ¾Ð·Ð´Ð°Ð½Ð½Ñ‹Ñ… ÐºÐ°Ñ‚ÐµÐ³Ð¾Ñ€Ð¸Ð¹
                                if ($new_category->old_id == $category_book_old_id) {
                                    $arrcatid[] = $new_category->id;
                                }
                            }
                        }
                        $book->categs = $arrcatid;
                    }
                    $tmp[] = $i;
                    $tmp[] = $book->bookid;
                    $tmp[] = $book->isbn;
                    $tmp[] = $book->title;
                    $tmp[] = $book->authors;
                    $tmp[] = $book->manufacturer;
                    if (!$book->check()) {
                        $tmp[] = $book->getError();
                        $retVal[$i] = $tmp;
                        unset($tmp);
                        continue;
                    }
                    if (!$book->store()) {
                        $tmp[] = $book->getError();
                        $retVal[$i] = $tmp;
                        unset($tmp);
                        continue;
                    } else {
                        $book->saveCategs();
                        $tmp[] = "OK";
                        //save categ
                        //get Reviews
                        if (mosBooklibraryImportExport::getXMLItemValue($book_xml, 'reviews') != "") {
                            $review_list = $book_xml->getElementsByTagName('review');
                            for ($j = 0;$j < $review_list->length;$j++) {
                                $review = $review_list->item($j);
                                //get for review - fk_bookid == #__booklibrary.id
                                /*                  $database->setQuery("SELECT id FROM #__booklibrary  ".
                                "\n WHERE isbn = '".$book->isbn."';");
                                $fk_bookid = $database->loadResult();*/
                                $fk_bookid = $book->id;
                                //get for review - fk_userid
                                $review_fk_userid = mosBooklibraryImportExport::getXMLItemValue($review, 'fk_userid');
                                //check - exist this user or not - if don't exist set he as anonymous
                                $database->setQuery("SELECT id FROM #__users " . "\n WHERE id = " . $review_fk_userid . ";");
                                $review_fk_userid = $database->loadResult();
                                if (count($review_fk_userid) == 0) $review_fk_userid = 0;
                                //get for review - date
                                $review_date = mosBooklibraryImportExport::getXMLItemValue($review, 'date');
                                //get for review - rating
                                $review_rating = mosBooklibraryImportExport::getXMLItemValue($review, 'rating');
                                //get for review - title
                                $review_title = mosBooklibraryImportExport::getXMLItemValue($review, 'title');
                                //get for review - comment
                                $review_comment = mosBooklibraryImportExport::getXMLItemValue($review, 'comment');
                                //insert data in table #__booklibrary_review
                                $database->setQuery("INSERT INTO #__booklibrary_review" . "\n (fk_bookid, fk_userid, date, rating, title, comment)" . "\n VALUES " . "\n (" . $fk_bookid . ", " . $review_fk_userid . ", '" . $review_date . "'," . $review_rating . ",'" . $review_title . "', '" . $review_comment . "');");
                                $database->query();
                            } //end for(...) - REVIEW
                            
                        } //end if(...) - REVIEW
                        $book->checkin();
                        //$book->updateOrder( "catid='$book->catid'" );
                        $retVal[$i] = $tmp;
                    }
                    //echo $i,':';var_dump($tmp); echo '<br/>';
                    unset($tmp);
                    continue;
                } //end foreach books */
                
            } // end if version >2.0
            
        } // endif version in XML exist
        else { // ----- OLD VERSION------
            //exit;
            $st = $bookid = "";
            $begin = $end = $kol = 0;
            $book_list = $dom->getElementsByTagName('book');
            for ($i = 0;$i < $book_list->length;$i++) {
                $book_class = new mosBookLibrary($database);
                $book = $book_list->item($i);
                //            echo $book_item->hasChildNodes() . "<br />";
                //get BookID
                $book_id = $book_class->bookid = 1 + $book_class->getMaxBookid(); //mosBooklibraryImportExport::getXMLItemValue($book,'bookid');
                //get ISBN
                $book_isbn = $book_class->isbn = mosBooklibraryImportExport::getXMLItemValue($book, 'isbn');
                //get Title(book)
                $book_title = $book_class->title = mosBooklibraryImportExport::getXMLItemValue($book, 'title');
                //get Authors
                $book_authors = $book_class->authors = mosBooklibraryImportExport::getXMLItemValue($book, 'authors');
                //get Manufacturer
                $book_manufacturer = $book_class->manufacturer = mosBooklibraryImportExport::getXMLItemValue($book, 'manufacturer');
                //get releasedate
                $book_class->release_Date = mosBooklibraryImportExport::getXMLItemValue($book, 'releaseDate');
                //get language
                $book_class->language = mosBooklibraryImportExport::getXMLItemValue($book, 'language');
                $book_class->langshow = mosBooklibraryImportExport::getXMLItemValue($book, 'langshow');
                //get hits
                $book_class->hits = mosBooklibraryImportExport::getXMLItemValue($book, 'hits');
                $book_class->user_name = mosBooklibraryImportExport::getXMLItemValue($book, 'user_name');
                //get rating
                $book_class->rating = mosBooklibraryImportExport::getXMLItemValue($book, 'rating');
                //get featured_clicks
                $book_class->featured_clicks = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'featured_clicks');
                //get featured_shows
                $book_class->featured_shows = mosBooklibraryImportExport::getXMLItemValue($book_xml, 'featured_shows');
                //get price
                $book_class->price = mosBooklibraryImportExport::getXMLItemValue($book, 'price');
                if (substr($book_class->price, 0, 1) == "$") {
                    $book_class->price = substr($book_class->price, 1);
                    $book_class->priceunit = 'USD';
                }
                //get URL
                $book_class->URL = mosBooklibraryImportExport::getXMLItemValue($book, 'url');
                //get imageURL
                $book_class->imageURL = mosBooklibraryImportExport::getXMLItemValue($book, 'imageURL');
                //get edition
                $book_class->edition = mosBooklibraryImportExport::getXMLItemValue($book, 'edition');
                //get ebookURL
                $book_class->ebookURL = mosBooklibraryImportExport::getXMLItemValue($book, 'ebookURL');
                //get informationFrom
                $book_class->informationFrom = mosBooklibraryImportExport::getXMLItemValue($book, 'informationFrom');
                //get date
                $book_class->date = mosBooklibraryImportExport::getXMLItemValue($book, 'date');
                //get comment
                $book_class->comment = mosBooklibraryImportExport::getXMLItemValue($book, 'comment');
                //get Categorie
                $book_class->categs = array($catid);
                //get Language
                $book_class->language = mosBooklibraryImportExport::getXMLItemValue($book, 'language');
                $book_class->langshow = mosBooklibraryImportExport::getXMLItemValue($book, 'langshow');
                //get Comment for book (item Book Description)
                $book_class->comment = mosBooklibraryImportExport::getXMLItemValue($book, 'comment');
                //get vm_id_product
                $book_class->vm_id_product = mosBooklibraryImportExport::getXMLItemValue($book, 'vm_id_product');
                //for output rezult in table
                $tmp[0] = $i;
                $tmp[1] = $book_id;
                $tmp[2] = $book_isbn;
                $tmp[3] = $book_title;
                $tmp[4] = $book_authors;
                $tmp[5] = $book_manufacturer;
                if (!$book_class->check()) {
                    $tmp[6] = $book_class->getError();
                    $retVal[$i] = $tmp;
                    continue;
                }
                if (!$book_class->store()) {
                    $tmp[6] = $book_class->getError();
                    $retVal[$i] = $tmp;
                    continue;
                } else {
                    $tmp[6] = "OK";
                    $book_class->saveCategs();
                }
                $book_class->checkin();
                //$book_class->updateOrder( "catid='$book_class->catid'" );
                $retVal[$i] = $tmp;
                //get Reviews
                if ($tmp[6] == "OK" && mosBooklibraryImportExport::getXMLItemValue($book, 'reviews') != "") {
                    $review_list = $book->getElementsByTagname('review');
                    for ($j = 0;$j < $review_list->length;$j++) {
                        $review = $review_list->item($j);
                        //get for review - fk_bookid == #__booklibrary.id
                        /*            $database->setQuery("SELECT id FROM #__booklibrary  ".
                        "\n WHERE isbn = '".$book_isbn."';");
                        $fk_bookid = $database->loadResult();*/
                        $fk_bookid = $book_class->id;
                        //get for review - fk_userid
                        $review_fk_userid = mosBooklibraryImportExport::getXMLItemValue($review, 'fk_userid');
                        //check - exist this user or not - if don't exist set he as anonymous
                        $database->setQuery("SELECT id FROM #__users " . "\n WHERE id = " . $review_fk_userid . ";");
                        $review_fk_userid = $database->loadResult();
                        if (count($review_fk_userid) == 0) $review_fk_userid = 0;
                        //get for review - date
                        $review_date = mosBooklibraryImportExport::getXMLItemValue($review, 'date');
                        //get for review - rating
                        $review_rating = mosBooklibraryImportExport::getXMLItemValue($review, 'rating');
                        //get for review - title
                        $review_title = mosBooklibraryImportExport::getXMLItemValue($review, 'title');
                        //get for review - comment
                        $review_comment = mosBooklibraryImportExport::getXMLItemValue($review, 'comment');
                        //insert data in table #__booklibrary_review
                        $database->setQuery("INSERT INTO #__booklibrary_review" . "\n (fk_bookid, fk_userid, date, rating, title, comment)" . "\n VALUES " . "\n (" . $fk_bookid . ", " . $review_fk_userid . ", '" . $review_date . "'," . $review_rating . ",'" . $review_title . "', '" . $review_comment . "');");
                        $database->query();
                    } //end for(...) - REVIEW
                    
                }
            } //end for(...) - BOOK
            
        }
        //var_dump($retVal);    exit;
        return $retVal;
    }
    //***************************************************************************************************
    //***********************   end add for import XML format   *****************************************
    //***************************************************************************************************
    static function exportBooksXML($books, $cats = '') {
        global $mosConfig_live_site, $mosConfig_absolute_path, $booklibrary_configuration, $database;
        $strXmlDoc = "";
        $strXmlDoc.= "<?xml version='1.0' encoding='utf-8' ?>\n";
        $strXmlDoc.= "<data>\n";
        $strXmlDoc.= "<version>" . $booklibrary_configuration['release']['version'] . "</version>\n";
        $strXmlDoc.= "<books>\n";
        //create and append list element
        //print_r($books); exit;
        foreach($books as $book) {
            $strXmlDoc.= $book->toXML2();
        }
        $strXmlDoc.= "</books>\n";
        if ($cats != '') {
            $strXmlDoc.= "<categories>\n";
            foreach($cats as $cat) {
                $strXmlDoc.= "<category>\n";
                foreach($cat as $field => $value) {
                    $strXmlDoc.= '<' . $field . '><![CDATA[' . $value . ']]></' . $field . ">\n";
                }
                $strXmlDoc.= "</category>\n";
            }
            $strXmlDoc.= "</categories>\n";
        }
        $strXmlDoc.= "</data>\n";
        return $strXmlDoc;
    }
   static function storeExportFile($data, $type) {
        global $mosConfig_live_site, $mosConfig_absolute_path, $booklibrary_configuration;
        $fileName = "booklibrary_" . date("Ymd_His");
        $fileBase = "/administrator/components/com_booklibrary/exports/";
        //echo 'PRINT : '.$mosConfig_absolute_path;exit;
        //write the xml file
        $fp = fopen($mosConfig_absolute_path . $fileBase . $fileName . ".xml", "w", 0); #open for writing
        fwrite($fp, $data); #write all of $data to our opened file
        fclose($fp); #close the file
        $InformationArray = array();
        $InformationArray['xml_file'] = $fileName . '.xml';
        $InformationArray['log_file'] = $fileName . '.log';
        $InformationArray['fileBase'] = "file://" . getcwd() . "/components/com_booklibrary/exports/";
        $InformationArray['urlBase'] = $mosConfig_live_site . $fileBase;
        $InformationArray['out_file'] = $InformationArray['xml_file'];
        $InformationArray['error'] = null;
        switch ($type) {
            case 'csv':
                $InformationArray['xslt_file'] = 'csv.xsl';
                $InformationArray['out_file'] = $fileName . '.csv';
                mosBooklibraryImportExport::transformPHP4($InformationArray);
            break;
            default:
            break;
        }
        return $InformationArray;
    }
   static function transformPHP4(&$InformationArray) {
        // create the XSLT processor^M
        $xh = xslt_create() or die("Could not create XSLT processor");
        // Process the document
        $result = xslt_process($xh, $InformationArray['fileBase'] . $InformationArray['xml_file'], $InformationArray['fileBase'] . $InformationArray['xslt_file'], $InformationArray['fileBase'] . $InformationArray['out_file']);
        if (!$result) {
            // Something croaked. Show the error
            $InformationArray['error'] = "Cannot process XSLT document: " . xslt_errno($xh) . " " . xslt_error($xh);
        }
        // Destroy the XSLT processor
        xslt_free($xh);
    }
    //////////  MY  ///////////////////////////////////////////////
    function datadump($table) {
        $cnt = 0;
        $result = '';
        $resrt = '';
        $reslt = "# Dump of $table \n";
        $reslt.= "# Dump DATE : " . date("d-M-Y") . "\n\n\n";
        $query = mysql_query('select * $table');
        while ($Row = mysql_fetch_assoc($query)) {
            //        print_r($Row);
            $reslt.= "INSERT INTO " . $table . " (";
            $resrt.= ") VALUES ('";
            while (list($key, $value) = each($Row)) {
                //            echo "Key: $key; Value: $value<br />\n";
                $reslt.= $key . ",";
                if (!get_magic_quotes_gpc()) $value = addslashes($value);
                $resrt.= $value . "','";
            }
            $reslt = substr($reslt, 0, -1);
            $resrt = substr($resrt, 0, -2);
            $resrt.= ");\n";
            $result.= ($reslt . $resrt);
            $reslt = '';
            $resrt = '';
        }
        return $result . "\n\n\n";
    }
    function wise_select_cat() {
        global $database;
        $fcnt = 0;
        $cnt = 0;
        $parenttmp = 0;
        $result = '';
        $reslt = '';
        $resrt = '';
        $reslt = "# Dump of category \n";
        $reslt.= "# Dump DATE : " . date("d-M-Y") . "\n\n\n";
        $query = "select * from #__booklibrary_main_categories where section = 'com_booklibrary' order by parent_id";
        $database->setQuery($query);
        $Rows = $database->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return;
        }
        for ($i = 0;$i < count($Rows);$i++) {
            $reslt.= "INSERT INTO #__booklibrary_main_categories (";
            $resrt.= ") VALUES ('";
            while (list($key, $value) = each($Rows[$i])) {
                $reslt.= $key . ",";
                switch ($fcnt) //specialize params
                {
                    case 0:
                        $resrt.= "','";
                    break;
                    case 1:
                        $resrt.= "%|%','";
                    break;
                    default:
                        if (!get_magic_quotes_gpc()) $value = addslashes($value);
                        $resrt.= $value . "','";
                    }
                    $fcnt++;
            }
            $reslt = substr($reslt, 0, -1);
            $resrt = substr($resrt, 0, -2);
            $resrt.= ")|%|\n";
            $result.= ($reslt . $resrt);
            $reslt = '';
            $resrt = '';
            $fcnt = 0;
        }
        return $result;
    }
    function wise_select_book() {
        global $database;
        $fcnt = 0;
        $cnt = 0;
        $parenttmp = 0;
        $result = '';
        $reslt = '';
        $resrt = '';
        $reslt = "# Dump of  \n";
        $reslt.= "# Dump DATE : " . date("d-M-Y") . "\n\n\n";
        $query = 'select * from #__booklibrary order by id';
        $database->setQuery($query);
        $Rows = $database->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return;
        }
        for ($i = 0;$i < count($Rows);$i++) {
            $reslt.= "INSERT INTO #__booklibrary (";
            $resrt.= ") VALUES ('";
            while (list($key, $value) = each($Rows[$i])) {
                $reslt.= $key . ",";
                switch ($fcnt) {
                    case 0:
                        $resrt.= "','";
                    break;
                    case 2:
                        $resrt.= "%|%','";
                    break;
                    default:
                        $resrt.= $database->getEscaped($value) . "','";
                }
                $fcnt++;
            }
            $reslt = substr($reslt, 0, -1);
            $resrt = substr($resrt, 0, -2);
            $resrt.= ")|%|\n";
            $result.= ($reslt . $resrt);
            $reslt = '';
            $resrt = '';
            $fcnt = 0;
        }
        return $result;
    }
    function select_catid() {
        global $database;
        $str = '';
        $c = 0;
        $i = 0;
        $query = 'select catid from #__booklibrary order by id';
        $database->setQuery($query);
        $Rows = $database->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return;
        }
        $arr = array();
        $arr = array_values($Rows);
        for ($i = 0;$i < count($Rows);$i++) {
            list($key, $value) = each($Rows[$i]);
            $arr[$c] = $value;
            $c++;
        }
        for ($i = 0;$i < count($arr);$i++) {
            $tmp = $arr[$i];
            $str.= $tmp . "::";
        }
        return substr($str, 0, -2);
    }
    function select_linked() {
        global $database;
        $str = '';
        $c = 0;
        $i = 0;
        $query = 'select id, parent_id from #__booklibrary_main_categories where section = "com_booklibrary" order by parent_id';
        $database->setQuery($query);
        $Rows = $database->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return;
        }
        $arr = array();
        for ($i = 0;$i < count($Rows);$i++) {
            list($key, $value) = each($Rows[$i]);
            $arr[$c][0] = $value;
            list($key, $value) = each($Rows[$i]);
            $arr[$c][1] = $value;
            $c++;
        }
        for ($i = 0;$i < count($arr);$i++) {
            $tmp0 = $arr[$i][0];
            $tmp1 = $arr[$i][1];
            $str.= $tmp0 . "::" . $tmp1 . "::";
        }
        return substr($str, 0, -2);
    }
    //************   begin add for 'MySQL tables import/export' #__booklibrary_review   **********
    function wise_select_review() {
        global $database;
        $fcnt = 0;
        $cnt = 0;
        $parenttmp = 0;
        $result = '';
        $reslt = '';
        $resrt = '';
        $reslt = "# Dump of  \n";
        $reslt.= "# Dump DATE : " . date("d-M-Y") . "\n\n\n";
        $query = 'select * from #__booklibrary_review order by id';
        $database->setQuery($query);
        $Rows = $database->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return;
        }
        for ($i = 0;$i < count($Rows);$i++) {
            $reslt.= "INSERT INTO #__booklibrary_review (";
            $resrt.= ") VALUES ('";
            while (list($key, $value) = each($Rows[$i])) {
                $reslt.= $key . ",";
                switch ($fcnt) {
                    case 1:
                        $resrt.= "','";
                    break;
                    default:
                        $resrt.= $database->getEscaped($value) . "','";
                }
                $fcnt++;
            }
            $reslt = substr($reslt, 0, -1);
            $resrt = substr($resrt, 0, -2);
            $resrt.= ")|%|\n";
            $result.= ($reslt . $resrt);
            $reslt = '';
            $resrt = '';
            $fcnt = 0;
        }
        return $result;
    }
    function load_isbn() {
        global $database;
        $result = '';
        $result = "# Dump of  \n";
        $result.= "# Dump DATE : " . date("d-M-Y") . "\n\n\n";
        $query = 'SELECT review.id,book.isbn FROM #__booklibrary_review AS review, #__booklibrary AS book WHERE review.fk_bookid=book.id;';
        $database->setQuery($query);
        $rows = $database->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return;
        }
        for ($i = 0;$i < count($rows);$i++) {
            if ($i == 0) {
                $result.= "::" . $rows[$i]->id . "::" . $rows[$i]->isbn . "::";
            } else {
                $result.= $rows[$i]->id . "::" . $rows[$i]->isbn . "::";
            }
        }
        return $result;
    }
    //***********   end add for 'MySQL tables import/export' #__booklibrary_review   *************
    function entire_export() {
        global $mosConfig_absolute_path, $mosConfig_live_site;
        $cats = mosBooklibraryImportExport::wise_select_cat();
        $books = mosBooklibraryImportExport::wise_select_book();
        $reviews = mosBooklibraryImportExport::wise_select_review();
        $id_isbn = mosBooklibraryImportExport::load_isbn();
        $sarr = mosBooklibraryImportExport::select_catid();
        $carr = mosBooklibraryImportExport::select_linked();
        $fileName = "booklibrary_full_backup_" . date("Ymd_His") . ".dat";
        $fileBase = "/administrator/components/com_booklibrary/exports/";
        $file_path = $mosConfig_absolute_path . $fileBase . $fileName;
        $fp = fopen($file_path, "w");
        fwrite($fp, $books . "\n\n###CAT\n\n" . $cats . "\n\n###" . $sarr . "\n\n###" . $carr . "\n\n###REVIEW\n\n" . $reviews . "\n\n###ISBN\n\n" . $id_isbn);
        fclose($fp);
        $InformationArray = array();
        $InformationArray['out_file'] = $fileName;
        $InformationArray['urlBase'] = $mosConfig_live_site . $fileBase;
        $InformationArray['error'] = null;
        return $InformationArray;
    }
    //////////////  IMPORT  ////////////////
    function import_cat($whole, $supp_file) {
        global $database;
        $tmparr = array();
        $cont = explode('|%|', $whole);
        $arr = mosBooklibraryImportExport::load_arr($supp_file);
        for ($i = 0;$i < count($cont);$i++) {
            $strquer = substr($cont[$i], strpos($cont[$i], "INSERT"));
            if (substr($strquer, 0, 6) == "INSERT") {
                if ($arr[$i][1] == 0) {
                    $strquer = str_replace('%|%', '0', $strquer);
                } else {
                    $tmp_ind = mosBooklibraryImportExport::search_in_arr($arr[$i][1], $arr);
                    $strquer = str_replace('%|%', $tmparr[$tmp_ind], $strquer);
                }
                $database->setQuery($strquer);
                $database->query();
                if ($database->getErrorNum()) {
                    echo $database->stderr();
                    return $database->stderr();
                }
                $tmparr[$i] = $database->insertid();
                $arr[$i][1] = $tmparr[$i];
            }
        }
        return $arr;
    }
    function search_in_arr($search, $arr) {
        for ($i = 0;$i < count($arr);$i++) {
            if ($arr[$i][0] == $search) return $i;
        }
        return 0;
    }
    function load_arr($whole) {
        $arr = array();
        $cont = explode('::', $whole);
        for ($i = 0;$i < count($cont) / 2;$i++) {
            $arr[$i][0] = $cont[$i * 2];
            $arr[$i][1] = $cont[$i * 2 + 1];
        }
        return $arr;
    }
    function load_catid($whole) {
        $arr = array();
        $cont = explode('::', $whole);
        for ($i = 0;$i < count($cont);$i++) $arr[$i] = $cont[$i];
        return $arr;
    }
    function import_book($whole, $supp_file, $arr) {
        global $database;
        $ctidarr = mosBooklibraryImportExport::load_catid($supp_file);
        $cont = explode('|%|', $whole);
        for ($i = 0;$i < count($cont);$i++) {
            $strquer = substr($cont[$i], strpos($cont[$i], "INSERT"));
            if (substr($strquer, 0, 6) == "INSERT") {
                $tmp_ind = mosBooklibraryImportExport::search_in_arr($ctidarr[$i], $arr);
                $strquer = str_replace('%|%', $arr[$tmp_ind][1], $strquer);
                $database->setQuery($strquer);
                $database->query();
                if ($database->getErrorNum()) {
                    echo $database->stderr();
                    return $database->stderr();
                }
            }
        }
        return "";
    }
    function import_review($review, $isbn) {
        global $database; //$pc[4]=review,$pc[5]=isbn
        //select new bookid for review
        $query = 'SELECT id,isbn FROM #__booklibrary;';
        $database->setQuery($query);
        $rows = $database->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return;
        }
        $query = 'SELECT id FROM #__users;';
        $database->setQuery($query);
        $users = $database->loadObjectList();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return;
        }
        $st = $isbn;
        $tmp = explode('::', $isbn);
        $mas = "";
        for ($i = 1;$i < count($tmp) - 1;$i++) {
            for ($j = 0;$j < count($rows);$j++) {
                if ($tmp[$i] == $rows[$j]->isbn) $mas.= $rows[$j]->id . "::";
            }
        }
        //new fk_bookid = $mas[$i]
        $mas = explode('::', $mas); //potom -- 'count($mas)-1'//in $mas = new id for #__booklibrary
        $st = $review;
        $kol = strlen($st);
        $st = substr($st, strpos($st, "INSERT"), $kol);
        $insert = ""; //rus --> 'ÑÑ‚Ð¾ Ñ‚Ð¾Ñ‚ Ð¼Ð°ÑÑÐ¸Ð² Ð»ÐµÐ¼ÐµÐ½Ñ‚Ñ‹ ÐºÐ¾Ñ‚Ð¾Ñ€Ð¾Ð³Ð¾ Ð½Ð°Ð´Ð¾ Ð±ÑƒÐ´ÐµÑ‚ Ð²ÑÑ‚Ð°Ð²Ð¸Ñ‚ÑŒ Ð² Ð±Ð°Ð·Ñƒ'
        for ($i = 0;$i < count($mas) - 1;$i++) {
            $k = strpos($st, "|%|");
            $insert = substr($st, strpos($st, "INSERT"), $k);
            $insert_1 = substr($insert, strpos($insert, "INSERT"), strpos($insert, ",'',"));
            $insert_2 = substr($insert, strpos($insert, ",'',") + 4, strlen($insert));
            //insert user
            $usr = substr($insert, strpos($insert, ",'',") + 4, strlen($insert));
            //$usr_1 == number user id old from file .dat
            $usr_1 = substr($usr, strpos($usr, "'") + 1, strpos($usr, "','") - 1);
            $usr_1 = (int)$usr_1;
            $status = false;
            for ($j = 0;$j < count($users);$j++) {
                if ($users[$j]->id == $usr_1) $status = true;
            }
            if ($status) {
                $zapros = $insert_1 . ",'" . $mas[$i] . "'," . $insert_2;
            } else {
                $usr_2 = substr($usr, strpos($usr, ",'"), strlen($usr));
                $zapros = $insert_1 . ",'" . $mas[$i] . "','0'" . $usr_2;
            }
            $query = $zapros . ";";
            $database->setQuery($query);
            @$kuku = $database->loadResult();
            if ($database->getErrorNum()) {
                echo $database->stderr();
                return;
            }
            $kol = strlen($st);
            $st = substr($st, $k + 4, $kol);
        }
        return "";
    }
    static function remove_info() {
        global $database;
        $database->setQuery('truncate #__booklibrary');
        $database->query();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return $database->stderr();
        }
        $database->setQuery("delete from #__booklibrary_main_categories where section='com_booklibrary'");
        $database->query();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return $database->stderr();
        }
        $database->setQuery('truncate #__booklibrary_review');
        $database->query();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return $database->stderr();
        }
        $database->setQuery('truncate #__booklibrary_categories');
        $database->query();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return $database->stderr();
        }
        $database->setQuery('truncate #__booklibrary_lend');
        $database->query();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return $database->stderr();
        }
        $database->setQuery('truncate #__booklibrary_lend_request');
        $database->query();
        if ($database->getErrorNum()) {
            echo $database->stderr();
            return $database->stderr();
        }
        
        return "";
    }
    function entire_import($file) {
        global $mosConfig_absolute_path;
        $ret = mosBooklibraryImportExport::remove_info();
        if ($ret != "") return;
        $fp = fopen($file, "r");
        $whole = fread($fp, filesize($file));
        $pc = array();
        $pc = explode('###', $whole);
        $urr = mosBooklibraryImportExport::import_cat(trim($pc[1]), trim($pc[3]));
        if (!is_array($urr)) return;
        $ret = mosBooklibraryImportExport::import_book(trim($pc[0]), trim($pc[2]), $urr);
        //$pc[4]=review,$pc[5]=isbn
        $ret = mosBooklibraryImportExport::import_review(trim($pc[4]), trim($pc[5]));
        if ($ret == "") echo "<h2 style='color:#0f0;'>OK</h2>";
    }
}