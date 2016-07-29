<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class IdentityController extends IdentityControllerCore {
    
    /*
    * module: geerio
    * date: 2016-07-29 05:40:04
    * version: 1.7
    */
    public function initContent() {
        parent::initContent();
        if ($this->customer->birthday) {
            $birthday = explode('-', $this->customer->birthday);
        } else {
            $birthday = array('-', '-', '-');
        }
        
        $this->context->smarty->assign(array(
                'years' => Tools::dateYears(),
                'sl_year' => $birthday[0],
                'months' => Tools::dateMonths(),
                'sl_month' => $birthday[1],
                'days' => Tools::dateDays(),
                'sl_day' => $birthday[2],
                'errors' => $this->errors,
                'genders' => Gender::getGenders(),
            ));
        $this->context->smarty->assign(array(
            'HOOK_CUSTOMER_IDENTITY_FORM' => Hook::exec('displayCustomerIdentityForm'),
            'HOOK_ALTER_UPDATE' => Hook::exec('displayAfterUpdate')
        ));
        $newsletter = Configuration::get('PS_CUSTOMER_NWSL') || (Module::isInstalled('blocknewsletter') && Module::getInstanceByName('blocknewsletter')->active);
        $this->context->smarty->assign('newsletter', $newsletter);
        $this->context->smarty->assign('optin', (bool)Configuration::get('PS_CUSTOMER_OPTIN'));
        $this->context->smarty->assign('field_required', $this->context->customer->validateFieldsRequiredDatabase());
        $this->setTemplate(_PS_MODULE_DIR_.'geerio/identity.tpl');
    }
}
