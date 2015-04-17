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

if (!defined('_PS_VERSION_'))
	exit;

include_once(_PS_MODULE_DIR_.'/paytpv/class_registro.php');

class Paytpv extends PaymentModule {

	private $_html = '';

	private $_postErrors = array();
	
	public function __construct() {

		$this->name = 'paytpv';
		$this->tab = 'payments_gateways';
		$this->author = 'PayTPV';
		$this->version = '6.0.5';
		// Array config:  configuration values
		$config = $this->getConfigValues();
		
		// Establishing properties from configuraction data
		if (isset($config['PAYTPV_USERCODE']))
			$this->usercode = $config['PAYTPV_USERCODE'];
		if (isset($config['PAYTPV_PASS']))
			$this->pass = $config['PAYTPV_PASS'];
		if (isset($config['PAYTPV_TERM']))
			$this->term = $config['PAYTPV_TERM'];
		if (isset($config['PAYTPV_CLIENTCODE']))
			$this->clientcode = $config['PAYTPV_CLIENTCODE'];
		if (isset($config['PAYTPV_OPERATIVA']))
			$this->operativa = $config['PAYTPV_OPERATIVA'];
		if (isset($config['PAYTPV_3DFIRST']))
			$this->tdfirst = $config['PAYTPV_3DFIRST'];
		if (isset($config['PAYTPV_3DMIN']))
			$this->tdmin = $config['PAYTPV_3DMIN'];
		else
			$this->tdmin = "";
		if (isset($config['PAYTPV_COMMERCEPASSWORD']))
			$this->commerce_password = $config['PAYTPV_COMMERCEPASSWORD'];
		if (isset($config['PAYTPV_TERMINALES']))
			$this->terminales = $config['PAYTPV_TERMINALES'];
		if (isset($config['PAYTPV_IFRAME']))
			$this->iframe = $config['PAYTPV_IFRAME'];
		if (isset($config['PAYTPV_SUSCRIPTIONS']))
			$this->suscriptions = $config['PAYTPV_SUSCRIPTIONS'];		
		if (isset($config['PAYTPV_REG_ESTADO']))
			$this->reg_estado = $config['PAYTPV_REG_ESTADO'];

		parent::__construct();
		$this->page = basename(__FILE__, '.php');

		$this->displayName = $this->l('paytpv.com');

		$this->description = $this->l('This module allows you to accept card payments via paytpv.com');
		
		// Show message in module configuration page when missing data.
		if (!isset($this->pass)
				OR !isset($this->usercode)
				OR !isset($this->clientcode)
				OR !isset($this->term)
				OR !isset($this->pass))
			$this->warning = $this->l('Missing data when configuring the module Paytpv');

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
		return true;
	}
	public function uninstall() {
		include_once(_PS_MODULE_DIR_.'/'.$this->name.'/paytpv_install.php');
		$paypal_install = new PayTpvInstall();
		$paypal_install->deleteConfiguration();
		return parent::uninstall();
	}


	private function _postValidation(){

	    // Show error when required fields.
		if (isset($_POST['btnSubmit']))
		{
			if ($_POST['operativa']==1 && empty($_POST['usercode']))
				$this->_postErrors[] = $this->l('The user code is required paytpv.com.');
			if (empty($_POST['pass']))
				$this->_postErrors[] = $this->l('Password is required paytpv.com product.');
			if (empty($_POST['term']))
				$this->_postErrors[] = $this->l('Terminal number paytpv.com required product.');
			if (empty($_POST['clientcode']))
				$this->_postErrors[] = $this->l('The client code is required paytpv.com.');
		}

	}
	private function _postProcess(){

	    // Update databse configuration
		if (isset($_POST['btnSubmit'])){
			Configuration::updateValue('PAYTPV_TERM', $_POST['term']);
			Configuration::updateValue('PAYTPV_PASS', $_POST['pass']);
			Configuration::updateValue('PAYTPV_USERCODE', $_POST['usercode']);
			Configuration::updateValue('PAYTPV_CLIENTCODE', $_POST['clientcode']);
			Configuration::updateValue('PAYTPV_OPERATIVA', $_POST['operativa']);
			Configuration::updateValue('PAYTPV_3DFIRST', $_POST['tdfirst']);
			Configuration::updateValue('PAYTPV_3DMIN', $_POST['tdmin']);
			Configuration::updateValue('PAYTPV_COMMERCEPASSWORD', $_POST['commerce_password']);
			Configuration::updateValue('PAYTPV_IFRAME', $_POST['iframe']); 
			Configuration::updateValue('PAYTPV_TERMINALES', $_POST['terminales']); 
			Configuration::updateValue('PAYTPV_SUSCRIPTIONS', $_POST['suscriptions']); 
			return '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('ok').'" /> '.$this->l('Updated configuration').'</div>';          
		}

	}
	public function getContent() {

		$errorMessage = '';
		if (!empty($_POST)) {
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$errorMessage = $this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$errorMessage .= '<div class="alert error">' . $err . '</div>';
		}
		else
			$errorMessage = '<br />';

		$conf_values = $this->getConfigValues();

		if (Tools::isSubmit('id_cart'))
			$this->validateOrder($_GET['id_cart'], _PS_OS_PAYMENT_, $_GET['amount'], $this->displayName, NULL);

		if (Tools::isSubmit('id_registro'))
			class_registro::remove($_GET['id_registro']);
		
		$carritos = class_registro::select();

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
		$this->context->smarty->assign('usercode', $conf_values['PAYTPV_USERCODE']);
		$this->context->smarty->assign('clientcode', $conf_values['PAYTPV_CLIENTCODE']);
		$this->context->smarty->assign('term', $conf_values['PAYTPV_TERM']);
		$this->context->smarty->assign('pass', $conf_values['PAYTPV_PASS']);
		$this->context->smarty->assign('iframe', $conf_values['PAYTPV_IFRAME']);
		$this->context->smarty->assign('operativa', $conf_values['PAYTPV_OPERATIVA']);
		$this->context->smarty->assign('tdfirst', $conf_values['PAYTPV_3DFIRST']);
		$this->context->smarty->assign('tdmin', $conf_values['PAYTPV_3DMIN']);
		$this->context->smarty->assign('commerce_password', $conf_values['PAYTPV_COMMERCEPASSWORD']);
		$this->context->smarty->assign('terminales', $conf_values['PAYTPV_TERMINALES']);
		$this->context->smarty->assign('suscriptions', $conf_values['PAYTPV_SUSCRIPTIONS']);
		$this->context->smarty->assign('OK',Context::getContext()->link->getModuleLink($this->name, 'urlok',array(),$ssl));
		$this->context->smarty->assign('KO',Context::getContext()->link->getModuleLink($this->name, 'urlko',array(),$ssl));
		$this->context->smarty->assign('NOTIFICACION',Context::getContext()->link->getModuleLink($this->name, 'url',array(),$ssl));
		$this->context->smarty->assign('base_dir', __PS_BASE_URI__);

		$this->context->controller->addCSS( $this->_path . 'css/admin.css' , 'all' );
		return $this->display(__FILE__, 'views/admin.tpl');

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

		// Eliminar Tarjeta

		$this->context->smarty->assign('msg_paytpv',"");
		$showcard = false;
		$msg_paytpv = "";

		$this->context->smarty->assign('msg_paytpv',$msg_paytpv);
		$this->context->smarty->assign('showcard',$showcard);

	    // Valor de compra				
		$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));

		$currency = new Currency(intval($id_currency));		
		$importe = number_format(Tools::convertPrice($params['cart']->getOrderTotal(true, 3), $currency)*100, 0, '.', '');		

		$paytpv_order_ref = str_pad($params['cart']->id, 8, "0", STR_PAD_LEFT);
		$ssl = Configuration::get('PS_SSL_ENABLED');
		$values = array(
			'id_cart' => (int)$params['cart']->id,
			'key' => Context::getContext()->customer->secure_key
		);
		$products = $params['cart']->getProducts();

		$paytpv_clientconcept = '';
		foreach ($products as $product) {
			$paytpv_clientconcept .= $product['quantity'].' '.$product['name']."<br>";
		}

		$tmpl_vars = array();
		
		$URLOK=$URLKO=Context::getContext()->link->getModuleLink($this->name, 'url',$values,$ssl);
		

		$suscripcion = (isset($_POST["paytpv_suscripcion"]))?$_POST["paytpv_suscripcion"]:0;

		$active_suscriptions = intval(Configuration::get('PAYTPV_SUSCRIPTIONS'));

		if($this->operativa==0){ // BANKSTORE		

			$saved_card = $this->getToken();
			$index = 0;
			foreach ($saved_card as $key=>$val){
				$values_aux = array_merge($values,array("TOKEN_USER"=>$val["TOKEN_USER"]));
				$saved_card[$key]['url'] = Context::getContext()->link->getModuleLink($this->name, 'capture',$values_aux,$ssl);	
				$index++;
			}
			$saved_card[$index]['url'] = 0;

			$tmpl_vars['capture_url'] = Context::getContext()->link->getModuleLink($this->name, 'capture',$values,$ssl);
			$this->context->smarty->assign('active_suscriptions',$active_suscriptions);
			$this->context->smarty->assign('saved_card',$saved_card);
			$this->context->smarty->assign('commerce_password',$this->commerce_password);
			$this->context->smarty->assign('id_cart',$params['cart']->id);
			
			$this->context->smarty->assign('base_dir', __PS_BASE_URI__);

		}else{  // TPV WEB
			$OPERATION = 1;
			$signature = md5($this->clientcode.$this->usercode.$this->term.$OPERATION.$paytpv_order_ref.$importe.$currency->iso_code.md5($this->pass));
			$fields = array(
				"ACCOUNT" => $this->clientcode,
				"USERCODE" => $this->usercode,
				"TERMINAL" => $this->term,
				"OPERATION" => $OPERATION,
				"REFERENCE" => $paytpv_order_ref,
				"AMOUNT" => $importe,
				"CURRENCY" => $currency->iso_code,
				"SIGNATURE" => $signature,
				"CONCEPT" => $paytpv_clientconcept.", ".($this->context->customer->isLogged() ? $cookie->customer_firstname.' '.$cookie->customer_lastname : ""),
				'URLOK' => $URLOK,
				'URLKO' => $URLKO
			);
		}

		$tmpl_vars = array_merge(
			array(
			'this_path' => $this->_path)
		);
		$this->context->smarty->assign($tmpl_vars);
		

		switch ($this->operativa){
			// BANKSTORE
			case 0:
			 	$arrAddCard = array("process"=>"addCard");
			    $arrSubscribe = array("process"=>"suscribe");
			 	$this->context->smarty->assign('addcard_url',Context::getContext()->link->getModuleLink('paytpv', 'actions', $arrAddCard, true));
			 	$this->context->smarty->assign('subscribe_url',Context::getContext()->link->getModuleLink('paytpv', 'actions', $arrSubscribe, true));
				return $this->display(__FILE__, 'payment_bsiframe.tpl');
				break;
			// TPV-WEB

			case 1:
				if($this->iframe==1)
					return $this->display(__FILE__, 'payment_iframe.tpl');
				else
					return $this->display(__FILE__, 'payment.tpl');
				break;
		}

	}

	public function isSecureTransaction($importe,$card){
        $op = $this->operativa;
        $terminales = $this->terminales;
        // Transaccion Segura:
        // Si es TPVWEB
        // Si es Bankstore y solo tiene Terminal Seguro
        if (1 == $op || (0 == $op && $terminales==0))
            return true;   
   

        // Si esta definido que el pago es 3d secure y no estamos usando una tarjeta tokenizada
        if ($this->tdfirst && $card==0)
            return true;

        // Si se supera el importe maximo para compra segura
        if ($terminales==2 && ($this->tdmin!="" && $this->tdmin < $importe))
            return true;

         // Si esta definido como que la primera compra es Segura y es la primera compra aunque este tokenizada
        if ($terminales==2 && $this->tdfirst && $card>0 && $this->isFirstPurchaseToken($card))
            return true;

        
        
        return false;
    }

    function isFirstPurchaseToken($IDUSER)
    {
        $sql = 'select * from ' . _DB_PREFIX_ .'paytpv_order where id_customer='.(int)$this->context->customer->id. ' and paytpv_iduser='.pSQL($IDUSER);
		$result = Db::getInstance()->getRow($sql);
		if (empty($result) === true){
        	return true;
        }
        return false;
    }

	public function isSecurePay($importe){
		// Terminal NO Seguro
		if ($this->terminales==1)
			return false;
		// Ambos Terminales, Usar 3D False e Importe < Importe Min 3d secure
		if ($this->terminales==2 && $this->tdfirst==0 && ($this->tdmin=="" || $importe<=$this->tdmin))
			return false;
		return true;
	}


	public function getToken(){

		$res = array();
		$sql = 'SELECT paytpv_iduser,paytpv_tokenuser,paytpv_cc,paytpv_brand,card_desc FROM '._DB_PREFIX_.'paytpv_customer WHERE not paytpv_cc="" and id_customer = '.(int)$this->context->customer->id . ' order by date desc';

		$assoc = Db::getInstance()->executeS($sql);

		foreach ($assoc as $key=>$row) {

			$res[$key]['IDUSER']= $row['paytpv_iduser'];
			$res[$key]['TOKEN_USER']= $row['paytpv_tokenuser'];
			$res[$key]['CC'] = $row['paytpv_cc'];
			$res[$key]['BRAND'] = $row['paytpv_brand'];
			$res[$key]['CARD_DESC'] = $row['card_desc'];
		}

		return  $res;

	}

	public function getDataToken($token){

		$res = array();

		$sql = 'SELECT paytpv_iduser,paytpv_tokenuser,paytpv_cc FROM '._DB_PREFIX_.'paytpv_customer WHERE paytpv_tokenuser="'.pSQL($token).'"';

		$assoc = Db::getInstance()->executeS($sql);

		foreach ($assoc as $key=>$row) {
			$res['IDUSER']= $row['paytpv_iduser'];
			$res['TOKEN_USER']= $row['paytpv_tokenuser'];
			$res['CC'] = $row['paytpv_cc'];
		}

		return  $res;

	}

	public function getDataTokenPaytpv_iduser($paytpv_iduser){

		$sql = 'SELECT paytpv_iduser,paytpv_tokenuser,paytpv_cc FROM '._DB_PREFIX_.'paytpv_customer WHERE paytpv_iduser="'.pSQL($paytpv_iduser).'"';
		$result = Db::getInstance()->getRow($sql);
		return $result;
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

		
		$result = $this->getSuscriptionOrder($id_order);
		if ($order->module == $this->name && !empty($result)){
			
			$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
			$currency = new Currency(intval($id_currency));

			$suscription_type = $this->l('This is a Subscription');
			
			$id_suscription = $result["id_suscription"];
			$id_customer = $result["id_customer"];
			$periodicity = $result["periodicity"];
			$cycles = ($result['cycles']!=0)?$result['cycles']:$this->l('N');
			$status = $result["status"];
			$date = $result["date"];
			$price = number_format(Tools::convertPrice($result['price'], $currency), 2, '.', '') . " " . $currency->sign;	
			$num_pagos = $result['pagos'];

			if ($status==0)
				$status = $this->l('ACTIVE');
			else if ($status==1)
				$status = $this->l('CANCELED');
			else if ($num_pagos==$result['cycles'] && $result['cycles']>0)	
				$status = $this->l('FINISHED');
                               
			

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
		return Configuration::getMultiple(array('PAYTPV_USERCODE', 'PAYTPV_PASS', 'PAYTPV_TERM', 'PAYTPV_CLIENTCODE', 'PAYTPV_OPERATIVA', 'PAYTPV_3DFIRST', 'PAYTPV_3DMIN', 'PAYTPV_COMMERCEPASSWORD', 'PAYTPV_TERMINALES', 'PAYTPV_IFRAME','PAYTPV_SUSCRIPTIONS','PAYTPV_REG_ESTADO'));
	}
	
	public function saveCard($id_customer,$paytpv_iduser,$paytpv_tokenuser,$paytpv_cc,$paytpv_brand){

		$paytpv_cc = '************' . substr($paytpv_cc, -4);

		$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_customer (`paytpv_iduser`, `paytpv_tokenuser`, `paytpv_cc`,`paytpv_brand`,`id_customer`,`date`) VALUES('.pSQL($paytpv_iduser).',"'.pSQL($paytpv_tokenuser).'","'.pSQL($paytpv_cc).'","'.pSQL($paytpv_brand).'",'.pSQL($id_customer).',"'.pSQL(date('Y-m-d H:i:s')).'")';
		Db::getInstance()->Execute($sql);
		
	}

	public function savePayTpvOrder($paytpv_iduser,$paytpv_tokenuser,$id_suscription,$id_customer,$id_order,$price){

		/*
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_order where id_order='.pSQL($id_order). ' and paytpv_iduser='.pSQL($paytpv_iduser);
		$result = Db::getInstance()->getRow($sql);
		if (empty($result) === true){
			$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_order (`paytpv_iduser`,`paytpv_tokenuser`,`id_suscription`, `id_customer`, `id_order`,`price`,`date`) VALUES('.pSQL($paytpv_iduser).',"'.pSQL($paytpv_tokenuser).'",'.pSQL($id_suscription).','.pSQL($id_customer).','.pSQL($id_order).',"'.pSQL($price).'","'.pSQL(date('Y-m-d H:i:s')).'")';
			Db::getInstance()->Execute($sql);
		}
		*/
		
		$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_order (`paytpv_iduser`,`paytpv_tokenuser`,`id_suscription`, `id_customer`, `id_order`,`price`,`date`) VALUES('.pSQL($paytpv_iduser).',"'.pSQL($paytpv_tokenuser).'",'.pSQL($id_suscription).','.pSQL($id_customer).','.pSQL($id_order).',"'.pSQL($price).'","'.pSQL(date('Y-m-d H:i:s')).'")';
		Db::getInstance()->Execute($sql);
	}


	public function saveSuscription($id_customer,$id_order,$paytpv_iduser,$paytpv_tokenuser,$periodicity,$cycles,$importe){
		// Datos usuario
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_suscription where id_customer = ' . pSQL($id_customer) .' AND id_order="'.pSQL($id_order).'"';	
		$result = Db::getInstance()->getRow($sql);

		// Si no existe la suscripcion la creamos
		if (empty($result) === true){
			$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_suscription(`id_customer`, `id_order`, `paytpv_iduser`,`paytpv_tokenuser`,`periodicity`,`cycles`,`price`,`date`) VALUES('.pSQL($id_customer).','.pSQL($id_order).','.pSQL($paytpv_iduser).',"'.pSQL($paytpv_tokenuser).'",'.pSQL($periodicity).','.pSQL($cycles).','.pSQL($importe).',"'.pSQL(date('Y-m-d H:i:s')).'")';
			Db::getInstance()->Execute($sql);
		}
	}

	public function subcriptionFromOrder($id_customer,$id_order){
		// Datos usuario
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_suscription where id_customer = ' . pSQL($id_customer) .' AND id_order="'. pSQL($id_order).'"';	
		$result = Db::getInstance()->getRow($sql);
		return $result;
	}


	public function getSuscriptionOrder($id_order){
		// Check if is a subscription order
		$sql = 'select ps.*,count(po.id_order) as pagos,1 as suscription FROM '._DB_PREFIX_.'paytpv_suscription ps
LEFT OUTER JOIN '._DB_PREFIX_.'paytpv_order po on ps.id_suscription = po.id_suscription and po.id_order!='. pSQL($id_order) . '
where ps.id_order = '. pSQL($id_order). ' group by ps.id_suscription order by ps.date desc';
		$result = Db::getInstance()->getRow($sql);

		if (empty($result)){
			// Check if is a suscription payment
			$sql = 'select ps.*,count(po.id_order) as pagos, 0 as suscription FROM '._DB_PREFIX_.'paytpv_suscription ps
LEFT OUTER JOIN '._DB_PREFIX_.'paytpv_order po on ps.id_suscription = po.id_suscription 
where po.id_order = '. pSQL($id_order). ' group by ps.id_suscription order by ps.date desc';
			$result = Db::getInstance()->getRow($sql);

		}
		return $result;
	}
	
	
	/* Obtener las suscripciones del usuario */
	public function getSuscriptions(){

		$res = array();
		$sql = 'select ps.*,count(po.id_order) as pagos FROM '._DB_PREFIX_.'paytpv_suscription ps
LEFT OUTER JOIN '._DB_PREFIX_.'paytpv_order po on ps.id_suscription = po.id_suscription and ps.id_order!=po.id_order
where ps.id_customer = '.(int)$this->context->customer->id . ' group by ps.id_suscription order by ps.date desc';
		
		$assoc = Db::getInstance()->executeS($sql);

		$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
		$currency = new Currency(intval($id_currency));

		foreach ($assoc as $key=>$row) {
			$res[$key]['ID_SUSCRIPTION']= $row['id_suscription'];
			$res[$key]['SUSCRIPTION_PAY'] = $this->getSuscriptionPay($row['id_suscription']);
			$order = new Order($row['id_order']);
			$res[$key]['ORDER_REFERENCE']= $order->reference;
			$res[$key]['ID_ORDER']= $row['id_order'];
			$res[$key]['PERIODICITY'] = $row['periodicity'];
			$res[$key]['CYCLES'] = ($row['cycles']!=0)?$row['cycles']:$this->l('Permanent');
			$res[$key]['PRICE'] = number_format(Tools::convertPrice($row['price'], $currency), 2, '.', '');		
			$res[$key]['DATE'] = $row['date'];
			$res[$key]['DATE_YYYYMMDD'] = ($this->context->language->iso_code=="es")?date("d-m-Y",strtotime($row['date'])):date("Y-m-d",strtotime($row['date']));

			$num_pagos = $row['pagos'];
			
			$status = $row['status'];
			if ($row['status']==1)
				$status = $row['status'];  // CANCELADA
			else if ($num_pagos==$row['cycles'] && $row['cycles']>0)	
				$status = 2; // FINALIZADO
							

			$res[$key]['STATUS'] = $status;
		}
		
		return  $res;
	}

	
	/* Obtener los pagos de una suscripcion */
	public function getSuscriptionPay($id_suscription){
		
		$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
		$currency = new Currency(intval($id_currency));

		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_order where id_suscription = ' . pSQL($id_suscription) . ' LIMIT 1,100';	
		$assoc = Db::getInstance()->executeS($sql);
		$res = array();	
		foreach ($assoc as $key=>$row) {
			$res[$key]["ID"] = $row["id"];
			$order = new Order($row['id_order']);
			$res[$key]['ID_ORDER']= $row['id_order'];
			$res[$key]['ORDER_REFERENCE']= $order->reference;
			$res[$key]["PRICE"] = number_format(Tools::convertPrice($row['price'], $currency), 2, '.', '');	
			$res[$key]['DATE'] = $row['date'];
			$res[$key]['DATE_YYYYMMDD'] = ($this->context->language->iso_code=="es")?date("d-m-Y",strtotime($row['date'])):date("Y-m-d",strtotime($row['date']));
		}

		return $res;

	}

	public function remove_user($paytpv_iduser,$paytpv_tokenuser){
		$client = new WS_Client(
			array(
				'clientcode' => $this->clientcode,
				'term' => $this->term,
				'pass' => $this->pass,
			)
		);

		$result = $client->remove_user( $paytpv_iduser, $paytpv_tokenuser);
		return $result;
	}


	public function removeCard($paytpv_iduser){
		include_once(_PS_MODULE_DIR_.'/paytpv/ws_client.php');

		$client = new WS_Client(
			array(
				'clientcode' => $this->clientcode,
				'term' => $this->term,
				'pass' => $this->pass,
			)
		);
		// Datos usuario

		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_customer where id_customer = '.(int)$this->context->customer->id . ' and paytpv_iduser="'.pSQL($paytpv_iduser) .'"';
		$result = Db::getInstance()->getRow($sql);
		if (empty($result) === true){
			return false;
		}else{
			$paytpv_iduser = $result["paytpv_iduser"];
			$paytpv_tokenuser = $result["paytpv_tokenuser"];

			$result = $client->remove_user( $paytpv_iduser, $paytpv_tokenuser);
			
			$sql = 'DELETE FROM '. _DB_PREFIX_ .'paytpv_customer where id_customer = '.(int)$this->context->customer->id . ' and `paytpv_iduser`="'.pSQL($paytpv_iduser).'"';
			Db::getInstance()->Execute($sql);
			return true;
		}
	}

	public function saveDescriptionCard($paytpv_iduser,$card_desc){
		$sql = 'UPDATE '. _DB_PREFIX_ .'paytpv_customer set card_desc = "' .pSQL($card_desc) .'" where id_customer = '.(int)$this->context->customer->id . ' and `paytpv_iduser`="'.pSQL($paytpv_iduser).'"';
		Db::getInstance()->Execute($sql);
		return true;
	}



	public function removeSuscription($id_suscription){
		include_once(_PS_MODULE_DIR_.'/paytpv/ws_client.php');

		$client = new WS_Client(
			array(
				'clientcode' => $this->clientcode,
				'term' => $this->term,
				'pass' => $this->pass,
			)
		);
		// Datos usuario

		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_suscription where id_customer = '.(int)$this->context->customer->id . ' and id_suscription = '.pSQL($id_suscription);
		$result = Db::getInstance()->getRow($sql);
		if (empty($result) === true){
			return false;
		}else{
			$paytpv_iduser = $result["paytpv_iduser"];
			$paytpv_tokenuser = $result["paytpv_tokenuser"];

			$result = $client->remove_subscription( $paytpv_iduser, $paytpv_tokenuser);
			if ( ( int ) $result[ 'DS_RESPONSE' ] == 1 ) {
				$sql = 'DELETE FROM '. _DB_PREFIX_ .'paytpv_suscription where id_customer = '.(int)$this->context->customer->id . ' and id_suscription = '.pSQL($id_suscription);
				Db::getInstance()->Execute($sql);
				return true;
			}
			return false;
		}
	}

	public function cancelSuscription($id_suscription){
		include_once(_PS_MODULE_DIR_.'/paytpv/ws_client.php');

		$client = new WS_Client(
			array(
				'clientcode' => $this->clientcode,
				'term' => $this->term,
				'pass' => $this->pass,
			)
		);
		// Datos usuario

		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_suscription where id_customer = '.(int)$this->context->customer->id . ' and id_suscription = '.pSQL($id_suscription);
		$result = Db::getInstance()->getRow($sql);
		if (empty($result) === true){
			return false;
		}else{
			$paytpv_iduser = $result["paytpv_iduser"];
			$paytpv_tokenuser = $result["paytpv_tokenuser"];

			$result = $client->remove_subscription( $paytpv_iduser, $paytpv_tokenuser);
			if ( ( int ) $result[ 'DS_RESPONSE' ] == 1 ) {
				$sql = 'UPDATE '. _DB_PREFIX_ .'paytpv_suscription set status=1 where id_customer = '.(int)$this->context->customer->id . ' and id_suscription = '.pSQL($id_suscription);
				Db::getInstance()->Execute($sql);
				return true;
			}
			return false;
		}
	}


	public function save_paytpv_order_info($id_customer,$id_cart,$paytpvagree,$suscription,$peridicity,$cycles,$paytpv_iduser){
		// Eliminamos la orden si existe.
		$sql = 'DELETE FROM '. _DB_PREFIX_ .'paytpv_order_info where id_customer = '.pSQL($id_customer) .' and id_cart= "'. pSQL($id_cart) .'"';
		Db::getInstance()->Execute($sql);

		// Insertamos los datos de la orden
		$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_order_info (`id_customer`,`id_cart`,`paytpvagree`,`suscription`,`periodicity`,`cycles`,`date`,`paytpv_iduser`) VALUES('.pSQL($id_customer).',"'.pSQL($id_cart).'",'.pSQL($paytpvagree).','.pSQL($suscription).','.pSQL($peridicity).','.pSQL($cycles).',"'.pSQL(date('Y-m-d H:i:s')).'",'.pSQL($paytpv_iduser).')';
		Db::getInstance()->Execute($sql);
		
		return true;

	}

	public function get_paytpv_order($id_order){
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_order where id_order="'.pSQL($id_order).'"';
		$result = Db::getInstance()->getRow($sql);
		return $result;
	}


	public function get_paytpv_order_info($id_customer,$id_cart){
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_order_info where id_customer = '.pSQL($id_customer) . ' and id_cart="'.pSQL($id_cart).'"';
		$result = Db::getInstance()->getRow($sql);
		return $result;
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

		$paytpv_order = $this->get_paytpv_order((int)$order->id);
		if (empty($paytpv_order)){
			die('error');
			return false;
		}


		$paytpv_iduser = $paytpv_order["paytpv_iduser"];
		$paytpv_tokenuser = $paytpv_order["paytpv_tokenuser"];

		$id_currency = $order->id_currency;
		$currency = new Currency(intval($id_currency));

		$orderPayment = $order->getOrderPaymentCollection()->getFirst();
		$authcode = $orderPayment->transaction_id;

		$products = $order->getProducts();
		$cancel_quantity = Tools::getValue('cancelQuantity');

		$amount = (float)($products[(int)$order_detail->id]['product_price_wt'] * (int)$cancel_quantity[(int)$order_detail->id]);
		$amount = number_format($amount * 100, 0, '.', '');

		$paytpv_order_ref = str_pad((int)$order->id_cart, 8, "0", STR_PAD_LEFT);

		$response = $this->_makeRefund($paytpv_iduser,$paytpv_tokenuser,$paytpv_order_ref,$currency->iso_code,$authcode,$amount);
		$refund_txt = $response["txt"];

		$message = $this->l('PayTPV Refund ').  "[" . $refund_txt . "]" .  '<br>';
		$this->_addNewPrivateMessage((int)$order->id, $message);

	}

	private function _makeRefund($paytpv_iduser,$paytpv_tokenuser,$paytpv_order_ref,$currency,$authcode,$amount){
		// Refund amount
		include_once(_PS_MODULE_DIR_.'/paytpv/ws_client.php');
		$client = new WS_Client(
			array(
				'clientcode' => $this->clientcode,
				'term' => $this->term,
				'pass' => $this->pass,
			)
		);
		
		// Refund amount of transaction
		$result = $client->execute_refund($paytpv_iduser, $paytpv_tokenuser, $paytpv_order_ref, $currency, $authcode, $amount);
		$refund_txt = $this->l('OK');
		$response["error"] = 0;
		$response["txt"] = $this->l('OK');
		if ( ( int ) $result[ 'DS_RESPONSE' ] != 1 ) {
			$response["txt"] = $this->l('ERROR') . " " . $result[ 'DS_ERROR_ID'];
			$response["error"] = 1;
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
			$message = $this->l('Payment message is not valid, please check your module.');

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

		$order = new Order((int)$params['id_order']);
		$result = $this->getSuscriptionOrder($params["id_order"]);
		if ($order->module == $this->name && !empty($result)){
			$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
			$currency = new Currency(intval($id_currency));

			$suscription = $result["suscription"];
			if ($suscription==1){
				$suscription_type = $this->l('This is a Subscription');
			}else{
				$suscription_type = $this->l('This is a Subscription Payment');
			}
			$id_suscription = $result["id_suscription"];
			$id_customer = $result["id_customer"];
			$periodicity = $result["periodicity"];
			$cycles = ($result['cycles']!=0)?$result['cycles']:$this->l('N');
			$status = $result["status"];
			$date = $result["date"];
			$price = number_format(Tools::convertPrice($result['price'], $currency), 2, '.', '');	
			$num_pagos = $result['pagos'];

			if ($status==0)
				$status = $this->l('ACTIVE');
			else if ($status==1)
				$status = $this->l('CANCELED');
			else if ($num_pagos==$result['cycles'] && $result['cycles']>0)	
				$status = $this->l('FINISHED');
                               
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
		if ($order->module == $this->name && $this->_canRefund((int)$params['id_order'])){

			if (version_compare(_PS_VERSION_, '1.5', '>='))
					$order_state = $order->current_state;
				else
					$order_state = OrderHistory::getLastOrderState($order->id);

			$products = $order->getProducts();
			$amt = 0.00;
			foreach ($products as $product)
				$amt += (float)($product['product_price_wt']) * ($product['product_quantity'] - $product['product_quantity_refunded']);
			$amt += (float)($order->total_shipping) + (float)($order->total_wrapping) - (float)($order->total_discounts);

			$currency = new Currency((int)$order->id_currency);

			$amt .= " " . $currency->sign;

			$this->context->smarty->assign(
					array(
						'base_url' => _PS_BASE_URL_.__PS_BASE_URI__,
						'module_name' => $this->name,
						'order_state' => $order_state,
						'params' => $params,
						'amount' => $amt,
						'ps_version' => _PS_VERSION_
					)
				);



			$template_refund = 'views/templates/admin/admin_order/refund.tpl';
			$this->_html .=  $this->display(__FILE__, $template_refund);
			$this->_postProcess();
		}

		return $this->_html;	
	}


	private function _doTotalRefund($id_order)
	{

		$paytpv_order = $this->get_paytpv_order((int)$id_order);
		if (empty($paytpv_order)){
			return false;
		}

		$order = new Order((int)$id_order);
		if (!Validate::isLoadedObject($order))
			return false;

		$products = $order->getProducts();
		$currency = new Currency((int)$order->id_currency);
		if (!Validate::isLoadedObject($currency))
			$this->_errors[] = $this->l('Not a valid currency');

		if (count($this->_errors))
			return false;

		$decimals = (is_array($currency) ? (int)$currency['decimals'] : (int)$currency->decimals) * _PS_PRICE_DISPLAY_PRECISION_;

		// Amount for refund
		$amt = 0.00;

		foreach ($products as $product)
			$amt += (float)($product['product_price_wt']) * ($product['product_quantity'] - $product['product_quantity_refunded']);
		$amt += (float)($order->total_shipping) + (float)($order->total_wrapping) - (float)($order->total_discounts);


		$paytpv_iduser = $paytpv_order["paytpv_iduser"];
		$paytpv_tokenuser = $paytpv_order["paytpv_tokenuser"];

		$id_currency = $order->id_currency;
		$currency = new Currency(intval($id_currency));

		$orderPayment = $order->getOrderPaymentCollection()->getFirst();
		$authcode = $orderPayment->transaction_id;

		$products = $order->getProducts();
		$cancel_quantity = Tools::getValue('cancelQuantity');

		$amount = number_format($amt * 100, 0, '.', '');

		$paytpv_order_ref = str_pad((int)$order->id_cart, 8, "0", STR_PAD_LEFT);

		$response = $this->_makeRefund($paytpv_iduser,$paytpv_tokenuser,$paytpv_order_ref,$currency->iso_code,$authcode,$amount);
		$refund_txt = $response["txt"];
		$message = $this->l('PayTPV Total Refund ').  "[" . $refund_txt . "]" .  '<br>';
		if ($response['error'] == 0)
		{
			if (!Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'paytpv_order` SET `payment_status` = \'Refunded\' WHERE `id_order` = '.(int)$id_order))
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

		$paytpv_order = Db::getInstance()->getRow('
			SELECT *
			FROM `'._DB_PREFIX_.'paytpv_order`
			WHERE `id_order` = '.(int)$id_order);

		return $paytpv_order && $paytpv_order['payment_status'] != 'Refunded';
	}
}

?>