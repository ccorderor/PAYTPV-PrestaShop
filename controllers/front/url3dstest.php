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
*  @author     Jose Ramon Garcia <jrgarcia@paytpv.com>
*  @copyright  2015 PAYTPV ON LINE S.L.
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
/**
 * @since 1.5.0
 */

class PaytpvUrl3dstestModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;
	public $display_column_right  = false;
	public $display_header = false;
	public $display_footer  = false;
	public $display_top  = false;


	public $ssl = true;
	

	public function initContent()

	{
		$this->context->controller->addJquery();
		parent::initContent();

		$paytpv = $this->module;

		$values = array(
			'id_cart' => Context::getContext()->cart->id,
			'key' => Context::getContext()->customer->secure_key
		);
		$ssl = Configuration::get('PS_SSL_ENABLED');

	    $arrCheckCard = array("process"=>"checkCard");

	    $TRANSACTION_TYPE = Tools::getValue('OPERATION')."_TEST";

	    $URLOK=Context::getContext()->link->getModuleLink($paytpv->name, 'urlko',$values,$ssl);
	    $URLKO=Context::getContext()->link->getModuleLink($paytpv->name, 'urlko',$values,$ssl);
	    if (Tools::getValue('OPERATION')==107)
	    	 $URLKO=Context::getContext()->link->getModuleLink($paytpv->name, 'account',array(),$ssl);
	 	
		$this->context->smarty->assign(array(
			'this_path' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/',
			'URL_KO' => $URLKO,
			'URL_OK' => $URLOK,
			'URL_NOT' => Context::getContext()->link->getModuleLink($paytpv->name, 'url',$values,$ssl),
			'id_cart' => Context::getContext()->cart->id,
			'MERCHANT_MERCHANTSIGNATURE' => Tools::getValue('MERCHANT_MERCHANTSIGNATURE'),
			'MERCHANT_ORDER'  => Tools::getValue('MERCHANT_ORDER'),
			'MERCHANT_AMOUNT' => Tools::getValue('MERCHANT_AMOUNT'),
			'MERCHANT_AMOUNT_DECIMAL' => number_format(Tools::getValue('MERCHANT_AMOUNT')/100, 2, '.', ''),
			'ID_USER' => Tools::getValue('IDUSER'),
			'TOKEN_USER' => "TESTTOKEN",
			'TRANSACTION_TYPE' => $TRANSACTION_TYPE,
			'CURRENCY' => Tools::getValue('MERCHANT_CURRENCY'),
			'MERCHAN_PAN' => Tools::getValue('MERCHAN_PAN'),
			'CHECK_CARD'=> Context::getContext()->link->getModuleLink('paytpv', 'actions', $arrCheckCard, true),
			'FECHA' => date("d/m/Y"),
			'HORA' => date("H:i:s"),
			'SHOP_NAME'=>Configuration::get('PS_SHOP_NAME')

		));


		$this->setTemplate('payment_3ds_test.tpl');

	}

}

