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
require_once ($mosConfig_absolute_path . "/components/com_booklibrary/booklibrary.class.lend.php");
require_once ($mosConfig_absolute_path . "/components/com_booklibrary/booklibrary.class.review.php");

/**
 * Book database table class
 */
class mosBooklibrary extends JTable {//for 1.6
    /** @var int Primary key */

    var $id = null;

    /** @var int */
    var $bookid = null;
    /** @var int */

    /** @var int */
    //var $sid=null;
    /** @var string */
    var $isbn = null;

    /** @var datetime */
    var $date = null;

    /** @var int */
    var $hits = null;

    /** @var int */
    var $ordering = null;

    /** @var int */
    var $published = null;

    /** @var boolean */
    var $checked_out = null;

    /** @var time */
    var $checked_out_time = null;

    /** @var int */
    var $archived = null;

    /** @var varchar(200) */
    var $title = null;

    /** @var varchar(200) */
    var $imageURL = null;

    /** @var varchar(250) */
    var $URL = null;

    /** @var int */
    var $rating = null;

    /** @var varchar(200) */
    var $authors = null;

    /** @var varchar(200) */
    var $manufacturer = null;

    /** @var varchar(300) */
    var $comment = null;

    /** @var int */
    var $informationFrom = null;

    /** @var varchar(3) */
    var $language = null;
    var $langshow = null;

    /** @var int */
    var $fk_lendid = null;

    /** @var publication year */
    var $release_Date = null;

    /** @var edition */
    var $edition = null;

    /** @var varchar(100) */
    var $featured_clicks = null;

    /** @var varchar(100) */
    var $featured_shows = null;

    /** @var ebookURL */
    var $ebookURL = null;

    /** @var price */
    var $price = null;

    /** @var priceunit */
    var $priceunit = null;

    /** @var vm_id_product */
    var $vm_id_product = null;

    /** @var numberOfPages */
    var $numberOfPages = null;

    /** @var owneremail */
    var $owneremail = null;
    var $owner_id = 0;

    //var $categs=null;
    /**
     * @param database - A database connector object
     */
    function __construct(&$db) {
        ///$this->mosDBTable( '#__booklibrary', 'id', $db );
        //$this->JTable( '#__booklibrary', 'id', $db );//for 1.6
        parent::__construct('#__booklibrary', 'id', $db); //for 1.6
    }

    // overloaded check function
    function check() {
        global $booklibrary_configuration;

        // check for valid name
        if (trim($this->isbn) == '') {
            $this->setError(_BOOKLIBRARY_LABEL_ISBN);
            return false;
        }

        // check for existing ISBN

        $this->_db->setQuery("SELECT id FROM $this->_tbl "
                . "\nWHERE bookid='$this->bookid'");
        $xid = intval($this->_db->loadResult());
        if ($xid && $xid != intval($this->id)) {
            $this->setError(_BOOKLIBRARY_ADMIN_INFOTEXT_JS_EDIT_BOOKID);
            return false;
        }
        if ($booklibrary_configuration['editbook']['check']['isbn'] == '1') {
            $this->_db->setQuery("SELECT id FROM #__booklibrary "
                    . "\nWHERE isbn='$this->isbn'");
            $xid = intval($this->_db->loadResult());
            if ($xid && $xid != intval($this->id)) {
                $this->_error = _BOOKLIBRARY_ADMIN_INFOTEXT_JS_EDIT_ISBN;
                return false;
            }
        }
        return true;
    }

    /**
     * Is used to chek exist book the ISBN number
     * @param string - the ISBN number
     */
    function checkISBN($isbn) {
        $this->_db->setQuery("SELECT 1 FROM $this->_tbl WHERE isbn='$isbn'");
        if ($this->_db->loadResult())
            return true; else
            return false;
    }

    /**
     * Is used to load a book by the ISBN number
     * @param string - the ISBN number
     */
    function loadISBN($isbn) {
        $this->_db->setQuery("SELECT * FROM $this->_tbl WHERE isbn='$isbn'");
        return $this->_db->loadObject($this);
    }

    /**
     * @param string - Target search string
     * not used at the moment
     */
    function search($text, $state = '', $sectionPrefix = '') {
        $text = trim($text);
        /** if ($text == '') { * */
        return array();
        /*         * }

          $this->_db->setQuery( "SELECT date AS created, title,"
          . "\n	author,  '1' AS browsernav, '{$sectionPrefix}Books' AS section"
          . "\nFROM #__booklibrary WHERE (title LIKE '%$text%' OR author LIKE '%$text%'"
          . "\n)"
          . "\n ORDER BY created DESC"
          );

          return $this->_db->loadObjectList(); * */
    }

    function getMaxBookid() {
        $this->_db->setQuery("SELECT MAX(CONVERT(bookid,DECIMAL)) FROM $this->_tbl");
        return $this->_db->loadResult();
    }

    //set book->categs array
    function setCategs() {
        $this->_db->setQuery("SELECT catid FROM #__booklibrary_categories \n" .
                "WHERE bookid='$this->id'");
        if (version_compare(JVERSION, '3.0', 'lt')) {
            $this->categs = $this->_db->loadResultArray();
        } else {
            $this->categs = $this->_db->loadColumn();
        }
    }

    //check access to book
    function getAccessBook() {

        if (!isset($this->categs))
            $this->setCategs();
        //---------------------- 3.5 ? peresmotret.
        if (version_compare(JVERSION, '3.0.0', 'ge'))
            $categoriesid = $this->categs;
        else
            $categoriesid = implode(',', $this->categs);

        //echo "<br /><pre>" . print_r($this->categs, true) . "<pre>";
        //echo "<br /><pre>" . print_r($categoriesid, true) . "<pre>"; exit;
        //print_r($this);exit;
///echo "=[";print_r($categoriesid);echo "]=";

        if (!$categoriesid) {
            return;
        }
        $this->_db->setQuery("SELECT params FROM #__booklibrary_main_categories WHERE id IN ($categoriesid[0])");

        /*  if(version_compare(JVERSION, '3.0', 'lt')) 
          {
          $this->_db->setQuery("SELECT params FROM #__booklibrary_main_categories WHERE id IN ($categoriesid[0])");
          }
          else
          {
          $this->_db->setQuery("SELECT params FROM #__booklibrary_main_categories WHERE id IN ($categoriesid)");
          }
         */
//       if(version_compare(JVERSION, '3.5.0', 'lt')) {
// 	$accesses = $this->_db->loadColumn();
// 	//$this->categs = $a[0];  
//       }else{
// 	$accesses = $this->_db->loadResultArray();
//       }

        if (version_compare(JVERSION, '3.0', 'lt')) {
            $accesses = $this->_db->loadResultArray();
        } else {
            $accesses = $this->_db->loadColumn();
        }

        //$accesses=$this->_db->loadResultArray();

        foreach ($accesses as $key => $access) {
            if ($access == '')
                $accesses[$key] = '-2';
        }
        return implode(',', $accesses);
    }

    //save array book->categs do #__booklibrary_categories
    function saveCategs() {
        $values = array();
        $categories = $this->categs;
        foreach ($categories as $category) {

            $values[] = '(' . $this->id . ',' . $category . ')';
        }

        $queryvalue = implode(', ', $values);

        $this->_db->setQuery("DELETE FROM #__booklibrary_categories \n" .
                "WHERE bookid=" . $this->id);
        $this->_db->query();
        $this->_db->setQuery("INSERT INTO #__booklibrary_categories (bookid,catid) \n" .
                "VALUES $queryvalue");
        $this->_db->query();
        echo $this->_db->getErrorMsg();
        //exit;
    }

    function getReviews() {
        $this->_db->setQuery("SELECT id FROM #__booklibrary_review \n" .
                "WHERE fk_bookid='$this->id' ORDER BY id");
        //$tmp = $this->_db->loadResultArray();	
        if (version_compare(JVERSION, '3.0', 'lt')) {
            $tmp = $this->_db->loadResultArray();
        } else {
            $tmp = $this->_db->loadColumn();
        }

        $retVal = array();
        for ($i = 0, $j = count($tmp); $i < $j; $i++) {
            $help = new mosBooklibrary_review($this->_db);
            $help->load(intval($tmp[$i]));
            $retVal[$i] = $help;
        }
        return $retVal;
    }

    function getLend() {
        $lend = null;
        if ($this->fk_lendid != null && $this->fk_lendid != 0) {
            $lend = new mosBookLibrary_lend($this->_db);
            // load the row from the db table
            $lend->load(intval($this->fk_lendid));
        }
        return $lend;
    }

    function getAllLends($exclusion = "") {
        $this->_db->setQuery("SELECT id FROM #__booklibrary_lend \n" .
                "WHERE fk_bookid='$this->id' " . $exclusion . " ORDER BY id");
        //$tmp = $this->_db->loadResultArray();	
        if (version_compare(JVERSION, '3.0', 'lt')) {
            $tmp = $this->_db->loadResultArray();
        } else {
            $tmp = $this->_db->loadColumn();
        }

        $retVal = array();
        for ($i = 0, $j = count($tmp); $i < $j; $i++) {
            $help = new mosBooklibrary_lend($this->_db);
            $help->load(intval($tmp[$i]));
            $retVal[$i] = $help;
        }
        return $retVal;
    }

    function getAllLendRequests($exclusion = "") {
        $this->_db->setQuery("SELECT id FROM #__booklibrary_lend_request \n" .
                "WHERE fk_bookid='$this->id'" . $exclusion . " ORDER BY id");
        //$tmp = $this->_db->loadResultArray();		
        if (version_compare(JVERSION, '3.0', 'lt')) {
            $tmp = $this->_db->loadResultArray();
        } else {
            $tmp = $this->_db->loadColumn();
        }

        $retVal = array();
        for ($i = 0, $j = count($tmp); $i < $j; $i++) {
            $help = new mosBooklibrary_lend_request($this->_db);
            $help->load(intval($tmp[$i]));
            $retVal[$i] = $help;
        }
        return $retVal;
    }

    function toXML1($xmlDoc, $all) {

        //create and append name element 
        $retVal = $xmlDoc->createElement("book");

        $bookid = $xmlDoc->createElement("bookid");
        $bookid->appendChild($xmlDoc->createTextNode($this->bookid));
        $retVal->appendChild($bookid);

        $catid = $xmlDoc->createElement("isbn");
        $catid->appendChild($xmlDoc->createTextNode($this->isbn));
        $retVal->appendChild($catid);

        $title = $xmlDoc->createElement("title");
        $title->appendChild($xmlDoc->createCDATASection($this->title));
        $retVal->appendChild($title);

        $authors = $xmlDoc->createElement("authors");
        $authors->appendChild($xmlDoc->createCDATASection($this->authors));
        $retVal->appendChild($authors);

        $manufacturer = $xmlDoc->createElement("manufacturer");
        $manufacturer->appendChild($xmlDoc->createCDATASection($this->manufacturer));
        $retVal->appendChild($manufacturer);

        $releasedate = $xmlDoc->createElement("releaseDate");
        $releasedate->appendChild($xmlDoc->createTextNode($this->release_Date));
        $retVal->appendChild($releasedate);

        $language = $xmlDoc->createElement("language");
        $language->appendChild($xmlDoc->createTextNode($this->language));
        $retVal->appendChild($language);

        $langshow = $xmlDoc->createElement("langshow");
        $langshow->appendChild($xmlDoc->createTextNode($this->langshow));
        $retVal->appendChild($langshow);

        $hits = $xmlDoc->createElement("hits");
        $hits->appendChild($xmlDoc->createTextNode($this->hits));
        $retVal->appendChild($hits);

        $rating = $xmlDoc->createElement("rating");
        $rating->appendChild($xmlDoc->createTextNode($this->rating));
        $retVal->appendChild($rating);

        $numberOfPages = $xmlDoc->createElement("numberOfPages");
        $numberOfPages->appendChild($xmlDoc->createTextNode($this->numberOfPages));
        $retVal->appendChild($numberOfPages);

        $price = $xmlDoc->createElement("price");
        $price->appendChild($xmlDoc->createTextNode($this->price));
        $retVal->appendChild($price);

        $url = $xmlDoc->createElement("url");
        $url->appendChild($xmlDoc->createCDATASection($this->URL));
        $retVal->appendChild($url);

        $imageURL = $xmlDoc->createElement("imageURL");
        $imageURL->appendChild($xmlDoc->createCDATASection($this->imageURL));
        $retVal->appendChild($imageURL);

        $edition = $xmlDoc->createElement("edition");
        $edition->appendChild($xmlDoc->createCDATASection($this->edition));
        $retVal->appendChild($edition);

        $featured_shows = $xmlDoc->createElement("featured_shows");
        $featured_shows->appendChild($xmlDoc->createCDATASection($this->featured_shows));
        $retVal->appendChild($featured_shows);

        $featured_clicks = $xmlDoc->createElement("featured_clicks");
        $featured_clicks->appendChild($xmlDoc->createCDATASection($this->featured_clicks));
        $retVal->appendChild($featured_clicks);

        $ebookURL = $xmlDoc->createElement("ebookURL");
        $ebookURL->appendChild($xmlDoc->createCDATASection($this->ebookURL));
        $retVal->appendChild($ebookURL);

        $informationFrom = $xmlDoc->createElement("informationFrom");
        $informationFrom->appendChild($xmlDoc->createTextNode($this->informationFrom));
        $retVal->appendChild($informationFrom);

        $date = $xmlDoc->createElement("date");
        $date->appendChild($xmlDoc->createTextNode($this->date));
        $retVal->appendChild($date);

        $comment = $xmlDoc->createElement("comment");
        $comment->appendChild($xmlDoc->createCDATASection($this->comment));
        $retVal->appendChild($comment);


        if ($all) {

            $reviews = $xmlDoc->createElement("reviews");
            $reviews_data = $this->getReviews();
            foreach ($reviews_data as $review_data) {
                $reviews->appendChild($review_data->toXML($xmlDoc));
            }
            $retVal->appendChild($reviews);
        }

        return $retVal;
    }

    function toXML2() {

        $retVal = "<book>\n";
        $retVal .= "<bookid>" . $this->bookid . "</bookid>\n";
        $retVal .= "<isbn>" . $this->isbn . "</isbn>\n";
        $retVal .= "<title><![CDATA[" . $this->title . "]]></title>\n";
//		$retVal .= "<title>" . htmlspecialchars( $this->title) . "</title>\n";
        $retVal .= "<authors><![CDATA[" . $this->authors . "]]></authors>\n";
        $retVal .= "<manufacturer><![CDATA[" . $this->manufacturer . "]]></manufacturer>\n";
        $retVal .= "<releaseDate>" . $this->release_Date . "</releaseDate>\n";
        $retVal .= "<language>" . $this->language . "</language>\n";
        $retVal .= "<langshow>" . $this->langshow . "</langshow>\n";
        $retVal .= "<hits>" . $this->hits . "</hits>\n";
        $retVal .= "<rating>" . $this->rating . "</rating>\n";
        $retVal .= "<price>" . $this->price . "</price>\n";
        $retVal .= "<priceunit>" . $this->priceunit . "</priceunit>\n";
        $retVal .= "<numberOfPages>" . $this->numberOfPages . "</numberOfPages>\n";
        $retVal .= "<url><![CDATA[" . $this->URL . "]]></url>\n";
        $retVal .= "<imageURL><![CDATA[" . $this->imageURL . "]]></imageURL>\n";
        $retVal .= "<edition><![CDATA[" . $this->edition . "]]></edition>\n";
        $retVal .= "<ebookURL><![CDATA[" . $this->ebookURL . "]]></ebookURL>\n";
        $retVal .= "<featured_clicks><![CDATA[" . $this->featured_clicks . "]]></featured_clicks>\n";
        $retVal .= "<featured_shows><![CDATA[" . $this->featured_shows . "]]></featured_shows>\n";
        $retVal .= "<informationFrom>" . $this->informationFrom . "</informationFrom>\n";
        $retVal .= "<date>" . $this->date . "</date>\n";
        $retVal .= "<comment><![CDATA[" . $this->comment . "]]></comment>\n";
        $retVal .= "<published>" . $this->published . "</published>\n";
        $retVal .= "<owneremail><![CDATA[" . $this->owneremail . "]]></owneremail>\n";
        $retVal .= "<owner_id><![CDATA[" . $this->owner_id . "]]></owner_id>\n";
        $retVal .= "<vm_id_product>" . $this->vm_id_product . "</vm_id_product>\n";

        $retVal .= "<reviews>\n";
        $reviews = $this->getReviews();
        foreach ($reviews as $review) {
            $retVal .= $review->toXML2();
        }
        $retVal .= "</reviews>\n";

        $retVal .= "<categs>\n";
        //if($this->categs=='')
        $this->setCategs();
        $categs = $this->categs;


        foreach ($categs as $categ) {
            $retVal .= "<categ>" . $categ . "</categ>";
        }

        $retVal .= "</categs>\n";

        $retVal .= "</book>\n";


        return $retVal;
    }

    function delete($pk = NULL) {

        if ($this->imageURL)
            @unlink($this->imageURL);
        //echo "<br /><pre>" . print_r($this->id) . "</pre>"; exit;
        $this->_db->setQuery("DELETE FROM #__booklibrary_review WHERE fk_bookid IN ($this->id)");
        if ($this->_db->query()) {
            echo "<script> alert('" . addslashes($this->_db->getErrorMsg()) . "'); window.history.go(-1); </script>\n";
        }
        $this->_db->setQuery("DELETE FROM #__booklibrary_categories WHERE bookid IN ($this->id)");
        if ($this->_db->query()) {
            echo "<script> alert('" . addslashes($this->_db->getErrorMsg()) . "'); window.history.go(-1); </script>\n";
        }
        $this->_db->setQuery("DELETE FROM #__booklibrary WHERE id IN ($this->id)");
        if (!$this->_db->query()) {
            echo "<script> alert('" . addslashes($this->_db->getErrorMsg()) . "'); window.history.go(-1); </script>\n";
        }
    }

}
