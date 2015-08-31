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
*  @author     Mikel Martin <mmartin@paytpv.com>
*  @author     Jose Ramon Garcia <jrgarcia@paytpv.com>
*  @copyright  2015 PAYTPV ON LINE S.L.
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_'))
	exit;

include_once dirname(__FILE__).'/class_registro.php';
include_once dirname(__FILE__).'/classes/Paytpv_Terminal.php';
include_once dirname(__FILE__).'/classes/Paytpv_Order.php';
include_once dirname(__FILE__).'/classes/Paytpv_Order_Info.php';
include_once dirname(__FILE__).'/classes/Paytpv_Customer.php';
include_once dirname(__FILE__).'/classes/Paytpv_Suscription.php';
include_once dirname(__FILE__).'/classes/Paytpv_Refund.php';

class Paytpv extends PaymentModule {

	private $_html = '';

	private $_postErrors = array();
	
	public function __construct() {

		$this->name = 'paytpv';
		$this->tab = 'payments_gateways';
		$this->author = 'PayTPV';
		$this->version = '6.2.1';

		$this->url_paytpv = "https://secure.paytpv.com/gateway/bnkgateway.php";
		
		//$this->bootstrap = true;
		// Array config:  configuration values
		$config = $this->getConfigValues();
		
		// Establishing properties from configuraction data
		if (isset($config['PAYTPV_ENVIRONMENT']))
			$this->environment = $config['PAYTPV_ENVIRONMENT'];

		// Test Mode
		if ($this->environment==1){
			$this->clientcode = "5tam14h8";
		// Real Mode
		}else{
			if (isset($config['PAYTPV_CLIENTCODE']))
				$this->clientcode = $config['PAYTPV_CLIENTCODE'];
		}

		if (isset($config['PAYTPV_COMMERCEPASSWORD']))
			$this->commerce_password = $config['PAYTPV_COMMERCEPASSWORD'];
		if (isset($config['PAYTPV_NEWPAGEPAYMENT']))
			$this->newpage_payment = $config['PAYTPV_NEWPAGEPAYMENT'];
		if (isset($config['PAYTPV_SUSCRIPTIONS']))
			$this->suscriptions = $config['PAYTPV_SUSCRIPTIONS'];		
		if (isset($config['PAYTPV_REG_ESTADO']))
			$this->reg_estado = $config['PAYTPV_REG_ESTADO'];

		

		parent::__construct();
		$this->page = basename(__FILE__, '.php');

		$this->displayName = $this->l('paytpv.com');
		$this->description = $this->l('This module allows you to accept card payments via paytpv.com');
		
		try{
			if ($this->environment!=1){
				if (!isset($this->clientcode) OR !Paytpv_Terminal::exist_Terminal())
					$this->warning = $this->l('Missing data when configuring the module Paytpv');
			}
		}catch (exception $e){}

	}

	protected function write_log(){

		if (Tools::usingSecureMode())
 			$domain = Tools::getShopDomainSsl(true);
 		else
 			$domain = Tools::getShopDomain(true);
		try{
			$url_log = "http://prestashop.paytpv.com/log_paytpv.php?dominio=".$domain."&version_modulo=".$this->version."&tienda=Prestashop&version_tienda="._PS_VERSION_;
			@file_get_contents($url_log);
		}catch (exception $e){}
	}

	
	public function runUpgradeModule(){
		$this->write_log();
		parent::runUpgradeModule();
	}



	public function install() {

		include_once(_PS_MODULE_DIR_.'/'.$this->name.'/paytpv_install.php');
		$paypal_install = new PayTpvInstall();
		$res = $paypal_install->createTables();
		if (!$res){
			$this->error = $this->l('Missing data when configuring the module Paytpv');
			return false;
		}

		$paypal_install->updateConfiguration();
		
		// Valores por defecto al instalar el módulo
		if (!parent::install() ||
			!$this->registerHook('displayPayment') ||
			!$this->registerHook('displayPaymentTop') ||
			!$this->registerHook('displayPaymentReturn') ||
			!$this->registerHook('displayMyAccountBlock') || 
			!$this->registerHook('displayAdminOrder') || 
			!$this->registerHook('displayCustomerAccount') ||
			!$this->registerHook('actionProductCancel') ||
			!$this->registerHook('displayShoppingCart')
			) 
			return false;
		$this->write_log();

		
		return true;
	}
	public function uninstall() {
		include_once(_PS_MODULE_DIR_.'/'.$this->name.'/paytpv_install.php');
		$paypal_install = new PayTpvInstall();
		$paypal_install->deleteConfiguration();
		return parent::uninstall();
	}

	public function getPath(){
		return $this->_path;
	}

	private function _postValidation(){

	    // Show error when required fields.
		if (isset($_POST['btnSubmit']))
		{

			if ($_POST["environment"]!=1){
				if (empty($_POST['clientcode']))
					$this->_postErrors[] = $this->l('Client Code required');
				if (empty($_POST['pass']))
					$this->_postErrors[] = $this->l('User Password required');
			}

			// Check Terminal empty fields
			foreach ($_POST['term'] as $key=>$term){
				if ($term=="" || !is_numeric($term) ){
					$this->_postErrors[] = $this->l('Terminal'). " " . ($key+1) . "º. ". $this->l('Terminal number invalid');
				}

				if ($_POST["pass"][$key]==""){
					$this->_postErrors[] = $this->l('Terminal'). " " . ($key+1) . "º. ". $this->l('Password invalid');
				}

				if ($_POST["terminales"][$key]==2 && ($_POST["tdmin"][$key]!="" && !is_numeric($_POST["tdmin"][$key])))
				{
					$this->_postErrors[] = $this->l('Terminal'). " " . ($key+1) . "º. " . $this->l('Use 3D Secure on purchases over invalid');
				}

				if (empty($_POST['moneda'][$key]))
					$this->_postErrors[] = $this->l('Terminal'). " " . ($key+1) . "º. ". $this->l('Currency required');
			}

			// Check Duplicate Terms
			$arrTerminales = array_unique($_POST['term']);
			if (sizeof($arrTerminales) != sizeof($_POST['term']))
				$this->_postErrors[] = $this->l('Duplicate Terminals');

			// Check Duplicate Currency
			$arrMonedas = array_unique($_POST['moneda']);
			if (sizeof($arrMonedas) != sizeof($_POST['moneda']))
				$this->_postErrors[] = $this->l('Duplicate Currency. Specify a different currency for each terminal');

		}

	}
	private function _postProcess(){

	    // Update databse configuration
		if (isset($_POST['btnSubmit'])){
			Configuration::updateValue('PAYTPV_ENVIRONMENT', $_POST['environment']);
			Configuration::updateValue('PAYTPV_CLIENTCODE', $_POST['clientcode']);

			Configuration::updateValue('PAYTPV_COMMERCEPASSWORD', $_POST['commerce_password']);
			Configuration::updateValue('PAYTPV_NEWPAGEPAYMENT', $_POST['newpage_payment']);
			Configuration::updateValue('PAYTPV_SUSCRIPTIONS', $_POST['suscriptions']); 
			
			// Save Paytpv Terminals
			Paytpv_Terminal::remove_Terminals();
			
			foreach ($_POST["term"] as $key=>$terminal){
				$_POST['tdmin'][$key] = ($_POST['tdmin'][$key]=='' || $_POST["terminales"][$key]!=2)?0:$_POST['tdmin'][$key];
				Paytpv_Terminal::add_Terminal($key+1,$terminal,$_POST["pass"][$key],$_POST["moneda"][$key],$_POST["terminales"][$key],$_POST["tdfirst"][$key],$_POST["tdmin"][$key]);
				
			}
			return '<div class="bootstrap"><div class="alert alert-success">'.$this->l('Configuration updated').'</div></div>';          
		}

	}
	public function getContent() {

		$errorMessage = '';
		if (!empty($_POST)) {
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$errorMessage = $this->_postProcess();
			else{
				$errorMessage .= '<div class="bootstrap"><div class="alert alert-warning"><strong>'.$this->l('Error').'</strong><ol>';
				foreach ($this->_postErrors AS $err)
					$errorMessage .= '<li>' . $err . '</li>';
				$errorMessage .= '</ol></div></div>';
			}
		}else
			$errorMessage = '';

		$conf_values = $this->getConfigValues();

		if (Tools::isSubmit('id_cart'))
			$this->validateOrder($_GET['id_cart'], _PS_OS_PAYMENT_, $_GET['amount'], $this->displayName, NULL);

		if (Tools::isSubmit('id_registro'))
			class_registro::remove($_GET['id_registro']);
		
		$carritos = class_registro::select();

		$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
		$currency_array =   Currency::getCurrencies($object = false, $active = 1);

		$ssl = Configuration::get('PS_SSL_ENABLED');
		// Set the smarty env
		$this->context->smarty->assign('serverRequestUri', Tools::safeOutput($_SERVER['REQUEST_URI']));
		$this->context->smarty->assign('displayName', Tools::safeOutput($this->displayName));
		$this->context->smarty->assign('description', Tools::safeOutput($this->description));
		$this->context->smarty->assign('currentindex',AdminController::$currentIndex);
		$this->context->smarty->assign('token',$_GET['token']);
		$this->context->smarty->assign('name', $this->name);
		$this->context->smarty->assign('reg_estado', $conf_values['PAYTPV_REG_ESTADO']);
		$this->context->smarty->assign('carritos', $carritos);
		$this->context->smarty->assign('errorMessage',$errorMessage);

		$this->context->smarty->assign('environment', (isset($_POST["environment"]))?$_POST["environment"]:$conf_values['PAYTPV_ENVIRONMENT']);
		$this->context->smarty->assign('clientcode', (isset($_POST["clientcode"]))?$_POST["clientcode"]:$conf_values['PAYTPV_CLIENTCODE']);

		$this->context->smarty->assign('terminales_paytpv', $this->obtenerTerminalesConfigurados($_POST));
	
		$this->context->smarty->assign('commerce_password', (isset($_POST["commerce_password"]))?$_POST["commerce_password"]:$conf_values['PAYTPV_COMMERCEPASSWORD']);
		$this->context->smarty->assign('newpage_payment', (isset($_POST["newpage_payment"]))?$_POST["newpage_payment"]:$conf_values['PAYTPV_NEWPAGEPAYMENT']);
		$this->context->smarty->assign('suscriptions', (isset($_POST["suscriptions"]))?$_POST["suscriptions"]:$conf_values['PAYTPV_SUSCRIPTIONS']);
		$this->context->smarty->assign('currency_array', $currency_array);
		$this->context->smarty->assign('default_currency', $id_currency);
		$this->context->smarty->assign('OK',Context::getContext()->link->getModuleLink($this->name, 'urlok',array(),$ssl));
		$this->context->smarty->assign('KO',Context::getContext()->link->getModuleLink($this->name, 'urlko',array(),$ssl));
		$this->context->smarty->assign('NOTIFICACION',Context::getContext()->link->getModuleLink($this->name, 'url',array(),$ssl));
		$this->context->smarty->assign('base_dir', __PS_BASE_URI__);

		$this->context->controller->addCSS( $this->_path . 'css/admin.css' , 'all' );
		return $this->display(__FILE__, 'views/admin.tpl');

	}

	 
    function obtenerTerminalesConfigurados($params) {
    	if (isset($params["term"])){
    		foreach ($params["term"] as $key=>$term){
    			$terminales[$key]["idterminal"] = $term;
    			$terminales[$key]["password"] = $params["pass"][$key];
    			$terminales[$key]["terminales"] = $params["terminales"][$key];
    			$terminales[$key]["tdfirst"] = $params["tdfirst"][$key];
    			$terminales[$key]["tdmin"] = $params["tdmin"][$key];
    			$terminales[$key]["currency_iso_code"] = $params["moneda"][$key];
    		}
    		
    	}else{
    		$terminales = Paytpv_Terminal::get_Terminals();
    		if (sizeof($terminales)==0){
    			$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
				$currency = new Currency(intval($id_currency));	

    			$terminales[0]["idterminal"] = "";
    			$terminales[0]["password"] = "";
    			$terminales[0]["terminales"] = 0;
    			$terminales[0]["tdfirst"] = 1;
    			$terminales[0]["tdmin"] = 0;
    			$terminales[0]["currency_iso_code"] = $currency->iso_code;
    		}
    	}
    	return $terminales;
    }

	public function hookDisplayShoppingCart()
	{
		$this->context->controller->addCSS( $this->_path . 'css/payment.css' , 'all' );
		$this->context->controller->addJS( $this->_path . 'js/paytpv.js');
	}

	

	public function hookDisplayPaymentTop($params) {
		$this->context->controller->addCSS( $this->_path . 'css/payment.css' , 'all' );
		$this->context->controller->addJS( $this->_path . 'js/paytpv.js');
		
	}

	public function hookDisplayPayment($params) {

		// Check New Page payment
		$newpage_payment = intval(Configuration::get('PAYTPV_NEWPAGEPAYMENT'));
		if ($newpage_payment){
			$this->context->smarty->assign('this_path',$this->_path);
			return $this->display(__FILE__, 'payment_newpage.tpl');
		}else{

			$this->context->smarty->assign('msg_paytpv',"");
			$showcard = false;
			$msg_paytpv = "";

			$this->context->smarty->assign('msg_paytpv',$msg_paytpv);
			$this->context->smarty->assign('showcard',$showcard);

		    // Valor de compra				
			$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));

			$currency = new Currency(intval($id_currency));		
			$importe = number_format($params['cart']->getOrderTotal(true, Cart::BOTH)*100, 0, '.', '');		

			$paytpv_order_ref = str_pad($params['cart']->id, 8, "0", STR_PAD_LEFT);
			$ssl = Configuration::get('PS_SSL_ENABLED');
			$values = array(
				'id_cart' => (int)$params['cart']->id,
				'key' => Context::getContext()->customer->secure_key
			);

			$active_suscriptions = intval(Configuration::get('PAYTPV_SUSCRIPTIONS'));

			$saved_card = Paytpv_Customer::get_Cards_Customer((int)$this->context->customer->id);
			$index = 0;
			foreach ($saved_card as $key=>$val){
				$values_aux = array_merge($values,array("TOKEN_USER"=>$val["TOKEN_USER"]));
				$saved_card[$key]['url'] = Context::getContext()->link->getModuleLink($this->name, 'capture',$values_aux,$ssl);	
				$index++;
			}
			$saved_card[$index]['url'] = 0;

			$tmpl_vars = array();
			$tmpl_vars['capture_url'] = Context::getContext()->link->getModuleLink($this->name, 'capture',$values,$ssl);
			$this->context->smarty->assign('active_suscriptions',$active_suscriptions);
			$this->context->smarty->assign('saved_card',$saved_card);
			$this->context->smarty->assign('commerce_password',$this->commerce_password);
			$this->context->smarty->assign('id_cart',$params['cart']->id);
			
			$this->context->smarty->assign('base_dir', __PS_BASE_URI__);

			
			$tmpl_vars = array_merge(
				array(
				'this_path' => $this->_path)
			);
			$this->context->smarty->assign($tmpl_vars);
			
		 	$arrAddCard = array("process"=>"addCard");
		    $arrSubscribe = array("process"=>"suscribe");
		 	$this->context->smarty->assign('addcard_url',Context::getContext()->link->getModuleLink('paytpv', 'actions', $arrAddCard, true));
		 	$this->context->smarty->assign('subscribe_url',Context::getContext()->link->getModuleLink('paytpv', 'actions', $arrSubscribe, true));
		 	

			return $this->display(__FILE__, 'payment_bsiframe.tpl');
		}

	}

	/**
	 * return array Term,Currency,amount
	 */
	public function TerminalCurrency($cart){

		// Si hay un terminal definido para la moneda del usuario devolvemos ese.
		$result = Paytpv_Terminal::get_Terminal_Currency($this->context->currency->iso_code);
		// Not exists terminal in user currency
		if (empty($result) === true){
			// Search for terminal in merchant default currency
			$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
			$currency = new Currency($id_currency);
			$result = Paytpv_Terminal::get_Terminal_Currency($currency->iso_code);

			// If not exists terminal in default currency. Select first terminal defined
			if (empty($result) === true){
				$result = Paytpv_Terminal::get_First_Terminal();
			}
		}

		$arrDatos["idterminal"] = $result["idterminal"];
		$arrDatos["password"] = $result["password"];
		$arrDatos["currency_iso_code"] = $this->context->currency->iso_code;
		$arrDatos["importe"] = number_format($cart->getOrderTotal(true, Cart::BOTH) * 100, 0, '.', '');
		
        return $arrDatos;
	}


	public function isSecureTransaction($idterminal,$importe,$card){
		$arrTerminal = Paytpv_Terminal::getTerminalByIdTerminal($idterminal);

        $terminales = $arrTerminal["terminales"];
        $tdfirst = $arrTerminal["tdfirst"];
        $tdmin = $arrTerminal["tdmin"];
        // Transaccion Segura:
        
        // Si solo tiene Terminal Seguro
        if ($terminales==0)
            return true;   

        // Si esta definido que el pago es 3d secure y no estamos usando una tarjeta tokenizada
        if ($tdfirst && $card==0)
            return true;

        // Si se supera el importe maximo para compra segura
        if ($terminales==2 && ($tdmin>0 && $tdmin < $importe))
            return true;

         // Si esta definido como que la primera compra es Segura y es la primera compra aunque este tokenizada
        if ($terminales==2 && $tdfirst && $card>0 && Paytpv_Order::isFirstPurchaseToken($this->context->customer->id,$card))
            return true;
        
        
        return false;
    }


	public function isSecurePay($importe){
		// Terminal NO Seguro
		if ($this->terminales==1)
			return false;
		// Ambos Terminales, Usar 3D False e Importe < Importe Min 3d secure
		if ($this->terminales==2 && $this->tdfirst==0 && ($this->tdmin==0 || $importe<=$this->tdmin))
			return false;
		return true;
	}


	public function hookDisplayPaymentReturn($params) {

		if (!$this->active)
			return;
		$this->context->smarty->assign(array(
			'this_path' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));

		$id_order = Order::getOrderByCartId(intval($params["objOrder"]->id_cart));
		$order = new Order($id_order);

		$this->context->smarty->assign('reference',$order->reference);
		$this->context->smarty->assign('base_dir',__PS_BASE_URI__);

		$this->_html .= $this->display(__FILE__, 'payment_return.tpl');

		
		$result = Paytpv_Suscription::get_Suscription_Order_Payments($id_order);
		if ($order->module == $this->name && !empty($result)){

			$id_currency = $order->id_currency;
			$currency = new Currency(intval($id_currency));

			$suscription_type = $this->l('This order is a Subscription');
			
			$id_suscription = $result["id_suscription"];
			$id_customer = $result["id_customer"];
			$periodicity = $result["periodicity"];
			$cycles = ($result['cycles']!=0)?$result['cycles']:$this->l('N');
			$status = $result["status"];
			$date = $result["date"];
			$price = number_format($result['price'], 2, '.', '') . " " . $currency->sign;	
			$num_pagos = $result['pagos'];

			if ($status==0)
				$status = $this->l('ACTIVE');
			else if ($status==1)
				$status = $this->l('CANCELLED');
			else if ($num_pagos==$result['cycles'] && $result['cycles']>0)	
				$status = $this->l('ENDED');
                               
			

			$date_YYYYMMDD = ($this->context->language->iso_code=="es")?date("d-m-Y",strtotime($result['date'])):date("Y-m-d",strtotime($result['date']));


			$this->context->smarty->assign('suscription_type', $suscription_type);
			$this->context->smarty->assign('id_customer', $id_customer);
			$this->context->smarty->assign('periodicity', $periodicity);
			$this->context->smarty->assign('cycles', $cycles);
			$this->context->smarty->assign('status', $status);
			$this->context->smarty->assign('date_yyyymmdd', $date_YYYYMMDD);
			$this->context->smarty->assign('price', $price);

			$this->_html .= $this->display(__FILE__, 'order_suscription_customer_info.tpl');
		}

		
		return $this->_html;

	}
	private function getConfigValues(){
		return Configuration::getMultiple(array('PAYTPV_CLIENTCODE', 'PAYTPV_ENVIRONMENT', 'PAYTPV_COMMERCEPASSWORD', 'PAYTPV_NEWPAGEPAYMENT', 'PAYTPV_SUSCRIPTIONS','PAYTPV_REG_ESTADO'));
	}
	
	public function saveCard($id_customer,$paytpv_iduser,$paytpv_tokenuser,$paytpv_cc,$paytpv_brand){

		$paytpv_cc = '************' . substr($paytpv_cc, -4);

		// Test Mode
		// First 100.000 paytpv_iduser for Test_Mode
		if ($this->environment==1){
			$paytpv_iduser = Paytpv_Customer::get_Customer();
			$paytpv_tokenuser = "TESTTOKEN";
		}

		Paytpv_Customer::add_Customer($paytpv_iduser,$paytpv_tokenuser,$paytpv_cc,$paytpv_brand,$id_customer);

		$result["paytpv_iduser"] = $paytpv_iduser;
		$result["paytpv_tokenuser"] = $paytpv_tokenuser;

		return $result;
	}

	
	public function remove_user($paytpv_iduser,$paytpv_tokenuser){
		$arrTerminal = Paytpv_Terminal::getTerminalByCurrency($this->context->currency->iso_code);
		
		$client = new WS_Client(
			array(
				'clientcode' => $this->clientcode,
				'term' => $arrTerminal["idterminal"],
				'pass' => $arrTerminal["password"]
			)
		);

		$result = $client->remove_user( $paytpv_iduser, $paytpv_tokenuser);
		return $result;
	}


	public function removeCard($paytpv_iduser){

		$arrTerminal = Paytpv_Terminal::getTerminalByCurrency($this->context->currency->iso_code);

		include_once(_PS_MODULE_DIR_.'/paytpv/ws_client.php');

		$client = new WS_Client(
			array(
				'clientcode' => $this->clientcode,
				'term' => $arrTerminal["idterminal"],
				'pass' => $arrTerminal["password"]
			)
		);
		// Datos usuario

		
		$result = Paytpv_Customer::get_Customer_Iduser($paytpv_iduser);
		if (empty($result) === true){
			return false;
		}else{
			$paytpv_iduser = $result["paytpv_iduser"];
			$paytpv_tokenuser = $result["paytpv_tokenuser"];

			$result = $client->remove_user( $paytpv_iduser, $paytpv_tokenuser);
			Paytpv_Customer::remove_Customer_Iduser((int)$this->context->customer->id,$paytpv_iduser);
			
			
			return true;
		}
	}

	
	public function removeSuscription($id_suscription){

		$arrTerminal = Paytpv_Terminal::getTerminalByCurrency($this->context->currency->iso_code);
		
		include_once(_PS_MODULE_DIR_.'/paytpv/ws_client.php');

		$client = new WS_Client(
			array(
				'clientcode' => $this->clientcode,
				'term' => $arrTerminal["idterminal"],
				'pass' => $arrTerminal["password"]
			)
		);
		// Datos usuario

		$result = Paytpv_Suscription::get_Suscription_Id((int)$this->context->customer->id,$id_suscription);
		
		if (empty($result) === true){
			return false;
		}else{
			$paytpv_iduser = $result["paytpv_iduser"];
			$paytpv_tokenuser = $result["paytpv_tokenuser"];

			// Test Mode
			if ($this->environment==1){
				$result['DS_RESPONSE']=1;
			}else{
				$result = $client->remove_subscription( $paytpv_iduser, $paytpv_tokenuser);
			}

			if ( ( int ) $result[ 'DS_RESPONSE' ] == 1 ) {
				Paytpv_Suscription::remove_Suscription((int)$this->context->customer->id,$id_suscription);
				
				return true;
			}
			return false;
		}
	}

	public function cancelSuscription($id_suscription){
		$arrTerminal = Paytpv_Terminal::getTerminalByCurrency($this->context->currency->iso_code);

		include_once(_PS_MODULE_DIR_.'/paytpv/ws_client.php');

		$client = new WS_Client(
			array(
				'clientcode' => $this->clientcode,
				'term' => $arrTerminal["idterminal"],
				'pass' => $arrTerminal["password"]
			)
		);
		// Datos usuario
		$result = Paytpv_Suscription::get_Suscription_Id((int)$this->context->customer->id,$id_suscription);
		if (empty($result) === true){
			return false;
		}else{
			$paytpv_iduser = $result["paytpv_iduser"];
			$paytpv_tokenuser = $result["paytpv_tokenuser"];

			// Test Mode
			if ($this->environment==1){
				// Operacion realizada en Modo Test
				if ($paytpv_tokenuser=="TESTTOKEN"){
					$result[ 'DS_RESPONSE' ] = 1;
					$response["error"] = 0;
					$response["txt"] = $this->l('OK');
				// Operacion real
				}else{
					$result[ 'DS_RESPONSE' ] = 0;
					$response["error"] = 1;
					$response["txt"] = $this->l('Test mode does not support returns orders placed in Real Mode');
				}
			}else{
				if ($paytpv_tokenuser=="TESTTOKEN"){
					$result[ 'DS_RESPONSE' ] = 1;
				// Operacion real
				}else{
					$result = $client->remove_subscription( $paytpv_iduser, $paytpv_tokenuser);
				}	
			}
			if ( ( int ) $result[ 'DS_RESPONSE' ] == 1 ) {
				Paytpv_Suscription::cancel_Suscription((int)$this->context->customer->id,$id_suscription);
				$response["error"] = 0;
			}else{
				$response["error"] = 1;
			}
			return $response;
		}
	}

	public function validPassword($id_customer,$passwd){
		$sql = 'select * from ' . _DB_PREFIX_ .'customer where id_customer = '.pSQL($id_customer) . ' and passwd="'. md5(pSQL(_COOKIE_KEY_.$passwd)) . '"';
		$result = Db::getInstance()->getRow($sql);
		return (empty($result) === true)?false:true;
	}


	/* 
		Refund
	*/

	public function hookActionProductCancel($params)
	{

		if (Tools::isSubmit('generateDiscount'))
			return false;
		elseif ($params['order']->module != $this->name || !($order = $params['order']) || !Validate::isLoadedObject($order))
			return false;
		elseif (!$order->hasBeenPaid())
			return false;

		$order_detail = new OrderDetail((int)$params['id_order_detail']);
		if (!$order_detail || !Validate::isLoadedObject($order_detail))
			return false;

		$paytpv_order = Paytpv_Order::get_Order((int)$order->id);
		if (empty($paytpv_order)){
			die('error');
			return false;
		}

		$paytpv_date = date("Ymd",strtotime($paytpv_order['date']));
		$paytpv_iduser = $paytpv_order["paytpv_iduser"];
		$paytpv_tokenuser = $paytpv_order["paytpv_tokenuser"];

		$id_currency = $order->id_currency;
		$currency = new Currency(intval($id_currency));

		$orderPayment = $order->getOrderPaymentCollection()->getFirst();
		$authcode = $orderPayment->transaction_id;

		$products = $order->getProducts();
		$cancel_quantity = Tools::getValue('cancelQuantity');

		$amt = (float)($products[(int)$order_detail->id]['product_price_wt'] * (int)$cancel_quantity[(int)$order_detail->id]);
		$amount = number_format($amt * 100, 0, '.', '');

		$paytpv_order_ref = str_pad((int)$order->id_cart, 8, "0", STR_PAD_LEFT);

		$response = $this->_makeRefund($paytpv_iduser,$paytpv_tokenuser,$order->id,$paytpv_order_ref,$paytpv_date,$currency->iso_code,$authcode,$amount,1);
		$refund_txt = $response["txt"];

		$message = $this->l('PayTPV Refund ').  ", " . $amt . " " . $currency->sign . " [" . $refund_txt . "]" .  '<br>';
		$this->_addNewPrivateMessage((int)$order->id, $message);

	}

	private function _makeRefund($paytpv_iduser,$paytpv_tokenuser,$order_id,$paytpv_order_ref,$paytpv_date,$currency_iso_code,$authcode,$amount,$type){
		
		$arrTerminal = Paytpv_Terminal::getTerminalByCurrency($currency_iso_code);

		// Refund amount
		include_once(_PS_MODULE_DIR_.'/paytpv/ws_client.php');
		$client = new WS_Client(
			array(
				'clientcode' => $this->clientcode,
				'term' => $arrTerminal["idterminal"],
				'pass' => $arrTerminal["password"]
			)
		);
	
		// Test Mode
		// Se devuelven solo las operaciones realizadas en modo Test
		if ($this->environment==1){
			// Operacion realizada en Modo Test
			if ($authcode=="Test_mode"){
				$result[ 'DS_RESPONSE' ] = 1;
				$response["error"] = 0;
				$response["txt"] = $this->l('OK');
			// Operacion real
			}else{
				$result[ 'DS_RESPONSE' ] = 1;
				$response["error"] = 1;
				$response["txt"] = $this->l('Test mode does not support returns orders placed in Real Mode');
			}
		}else{
			// Refund amount of transaction
			$result = $client->execute_refund($paytpv_iduser, $paytpv_tokenuser, $paytpv_order_ref, $currency_iso_code, $authcode, $amount);
			$refund_txt = $this->l('OK');
			$response["error"] = 0;
			$response["txt"] = $this->l('OK');

			// If is a subscription and error y initial refund.
			if ($result[ 'DS_ERROR_ID']==130){
				$paytpv_order_ref .= "[" . $paytpv_iduser . "]" . $paytpv_date;
				// Refund amount of transaction
				$result = $client->execute_refund($paytpv_iduser, $paytpv_tokenuser, $paytpv_order_ref, $currency_iso_code, $authcode, $amount);
				$refund_txt = $this->l('OK');
				$response["error"] = 0;
				$response["txt"] = $this->l('OK');
			}
		}
		if ( ( int ) $result[ 'DS_RESPONSE' ] != 1 ){
			$response["txt"] = $this->l('ERROR') . " " . $result[ 'DS_ERROR_ID'];
			$response["error"] = 1;
		}else{
			$amount = number_format($amount/100, 2, '.', '');
			Paytpv_Refund::add_Refund($order_id,$amount,$type);
		}
		return $response;

	}

	public function _addNewPrivateMessage($id_order, $message)
	{
		if (!(bool)$id_order)
			return false;

		$new_message = new Message();
		$message = strip_tags($message, '<br>');

		if (!Validate::isCleanHtml($message))
			$message = $this->l('Payments messages are invalid, please check the module.');

		$new_message->message = $message;
		$new_message->id_order = (int)$id_order;
		$new_message->private = 1;

		return $new_message->add();
	}

	/*

	Datos cuenta
	*/

	public function hookDisplayCustomerAccount($params)
	{
		$this->smarty->assign('in_footer', false);
		return $this->display(__FILE__, 'my-account.tpl');
	}


	/*

	Datos cuenta
	*/

	public function hookDisplayAdminOrder($params)
	{
		
		if (Tools::isSubmit('submitPayTpvRefund'))
			$this->_doTotalRefund($params['id_order']);

		if (Tools::isSubmit('submitPayTpvPartialRefund'))
			$this->_doPartialRefund($params['id_order']);

		$order = new Order((int)$params['id_order']);
		$result = Paytpv_Suscription::get_Suscription_Order_Payments($params["id_order"]);
		if ($order->module == $this->name && !empty($result)){
			
			$id_currency = $order->id_currency;
			$currency = new Currency(intval($id_currency));


			$suscription = $result["suscription"];
			if ($suscription==1){
				$suscription_type = $this->l('This order is a Subscription');
			}else{
				$suscription_type = $this->l('This order is a payment for Subscription');
			}
			$id_suscription = $result["id_suscription"];
			$id_customer = $result["id_customer"];
			$periodicity = $result["periodicity"];
			$cycles = ($result['cycles']!=0)?$result['cycles']:$this->l('N');
			$status = $result["status"];
			$date = $result["date"];
			$price = number_format($result['price'], 2, '.', '') . " " . $currency->sign;	
			$num_pagos = $result['pagos'];

			if ($status==0)
				$status = $this->l('ACTIVE');
			else if ($status==1)
				$status = $this->l('CANCELLED');
			else if ($num_pagos==$result['cycles'] && $result['cycles']>0)	
				$status = $this->l('ENDED');
                               
			$date_YYYYMMDD = ($this->context->language->iso_code=="es")?date("d-m-Y",strtotime($result['date'])):date("Y-m-d",strtotime($result['date']));


			$this->context->smarty->assign('suscription_type', $suscription_type);
			$this->context->smarty->assign('id_customer', $id_customer);
			$this->context->smarty->assign('periodicity', $periodicity);
			$this->context->smarty->assign('cycles', $cycles);
			$this->context->smarty->assign('status', $status);
			$this->context->smarty->assign('date_yyyymmdd', $date_YYYYMMDD);
			$this->context->smarty->assign('price', $price);

			$this->_html .= $this->display(__FILE__, 'order_suscription_info.tpl');
		}

		// Total Refund Template
		if ($order->module == $this->name && $this->_canRefund($order->id)){

			if (version_compare(_PS_VERSION_, '1.5', '>='))
					$order_state = $order->current_state;
				else
					$order_state = OrderHistory::getLastOrderState($order->id);

			$total_amount = $order->total_paid;

			$amount_returned =  Paytpv_Refund::get_TotalRefund($order->id);
			$amount_returned = number_format($amount_returned, 2, '.', '');


			$total_pending = $total_amount - $amount_returned;
			$total_pending =  number_format($total_pending, 2, '.', '');

			$currency = new Currency((int)$order->id_currency);

			$amt_sign = $total_pending . " " . $currency->sign;

			$error_msg = "";
			if (Tools::getValue('paytpPartialRefundAmount')){
				$amt_refund = str_replace(",",".",Tools::getValue('paytpPartialRefundAmount'));
				if (is_numeric($amt_refund))
					$amt_refund = number_format($amt_refund, 2, '.', '');

				if (Tools::getValue('paytpPartialRefundAmount') && ($amt_refund>$total_pending || $amt_refund=="" || !is_numeric($amt_refund))){
					$error_msg = Tools::displayError($this->l('The partial amount should be less than the outstanding amount'));
				}
			}

			$arrRefunds = array();
			if ($amount_returned>0){
				$arrRefunds = Paytpv_Refund::get_Refund($order->id);
			}
			

			$this->context->smarty->assign(
					array(
						'base_url' => _PS_BASE_URL_.__PS_BASE_URI__,
						'module_name' => $this->name,
						'order_state' => $order_state,
						'params' => $params,
						'total_amount' => $total_amount,
						'amount_returned' => $amount_returned,
						'arrRefunds' => $arrRefunds,
						'amount' => $amt_sign,
						'sign'	 => $currency->sign,
						'error_msg' => $error_msg,
						'ps_version' => _PS_VERSION_
					)
				);



			$template_refund = 'views/templates/admin/admin_order/refund.tpl';
			$this->_html .=  $this->display(__FILE__, $template_refund);
			$this->_postProcess();
		}

		return $this->_html;	
	}

	private function _doPartialRefund($id_order)
	{

		$paytpv_order = Paytpv_Order::get_Order((int)$id_order);
		if (empty($paytpv_order)){
			return false;
		}

		$order = new Order((int)$id_order);
		if (!Validate::isLoadedObject($order))
			return false;

		$products = $order->getProducts();
		$currency = new Currency((int)$order->id_currency);
		if (!Validate::isLoadedObject($currency))
			$this->_errors[] = $this->l('Invalid Currency');

		if (count($this->_errors))
			return false;

		$decimals = (is_array($currency) ? (int)$currency['decimals'] : (int)$currency->decimals) * _PS_PRICE_DISPLAY_PRECISION_;

		$total_amount = $order->total_paid;

		$total_pending = $total_amount - Paytpv_Refund::get_TotalRefund($order->id);
		$total_pending =  number_format($total_pending, 2, '.', '');

		$amt_refund  = str_replace(",",".",Tools::getValue('paytpPartialRefundAmount'));
		if (is_numeric($amt_refund))
			$amt_refund = number_format($amt_refund, 2, '.', '');
		
		if ($amt_refund>$total_pending || $amt_refund=="" || !is_numeric($amt_refund)){
			$this->errors[] = Tools::displayError($this->l('The partial amount should be less than the outstanding amount'));
			
		}else{

			$amt = $amt_refund;

			$paytpv_date = date("Ymd",strtotime($paytpv_order['date']));
			$paytpv_iduser = $paytpv_order["paytpv_iduser"];
			$paytpv_tokenuser = $paytpv_order["paytpv_tokenuser"];

			$id_currency = $order->id_currency;
			$currency = new Currency(intval($id_currency));

			$orderPayment = $order->getOrderPaymentCollection()->getFirst();
			$authcode = $orderPayment->transaction_id;

			$amount = number_format($amt * 100, 0, '.', '');

			$paytpv_order_ref = str_pad((int)$order->id_cart, 8, "0", STR_PAD_LEFT);

			$response = $this->_makeRefund($paytpv_iduser,$paytpv_tokenuser,$order->id,$paytpv_order_ref,$paytpv_date,$currency->iso_code,$authcode,$amount,1);
			$refund_txt = $response["txt"];
			$message = $this->l('PayTPV Refund ').  ", " . $amt . " " . $currency->sign . " [" . $refund_txt . "]" .  '<br>';
			
			$this->_addNewPrivateMessage((int)$id_order, $message);

			Tools::redirect($_SERVER['HTTP_REFERER']);
		}
	}

	private function _doTotalRefund($id_order)
	{

		$paytpv_order = Paytpv_Order::get_Order((int)$id_order);
		if (empty($paytpv_order)){
			return false;
		}

		$order = new Order((int)$id_order);
		if (!Validate::isLoadedObject($order))
			return false;

		$products = $order->getProducts();
		$currency = new Currency((int)$order->id_currency);
		if (!Validate::isLoadedObject($currency))
			$this->_errors[] = $this->l('Invalid Currency');

		if (count($this->_errors))
			return false;

		$decimals = (is_array($currency) ? (int)$currency['decimals'] : (int)$currency->decimals) * _PS_PRICE_DISPLAY_PRECISION_;

		$total_amount = $order->total_paid;

		$total_pending = $total_amount - Paytpv_Refund::get_TotalRefund($order->id);
		$total_pending =  number_format($total_pending, 2, '.', '');

		$paytpv_date = date("Ymd",strtotime($paytpv_order['date']));
		$paytpv_iduser = $paytpv_order["paytpv_iduser"];
		$paytpv_tokenuser = $paytpv_order["paytpv_tokenuser"];

		$id_currency = $order->id_currency;
		$currency = new Currency(intval($id_currency));

		$orderPayment = $order->getOrderPaymentCollection()->getFirst();
		$authcode = $orderPayment->transaction_id;

		$products = $order->getProducts();
		$cancel_quantity = Tools::getValue('cancelQuantity');

		$amount = number_format($total_pending * 100, 0, '.', '');

		$paytpv_order_ref = str_pad((int)$order->id_cart, 8, "0", STR_PAD_LEFT);

		$response = $this->_makeRefund($paytpv_iduser,$paytpv_tokenuser,$order->id,$paytpv_order_ref,$paytpv_date,$currency->iso_code,$authcode,$amount,0);
		$refund_txt = $response["txt"];
		$message = $this->l('PayTPV Total Refund ').  ", " . $total_pending . " " . $currency->sign . " [" . $refund_txt . "]" .  '<br>';
		if ($response['error'] == 0)
		{
			if (!Paytpv_Order::set_Order_Refunded($id_order))
				die(Tools::displayError('Error when updating PayTPV database'));

			$history = new OrderHistory();
			$history->id_order = (int)$id_order;
			$history->changeIdOrderState((int)Configuration::get('PS_OS_REFUND'), $history->id_order);
			$history->addWithemail();
			$history->save();
		}

		$this->_addNewPrivateMessage((int)$id_order, $message);

		Tools::redirect($_SERVER['HTTP_REFERER']);
	}


	private function _canRefund($id_order)
	{
		if (!(bool)$id_order)
			return false;

		$paytpv_order = Paytpv_Order::get_Order((int)$id_order);

		return $paytpv_order;//&& $paytpv_order['payment_status'] != 'Refunded';
	}
}

?>
