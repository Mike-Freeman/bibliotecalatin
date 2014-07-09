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
class mosBooklibraryWS {
    /**
     * Catching information from array - in the correct language
     * @return array that contains all Output from getWSArray() including the
     * possibility for user to input the information directly
     */
    static function getArray() {
        $help = mosBooklibraryWS::getWSArray();
        $help1 = array();
        array_push($help1, array(0, _BOOKLIBRARY_WS_NO, ""));
        return array_merge($help1, $help);
    }
    /**
     * Catching WS Informations array
     * @return array containing all possible WS and their settings
     */
    static function getWSArray() {
        global $mosConfig_absolute_path;
        $retVal = array();
        array_push($retVal, array(1, _BOOKLIBRARY_AMAZON_COM, "COM"));
        array_push($retVal, array(2, _BOOKLIBRARY_AMAZON_UK, "UK"));
        array_push($retVal, array(3, _BOOKLIBRARY_AMAZON_DE, "DE"));
        array_push($retVal, array(4, _BOOKLIBRARY_AMAZON_JP, "JP"));
        array_push($retVal, array(5, _BOOKLIBRARY_AMAZON_FR, "FR"));
        array_push($retVal, array(6, _BOOKLIBRARY_AMAZON_CA, "CA"));
        array_push($retVal, array(7, _BOOKLIBRARY_AMAZON_IT, "IT"));
        array_push($retVal, array(8, _BOOKLIBRARY_AMAZON_ES, "ES"));
        array_push($retVal, array(9, _BOOKLIBRARY_AMAZON_CN, "CN"));
        array_push($retVal, array(10, _BOOKLIBRARY_AMAZON_US, "US"));
        
        return $retVal;
    }
    /**
     * fetching the information depending on the information already in the
     * $book parameter; if parameter is set to insert information on your own
     * nothing is done,
     * @param booklibrary.class.php store the information that is already
     * known of this book - $informationFrom must be set at least
     * @return booklibrary.class.php including all information that should be
     * added by webservices
     */
    function fetchInfos($book) {
        if (intval($book->informationFrom) != 0) {
            if ($book->informationFrom < 11) {
                return mosBooklibraryWS::fetchAmazonInfosForBookRest($book); //Amazon WS
                
            } else {
                return mosBooklibraryWS::fetchYazInfos($book);
            }
        } else {
            //information is already provided by the user
            return $book;
        }
    }
    function fetchYazInfos($book) {
        global $booklibrary_configuration;
        if (!extension_loaded('yaz')) {
            echo "<script> alert('Sorry, \'yaz.so\' isn\'t loaded....'); window.history.go(-1);</script>\n";
            exit;
        }
        $param_ws = mosBooklibraryWS::getWsParamById($book->informationFrom) - 1;
        $hosts = mosBooklibraryWS::getWSArray();
        $str_conect = trim($hosts[$param_ws][3]) . ":" . trim($hosts[$param_ws][4]) . "/" . trim($hosts[$param_ws][5]);
        //$str_conect ="z3950.bibsys.no:2100";// "140.147.249.38:7090/voyager";
        //$str_conect = "troy.lib.sfu.ca:210/innopac";
        //echo $str_conect;
        $id = yaz_connect($str_conect);
        yaz_syntax($id, $hosts[$param_ws][6]);
        $query = '@attr 1=7  ' . $book->isbn;
        yaz_search($id, 'rpn', $query);
        yaz_wait();
        $error = yaz_error($id);
        if (!empty($error)) {
            echo "<script> alert('ERROR:" . addslashes($error) . "'); window.history.go(-1);</script>\n";
            exit;
        }
        $rec = yaz_record($id, 1, "array");
        $error = yaz_error($id);
        if (yaz_hits($id) == 0) {
            echo "<script> alert('On this ISBN(" . addslashes($book->isbn) . ") of the not found records'); window.history.go(-1);</script>\n";
            yaz_close($id);
            exit;
        } elseif (!empty($error)) {
            echo "<script> alert('ERROR:" . addslashes($error) . "'); window.history.go(-1);</script>\n";
            yaz_close($id);
            exit;
        }
        yaz_close($id);
        $book->comment = "";
        $book->title = "";
        $book->authors = "";
        $book->manufacturer = "";
        $book->release_Date = "";
        $book->URL = "";
        foreach($rec as $i => $value) {
            $s = explode(")(", $rec[$i][0]);
            if (isset($s[2])) {
                switch (substr($s[0], 3, strlen($s[0]))) {
                    case '245':
                        $book->comment.= $rec[$i][1];
                        if (substr($s[2], 2, strlen($s[2]) - 3) == 'a') {
                            $book->title.= $rec[$i][1];
                        }
                    break;
                    case '100':
                        $book->authors.= $rec[$i][1];
                    break;
                    case '260':
                        $book->manufacturer.= $rec[$i][1];
                        if (substr($s[2], 2, strlen($s[2]) - 3) == 'c') {
                            $book->release_Date.= $rec[$i][1];
                        }
                    break;
                    case '856':
                        $book->URL.= $rec[$i][1];
                    break;
                }
            }
        }
        return $book;
    }
    /**
     * fetch the information from a webservice depending on the $informationFrom
     * variable set in the $book
     * @param booklibrary.class.php store the information that is already
     * known of this book - $informationFrom must be set at least
     * @return booklibrary.class.php including all information that should be
     * added by webservices
     * @global string $booklibrary_configuration
     */
    //***********************   add in function 'books'   *************************
    function fetchAmazonInfos($book) {
        global $booklibrary_configuration, $my, $acl;
        //******************************   Added by OrdaSoft   **********************************
        $param_ws = mosBooklibraryWS::getWsParamById($book->informationFrom);
        $Timestamp = date("Y-m-d") . "T" . date("H:i:s") . "Z";
        //if amazon.com
        if (($param_ws == "COM") || ($param_ws == "UK") || ($param_ws == "CA")) {
            try {
                if (($param_ws == "UK") || ($param_ws == "CA")) {
                    $client = new soapclient("https://webservices.amazon.com/AWSECommerceService/" . $param_ws . "/AWSECommerceService.wsdl", array('proxy_host' => $booklibrary_configuration['proxy_server']['address'], 'proxy_port' => $booklibrary_configuration['port_proxy_server']['address'], 'proxy_login' => $booklibrary_configuration['login_proxy_server']['address'], 'proxy_password' => $booklibrary_configuration['password_proxy_server']['address']));
                } else if (($param_ws == "COM")) {
                    $client = new soapclient("https://webservices.amazon.com/AWSECommerceService/AWSECommerceService.wsdl", array('proxy_host' => $booklibrary_configuration['proxy_server']['address'], 'proxy_port' => $booklibrary_configuration['port_proxy_server']['address'], 'proxy_login' => $booklibrary_configuration['login_proxy_server']['address'], 'proxy_password' => $booklibrary_configuration['password_proxy_server']['address']));
                }
                $client->xml_encoding = "UTF-8";
                $params = array('Request' => array('SearchIndex' => 'Books', //add for isbn-13
                'IdType' => 'ISBN', //ISBN - for isbn-13  'ASIN' - for isbn-10
                'ItemId' => $book->isbn, //'0596005431',
                'ResponseGroup' => 'Large'), //Medium
                //old valid                'AWSAccessKeyId' => '1Z21K9KD9G8MAN3VWV82'
                'AWSAccessKeyId' => $booklibrary_configuration['ws']['amazon']['devtag'], 'AssociateTag' => $booklibrary_configuration['ws']['amazon']['tag'], 'Timestamp' => $Timestamp, 'Signature' => "ItemLookup" . $Timestamp);
                $result = $client->ItemLookup($params);
            }
            catch(SoapFault $fault) {
                $retVal = "SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})";
                return $retVal;
                //                  trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
                
            }
        }
        //if amazon. UK--yes DE--yes JP--yes(not reviews) FR--yes CA--yes
        else {
            try {
                $client = new soapclient("https://webservices.amazon.com/AWSECommerceService/" . $param_ws . "/AWSECommerceService.wsdl", array('proxy_host' => $booklibrary_configuration['proxy_server']['address'], 'proxy_port' => $booklibrary_configuration['port_proxy_server']['address'], 'proxy_login' => $booklibrary_configuration['login_proxy_server']['address'], 'proxy_password' => $booklibrary_configuration['password_proxy_server']['address']));
                $client->xml_encoding = "UTF-8";
                $params = array('Request' => array('IdType' => 'ASIN', 'ItemId' => $book->isbn, //'0596005431',
                'ResponseGroup' => 'Large'), //Medium
                //old valid                'AWSAccessKeyId' => '1Z21K9KD9G8MAN3VWV82'
                'AWSAccessKeyId' => $booklibrary_configuration['ws']['amazon']['devtag'], 'AssociateTag' => $booklibrary_configuration['ws']['amazon']['tag'], 'Timestamp' => $Timestamp, 'Signature' => "ItemLookup" . $Timestamp);
                $result = $client->ItemLookup($params);
            }
            catch(SoapFault $fault) {
                $retVal = "SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})";
                return $retVal;
                //                  trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
                
            }
        }
        //Errors test -- 1
        if (array_key_exists('Errors', $result->OperationRequest)) {
            $retVal = "SOAP Fault: (faultcode: {$result->OperationRequest->Errors->Error->Code}, faultstring: {$result->OperationRequest->Errors->Error->Message})";
            return $retVal;
        }
        //Errors test -- 2
        if (array_key_exists('Errors', $result->Items->Request)) {
            $retVal = "SOAP Fault: (faultcode: {$result->Items->Request->Errors->Error->Code}, faultstring: {$result->Items->Request->Errors->Error->Message})";
            return $retVal;
        }
        //Body -- Output in joomla form
        //ProductName
        $book->title = $result->Items->Item->ItemAttributes->Title;
        //ImageUrlMedium
        $book->imageURL = $result->Items->Item->MediumImage->URL;
        //URL
        $book->URL = $result->Items->Item->DetailPageURL;
        //Manufacturer
        if (array_key_exists('Manufacturer', $result->Items->Item->ItemAttributes)) {
            $book->manufacturer = $result->Items->Item->ItemAttributes->Manufacturer;
        }
        //Author
        $book->authors = "";
        if (is_array($result->Items->Item->ItemAttributes->Author)) { //Authors array
            foreach($result->Items->Item->ItemAttributes->Author as $Author) {
                if (strlen($book->authors) > 0) {
                    $book->authors = $book->authors . ", " . $Author;
                } else {
                    $book->authors = $Author;
                }
            }
        } else {
            $book->authors = $result->Items->Item->ItemAttributes->Author; //Authors not array
            
        }
        //Rating
        if (array_key_exists('CustomerReviews', $result->Items->Item)) {
            $book->rating = ($result->Items->Item->CustomerReviews->AverageRating * 2);
        }
        //PublicationDate
        if (array_key_exists('PublicationDate', $result->Items->Item->ItemAttributes)) {
            $book->release_Date = $result->Items->Item->ItemAttributes->PublicationDate;
        }
        //ReleaseDate
        if (array_key_exists('ReleaseDate', $result->Items->Item->ItemAttributes)) {
            $book->release_Date = $result->Items->Item->ItemAttributes->ReleaseDate;
        }
        //Edition
        if (array_key_exists('Edition', $result->Items->Item->ItemAttributes)) {
            $book->edition = $result->Items->Item->ItemAttributes->Edition;
        }
        //Price no partner
        if (array_key_exists('Offer', $result->Items->Item->Offers) && array_key_exists('FormattedPrice', $result->Items->Item->Offers->Offer->OfferListing->Price)) {
            $book->price = $result->Items->Item->Offers->Offer->OfferListing->Price->FormattedPrice;
            $mas = $book->price;
            $mas = ereg_replace("\xC2\xA3", "GBP ", $mas); //for funt
            $mas = ereg_replace("\xEF\xBF\xA5", "JPY", $mas); //for ena
            $book->price = $mas;
        } else {
            $book->price = "Does not exist anymore!";
        }
        //************************   begin add for Book Description   *********************
        if (($booklibrary_configuration['merge_description']['use'])) {
            if (checkAccessBL($booklibrary_configuration['merge_description']['registrationlevel'], 'RECURSE', userGID_BL($my->id), $acl)) {
                $book->comment = $book->comment . "<br /><p></p> ";
            } else $book->comment = "";
        } else $book->comment = "";
        if ((array_key_exists('EditorialReviews', $result->Items->Item)) && (array_key_exists('EditorialReview', $result->Items->Item->EditorialReviews))) {
            if (count($result->Items->Item->EditorialReviews->EditorialReview) == 1) {
                $book->comment.= "<strong>" . $result->Items->Item->EditorialReviews->EditorialReview->Source . "</strong><br />";
                $book->comment.= $result->Items->Item->EditorialReviews->EditorialReview->Content;
            } else if (count($result->Items->Item->EditorialReviews->EditorialReview) >= 1) {
                for ($i = 0;$i < count($result->Items->Item->EditorialReviews->EditorialReview);$i++) {
                    $book->comment.= "<strong>" . $result->Items->Item->EditorialReviews->EditorialReview[$i]->Source . "</strong><br />";
                    $book->comment.= $result->Items->Item->EditorialReviews->EditorialReview[$i]->Content . "<br />";
                } //end for
                
            } //end if
            
        }
        //************************   end add for Book Description   ************************
        return $book;
    }
    //***********************   add in function 'books'   *************************
    function fetchAmazonInfosForBookRest($book, $amazon_version = '2011-08-01') {
        global $booklibrary_configuration, $my, $acl;
        //******************************   Added by OrdaSoft   **********************************
        $param_ws = mosBooklibraryWS::getWsParamById($book->informationFrom);
        if ($param_ws == "COM") $endpoint = "http://ecs.amazonaws.com/onca/xml";
        else if ($param_ws == "UK") $endpoint = "http://ecs.amazonaws.co.uk/onca/xml";
        else if ($param_ws == "CA") $endpoint = "http://ecs.amazonaws.ca/onca/xml";
        else if ($param_ws == "DE") $endpoint = "http://ecs.amazonaws.de/onca/xml";
        else if ($param_ws == "JP") $endpoint = "http://ecs.amazonaws.jp/onca/xml";
        else if ($param_ws == "FR") $endpoint = "http://ecs.amazonaws.fr/onca/xml";
        else if ($param_ws == "ES") $endpoint = "http://webservices.amazon.es/onca/xml";
        else if ($param_ws == "IT") $endpoint = "http://webservices.amazon.it/onca/xml";
        else if ($param_ws == "CN") $endpoint = "http://webservices.amazon.cn/onca/xml";
        else if ($param_ws == "US") $endpoint = "http://webservices.amazon.com/onca/xml";
        if ($booklibrary_configuration['ws']['amazon']['secret_key'] == "") $secret_key = "ooTVCJy06UNXeMujmlyso9Wj4VD1flgEPsCx5HYY";
        else $secret_key = $booklibrary_configuration['ws']['amazon']['secret_key'];
        $request = "$endpoint?" . "Service=AWSECommerceService" . "&Operation=ItemLookup" . "&Condition=All" . "&Version={$amazon_version}" . "&AWSAccessKeyId=" . $booklibrary_configuration['ws']['amazon']['devtag'] . "&AssociateTag=" . $booklibrary_configuration['ws']['amazon']['tag'] . "&SearchIndex=Books" . //add for isbn-13
        "&ResponseGroup=Large" . "&IdType=ISBN" . //ISBN - for isbn-13  'ASIN' - for isbn-10
        "&ItemId=$book->isbn";
        // Get a nice array of elements to work with
        $uri_elements = parse_url($request);
        // Grab our request elements
        $request = $uri_elements['query'];
        // Throw them into an array
        parse_str($request, $parameters);
        // Add the new required paramters
        $parameters['Timestamp'] = gmdate("Y-m-d\TH:i:s\Z");
        $parameters['Version'] = $amazon_version;
        // The new authentication requirements need the keys to be sorted
        ksort($parameters);
        // Create our new request
        foreach($parameters as $parameter => $value) {
            // We need to be sure we properly encode the value of our parameter
            $parameter = str_replace("%7E", "~", rawurlencode($parameter));
            $value = str_replace("%7E", "~", rawurlencode($value));
            $request_array[] = $parameter . '=' . $value;
        }
        // Put our & symbol at the beginning of each of our request variables and put it in a string
        $new_request = implode('&', $request_array);
        // Create our signature string
        $signature_string = "GET\n{$uri_elements['host']}\n{$uri_elements['path']}\n{$new_request}";
        if (function_exists("hash_hmac")) {
            $signature = urlencode(base64_encode(hash_hmac('sha256', $signature_string, $secret_key, true)));
        } elseif (function_exists("mhash")) {
            $signature = urlencode(base64_encode(mhash(MHASH_SHA256, $signature_string, $secret_key)));
        }
        // Create our signature using hash_hmac
        // new request
        $request = "http://{$uri_elements['host']}{$uri_elements['path']}?{$new_request}&Signature={$signature}";
        // Load the call and capture the document returned by the Shopping API
        //        if((int)ini_get('allow_url_fopen')==1)
        //    $result = simplexml_load_file($request);
        //        else
        //        {
        //            $retVal = "Error: variable 'allow_url_fopen' in php.ini set 'Off'. Fetch information require this variable On";
        //            return $retVal;
        //        }
        $ch = curl_init();
        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
        // grab URL and pass it to the browser
        $result = curl_exec($ch);
        // close cURL resource, and free up system resources
        curl_close($ch);
        $result = simplexml_load_string($result);
        //echo '<pre>';var_dump($result); echo '</pre>';exit;
        //Errors test -- 1
        if (array_key_exists('Errors', $result->OperationRequest)) {
            $retVal = "faultcode: {$result->OperationRequest->Errors->Error->Code}, faultstring: {$result->OperationRequest->Errors->Error->Message}";
            return $retVal;
        }
        //Errors test -- 2
        if (array_key_exists('Errors', $result->Items->Request)) {
            $retVal = "faultcode: {$result->Items->Request->Errors->Error->Code}, faultstring: {$result->Items->Request->Errors->Error->Message}";
            return $retVal;
        }
        //Body -- Output in joomla form
        //ProductName
        $book->title = (string)$result->Items->Item->ItemAttributes->Title;
        //ImageUrlMedium
        $book->imageURL = (string)$result->Items->Item->MediumImage->URL;
        //URL
        $book->URL = (string)$result->Items->Item->DetailPageURL;
        //Number Of Pages
        $book->numberOfPages = (int)$result->Items->Item->ItemAttributes->NumberOfPages;
        //Manufacturer
        if (array_key_exists('Manufacturer', $result->Items->Item->ItemAttributes)) {
            $book->manufacturer = (string)$result->Items->Item->ItemAttributes->Manufacturer;
        }
        //Author
        $i = 0;
        $book->authors = "";
        foreach($result->Items->Item->ItemAttributes->Author as $item) {
            if ($i > 0) $book->authors.= ', ';
            $book->authors.= $item;
            $i++;
        }
        //Rating
        //      if (array_key_exists('CustomerReviews', $result->Items->Item)) {
        //         $book->rating = (string)($result->Items->Item->CustomerReviews->AverageRating * 2);
        //      }
        //PublicationDate
        if (array_key_exists('PublicationDate', $result->Items->Item->ItemAttributes)) {
            $book->release_Date = (string)$result->Items->Item->ItemAttributes->PublicationDate;
        }
        //ReleaseDate
        if (array_key_exists('ReleaseDate', $result->Items->Item->ItemAttributes)) {
            $book->release_Date = (string)$result->Items->Item->ItemAttributes->ReleaseDate;
        }
        //Edition
        if (array_key_exists('Edition', $result->Items->Item->ItemAttributes)) {
            $book->edition = (string)$result->Items->Item->ItemAttributes->Edition;
        }
        //Price no partner
        if (array_key_exists('Offer', $result->Items->Item->Offers) && array_key_exists('FormattedPrice', $result->Items->Item->Offers->Offer->OfferListing->Price)) {
            $book->price = substr_replace((string)$result->Items->Item->Offers->Offer->OfferListing->Price->Amount, '.', -2, 0);
            $book->priceunit = (string)$result->Items->Item->Offers->Offer->OfferListing->Price->CurrencyCode;
            $mas = $book->price;
            //$mas = ereg_replace("\xC2\xA3", "GBP ", $mas);  //for funt
            //$mas = ereg_replace("\xEF\xBF\xA5", "JPY", $mas);  //for ena
            $book->price = $mas;
        } else {
            $book->price = "Does not exist anymore!";
        }
        //echo '<pre>';var_dump($book);echo '</pre>';exit;
        //************************   begin add for Book Description   *********************
        if (($booklibrary_configuration['merge_description']['use'])) {
            if (checkAccessBL($booklibrary_configuration['merge_description']['registrationlevel'], 'RECURSE', userGID_BL($my->id), $acl)) {
                $book->comment = $book->comment . "<br /><p></p> ";
            } else $book->comment = "";
        } else $book->comment = "";
        if ($result->Items->Item->EditorialReviews->EditorialReview) {
            foreach($result->Items->Item->EditorialReviews->EditorialReview as $item) {
                $book->comment.= "<strong>" . $item->Source . "</strong><br />";
                $book->comment.= $item->Content . "<br />";
            }
        }
        //************************   end add for Book Description   ************************
        return $book;
    }
    /**
     * Get the name of the WS by the id
     * @param int $id the id of the WS-Name that should be returned
     * @return string the name of the WS or null if it can't be found
     */
    static function getWsNameById($id) {
        $services = mosBooklibraryWS::getArray();
        for ($i = 0, $n = count($services);$i < $n;$i++) {
            if (intval($services[$i][0]) == intval($id)) {
                return $services[$i][1];
            }
        }
        return null;
    }
    /**
     * Get the parameter of the WS by the id
     * @param int $id the id of the WS-pram that should be returned
     * @return string the parameter of the WS or null if it can't be found
     */
    function getWsParamById($id) {
        $services = mosBooklibraryWS::getArray();
        for ($i = 0, $n = count($services);$i < $n;$i++) {
            if (intval($services[$i][0]) == intval($id)) {
                return $services[$i][2];
            }
        }
        return null;
    }
}