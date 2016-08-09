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
require_once _PS_MODULE_DIR_.'geerio/GeerioGetData.php';

if (!defined('_PS_VERSION_'))
    exit;

class Geerio extends Module {

    public function __construct() {
        $this->name = 'geerio';
        $this->tab = 'administration';
        $this->version = '1.7';
        $this->author = 'To Thanh Trung';
        $this->need_instance = 0;
        $this->ps_version_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
        if(_PS_VERSION_ >= 1.6)
                $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Geer.io');
        $this->description = $this->l('Geer.io Trigger Marketing');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('PS_GEER_IO_TRIGGER'));
                $this->warning = $this->l('No name provided');
    }

    public function install() {
        if (!parent::install() || 
            !Configuration::updateValue('PS_GEER_IO_TRIGGER', 'trigger') || 
            !Configuration::updateValue('CONTACT_STATE', false) ||
            !Configuration::updateValue('PS_GEER_IO_PAGES', 'NONE')
           ) {
            return false;
        }
        $this->registerHook('actionAfterUpdate');
        $this->registerHook('displayHeader'); 
        $this->registerHook('displayBackOfficeHeader') ;
        $this->registerHook('authentication'); 
        $this->registerHook('actionCustomerAccountAdd');
        $this->registerHook('displayTop') ;
        $this->registerHook('displayRightColumnProduct');
        $this->registerHook('actionCartSave') ;
        $this->registerHook('ActionAdminControllerSetMedia'); //
        $this->registerHook('actionOrderStatusUpdate'); //
        $this->registerHook('displayBackOfficeTop'); //
        $this->registerHook('displayBackOfficeFooter');
        return true;
    }

    public function uninstall() {
        if (!parent::uninstall() ||
                !Configuration::deleteByName('PS_GEER_IO_TRIGGER') ||
                !Configuration::deleteByName('PS_GEER_IO_PAGES')
        ) {
            return false;
        }
        $this->unregisterHook('actionAfterUpdate');
        $this->unregisterHook('displayHeader') ;
        $this->unregisterHook('displayBackOfficeHeader') ;
        $this->unregisterHook('authentication');
        $this->unregisterHook('actionCustomerAccountAdd');
        $this->unregisterHook('displayTop');
        $this->unregisterHook('displayRightColumnProduct');
        $this->unregisterHook('actionCartSave');
        $this->unregisterHook('ActionAdminControllerSetMedia');
        $this->unregisterHook('actionOrderStatusUpdate');
        $this->unregisterHook('displayBackOfficeTop'); 
        $this->unregisterHook('displayBackOfficeFooter');

        return true;
    }
    
    function hookdisplayBackOfficeFooter(){
        if (isset($this->context->cookie->STATE_ORDER)) {
            $order = new OrderCore($this->context->cookie->STATE_ORDER);
            $order_state = $order->current_state;
            $list_state = explode(';', Configuration::get('PS_GEER_IO_ORDERS_STATUS'));
            //var_dump($list_state);
            if (in_array($order_state, $list_state)) {
                $this->context->smarty->assign(
                        array(
                            'ORDER_STATUS' => 1
                ));
            } else {
                $this->context->smarty->assign(
                        array(
                            'ORDER_STATUS' => 0
                ));
            }
            $this->context->smarty->assign(
                    array(
                        'ORDER_SEND_GEERIO' => true,
                        'ORDER_NAME' => $this->context->cookie->STATE_ORDER_NAME,
                        'ORDER_CONTACT' => $order->id_customer,
                        'ORDER_CART' => $order->id_cart,
                        'ORDER_ID' => $order->id,
                        'ORDER_CREATE' => strtotime($order->date_add),
                        'ORDER_UPDATE' => strtotime($order->date_upd),
                        'ORDER_VALUE' => $order->total_paid
            ));
            $this->context->cookie->__unset('STATE_ORDER');
             $this->context->cookie->__unset('STATE_ORDER_NAME');
            return $this->display(__FILE__, 'displaytop-tag.tpl');
        }
        return '';
    }
    function hookactionOrderStatusUpdate($params){
        
        $this->context->cookie->__set('STATE_ORDER',$params['id_order']);  
        $this->context->cookie->__set('STATE_ORDER_NAME',$params['newOrderStatus']->name);
        
    }
    
    function hookActionAdminControllerSetMedia(){
         if(strtolower(Tools::getValue('controller')) == 'adminmodules' && Tools::getValue('configure')=='geerio'){
             $this->context->controller->addJS( $this->_path .'js/config.js' );
             $this->context->controller->addCSS($this->_path.'css/config.css');
         }
    }
  
    
    public function hookdisplayRightColumnProduct() {
        if (Configuration::get('PS_GEER_IO_PAGES')=='PRODUCT'){
            $this->context->smarty->assign(
                array(
                    'NAV_SHOW'=>true
                )
             );
            return $this->display(__FILE__, 'displaytop-tag.tpl');
        }
        return '';
    }

    public function hookdisplayBackOfficeHeader() {
        
        $this->context->smarty->assign(
                array(
                    'KEY' => Configuration::get('PS_GEER_IO_HMAC_SECRET')
                )
        );
        return $this->display(__FILE__, 'script-header.tpl');
    }
    public function hookdisplayHeader() {
        $this->context->smarty->assign(
                array(
                    'KEY' => Configuration::get('PS_GEER_IO_HMAC_SECRET')
                    
                )
        );
        return $this->display(__FILE__, 'script-header.tpl');
    }
    function hookdisplayTop() {
        $this->context->cookie->__set('SHOPPING_CART_GEERIO',0);
        if (Configuration::get('PS_GEER_IO_PAGES')=='ALL'){
            $this->context->smarty->assign(
                array(
                    'NAV_SHOW'=>true
                )
             );
        }
        if(substr( Configuration::get('PS_GEER_IO_PAGES'),0, 5 )=='start'){
            $list_page_value = Configuration::get('PS_GEER_IO_PAGES');
            $list_page_value = substr($list_page_value,6);
            $link_current = substr($_SERVER['REQUEST_URI'],strlen(__PS_BASE_URI__));
            if($list_page_value == substr($link_current,0,strlen($list_page_value))){
                $this->context->smarty->assign(
                array(
                    'LINK' => $link_current,
                    'PAGE' =>$list_page_value,
                    'NAV_SHOW'=>true
                )
             );
            }
        }
        if(substr( Configuration::get('PS_GEER_IO_PAGES'),0, 3 )=='end'){
            $list_page_value = Configuration::get('PS_GEER_IO_PAGES');
            $list_page_value = substr($list_page_value,4);
            $list_page_value = strrev($list_page_value);
            $link_current = substr($_SERVER['REQUEST_URI'],strlen(__PS_BASE_URI__));
            $link_current =strrev($link_current);
            if($list_page_value == substr($link_current,0,strlen($list_page_value))){
                $this->context->smarty->assign(
                array(
                    'NAV_SHOW'=>true
                )
             );
            }
        }
        if (isset($this->context->cookie->STATE_CONTACT)) { 
            $id = $this->context->cookie->id_customer;
            $info = GeerioGetData::getCustomerByID($id);
            $this->context->smarty->assign(
                array(
                    'INFO' => $info,
                    'CONTACT_SCRIPT'=>true
                )
             );
            $this->context->cookie->__unset('STATE_CONTACT');
        }
        return $this->display(__FILE__, 'displaytop-tag.tpl');
    }
    public function hookactionCustomerAccountAdd() {
        
      $this->context->cookie->__set('STATE_CONTACT',1);
    }
    public function  hookactionAuthentication(){
        $this->context->cookie->__set('STATE_CONTACT',1);
    }
    public function hookactionAfterUpdate(){
        $this->context->cookie->__set('STATE_CONTACT',1);
    }
    public function hookactionCartSave(){
        if(!Configuration::get('PS_BLOCK_CART_AJAX')){
            $this->context->cookie->__set('STATE_CART',1);
        }
    }
    public function getContent() { 
        $output = null;

        if (Tools::isSubmit('triggerConfSubmit')) {
            $trigger_module = strval(Configuration::get('PS_GEER_IO_TRIGGER'));
            if (!$trigger_module || empty($trigger_module) || !Validate::isGenericName($trigger_module))
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            else {
                Configuration::updateValue('PS_GEER_IO_TRIGGER', $trigger_module);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
            if ($hmac_secret = Tools::getValue('hmac_secret'))
                Configuration::updateValue('PS_GEER_IO_HMAC_SECRET', $hmac_secret);
            if ($list_page = Tools::getValue('list-page'))
                Configuration::updateValue('PS_GEER_IO_PAGES', $list_page);
            if ($link_start = Tools::getValue('value-link-start'))
                Configuration::updateValue('PS_GEER_LINK_START', $link_start);
            if ($link_end = Tools::getValue('value-link-end'))
                Configuration::updateValue('PS_GEER_LINK_END', $link_end);
            if ($orders_status = Tools::getValue('orders_status'))
                Configuration::updateValue('PS_GEER_IO_ORDERS_STATUS', implode(';', $orders_status));
        }
        return $output . $this->displayForm();
    }
    public function getValueRadio($str){
        if(substr( Configuration::get('PS_GEER_IO_PAGES'),0, 5 )=='start' && $str=='start'){
            return Configuration::get('PS_GEER_IO_PAGES');
        }
        if(substr( Configuration::get('PS_GEER_IO_PAGES'),0, 3 )=='end' && $str=='end'){
            return Configuration::get('PS_GEER_IO_PAGES');
        }
        return '';
    }

    public function displayForm() {
        // Get default language
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $array_order_status = OrderStateCore::getOrderStates($default_lang);
        $options = array();
        foreach ($array_order_status as $order_status_arr) {
            $order_status_name = $order_status_arr['name'];
            $order_status_id = $order_status_arr['id_order_state'];
            $single_option = array(
                'id_option' => $order_status_id, // The value of the 'value' attribute of the <option> tag.
                'name' => $order_status_name, // The value of the text content of the  <option> tag.
            );
            array_push($options, $single_option);
        }
        
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Secret Key'),
                    'name' => 'hmac_secret',
                    'required' => true,
                    'desc' => $this->l('Please fill in this field with your Secret Key (you can find this secret key by accessing to your account: My account / Connector)'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Status'),
                    'name' => 'orders_status[]',
                    'multiple' => true,
                    'options' => array(
                        'query' => $options,
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                    'desc' => $this->l('Please select status in which orders are considered as validated'),
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Enable this option'),
                    'name' => 'list-page',
                    'required' => true,
                    'class' => 'list-page-radio',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'none',
                            'value' => 'NONE',
                            'label' => $this->l('None')
                        ),
                        array(
                            'id' => 'all',
                            'value' => 'ALL',
                            'label' => $this->l('All Page')
                        ),
                        array(
                            'id' => 'product',
                            'value' => 'PRODUCT',
                            'label' => $this->l('Product Page')
                        ),
                        array(
                            'id' => 'link-start',
                            'class' => 'radio-link',
                            'value' => 'start_'.Configuration::get('PS_GEER_LINK_START'),
                            'label' => $this->l('Link Start with')
                        ),
                        array(
                            'id' => 'link-end',
                            'class' => 'radio-link',
                            'value' => 'end_'.Configuration::get('PS_GEER_LINK_END'),
                            'label' => $this->l('Link End with')
                        )
                    )
                ),
                array(
                    'type' => 'text',
                    'id' => 'value-for-link-start',
                    'name' =>'value-link-start',
                    'class' => 'value-for-link'
                   
                ),
                array(
                    'type' => 'text',
                    'id' => 'value-for-link-end',
                    'name' =>'value-link-end',
                    'class' => 'value-for-link'
                  
                ),
                
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );
        

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'triggerConfSubmit';
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['PS_GEER_IO_TRIGGER'] = Configuration::get('PS_GEER_IO_TRIGGER');
        $helper->fields_value['hmac_secret'] = Configuration::get('PS_GEER_IO_HMAC_SECRET');
        $helper->fields_value['orders_status[]'] = explode(';',Configuration::get('PS_GEER_IO_ORDERS_STATUS'));
        $helper->fields_value['connector'] = _PS_BASE_URL_.__PS_BASE_URI__.'module/'.$this->name.'/trigger';
        $helper->fields_value['list-page'] = Configuration::get('PS_GEER_IO_PAGES');
        $helper->fields_value['value-link-start'] = Configuration::get('PS_GEER_LINK_START');
        $helper->fields_value['value-link-end'] = Configuration::get('PS_GEER_LINK_END');

        return $helper->generateForm($fields_form);
    }
    
        
}
