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
		parent::initContent();
        $this->context->smarty->assign(array(
            'this_path' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
        ));
		$paytpv = $this->module;
		$client = new WS_Client(
			array(
				'clientcode' => $paytpv->clientcode,
				'term' => $paytpv->term,
				'pass' => $paytpv->pass,
			)
		);
		$data = $paytpv->getToken();
		$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
		$currency = new Currency(intval($id_currency));

		$importe = number_format(Tools::convertPrice($this->context->cart->getOrderTotal(true, 3), $currency), 2, '.', '');

		$charge = $client->execute_purchase( $data['IDUSER'],$data['TOKENUSER'],$this->context->currency->iso_code,$importe,$this->context->cart->id );


		if ( ( int ) $charge[ 'DS_RESPONSE' ] == 1 ) {
			//Esperamos a que la notificación genere el pedido
			sleep ( 3 );
			$id_order = Order::getOrderByCartId(intval($this->context->cart->id));
			$values = array(
				'id_cart' => $this->context->cart->id,
				'id_module' => (int)$this->module->id,
				'id_order' => $id_order,
				'key' => $this->context->customer->secure_key
			);
			Tools::redirect(Context::getContext()->link->getPageLink('order-confirmation',$this->ssl,null,$values));
			return;
		}else{
			if ($reg_estado == 1)
			//se anota el pedido como no pagado
				class_registro::add($cart->id_customer, $this->context->cart->id, $importe, $charge[ 'DS_RESPONSE' ]);
		}

        $this->setTemplate('payment_fail.tpl');
    }
}
