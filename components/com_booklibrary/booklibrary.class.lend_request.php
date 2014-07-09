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
///class mosBooklibrary_lend_request extends mosDBTable {
class mosBooklibrary_lend_request extends JTable { //for 1.6
    /** @var int Primary key */

    var $id = null;

    /** @var int - the book id this lend is assosiated with */
    var $fk_bookid = null;

    /** @var int - the user id of the user who lend this book can also be null if $user_name is set */
    var $fk_userid = null;

    /** @var datetime - since when this book is lend out */
    var $lend_from = null;

    /** @var datetime - when the book should be returned */
    var $lend_until = null;

    /** @var datetime - when the book realy was/is returned */
    var $lend_request = null;

    /** @var boolean */
    var $checked_out = null;

    /** @var time */
    var $checked_out_time = null;

    /** @var string - the user who lent this book if it's no user of the database */
    var $user_name = null;

    /** @var string – the name of the user who lent this book if it's no user of the database */
    var $user_email = null;

    /** @var string – the e-mail address of the user who lent this book if it's no user of the database */
    var $user_mailing = null;

    /** @var string – the e-mail address of the user who lent this book if it's no user of the database */
    var $status = null;

    /**
     * @param database - A database connector object
     */
    function __construct(&$db) {
        parent::__construct('#__booklibrary_lend_request', 'id', $db);
    }

    // overloaded check function
    function check() {

        // check if book is already lent out
        $this->_db->setQuery("SELECT fk_lendid FROM #__booklibrary "
                . "\nWHERE id='$this->fk_bookid' AND fk_lendid = null"
        );
        $xid = intval($this->_db->loadResult());
        if ($xid) {
            $this->_error = _BOOKLIBRARY_BOOK_LEND_OUT;
            return false;
        }
        return true;
    }

    /**
     * @return array – name: the string of the user the book is lent to - e-mail: the e-mail address of the user
     */
    function getLendTo() {
        $retVal['name'] = null;
        $retVal['email'] = null;
        if ($this->fk_userid != null && $this->fk_userid != 0) {
            $this->_db->setQuery("SELECT name, email from #__users where id=$this->fk_userid");
            $help = $this->_db->loadRow();
            $retVal['name'] = $help[0];
            $retVal['email'] = $help[1];
        } else {
            $retVal['name'] = $user_name;
            $retVal['email'] = $user_email;
        }
        return $retVal;
    }

    //status codes
    //0: just inserted
    //1: accepted
    //2: not accepted

    function accept() {
        global $my;
        if ($this->id == null) {
            return "Method called on a non instant object";
        }
        $this->checkout($my->id);

        //create new lend dataset
        $lend = new mosBookLibrary_lend($this->_db);
        $book = new mosBookLibrary($this->_db); //print_r($this);exit;
        //print_r($book);exit;
        //$book->checkout($my->id);
        $book->load($this->fk_bookid);
        //if($book->fk_lendid != 0){
        //	return "Book already lent out!";
        //}


        $lend->fk_bookid = $this->fk_bookid; //for 1.6
        $lend->fk_userid = $this->fk_userid; //for 1.6
        ///$lend->until=$this->lend_until;
        $lend->user_name = $this->user_name;
        $lend->user_email = $this->user_email;
        $lend->user_mailing = $this->user_mailing;


        $lend->lend_from = $this->lend_from; // date("Y-m-d H:i:s");
        $lend->lend_until = $this->lend_until;
        $lend->fk_bookid = $this->fk_bookid; //for 1.6
        $lend->fk_userid = $this->fk_userid; //for 1.6
        //	$lend->lend_until = $this->lend_until;		

        $data = JFactory::getDBO();
//rent check start
        $query = "SELECT * FROM #__booklibrary_lend where fk_bookid = " . $this->fk_bookid .
                " AND lend_return is NULL ";
        $data->setQuery($query);
        //$rentTerm = $data->loadObjectList();	

        if (version_compare(JVERSION, '3.5', 'lt')) {
            $rentTerm = $data->loadObjectList();
        } else {
            $rentTerm = $data->loadColumn();
        }
        //print_r($rentTerm);exit;
        $lend_from = substr($lend->lend_from, 0, 10);
        $lend_until = substr($lend->lend_until, 0, 10);

        if (isset($rentTerm[0])) {

            for ($e = 0, $m = count($rentTerm); $e < $m; $e++) {

                $rentTerm[$e]->lend_from = substr($rentTerm[$e]->lend_from, 0, 10);
                $rentTerm[$e]->lend_until = substr($rentTerm[$e]->lend_until, 0, 10);
                //проверка  аренды
                if (( $lend_from >= $rentTerm[$e]->lend_from && $lend_from <= $rentTerm[$e]->lend_until)
                        || ($lend_from <= $rentTerm[$e]->lend_from && $lend_until >= $rentTerm[$e]->lend_until)
                        || ( $lend_until >= $rentTerm[$e]->lend_from && $lend_until <= $rentTerm[$e]->lend_until)) {
                    echo "<script> alert('Sorry rent out from " . $rentTerm[$e]->lend_from .
                    " until " . $rentTerm[$e]->lend_until . "'); window.history.go(-1); </script>\n";
                    exit();
                }
            }
        }
        if (!$lend->check($lend)) {
            return '11111111111';
        }
        if (!$lend->store()) {
            return '22222222222222';
        }

        $lend->checkin();
        //update book with lend id		
        $book->fk_lendid = $lend->id;
        if (!$book->store()) {
            return '43333333';
        }
        $book->checkin();

        $this->status = 1;

        if (!$this->store()) {
            return $this->getError();
        }
        $this->checkin();
        return null;
    }

    function decline() {
        if ($this->id == null) {
            return "Method called on a non instant object";
        }
        $this->status = 2;
        if (!$this->store()) {
            return $this->getError();
        }
        return null;
    }

    function toXML1($xmlDoc) {

        //create and append name element 
        $retVal = $xmlDoc->createElement("lendrequest");

        $fk_userid = $xmlDoc->createElement("fk_userid");
        $fk_userid->appendChild($xmlDoc->createTextNode($this->fk_userid));
        $retVal->appendChild($fk_userid);

        $lend_from = $xmlDoc->createElement("lend_from");
        $lend_from->appendChild($xmlDoc->createTextNode($this->lend_from));
        $retVal->appendChild($lend_from);

        $lend_until = $xmlDoc->createElement("lend_until");
        $lend_until->appendChild($xmlDoc->createTextNode($this->lend_until));
        $retVal->appendChild($lend_until);

        $lend_request = $xmlDoc->createElement("lend_retquest");
        $lend_request->appendChild($xmlDoc->createTextNode($this->lend_request));
        $retVal->appendChild($lend_request);

        $user_name = $xmlDoc->createElement("user_name");
        $user_name->appendChild($xmlDoc->createTextNode($this->user_name));
        $retVal->appendChild($user_name);

        $user_email = $xmlDoc->createElement("user_email");
        $user_email->appendChild($xmlDoc->createTextNode($this->user_email));
        $retVal->appendChild($user_email);

        $user_mailing = $xmlDoc->createElement("user_mailing");
        $user_mailing->appendChild($xmlDoc->createTextNode($this->user_mailing));
        $retVal->appendChild($user_mailing);

        $status = $xmlDoc->createElement("status");
        $status->appendChild($xmlDoc->createTextNode($this->status));
        $retVal->appendChild($status);

        return $retVal;
    }

    function toXML2() {

        //create and append name element
        $retVal = "<lendrequest>";

        $retVal .= "<fk_userid>" . $this->fk_userid . "</fk_userid>\n";
        $retVal .= "<lend_from>" . $this->lend_from . "</lend_from>\n";
        $retVal .= "<lend_until>" . $this->lend_until . "</lend_until>\n";
        $retVal .= "<lend_retquest>" . $this->lend_retquest . "</lend_retquest>\n";
        $retVal .= "<user_name>" . $this->user_name . "</user_name>\n";
        $retVal .= "<user_email>" . $this->user_email . "</user_email>\n";
        $retVal .= "<user_mailing>" . $this->user_mailing . "</user_mailing>\n";
        $retVal .= "<status>" . $this->status . "</status>\n";
        $retVal .= "</lendrequest>\n";


        return $retVal;
    }

}
