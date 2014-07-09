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
class mosBooklibrary_review extends JTable {

    /** @var int Primary key */
    var $id = null;

    /** @var int - the book id this lend is assosiated with */
    var $fk_bookid = null;

    /** @var int - the user id of the user who lent this book; can also be null if $user_name is set */
    var $fk_userid = null;

    /** @var datetime - date when adding this review */
    var $date = null;

    /** @var comment - the comment to this */
    var $comment = null;

    /** @var titel */
    var $title = null;

    /** @var rating */
    var $rating = 0;

    /** @var boolean */
    var $checked_out = null;

    /** @var time */
    var $checked_out_time = null;
    var $published = null;

    /**
     * @param database - A database connector object
     */
    function __construct(&$db) {
        parent::__construct('#__booklibrary_review', 'id', $db);
    }

    /**
     * @return array - name: the string of the user the book is lent to - e-mail: the e-mail address of the user
     */
    function getReviewFrom() {
        $retVal['name'] = null;
        $retVal['email'] = null;
        if ($this->fk_userid != null && $this->fk_userid != 0) {
            $this->_db->setQuery("SELECT name, email from #__users where id=$this->fk_userid");
            $help = $this->_db->loadRow();
            $retVal['name'] = $help[0];
            $retVal['email'] = $help[1];
        } else {
            $retVal['name'] = _BOOKLIBRARY_LABEL_ANONYMOUS;
            $retVal['email'] = null;
        }
        return $retVal;
    }

    function toXML1($xmlDoc) {

        //create and append name element 
        $retVal = $xmlDoc->createElement("review");

        $fk_userid = $xmlDoc->createElement("fk_userid");
        $fk_userid->appendChild($xmlDoc->createTextNode($this->fk_userid));
        $retVal->appendChild($fk_userid);

        $rating = $xmlDoc->createElement("rating");
        $rating->appendChild($xmlDoc->createTextNode($this->rating));
        $retVal->appendChild($rating);

        $date = $xmlDoc->createElement("date");
        $date->appendChild($xmlDoc->createTextNode($this->date));
        $retVal->appendChild($date);

        $title = $xmlDoc->createElement("title");
        $title->appendChild($xmlDoc->createCDATASection($this->title));
        $retVal->appendChild($title);

        $comment = $xmlDoc->createElement("comment");
        $comment->appendChild($xmlDoc->createCDATASection($this->comment));
        $retVal->appendChild($comment);

        $published = $xmlDoc->createElement("published");
        $published->appendChild($xmlDoc->createCDATASection($this->published));
        $retVal->appendChild($published);

        return $retVal;
    }

    function toXML2() {

        //create and append name element 
        $retVal = "<review>\n";
        $retVal .= "<fk_userid>" . $this->fk_userid . "</fk_userid>\n";
        $retVal .= "<rating>" . $this->rating . "</rating>\n";
        $retVal .= "<date>" . $this->date . "</date>\n";
        $retVal .= "<title><![CDATA[" . $this->title . "]]></title>\n";
        $retVal .= "<comment><![CDATA[" . $this->comment . "]]></comment>\n";
        $retVal .= "<published><![CDATA[" . $this->published . "]]></published>\n";
        $retVal .= "</review>\n";

        return $retVal;
    }

    function updateRatingBook() {
        $this->_db->setQuery("SELECT SUM(rating),COUNT(rating) FROM #__booklibrary_review WHERE fk_bookid=$this->fk_bookid");
        $rating = $this->_db->loadRow();
        //var_dump($this->fk_bookid,$rating);
        $this->_db->setQuery("UPDATE #__booklibrary as a SET a.rating=" . $rating[0] / $rating[1] . " WHERE a.id = $this->fk_bookid");
        $this->_db->query();
        echo $this->_db->getErrorMsg(); //exit;
    }

}
