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
        if (!parent::install() || !Configuration::updateValue('PS_GEER_IO_TRIGGER', 'trigger') || !Configuration::updateValue('CONTACT_STATE', false)) {
            return false;
        }
        $this->registerHook('displayHeader'); 
        $this->registerHook('authentication'); 
        $this->registerHook('actionCustomerAccountAdd');
        $this->registerHook('displayTop') ;
        $this->registerHook('displayCustomerIdentityForm'); 
        $this->registerHook('displayBackOfficeHeader');
        return true;
    }

    public function uninstall() {
        if (!parent::uninstall() ||
                !Configuration::deleteByName('PS_GEER_IO_TRIGGER')
        ) {
            return false;
        }
        $this->unregisterHook('displayHeader') ;
        $this->unregisterHook('authentication');
        $this->unregisterHook('actionCustomerAccountAdd');
        $this->unregisterHook('displayTop');
        $this->unregisterHook('displayCustomerIdentityForm');
        $this->unregisterHook('displayBackOfficeHeader');
        return true;
    }
    public function hookdisplayHeader($params) {
        $this->context->smarty->assign(
                array(
                    'KEY' => Configuration::get('PS_GEER_IO_HMAC_SECRET')
                )
        );
        return $this->display(__FILE__, 'script-header.tpl');
    }
    public function hookdisplayBackOfficeHeader($params){
        $this->context->smarty->assign(
                array(
                    'KEY' => Configuration::get('PS_GEER_IO_HMAC_SECRET')
                )
        );
        return $this->display(__FILE__, 'script-header.tpl');
    }
    function hookdisplayTop() {
        
        //$state_conf = Configuration::get('CONTACT_STATE');
        if (Configuration::get('CONTACT_STATE')) {
            $id = $this->context->cookie->id_customer;
            $info = TriggerTool::getCustomerByID($id);
            $this->context->smarty->assign(
                array(
                    'INFO' => $info
                )
             );
            Configuration::updateValue('CONTACT_STATE', false);
            return $this->display(__FILE__, 'contact-tag.tpl');
        }
        else {
           return false;
        }
    }
    public function hookactionCustomerAccountAdd($param) {
        Configuration::updateValue('CONTACT_STATE', true);
    }
    public function  hookactionAuthentication($param){
       Configuration::updateValue('CONTACT_STATE', true);
    }
    
//    public function  hookdisplayCustomerIdentityForm(){
//      $id = $this->context->cookie->id_customer;
//      $info = TriggerTool::getCustomerByID($id);
//            $this->context->smarty->assign(
//                array(
//                    'INFO' => $info
//                )
//             );
//      return $this->display(__FILE__, 'contact-tag-upd.tpl');
//    }
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
            if ($orders_status = Tools::getValue('orders_status'))
                Configuration::updateValue('PS_GEER_IO_ORDERS_STATUS', implode(';', $orders_status));
        }
        return $output . $this->displayForm();
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
                    'type'=>'free',
                    'label' =>  $this->l('Connector URL'),
                    'name' => 'connector'
                ),
                
                array(
                    'type'=>'free',
                    'label' =>  $this->l('Version'),
                    'name' => 'version'
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
        $helper->fields_value['version'] = $this->version;

        return $helper->generateForm($fields_form);
    }

}
