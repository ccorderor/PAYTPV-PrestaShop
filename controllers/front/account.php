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

		if (!Context::getContext()->customer->isLogged())
			Tools::redirect('index.php?controller=authentication&redirect=module&module=paytpv&action=account');

		if (Context::getContext()->customer->id)
		{

			$paytpv = $this->module;
			$saved_card = Paytpv_Customer::get_Cards_Customer((int)$this->context->customer->id);
			$suscriptions = Paytpv_Suscription::get_Suscriptions_Customer($this->context->language->iso_code,(int)$this->context->customer->id);

			if (Context::getContext()->customer->id){
				$order = Context::getContext()->customer->id;
				$operation = 107;
				$ssl = Configuration::get('PS_SSL_ENABLED');

				$arrTerminal = Paytpv_Terminal::getTerminalByCurrency($this->context->currency->iso_code);
				$idterminal = $arrTerminal["idterminal"];
				$pass = $arrTerminal["password"];
				
				$secure_pay = $paytpv->isSecureTransaction($idterminal,0,0)?1:0;
		
				$URLOK=$URLKO=Context::getContext()->link->getModuleLink($paytpv->name, 'account',array(),$ssl);  
				// Cálculo Firma
				$signature = md5($paytpv->clientcode.$idterminal.$operation.$order.md5($pass));
				$fields = array
				(
					'MERCHANT_MERCHANTCODE' => $paytpv->clientcode,
					'MERCHANT_TERMINAL' => $idterminal,
					'OPERATION' => $operation,
					'LANGUAGE' => $this->context->language->iso_code,
					'MERCHANT_MERCHANTSIGNATURE' => $signature,
					'MERCHANT_ORDER' => $order,
					'URLOK' => $URLOK,
				    'URLKO' => $URLKO,
				    '3DSECURE' => $secure_pay
				);

				$query = http_build_query($fields);

				if ($paytpv->environment!=1)
					$url_paytpv = $paytpv->url_paytpv . "?".$query;
				// Test Mode
				else
					$url_paytpv = Context::getContext()->link->getModuleLink($paytpv->name, 'urltestmode',$fields,$ssl);


				$paytpv_path = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$paytpv->name.'/';
				
				$this->context->controller->addCSS( $paytpv_path . 'css/account.css' , 'all' );
			 	$this->context->controller->addJS( $paytpv_path . 'js/paytpv_account.js');

				$this->context->smarty->assign('url_paytpv',$url_paytpv);
				$this->context->smarty->assign('saved_card',$saved_card);
				$this->context->smarty->assign('suscriptions',$suscriptions);
				$this->context->smarty->assign('base_dir', __PS_BASE_URI__);

				$this->context->smarty->assign('url_removecard',Context::getContext()->link->getModuleLink('paytpv', 'actions', array("process"=>"removeCard"), true));
				$this->context->smarty->assign('url_savedesc',Context::getContext()->link->getModuleLink('paytpv', 'actions', array("process"=>"saveDescriptionCard"), true));
				$this->context->smarty->assign('url_cancelsuscription',Context::getContext()->link->getModuleLink('paytpv', 'actions',array("process"=>"cancelSuscription"), true));
				
				$this->context->smarty->assign('newpage_payment', $paytpv->newpage_payment);
				$this->context->smarty->assign('status_canceled',$paytpv->l('CANCELLED'));
				$this->setTemplate('paytpv-account.tpl');
			}
		}
	}
}