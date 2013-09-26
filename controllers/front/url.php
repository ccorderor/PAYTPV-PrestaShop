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
		// Recoger datos de respuesta
		if(isset($_REQUEST['i']))
			$importe  = number_format($_REQUEST['i'] / 100, 2);
		if(isset($_REQUEST['r']))
			$ref = $_REQUEST['r'];
// 		if(isset($_REQUEST['id_cart']))
// 			$id_cart = intval($_REQUEST['id_cart']);
		$result = 666;
		if(isset($_REQUEST['ret']))
			$result = intval($_REQUEST['ret']);
		if(isset($_REQUEST['h']))
			$sign = $_REQUEST['h'];
        $id_cart = (int)substr($ref,0,8);
        $cart = new Cart($id_cart);
		$customer = new Customer((int) $cart->id_customer);
		$context = Context::getContext();
		$context->cart = $cart;
		$context->customer = $customer;


		$paytpv = $this->module;
        if($sign == md5($paytpv->usercode.$ref.$paytpv->pass.$result) && $result == 0){       
			$transaction = array(
				//'transaction_id' => ''
				'result' => $result
			);                
			if($paytpv->validateOrder($id_cart, _PS_OS_PAYMENT_, $importe, $paytpv->displayName, NULL, $transaction, NULL, false, $customer->secure_key)){
				$values = array(
					'id_cart' => $id_cart,
					'id_module' => (int)$this->module->id,
					'id_order' => Order::getOrderByCartId(intval($id_cart)),
					'key' => $_REQUEST['key']
				);				
				if ($reg_estado == 1)
					class_registro::removeByCartID($id_cart);                
				Tools::redirect(Context::getContext()->link->getPageLink('order-confirmation',$this->ssl,null,$values));
				return;
			}
		}else{
			if ($reg_estado == 1)
			//se anota el pedido como no pagado
				class_registro::add($cart->id_customer, $id_cart, $importe, $result);
		}

        $this->setTemplate('payment_fail.tpl');
    }
}
