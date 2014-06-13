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
		if(isset($_REQUEST['Amount'])
			AND isset($_REQUEST['Order'])
			AND isset($_REQUEST['Response'])
			AND isset($_REQUEST['ExtendedSignature']))
		{
			$importe  = number_format($_REQUEST['Amount'] / 100, 2);
			$ref = $_REQUEST['Order'];
			$result = $_REQUEST['Response']=='OK'?0:-1;
			$sign = $_REQUEST['ExtendedSignature'];
			$esURLOK = false;
			$local_sign = md5($paytpv->clientcode.$paytpv->term.$_REQUEST['TransactionType'].$ref.$_REQUEST['Amount'].$_REQUEST['Currency'].md5($paytpv->pass).$_REQUEST['BankDateTime'].$_REQUEST['Response']);
		}
        // Recoger datos de respuesta de la urlok
		else if(isset($_REQUEST['i'])
			AND isset($_REQUEST['r'])
			AND isset($_REQUEST['ret'])
			AND isset($_REQUEST['h']))
		{
			$importe  = number_format($_REQUEST['i'] / 100, 2);
			$ref = $_REQUEST['r'];
			$result = intval($_REQUEST['ret']);
			$sign = $_REQUEST['h'];
			$esURLOK = true;
			$local_sign = md5($paytpv->usercode.$ref.$paytpv->pass.$result);
		}
        $id_cart = (int)substr($ref,0,8);
        $cart = new Cart($id_cart);
		$customer = new Customer((int) $cart->id_customer);
		$context = Context::getContext();
		$context->cart = $cart;
		$context->customer = $customer;

        if($sign == $local_sign && $result == 0){
			$transaction = array(
				'transaction_id' => $_REQUEST['AuthCode'],
				'result' => $result
			);
			$id_order = Order::getOrderByCartId(intval($id_cart));
			if($id_order){
				$order = new Order(intval($id_order));
				$pagoRegistrado = $order->getCurrentState() == Configuration::get('PS_OS_PAYMENT');
			}else{
				$pagoRegistrado = $paytpv->validateOrder($id_cart, _PS_OS_PAYMENT_, $importe, $paytpv->displayName, NULL, $transaction, NULL, false, $customer->secure_key);
				if ($pagoRegistrado AND $reg_estado == 1)
					class_registro::removeByCartID($id_cart);
			}
			if($esURLOK && $pagoRegistrado){
				$values = array(
					'id_cart' => $id_cart,
					'id_module' => (int)$this->module->id,
					'id_order' => $id_order,
					'key' => $_REQUEST['key']
				);				
				Tools::redirect(Context::getContext()->link->getPageLink('order-confirmation',$this->ssl,null,$values));
				return;
			}
			else if($pagoRegistrado){
				die('Pago registrado');
			}
		}else{
			if ($reg_estado == 1)
			//se anota el pedido como no pagado
				class_registro::add($cart->id_customer, $id_cart, $importe, $result);
			if ($sign != $local_sign){
				header("HTTP/1.0 466 Invalid Signature");
				die('HAcking Attenpt!!');
			}
		}

        $this->setTemplate('payment_fail.tpl');
    }
}
