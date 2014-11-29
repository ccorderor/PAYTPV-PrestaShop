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

include_once(_PS_MODULE_DIR_.'/paytpv/ws_client.php');
class PaytpvCaptureModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $ssl = true;
   
    /**
     * @see FrontController::initContent()
     */
   	public function initContent()
    {
    	global $smarty;

    	parent::initContent();
        $this->context->smarty->assign(array(
            'this_path' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
        ));  

		$paytpv = $this->module;

		$msg_paytpv_contrasena = "";

		 // Verificar contraseña usuario.
        if (!$paytpv->validPassword($this->context->cart->id_customer,Tools::getValue('password'))){
        	$this->setTemplate('payment_fail.tpl');
        	$msg_paytpv_contrasena = $paytpv->l('Contraseña invalida');
        	$smarty->assign('msg_paytpv_contrasena',$msg_paytpv_contrasena);
        	return;
        }

		$client = new WS_Client(
			array(
				'clientcode' => $paytpv->clientcode,
				'term' => $paytpv->term,
				'pass' => $paytpv->pass,
			)
		);
		$data = $paytpv->getDataToken($_GET["TOKENUSER"]);
		
		$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));

		$currency = new Currency(intval($id_currency));
		$importe = number_format(Tools::convertPrice($this->context->cart->getOrderTotal(true, 3), $currency), 2, '.', '');
		$charge = $client->execute_purchase( $data['IDUSER'],$data['TOKENUSER'],$this->context->currency->iso_code,$importe,$this->context->cart->id );
		
		if ( ( int ) $charge[ 'DS_RESPONSE' ] == 1 ) {
			$id_cart = (int)substr($charge[ 'DS_MERCHANT_ORDER'],0,8);
			$importe = number_format($charge[ 'DS_MERCHANT_AMOUNT']/ 100, 2);
			$result = $charge[ 'DS_ERROR_ID'];
			$transaction = array(
				'transaction_id' => $charge['DS_MERCHANT_AUTHCODE'],
				'result' => $result
			);
			$customer = new Customer((int) $this->context->cart->id_customer);
			// Registramos el pago
			$pagoRegistrado = $paytpv->validateOrder($id_cart, _PS_OS_PAYMENT_, $importe, $paytpv->displayName, NULL, $transaction, NULL, false, $customer->secure_key);

			$values = array(
				'id_cart' => $this->context->cart->id,
				'id_module' => (int)$this->module->id,
				'id_order' => $id_order,
				'key' => $this->context->customer->secure_key
			);
			Tools::redirect(Context::getContext()->link->getPageLink('order-confirmation',$this->ssl,null,$values));
			return;
		}else{

			if (isset($reg_estado) && $reg_estado == 1)
			//se anota el pedido como no pagado
			class_registro::add($cart->id_customer, $this->context->cart->id, $importe, $charge[ 'DS_RESPONSE' ]);
		}
		$smarty->assign('base_dir',__PS_BASE_URI__);
        $this->setTemplate('payment_fail.tpl');

    }

}

