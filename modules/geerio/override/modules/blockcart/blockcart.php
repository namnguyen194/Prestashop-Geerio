<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('_CAN_LOAD_FILES_'))
    exit;
Class BlockCartOverride extends BlockCart {
    public function hookHeader()
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
		$this->context->controller->addCSS(($this->_path).'blockcart.css', 'all');
		if ((int)(Configuration::get('PS_BLOCK_CART_AJAX')))
		{
			$this->context->controller->addJS(_PS_MODULE_DIR_.'geerio/ajax-cart-override.js');
			$this->context->controller->addJqueryPlugin(array('scrollTo', 'serialScroll', 'bxslider'));
		}
	}
    public function hookAjaxCall($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
		$this->assignContentVars($params);
                $id_cart = $this->context->cart->id;
                $cart_current = new Cart($id_cart);
                
		$res = Tools::jsonDecode($this->display(__FILE__, 'blockcart-json.tpl'), true);
                $res['cart']['id'] = $cart_current->id;
                $res['cart']['customer'] = $cart_current->id_customer;
                $res['cart']['status'] =  ($cart_current->delivery_option) ? 1 : 0;
                $res['cart']['create'] = strtotime($cart_current->date_add);
                $res['cart']['update'] = strtotime( $cart_current->date_upd);
                $res['cart']['currency'] = (new Currency($cart_current->id_currency))->iso_code;
                $taxCalculationMethod = Group::getPriceDisplayMethod((int)Group::getCurrent()->id);
		$useTax = !($taxCalculationMethod == PS_TAX_EXC);
                $totalToPay = number_format($cart_current->getOrderTotal($useTax), 2, '.', ',');
                $res['cart']['value'] = $totalToPay;
                foreach ($res['products'] as $index => $product){
                    $product_current = new Product($product['id']);
                    $res['products'][$index]['category'] =  (new Category($product_current->id_category_default,$product_current->id_shop_default))->name;
                    $res['products'][$index]['label'] = $product_current->condition;
                    $res['products'][$index]['sku'] = $product_current->reference;
                    $res['products'][$index]['brand'] = $product_current->manufacturer_name;
                 }
		if (is_array($res) && ($id_product = Tools::getValue('id_product')) && Configuration::get('PS_BLOCK_CART_SHOW_CROSSSELLING'))
		{
			$this->smarty->assign('orderProducts', OrderDetail::getCrossSells($id_product, $this->context->language->id,
				Configuration::get('PS_BLOCK_CART_XSELL_LIMIT')));
			$res['crossSelling'] = $this->display(__FILE__, 'crossselling.tpl');
		}
		$res = Tools::jsonEncode($res);
		return $res;
	}
}