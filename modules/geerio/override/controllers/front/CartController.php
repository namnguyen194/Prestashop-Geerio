<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CartController extends CartControllerCore {
    public function displayAjax()
    {
        if ($this->errors) {
            $this->ajaxDie(Tools::jsonEncode(array('hasError' => true, 'errors' => $this->errors)));
        }
        if ($this->ajax_refresh) {
            $this->ajaxDie(Tools::jsonEncode(array('refresh' => true)));
        }
        
        // write cookie if can't on destruct
        $this->context->cookie->write();
        
        if (Tools::getIsset('summary')) {
            $result = array();
            if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1) {
                $groups = (Validate::isLoadedObject($this->context->customer)) ? $this->context->customer->getGroups() : array(1);
                if ($this->context->cart->id_address_delivery) {
                    $deliveryAddress = new Address($this->context->cart->id_address_delivery);
                }
                $id_country = (isset($deliveryAddress) && $deliveryAddress->id) ? (int)$deliveryAddress->id_country : (int)Tools::getCountry();

                Cart::addExtraCarriers($result);
            }
            $id_cart = $this->context->cart->id;
            $cart_current = new Cart($id_cart); 
            $result['cart']['id'] = $cart_current->id;
            $result['cart']['customer'] = $cart_current->id_customer;
            $result['cart']['status'] = ($cart_current->delivery_option) ? 1 : 0;
            $result['cart']['create'] = strtotime($cart_current->date_add);
            $result['cart']['update'] = strtotime($cart_current->date_upd);
            $result['cart']['currency'] = (new Currency($cart_current->id_currency))->iso_code;
            $taxCalculationMethod = Group::getPriceDisplayMethod((int)Group::getCurrent()->id);
            $useTax = !($taxCalculationMethod == PS_TAX_EXC);
            $totalToPay = number_format($cart_current->getOrderTotal($useTax), 2, '.', ',');
            $result['cart']['value'] = $totalToPay;
            foreach ($result['products'] as $index => $product){
                    $product_current = new Product($product['id']);
                    $result['products'][$index]['category'] =  (new Category($product_current->id_category_default,$product_current->id_shop_default))->name;
                    $result['products'][$index]['label'] = $product_current->condition;
                    $result['products'][$index]['sku'] = $product_current->reference;
                    $result['products'][$index]['brand'] = $product_current->manufacturer_name;
                 }
            $result['summary'] = $this->context->cart->getSummaryDetails(null, true);

            $result['customizedDatas'] = Product::getAllCustomizedDatas($this->context->cart->id, null, true);
            $result['HOOK_SHOPPING_CART'] = Hook::exec('displayShoppingCartFooter', $result['summary']);
            $result['HOOK_SHOPPING_CART_EXTRA'] = Hook::exec('displayShoppingCart', $result['summary']);

            foreach ($result['summary']['products'] as $key => &$product) {
                $product['quantity_without_customization'] = $product['quantity'];
                if ($result['customizedDatas'] && isset($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']])) {
                    foreach ($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']] as $addresses) {
                        foreach ($addresses as $customization) {
                            $product['quantity_without_customization'] -= (int)$customization['quantity'];
                        }
                    }
                }
            }
            if ($result['customizedDatas']) {
                Product::addCustomizationPrice($result['summary']['products'], $result['customizedDatas']);
            }

            $json = '';
            Hook::exec('actionCartListOverride', array('summary' => $result, 'json' => &$json));
            $this->ajaxDie(Tools::jsonEncode(array_merge($result, (array)Tools::jsonDecode($json, true))));
        }
        // @todo create a hook
        elseif (file_exists(_PS_MODULE_DIR_.'/blockcart/blockcart-ajax.php')) {
            require_once(_PS_MODULE_DIR_.'/blockcart/blockcart-ajax.php');
        }
    }
}