<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
class TriggerTool extends WebserviceOutputBuilderCore {

    public static function addDateWithFormat($date) {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $date->format(DateTime::ISO8601);
    }

    public static function getDobWithFormat($date) {
        $dob = DateTime::createFromFormat('Y-m-d', $date);
        $dob->setTime(0, 0, 0);
        return $dob->format(DateTime::ISO8601);
    }
    public static function getCustomerByID($customer_id){
        if(!$customer_id){
            return null;
        }
        $customer = new Customer($customer_id);
        $arr = array();
        $arr['id'] = $customer_id;
        $arr['firstname'] = $customer->firstname;
        $arr['lastname'] = $customer->lastname;
        $arr['email'] = $customer->email;
        $arr['company'] = $customer->company;
        $arr['birthday'] = '0000-00-00' != $customer->birthday ? TriggerTool::getDobWithFormat($customer->birthday) : TriggerTool::getDobWithFormat(self::DEFAULT_DOB); // format ISO 8601
        if (0 != $customer->id_gender) {
            $arr['title'] = $customer->id_gender;
        }
        $lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $address_data = $customer->getAddresses($lang_id);
        if (!empty($address_data)) {
                $address = $address_data[0];
                $phone = $address['phone'];
                $mobile = $address['phone_mobile'];
                $arr['phone'] = $phone;
                $arr['mobile'] = $mobile;
                $arr['address1'] = $address['address1'];
                $arr['address2'] = $address['address2'];
                $arr['postcode'] = $address['postcode'];
                $arr['city'] = $address['city'];
                $arr['country'] = Country::getIsoById($address['id_country']);
        }
        else{
             $arr['phone'] = '';
                $arr['mobile'] = '';
                $arr['address1'] = '';
                $arr['address2'] = '';
                $arr['postcode'] = '';
                $arr['city'] = '';
                $arr['country'] = '';
        }
        $arr['optin'] = $customer->optin;
        $arr['datecreated'] = TriggerTool::addDateWithFormat($customer->date_add);
        $arr['dateupdated'] = TriggerTool::addDateWithFormat($customer->date_upd);
        $arr['language'] = Language::getIsoById($customer->id_lang);
        $customer_group_id = Customer::getDefaultGroupId($customer_id);
        $group_name = TriggerTool::getGroupName($customer_group_id);
        foreach ($group_name as $value) {
            $arr['group'][] = $value['name'];
        }
        return $arr;
    }
    public static function filterIdOrdersByDate($from, $to) {
        $sql_nd = 'SELECT DISTINCT `id_order`
            FROM `' . _DB_PREFIX_ . 'orders` ORDER BY id_order ASC';
        $sql_hd = 'SELECT DISTINCT `id_order`
            FROM `' . _DB_PREFIX_ . 'orders`
            WHERE `date_upd` between "' . $from . '" AND "' . $to . '" ORDER BY id_order ASC';
        if (isset($from) && isset($to)) {
            $id = Db::getInstance()->executeS($sql_hd);
        } else {
            $id = Db::getInstance()->executeS($sql_nd);
        }
        return $id;
    }
    public static function validateDate($date, $format = 'Y-m-d', $return = false, &$dateObj = null) {
        try {
            if ('Y-m' == $format) {
                $date = $date . '-01';
                $format = 'Y-m-d';
            } elseif ('Y' == $format) {
                $date = $date . '-01-01';
                $format = 'Y-m-d';
            }
            $d = DateTime::createFromFormat($format, $date);

            if ($return) {
                $dateObj = $d;
            }
            return $d && $d->format($format) == $date;
        } catch (\Exception $e) {
            self::showMethodInternalServerError();
            return;
        }
    }

    public static function filterIdContactsByDate($from, $to) {
        $sql_nd = 'SELECT DISTINCT `id_customer`
            FROM `' . _DB_PREFIX_ . 'customer` ORDER BY id_customer ASC';
        $sql_hd = 'SELECT DISTINCT `id_customer`
            FROM `' . _DB_PREFIX_ . 'customer`
            WHERE `date_upd` between "' . $from . '" AND "' . $to . '" ORDER BY id_customer ASC';
        if (isset($from) && isset($to)) {
            $id = Db::getInstance()->executeS($sql_hd);
        } else {
            $id = Db::getInstance()->executeS($sql_nd);
        }
        return $id;
    }

    
    public static function filterIdProductsByDate($from, $to) {
        $sql_nd = 'SELECT DISTINCT `id_product`
            FROM `' . _DB_PREFIX_ . 'product` ORDER BY id_product ASC';
        $sql_hd = 'SELECT DISTINCT `id_product`
            FROM `' . _DB_PREFIX_ . 'product`
            WHERE `date_upd` between "' . $from . '" AND "' . $to . '" ORDER BY id_product ASC';
        if (isset($from) && isset($to)) {
            $id = Db::getInstance()->executeS($sql_hd);
        } else {
            $id = Db::getInstance()->executeS($sql_nd);
        }
        return $id;
    }

    public static function filterIdCartsByDate($from, $to) {
        $sql_nd = 'SELECT DISTINCT `id_cart`
            FROM `' . _DB_PREFIX_ . 'cart` ORDER BY id_cart ASC';
        $sql_hd = 'SELECT DISTINCT `id_cart`
            FROM `' . _DB_PREFIX_ . 'cart`
            WHERE `date_upd` between "' . $from . '" AND "' . $to . '" ORDER BY id_cart ASC';
        if (isset($from) && isset($to)) {
            $id = Db::getInstance()->executeS($sql_hd);
        } else {
            $id = Db::getInstance()->executeS($sql_nd);
        }
        return $id;
    }
    
    public static function filterIdNewslettersByDate($from, $to) {
        $sql_nd = 'SELECT DISTINCT `id_customer`
            FROM `' . _DB_PREFIX_ . 'customer` ORDER BY id_customer ASC';
        $sql_hd = 'SELECT DISTINCT `id_customer`
            FROM `' . _DB_PREFIX_ . 'customer`
            WHERE `date_upd` between "' . $from . '" AND "' . $to . '" AND newsletter = 1  ORDER BY id_customer ASC';
        if (isset($from) && isset($to)) {
            $id = Db::getInstance()->executeS($sql_hd);
        } else {
            $id = Db::getInstance()->executeS($sql_nd);
        }
        return $id;
    }

    public static function getGroupName($id) {
        $sql = 'SELECT DISTINCT  gl.`name`
            FROM `' . _DB_PREFIX_ . 'group` g
            LEFT JOIN `' . _DB_PREFIX_ . 'group_lang` gl ON (g.`id_group` = gl.`id_group` AND gl.`id_lang` = ' . (int) Context::getContext()->language->id . ')
            WHERE g.id_group = ' . $id;
        $groupname = Db::getInstance()->executeS($sql);
        return $groupname;
    }

    public static function statusCode200Ok($data = null) {
        http_response_code(200);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires:' . date('r'));
        header('status: 200');
        header('statusmsg: Not Found');
        echo($data);
    }

    public static function statusCode404NotFound() {
        http_response_code(404);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires:' . date('r'));
        header('status: 404');
        header('statusmsg: Not Found');
    }

    public static function statusCode406NotAcceptable() {
        http_response_code(406);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires:' . date('r'));
    }

    public static function showMethodNotImplemented() {
        http_response_code(501);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires:' . date('r'));
        header('status: 501');
        header('statusmsg: Not Implemented');
        header('datas: ');
    }

    public static function showMethodInternalServerError() {
        http_response_code(500);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires:' . date('r'));
        header('status: 500');
    }

    public static function statusCode400BadRequest() {
        http_response_code(400);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires:' . date('r'));
    }

    public static function showMethodNotAllowed() {
        http_response_code(405);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires:' . date('r'));
        header('status: 405');
        header('statusmsg: Method Not Allowed');
        header('datas: ');
    }
    public static function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return $_SERVER;
        }

        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }
    
    public static function getMethod()
    {
        return self::getServer('REQUEST_METHOD');
    }
    
    public static function isGet()
    {
        if ('GET' == self::getMethod()) {
            return true;
        }

        return false;
    }
    
    public static function getSECRETHMAC() {
        return Configuration::get('PS_GEER_IO_HMAC_SECRET');
    }
    
    public static function hmachash($data, $salt = '', $iterations = 10) {
        $hash = $data;
        foreach (range(1, $iterations) as $i) {
            $hash = hash('sha256', $hash . md5($i) . $salt);
        }
        return $hash;
    }

    public static function test() {
        if (!function_exists('http_response_code')) {

            function http_response_code($code = NULL) {

                if ($code !== NULL) {

                    switch ($code) {
                        case 100: $text = 'Continue';
                            break;
                        case 101: $text = 'Switching Protocols';
                            break;
                        case 200: $text = 'OK';
                            break;
                        case 201: $text = 'Created';
                            break;
                        case 202: $text = 'Accepted';
                            break;
                        case 203: $text = 'Non-Authoritative Information';
                            break;
                        case 204: $text = 'No Content';
                            break;
                        case 205: $text = 'Reset Content';
                            break;
                        case 206: $text = 'Partial Content';
                            break;
                        case 300: $text = 'Multiple Choices';
                            break;
                        case 301: $text = 'Moved Permanently';
                            break;
                        case 302: $text = 'Moved Temporarily';
                            break;
                        case 303: $text = 'See Other';
                            break;
                        case 304: $text = 'Not Modified';
                            break;
                        case 305: $text = 'Use Proxy';
                            break;
                        case 400: $text = 'Bad Request';
                            break;
                        case 401: $text = 'Unauthorized';
                            break;
                        case 402: $text = 'Payment Required';
                            break;
                        case 403: $text = 'Forbidden';
                            break;
                        case 404: $text = 'Not Found';
                            break;
                        case 405: $text = 'Method Not Allowed';
                            break;
                        case 406: $text = 'Not Acceptable';
                            break;
                        case 407: $text = 'Proxy Authentication Required';
                            break;
                        case 408: $text = 'Request Time-out';
                            break;
                        case 409: $text = 'Conflict';
                            break;
                        case 410: $text = 'Gone';
                            break;
                        case 411: $text = 'Length Required';
                            break;
                        case 412: $text = 'Precondition Failed';
                            break;
                        case 413: $text = 'Request Entity Too Large';
                            break;
                        case 414: $text = 'Request-URI Too Large';
                            break;
                        case 415: $text = 'Unsupported Media Type';
                            break;
                        case 500: $text = 'Internal Server Error';
                            break;
                        case 501: $text = 'Not Implemented';
                            break;
                        case 502: $text = 'Bad Gateway';
                            break;
                        case 503: $text = 'Service Unavailable';
                            break;
                        case 504: $text = 'Gateway Time-out';
                            break;
                        case 505: $text = 'HTTP Version not supported';
                            break;
                        default:
                            exit('Unknown http status code "' . htmlentities($code) . '"');
                            break;
                    }

                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

                    header($protocol . ' ' . $code . ' ' . $text);

                    $GLOBALS['http_response_code'] = $code;
                } else {

                    $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
                }

                return $code;
            }

        }
    }
    
}

// fix for function http_response_code that exist on  PHP 5 >= 5.4.0, PHP 7
if (!function_exists('http_response_code')) {

    function http_response_code($code = NULL) {

        if ($code !== NULL) {

            switch ($code) {
                case 100: $text = 'Continue';
                    break;
                case 101: $text = 'Switching Protocols';
                    break;
                case 200: $text = 'OK';
                    break;
                case 201: $text = 'Created';
                    break;
                case 202: $text = 'Accepted';
                    break;
                case 203: $text = 'Non-Authoritative Information';
                    break;
                case 204: $text = 'No Content';
                    break;
                case 205: $text = 'Reset Content';
                    break;
                case 206: $text = 'Partial Content';
                    break;
                case 300: $text = 'Multiple Choices';
                    break;
                case 301: $text = 'Moved Permanently';
                    break;
                case 302: $text = 'Moved Temporarily';
                    break;
                case 303: $text = 'See Other';
                    break;
                case 304: $text = 'Not Modified';
                    break;
                case 305: $text = 'Use Proxy';
                    break;
                case 400: $text = 'Bad Request';
                    break;
                case 401: $text = 'Unauthorized';
                    break;
                case 402: $text = 'Payment Required';
                    break;
                case 403: $text = 'Forbidden';
                    break;
                case 404: $text = 'Not Found';
                    break;
                case 405: $text = 'Method Not Allowed';
                    break;
                case 406: $text = 'Not Acceptable';
                    break;
                case 407: $text = 'Proxy Authentication Required';
                    break;
                case 408: $text = 'Request Time-out';
                    break;
                case 409: $text = 'Conflict';
                    break;
                case 410: $text = 'Gone';
                    break;
                case 411: $text = 'Length Required';
                    break;
                case 412: $text = 'Precondition Failed';
                    break;
                case 413: $text = 'Request Entity Too Large';
                    break;
                case 414: $text = 'Request-URI Too Large';
                    break;
                case 415: $text = 'Unsupported Media Type';
                    break;
                case 500: $text = 'Internal Server Error';
                    break;
                case 501: $text = 'Not Implemented';
                    break;
                case 502: $text = 'Bad Gateway';
                    break;
                case 503: $text = 'Service Unavailable';
                    break;
                case 504: $text = 'Gateway Time-out';
                    break;
                case 505: $text = 'HTTP Version not supported';
                    break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;
        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
        }

        return $code;
    }

}
