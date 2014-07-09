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
$mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path'];

class mosBooklibrary_lend extends JTable {

    /** @var int - Primary key */
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
    var $lend_return = null;

    /** @var boolean */
    var $checked_out = null;

    /** @var time */
    var $checked_out_time = null;

    /** @var string - the user who lent this book if it's no user of the database */
    var $user_name = null;

    /** @var string the e-mail adress user who lent this book if it's no user of the database */
    var $user_email = null;

    /** @var string the e-mail adress user who lent this book if it's no user of the database */
    var $user_mailing = null;

    /**
     * @param database A database connector object
     */
    function __construct(&$db) {
        parent::__construct('#__booklibrary_lend', 'id', $db);
    }

    // overloaded check function
    function check() {

        // check if book is already lent out
        $this->_db->setQuery("SELECT id FROM #__booklibrary_lend "
                . "\nWHERE fk_bookid='$this->fk_bookid' AND lend_return = null"
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
    function getLendTo($userid) {
        if ($userid != null && $userid != 0) {
            $this->_db->setQuery("SELECT name, email from #__users where id=$userid");
            $help = $this->_db->loadRow();
            $this->user_name = $help[0];
            $this->user_email = $help[1];
        } else {
            $this->user_name = _BOOKLIBRARY_LABEL_ANONYMOUS;
            $this->user_email = null;
        }
    }

    function toXML1($xmlDoc, $elementname = "lend") {


        //create and append name element 
        $retVal = $xmlDoc->createElement("lend");

        $fk_userid = $xmlDoc->createElement("fk_userid");
        $fk_userid->appendChild($xmlDoc->createTextNode($this->fk_userid));
        $retVal->appendChild($fk_userid);

        $lend_from = $xmlDoc->createElement("lend_from");
        $lend_from->appendChild($xmlDoc->createTextNode($this->lend_from));
        $retVal->appendChild($lend_from);

        $lend_until = $xmlDoc->createElement("lend_until");
        $lend_until->appendChild($xmlDoc->createTextNode($this->lend_until));
        $retVal->appendChild($lend_until);

        $lend_return = $xmlDoc->createElement("lend_return");
        $lend_return->appendChild($xmlDoc->createTextNode($this->lend_return));
        $retVal->appendChild($lend_return);

        $user_name = $xmlDoc->createElement("user_name");
        $user_name->appendChild($xmlDoc->createTextNode($this->user_name));
        $retVal->appendChild($user_name);

        $user_email = $xmlDoc->createElement("user_email");
        $user_email->appendChild($xmlDoc->createTextNode($this->user_email));
        $retVal->appendChild($user_email);

        $user_mailing = $xmlDoc->createElement("user_mailing");
        $user_mailing->appendChild($xmlDoc->createTextNode($this->user_mailing));
        $retVal->appendChild($user_mailing);


        return $retVal;
    }

    function toXML2() {
        $retVal .= "<lend>\n";
        $retVal .= "<fk_userid>" . $this->fk_userid . "</fk_userid>\n";
        $retVal .= "<lend_from>" . $this->lend_from . "</lend_from>\n";
        $retVal .= "<lend_until>" . $this->lend_until . "</lend_until>\n";
        $retVal .= "<lend_return>" . $this->lend_return . "</lend_return>\n";
        $retVal .= "<user_name>" . $this->user_name . "</user_name>\n";
        $retVal .= "<user_email>" . $this->user_email . "</user_email>\n";
        $retVal .= "<user_mailing>" . $this->ordering . "</user_mailing>\n";
        $retVal .= "</lend>\n";

        return $retVal;
    }

}
