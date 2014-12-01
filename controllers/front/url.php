<?php

/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 13573 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
/**
 * @since 1.5.0
 */

class PaytpvUrlModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;

	public $ssl = true;
	/**
	 * @see FrontController::initContent()
	 */

	public function initContent()

	{

		parent::initContent();
		$this->context->smarty->assign(array(
			'this_path' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
		));

		$esURLOK = false;
		$pagoRegistrado = false;
		$result = 666;
		$paytpv = $this->module;
		
		// Recoger datos de respuesta de la notificación
		// (execute_purchase)
		if(Tools::getValue('Amount')
			AND Tools::getValue('Order')
			AND Tools::getValue('Response')
			AND Tools::getValue('ExtendedSignature'))
		{
			$importe  = number_format(Tools::getValue('Amount')/ 100, 2);
			$ref = Tools::getValue('Order');
			$result = Tools::getValue('Response')=='OK'?0:-1;
			$sign = Tools::getValue('ExtendedSignature');
			$esURLOK = false;
			$local_sign = md5($paytpv->clientcode.$paytpv->term.Tools::getValue('TransactionType').$ref.Tools::getValue('Amount').Tools::getValue('Currency').md5($paytpv->pass));
		// (add_user)
		}else if (Tools::getValue('TransactionType')==107){
			// Miramos si el cliente 
			include_once(_PS_MODULE_DIR_.'/paytpv/ws_client.php');
			$client = new WS_Client(
				array(
					'clientcode' => $paytpv->clientcode,
					'term' => $paytpv->term,
					'pass' => $paytpv->pass,
				)
			);
			$id_customer = Tools::getValue('Order');
			$result = $client->info_user( Tools::getValue('IdUser'),Tools::getValue('TokenUser'), $_SERVER['REMOTE_ADDR'], true );
			$paytpv->saveCard(0,$id_customer,Tools::getValue('IdUser'),Tools::getValue('TokenUser'),$result['DS_MERCHANT_PAN'],$result['DS_CARD_BRAND']);
			die('Usuario registrado');
		}
		// Recoger datos de respuesta de la urlok TPV WEB
		else if(Tools::getValue('i')
			AND Tools::getValue('r')
			AND Tools::getValue('ret')!=""
			AND Tools::getValue('h'))
		{
			$importe  = number_format(Tools::getValue('i')/ 100, 2);
			$ref = Tools::getValue('r');	
			$result = intval(Tools::getValue('ret'));
			$sign = Tools::getValue('h');
			$esURLOK = true;
			$local_sign = md5($paytpv->usercode.$ref.$paytpv->pass.$result);
		}

		$id_cart = (int)substr($ref,0,8);
		$cart = new Cart($id_cart);
		$customer = new Customer((int) $cart->id_customer);
		$context = Context::getContext();
		$context->cart = $cart;
		$context->customer = $customer;

		if($result == 0){
			$id_order = Order::getOrderByCartId(intval($id_cart));
			// BANKSTORE: Si hay notificacion
			if(Tools::getValue('IdUser')){
				// Si ha pulsado en el acuerdo guardamos el token
				if ($paytpv->paytpvagree_save($cart->id_customer)){
					// Miramos si el cliente 
					include_once(_PS_MODULE_DIR_.'/paytpv/ws_client.php');
					$client = new WS_Client(
						array(
							'clientcode' => $paytpv->clientcode,
							'term' => $paytpv->term,
							'pass' => $paytpv->pass,
						)
					);
					$result = $client->info_user( Tools::getValue('IdUser'),Tools::getValue('TokenUser'), $_SERVER['REMOTE_ADDR'], true );
					$paytpv->saveCard($id_order,$cart->id_customer,Tools::getValue('IdUser'),Tools::getValue('TokenUser'),$result['DS_MERCHANT_PAN'],$result['DS_CARD_BRAND']);
				}
			}
			$transaction = array(
				'transaction_id' => Tools::getValue('AuthCode'),
				'result' => $result
			);
			if($id_order){
				$sql = 'SELECT COUNT(oh.`id_order_history`) AS nb
						FROM `'._DB_PREFIX_.'order_history` oh
						WHERE oh.`id_order` = '.(int)$id_order.'
				AND oh.id_order_state = '.Configuration::get('PS_OS_PAYMENT');
				$n = Db::getInstance()->getValue($sql);
				$pagoRegistrado = $n>0;
			}else{
				$pagoRegistrado = $paytpv->validateOrder($id_cart, _PS_OS_PAYMENT_, $importe, $paytpv->displayName, NULL, $transaction, NULL, false, $customer->secure_key);
				if ($pagoRegistrado AND $reg_estado == 1)
					class_registro::removeByCartID($id_cart);
			}
			// Si venimos de URLOK y se ha registrado el pago mandamos a la pagina de confirmacion de orden
			if($esURLOK && $pagoRegistrado){
				$values = array(
					'id_cart' => $id_cart,
					'id_module' => (int)$this->module->id,
					'id_order' => $id_order,
					'key' => Tools::getValue('key')
				);              
				Tools::redirect(Context::getContext()->link->getPageLink('order-confirmation',$this->ssl,null,$values));
				return;
			}
			else if($pagoRegistrado){
				die('Pago registrado');
			}
		}else{
			//se anota el pedido como no pagado
			if ($reg_estado == 1)
				class_registro::add($cart->id_customer, $id_cart, $importe, $result);

			/*if ($sign != $local_sign){
				header("HTTP/1.0 466 Invalid Signature");
				die('HAcking Attenpt!!');
			}*/

		}
		$this->setTemplate('payment_fail.tpl');

	}

}

