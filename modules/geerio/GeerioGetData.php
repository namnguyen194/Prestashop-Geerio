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
class GeerioGetData {
    public static function getDobWithFormat($date) {
        $dob = DateTime::createFromFormat('Y-m-d', $date);
        $dob->setTime(0, 0, 0);
        return $dob->format(DateTime::ISO8601);
    }
     public static function addDateWithFormat($date) {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $date->format(DateTime::ISO8601);
    }
     public static function getGroupName($id) {
        $sql = 'SELECT DISTINCT  gl.`name`
            FROM `' . _DB_PREFIX_ . 'group` g
            LEFT JOIN `' . _DB_PREFIX_ . 'group_lang` gl ON (g.`id_group` = gl.`id_group` AND gl.`id_lang` = ' . (int) Context::getContext()->language->id . ')
            WHERE g.id_group = ' . $id;
        $groupname = Db::getInstance()->executeS($sql);
        return $groupname;
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
        $arr['birthday'] = strtotime( $customer->birthday  );
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
        $arr['datecreated'] = strtotime( $customer->date_add); 
        $arr['dateupdated'] = strtotime( $customer->date_upd); 
        $arr['language'] = Language::getIsoById($customer->id_lang);
        $customer_group_id = Customer::getDefaultGroupId($customer_id);
        $group_name = GeerioGetData::getGroupName($customer_group_id);
        foreach ($group_name as $value) {
            $arr['group'][] = $value['name'];
        }
        return $arr;
    }
    
    public static function getCartsByID($id = null) {
        $arr = array();
        $lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
            $cart_general = new Cart($id);
            if ($cart_general->id) {
                $products_list = $cart_general->getProducts();
                $products = array();
                $value_cart = 0;
                
                foreach ($products_list as $products_detail_arr) {
                    if (0 != $products_detail_arr['price']) {
                        $product = array();
                        $product['id'] = $products_detail_arr['id_product'];
                        $product['name'] = $products_detail_arr['name'];
                        $product['quantity'] = $products_detail_arr['quantity'];
                        $product['price'] = floatval(number_format($products_detail_arr['price_wt'], 2, '.', ''));
                        $value_cart+=$products_detail_arr['price_wt'];
                        $products[] = $product;
                    }
                }
                $link = new Link();
                $image = Image::getCover($product['id']);
                $arr['img'] = $link->getImageLink($product->link_rewrite, $image['id_image'], 'home_default');
                $arr['value'] = floatval(number_format($value_cart, 2, '.', ''));
                //$arr['status'] = $cart_general;
                $currency = new Currency($cart_general->id_currency);
                $arr['currency'] = $currency->iso_code;
                $arr['id'] = $id;
                if ($cart_general->id_customer) {
                    $arr['idcontact'] = $cart_general->id_customer;
                }
                $arr['products'] = $products;
                $arr['datecreated'] = strtotime($cart_general->date_add);
                $arr['dateupdated'] = strtotime($cart_general->date_upd);
            }
            return $arr;
                
    }
               
}
        