<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class PaytpvAccountModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	public function init()
	{
		parent::init();

		
	}

	public function initContent()
	{
		parent::initContent();

		
		$this->context->controller->addJqueryPlugin('fancybox');
		global $cookie;

		if (!Context::getContext()->customer->isLogged())
			Tools::redirect('index.php?controller=authentication&redirect=module&module=paytpv&action=account');

		if (Context::getContext()->customer->id)
		{
			$paytpv = $this->module;
			$saved_card = $paytpv->getToken();
			
			if (Context::getContext()->customer->id){
				$ps_language = new Language(intval($cookie->id_lang));
				$order = Context::getContext()->customer->id;
				$operation = 107;
				$ssl = Configuration::get('PS_SSL_ENABLED');
		
				$URLOK=$URLKO=Context::getContext()->link->getModuleLink($paytpv->name, 'account',array(),$ssl);  
				// Cálculo Firma
				$signature = md5($paytpv->clientcode.$paytpv->term.$operation.$order.md5($paytpv->pass));
				$fields = array
				(
					'MERCHANT_MERCHANTCODE' => $paytpv->clientcode,
					'MERCHANT_TERMINAL' => $paytpv->term,
					'OPERATION' => $operation,
					'LANGUAGE' => $ps_language->iso_code,
					'MERCHANT_MERCHANTSIGNATURE' => $signature,
					'MERCHANT_ORDER' => $order,
					'URLOK' => $URLOK,
				    'URLKO' => $URLKO
				);
				
				$this->context->smarty->assign('query',http_build_query($fields));
				$this->context->smarty->assign('saved_card',$saved_card);
				$this->setTemplate('paytpv-account.tpl');
			}
		}
	}
}