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
require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once _PS_MODULE_DIR_.'geerio/TriggerTool.php';

class TriggerData {

    const DEFAULT_DOB = '1900-01-01';

    public static function getContactsData($id = null, $since = null, $until = null) {
        $arr = array();
        $jsondata = '';
        if ($id) {
            $customer = new Customer($id);
            if ($customer->id) {
                $arr['id'] = $id;
                $arr['firstname'] = $customer->firstname;
                $arr['lastname'] = $customer->lastname;
                $arr['email'] = $customer->email;
                $arr['dateofbirth'] = '0000-00-00' != $customer->birthday ? TriggerTool::getDobWithFormat($customer->birthday) : TriggerTool::getDobWithFormat(self::DEFAULT_DOB); // format ISO 8601
                if (0 != $customer->id_gender) {
                    $arr['title'] = $customer->id_gender;
                }
                $lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
                $address_data = $customer->getAddresses($lang_id);
                if (!empty($address_data)) {
                    foreach ($address_data as $address) {
                        $phone = $address['phone'];
                        $mobile = $address['phone_mobile'];
                        $arr['phone'] = $phone;
                        $arr['mobile'] = $mobile;
                        $arr['address'] = $address['address1'];
                        $arr['address2'] = $address['address2'];
                        $arr['postcode'] = $address['postcode'];
                        $arr['city'] = $address['city'];
                        $arr['country'] = Country::getIsoById($address['id_country']);
                        $arr['company'] = $address['company'];
                    }
                }
                $arr['optin'] = $customer->optin;
                $arr['datecreated'] = TriggerTool::addDateWithFormat($customer->date_add);
                $arr['dateupdated'] = TriggerTool::addDateWithFormat($customer->date_upd);
                $arr['language'] = Language::getIsoById($customer->id_lang);
                $customer_group_id = Customer::getDefaultGroupId($id);
                $group_name = TriggerTool::getGroupName($customer_group_id);
                foreach ($group_name as $value) {
                    $arr['group'] = $value['name'];
                }
                
                if(phpversion() < '5.4'){
                    $jsondata = stripcslashes(json_encode($arr));
                }
                else{
                    $jsondata = stripcslashes(json_encode($arr, JSON_UNESCAPED_UNICODE));
                }
                
            } else {
                TriggerTool::statusCode404NotFound();
                return;
            }
        } else {
            $id_list = TriggerTool::filterIdContactsByDate($since, $until);
            foreach ($id_list as $arr_id) {
                $arr[] = $arr_id['id_customer'];
            }
            $jsondata = stripcslashes(json_encode(array('id' => $arr)));
        }
        TriggerTool::statusCode200Ok($jsondata);
    }

    public static function getOrdersData($id = null, $since = null, $until = null) {
        $arr = array();
        $jsondata = '';
        $lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
        if ($id) {
            $order_general = new Order($id);
            $orders_list = OrderDetail::getList($id);
            if (!empty($orders_list)) {
                $products = array();
                foreach ($orders_list as $order_detail_arr) {
                    if (0 != $order_detail_arr['product_price']) {
                        $product = array();
                        $product['id'] = $order_detail_arr['product_id'];
                        $product['quantity'] = (int)$order_detail_arr['product_quantity'];
                        $product['price'] = floatval(number_format($order_detail_arr['unit_price_tax_incl'], 2, '.', ''));
                        $products[] = $product;
                    }
                }
                $arr['id'] = $id;
                $arr['idcontact'] = $order_general->id_customer;
                $arr['products'] = $products;
                $arr['price'] = floatval(number_format($order_general->total_products_wt, 2, '.', ''));
                $listStatus = explode(';', Configuration::get('PS_GEER_IO_ORDERS_STATUS'));
                
                $current_state_info = $order_general->getCurrentStateFull($lang_id);
                $current_state = $current_state_info['name'];
                $current_state_id = $current_state_info['id_order_state'];
                $arr['status'] = (in_array($current_state_id, $listStatus)) ? 1 : 0;
                $arr['statuslabel'] = $current_state;
                $currency_info = Currency::getCurrency($order_general->id_currency);
                $currency = $currency_info['iso_code'];
                $arr['currency'] = $currency;
                $arr['datecreated'] = TriggerTool::addDateWithFormat($order_general->date_add);
                $arr['dateupdated'] = TriggerTool::addDateWithFormat($order_general->date_upd);
                
                if(phpversion() < '5.4'){
                    $jsondata = stripcslashes(json_encode($arr));
                }
                else{
                    $jsondata = stripcslashes(json_encode($arr, JSON_UNESCAPED_UNICODE));
                }
            } else {
                TriggerTool::statusCode404NotFound();
                return;
            }
        } else {
            $id_list = TriggerTool::filterIdOrdersByDate($since, $until);
            foreach ($id_list as $arr_id) {
                $arr[] = $arr_id['id_order'];
            }
            $jsondata = stripcslashes(json_encode(array('id' => $arr)));
        }
        TriggerTool::statusCode200Ok($jsondata);
    }

    public static function getProductsData($id = null, $since = null, $until = null) {
        $arr = array();
        $jsondata = '';
        $lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
        if ($id) {
            $product_general = new Product($id, '', $lang_id);
            Context::getContext()->cart = new Cart();
            $price_static = Product::getPriceStatic($id, true);
            $priceWithoutReduct = $product_general->getPriceWithoutReduct();
            if ($product_general->name) {
                $arr['id'] = $id;
                $arr['name'] = $product_general->name;
                if ($product_general->reference) {
                    $arr['reference'] = $product_general->reference;
                }
                $arr['price'] = floatval(number_format($priceWithoutReduct, 2, '.', ''));
                $arr['saleprice'] = floatval(number_format($price_static, 2, '.', ''));
                $arr['urlproduct'] = $product_general->getLink();
                $image_cover_info = Product::getCover($id);
                $id_image = $image_cover_info['id_image'];
                $link_rewrite = $product_general->link_rewrite;
                $link = new Link();
                $image_path = $link->getImageLink($link_rewrite, $id_image, 'medium_default');
                $arr['urlimage'] = $image_path;
                $arr['datecreated'] = TriggerTool::addDateWithFormat($product_general->date_add);
                $arr['dateupdated'] = TriggerTool::addDateWithFormat($product_general->date_upd);
                $arr['brand'] = $product_general->id_manufacturer;
                $categories = $product_general->getCategories();
                if (!empty($categories)) {
                    $arr['category'] = $categories[0];
                }
                if(phpversion() < '5.4'){
                    $jsondata = stripcslashes(json_encode($arr));
                }
                else{
                    $jsondata = stripcslashes(json_encode($arr, JSON_UNESCAPED_UNICODE));
                }
            } else {
                TriggerTool::statusCode404NotFound();
                return;
            }
        } else {
            $id_list = TriggerTool::filterIdProductsByDate($since, $until);
            foreach ($id_list as $arr_id) {
                $arr[] = $arr_id['id_product'];
            }
            $jsondata = stripcslashes(json_encode(array('id' => $arr)));
        }
        TriggerTool::statusCode200Ok($jsondata);
    }

    public static function getCartsData($id = null, $since = null, $until = null) {
        $arr = array();
        $jsondata = '';
        $lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
        if ($id) {
            $cart_general = new Cart($id);
            if ($cart_general->id) {
                $carts = array();
                $products_list = $cart_general->getProducts();
                $products = array();
                foreach ($products_list as $products_detail_arr) {
                    if (0 != $products_detail_arr['price']) {
                        $product = array();
                        $product['id'] = $products_detail_arr['id_product'];
                        $product['quantity'] = $products_detail_arr['quantity'];
                        $product['price'] = floatval(number_format($products_detail_arr['price_wt'], 2, '.', ''));
                        $products[] = $product;
                    }
                }
                $arr['id'] = $id;
                if ($cart_general->id_customer) {
                    $arr['idcontact'] = $cart_general->id_customer;
                }
                $arr['products'] = $products;
                $arr['datecreated'] = TriggerTool::addDateWithFormat($cart_general->date_add);
                $arr['dateupdated'] = TriggerTool::addDateWithFormat($cart_general->date_upd);
                if(phpversion() < '5.4'){
                    $jsondata = stripcslashes(json_encode($arr));
                }
                else{
                    $jsondata = stripcslashes(json_encode($arr, JSON_UNESCAPED_UNICODE));
                }
            } else {
                TriggerTool::statusCode404NotFound();
                return;
            }
        } else {
            $id_list = TriggerTool::filterIdCartsByDate($since, $until);
            foreach ($id_list as $arr_id) {
                $arr[] = $arr_id['id_cart'];
            }
            $jsondata = stripcslashes(json_encode(array('id' => $arr)));
        }
        TriggerTool::statusCode200Ok($jsondata);
    }

    public static function getNewsletterData($id = null, $since = null, $until = null) {
        $arr = array();
        $jsondata = '';
        if ($id) {
            $subcreber_id = Db::getInstance()->executeS('SELECT DISTINCT  `id_customer`
            FROM `' . _DB_PREFIX_ . 'customer`
            WHERE newsletter = 1 AND `id_customer` = ' . $id);
            if (!empty($subcreber_id)) {
                foreach ($subcreber_id as $value) {
                    $customer_id = $value['id_customer'];
                }
                $customer = new Customer($customer_id);
                $arr['id'] = $id;
                $arr['firstname'] = $customer->firstname;
                $arr['lastname'] = $customer->lastname;
                $arr['email'] = $customer->email;
                $arr['optin'] = $customer->optin;
                $arr['datecreated'] = TriggerTool::addDateWithFormat($customer->date_add);
                $arr['dateupdated'] = TriggerTool::addDateWithFormat($customer->date_upd);
                $arr['language'] = Language::getIsoById($customer->id_lang);
                if(phpversion() < '5.4'){
                    $jsondata = stripcslashes(json_encode($arr));
                }
                else{
                    $jsondata = stripcslashes(json_encode($arr, JSON_UNESCAPED_UNICODE));
                }
            } else {
                TriggerTool::statusCode404NotFound();
                return;
            }
        } else {
            $id_list = TriggerTool::filterIdNewslettersByDate($since, $until);
            foreach ($id_list as $arr_id) {
                $arr[] = $arr_id['id_customer'];
            }
            $jsondata = stripcslashes(json_encode(array('id' => $arr)));
        }
        TriggerTool::statusCode200Ok($jsondata);
    }

}
