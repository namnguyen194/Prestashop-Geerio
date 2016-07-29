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
require_once _PS_MODULE_DIR_.'geerio/TriggerData.php';

class TriggerWithAction {

    public function getData() {
        $action = Tools::getValue('ressource');

        $id = null;
        $since = null;
        $until = null;
        $dateObj = null;
        $sinceDateObj = null;
        $untilDateObj = null;
        if (is_numeric(Tools::getValue('id'))) {
            $id = Tools::getValue('id');
        }
        
        if (Tools::getValue('since') || Tools::getValue('until')) {
            if (TriggerTool::validateDate(Tools::getValue('since'), 'Y-m-d', true, $sinceDateObj) && Tools::getValue('until') && TriggerTool::validateDate(Tools::getValue('until'), 'Y-m-d', true, $untilDateObj)) {
                $since = $sinceDateObj->setTime(0, 0, 0)->format('Y-m-d H:i:s');
                $until = $untilDateObj->setTime(23, 59, 59)->format('Y-m-d H:i:s');
            } else {
                TriggerTool::showMethodInternalServerError();
                return;
            }
        } 
        
        if (Tools::getValue('date')) {
            if (TriggerTool::validateDate(Tools::getValue('date'), 'Y')) {
                $start = mktime(0, 0, 0, 1, 1, Tools::getValue('date'));
                $since = date('Y-m-d', $start);
                $end = mktime(23, 59, 59, 12, 31, Tools::getValue('date'));
                $until = date('Y-m-d', $end);
            } elseif (TriggerTool::validateDate(Tools::getValue('date'), 'Y-m', true, $dateObj)) {
                $since = $dateObj->setDate($dateObj->format('Y'), $dateObj->format('m'), 1)->setTime(0, 0, 0)->format('Y-m-d H:i:s');
                $until = $dateObj->setDate($dateObj->format('Y'), $dateObj->format('m'), 1)->setTime(23, 59, 59)->format('Y-m-t H:i:s');
            } elseif (TriggerTool::validateDate(Tools::getValue('date'), 'Y-m-d', true, $dateObj)) {
                $since = $dateObj->setTime(0, 0, 0)->format('Y-m-d H:i:s');
                $until = $dateObj->setTime(23, 59, 59)->format('Y-m-d H:i:s');
            } else {
                TriggerTool::showMethodInternalServerError();
                return;
            }
        }

        switch ($action) {
            case 'contacts':
                TriggerData::getContactsData($id, $since, $until);
                break;
            case 'orders':
                TriggerData::getOrdersData($id, $since, $until);
                break;
            case 'products':
                TriggerData::getProductsData($id, $since, $until);
                break;
            case 'carts':
                TriggerData::getCartsData($id, $since, $until);
                break;
            case 'newsletter':
                TriggerData::getNewsletterData($id, $since, $until);
                break;
            default : TriggerTool::statusCode400BadRequest();
                break;
        }
    }

    private function checkHMAC() {
        $hmac = TriggerTool::getServer('HTTP_X_HMAC');
        $hmactime = TriggerTool::getServer('HTTP_X_HMAC_TIME');
        $hmacdata = TriggerTool::getServer('HTTP_X_HMAC_DATA');
        $hash1 = TriggerTool::hmachash($hmacdata, $hmactime, 10);
        $hash2 = TriggerTool::hmachash(TriggerTool::getSECRETHMAC(), '', 10);
        $token = TriggerTool::hmachash($hash1, $hash2, 100);
        if ($token != $hmac) {
            return false;
        }
        return true;
    }

    public function runTrigger() {
        if (!TriggerTool::isGet()) {
            TriggerTool::showMethodNotImplemented();
        }
        if (!$this->checkHMAC()) {
            TriggerTool::statusCode400BadRequest();
        } 
        else {
            $this->getData();
        }
    }

}
