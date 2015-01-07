<?php

/**
* www.paytpv.com Module Copyright (c) 2014 PayTPV Software
*  
*  info@paytpv.com -- http://paytpv.com/
*
*  Released under the GNU General Public License
*
*  Autor: Jose Ramón Garcia (jrgarcia@paytpv.com)
**/

if (!defined('_PS_VERSION_'))

	exit;
include_once(_PS_MODULE_DIR_.'/paytpv/class_registro.php');

class Paytpv extends PaymentModule {

	const INSTALL_SQL_FILE = 'install.sql';
	private $_html = '';

	private $_postErrors = array();
	
	public function __construct() {

		$this->name = 'paytpv';
		$this->tab = 'payment_security';
		$this->author = 'PayTPV';
		$this->version = '5.2.0';
		// Array config con los datos de configuración

		$config = $this->getConfigValues();
		// Establecer propiedades según los datos de configuración

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

		$this->description = $this->l('Aceptar pagos con tarjeta de crédito vía paytpv.com');
		// Mostrar aviso en la página principal de módulos si faltan datos de configuración.

		if (!isset($this->pass)
				OR !isset($this->usercode)
				OR !isset($this->clientcode)
				OR !isset($this->term)
				OR !isset($this->pass))
			$this->warning = $this->l('Te faltan datos a configurar el m&oacute;dulo Paytpv.');

	}
	public function install() {

		include_once(_PS_MODULE_DIR_.'/'.$this->name.'/paytpv_install.php');
		$paypal_install = new PayTpvInstall();
		$res = $paypal_install->createTables();
		if (!$res){
			$this->error = $this->l('Te faltan datos a configurar el m&oacute;dulo Paytpv.');
			return false;
		}

		$paypal_install->updateConfiguration();
		// Valores por defecto al instalar el módulo
		if (!parent::install() ||
			!$this->registerHook('payment') ||
			!$this->registerHook('paymentReturn') ||
			!$this->registerHook('displayMyAccountBlock') || 
			!$this->registerHook('displayCustomerAccount'))
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

	    // Si al enviar los datos del formulario de configuración hay campos vacios, mostrar errores.

		if (isset($_POST['btnSubmit']))
		{
			if (empty($_POST['usercode']))
				$this->_postErrors[] = $this->l('Se requiere el código de usuario de paytpv.com.');
			if (empty($_POST['pass']))
				$this->_postErrors[] = $this->l('Se requiere contrase&ntilde;a de producto de paytpv.com.');
			if (empty($_POST['term']))
				$this->_postErrors[] = $this->l('Se requiere número de terminal de producto de paytpv.com.');
			if (empty($_POST['clientcode']))
				$this->_postErrors[] = $this->l('Se requiere el código de cliente de paytpv.com.');
		}

	}
	private function _postProcess(){

	    // Actualizar la configuración en la BBDD

		if (isset($_POST['btnSubmit'])){
			Configuration::updateValue('PAYTPV_TERM', $_POST['term']);
			Configuration::updateValue('PAYTPV_PASS', $_POST['pass']);
			Configuration::updateValue('PAYTPV_USERCODE', $_POST['usercode']);
			Configuration::updateValue('PAYTPV_CLIENTCODE', $_POST['clientcode']);
			Configuration::updateValue('PAYTPV_OPERATIVA', $_POST['operativa']);
			Configuration::updateValue('PAYTPV_3DFIRST', $_POST['3dfirst']);
			Configuration::updateValue('PAYTPV_IFRAME', $_POST['iframe']); 
			Configuration::updateValue('PAYTPV_TERMINALES', $_POST['terminales']); 
			Configuration::updateValue('PAYTPV_SUSCRIPTIONS', $_POST['suscriptions']); 
			return '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('ok').'" /> '.$this->l('Configuración actualizada').'</div>';          
		}

	}
	public function getContent() {

		// Recoger datos
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
		$this->context->smarty->assign('3dfirst', $conf_values['PAYTPV_3DFIRST']);
		$this->context->smarty->assign('terminales', $conf_values['PAYTPV_TERMINALES']);
		$this->context->smarty->assign('suscriptions', $conf_values['PAYTPV_SUSCRIPTIONS']);
		$this->context->smarty->assign('OK',Context::getContext()->link->getModuleLink($this->name, 'url',array(),$ssl));
		$this->context->smarty->assign('KO',Context::getContext()->link->getModuleLink($this->name, 'url',array(),$ssl));
		$this->context->smarty->assign('base_dir', __PS_BASE_URI__);
		return $this->display(__FILE__, 'views/admin.tpl');

	}
	public function hookPayment($params) {

        // Variables necesarias de fuera		

		global $smarty, $cookie, $cart;
		// Eliminar Tarjeta

		$smarty->assign('msg_paytpv',"");
		$showcard = false;
		$msg_paytpv = "";

		
		
		if (isset($_POST["action_paytpv"])){
			switch ($_POST["action_paytpv"]){
				case "remove":
					$res = $this->removeCard($_POST["paytpv_cc"]);
					if ($res)
						$msg_paytpv = $this->l('La tarjeta se ha eliminado correctamente');
				break;

				case "add":
					
					$paytpv_agree = $_POST["paytpv_agree"];
					$suscripcion = $_POST["paytpv_suscripcion"];
					$periodicity = $_POST["paytpv_periodicity"];
					$cycles = $_POST["paytpv_cycles"];

				    $paytpv_order_ref = str_pad($params['cart']->id, 8, "0", STR_PAD_LEFT) . date('is');
				 	$this->save_paytpv_order_info((int)$this->context->customer->id,$cart->id,$paytpv_agree,$suscripcion,$periodicity,$cycles);
					$showcard = true;
				  
				break;
			}
		}
		$smarty->assign('msg_paytpv',$msg_paytpv);
		$smarty->assign('showcard',$showcard);

	    // Valor de compra				
		$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));

		$currency = new Currency(intval($id_currency));		
		$importe = number_format(Tools::convertPrice($params['cart']->getOrderTotal(true, 3), $currency)*100, 0, '.', '');		

		// El número de pedido es el ID del carrito.

		$paytpv_order_ref = str_pad($params['cart']->id, 8, "0", STR_PAD_LEFT) . date('is');
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
		$ps_language = new Language(intval($cookie->id_lang));

		$suscripcion = (isset($_POST["paytpv_suscripcion"]))?$_POST["paytpv_suscripcion"]:0;

		$active_suscriptions = intval(Configuration::get('PAYTPV_SUSCRIPTIONS'));

		if($this->operativa==0){ // BANKSTORE
			switch ($suscripcion){
				// Sin suscripcion
				case 0:
					$OPERATION = "1";
					// Cálculo Firma
					$signature = md5($this->clientcode.$this->term.$OPERATION.$paytpv_order_ref.$importe.$currency->iso_code.md5($this->pass));
					$fields = array
					(
						'MERCHANT_MERCHANTCODE' => $this->clientcode,
						'MERCHANT_TERMINAL' => $this->term,
						'OPERATION' => $OPERATION,
						'LANGUAGE' => $ps_language->iso_code,
						'MERCHANT_MERCHANTSIGNATURE' => $signature,
						'MERCHANT_ORDER' => $paytpv_order_ref,
						'MERCHANT_AMOUNT' => $importe,
						'MERCHANT_CURRENCY' => $currency->iso_code,
						'URLOK' => $URLOK,
						'URLKO' => $URLKO,
						'3DSECURE' => $this->tdfirst
					);
					break;

				// Suscripcion
				case 1:
					$OPERATION = "9";
					$subscription_stratdate = date("Ymd");
					$susc_periodicity = $_POST["paytpv_periodicity"];
					$subs_cycles = $_POST["paytpv_cycles"];

					// Si es indefinido, ponemos como fecha tope la fecha + 10 años.
					if ($subs_cycles==0)
						$subscription_enddate = date("Y")+5 . date("m") . date("d");
					else{
						// Dias suscripcion
						$dias_subscription = $subs_cycles * $susc_periodicity;
						$subscription_enddate = date('Ymd', strtotime("+".$dias_subscription." days"));
					}

					// Cálculo Firma
					$signature = md5($this->clientcode.$this->term.$OPERATION.$paytpv_order_ref.$importe.$currency->iso_code.md5($this->pass));
					$fields = array
					(
						'MERCHANT_MERCHANTCODE' => $this->clientcode,
						'MERCHANT_TERMINAL' => $this->term,
						'OPERATION' => $OPERATION,
						'LANGUAGE' => $ps_language->iso_code,
						'MERCHANT_MERCHANTSIGNATURE' => $signature,
						'MERCHANT_ORDER' => $paytpv_order_ref,
						'MERCHANT_AMOUNT' => $importe,
						'MERCHANT_CURRENCY' => $currency->iso_code,
						'SUBSCRIPTION_STARTDATE' => $subscription_stratdate, 
						'SUBSCRIPTION_ENDDATE' => $subscription_enddate,
						'SUBSCRIPTION_PERIODICITY' => $susc_periodicity,
						'URLOK' => $URLOK,
						'URLKO' => $URLKO,
						'3DSECURE' => $this->tdfirst
					);

					break;

			}
			
			//$tmpl_vars = $this->getToken($fields);

			$saved_card = $this->getToken();
			$index = 0;
			foreach ($saved_card as $key=>$val){
				$values_aux = array_merge($values,array("TOKEN_USER"=>$val["TOKEN_USER"]));
				$saved_card[$key]['url'] = Context::getContext()->link->getModuleLink($this->name, 'capture',$values_aux,$ssl);	
				$index++;
			}
			$saved_card[$index]['url'] = 0;



			$tmpl_vars['capture_url'] = Context::getContext()->link->getModuleLink($this->name, 'capture',$values,$ssl);
			$smarty->assign('active_suscriptions',$active_suscriptions);
			$smarty->assign('saved_card',$saved_card);
			$smarty->assign('id_cart',$params['cart']->id);
			
			$smarty->assign('base_dir', __PS_BASE_URI__);

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
				"CONCEPT" => $paytpv_clientconcept.", ".($cookie->logged ? $cookie->customer_firstname.' '.$cookie->customer_lastname : ""),
				'URLOK' => $URLOK,
				'URLKO' => $URLKO
			);

		}

		$tmpl_vars = array_merge(
			array(
			'fields' => $fields,
			'query' => http_build_query($fields),
			'this_path' => $this->_path)
		);
		$smarty->assign($tmpl_vars);
		

		switch ($this->operativa){
			// BANKSTORE
			case 0: 
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
	public function getToken(){

		$res = array();
		$sql = 'SELECT paytpv_iduser,paytpv_tokenuser,paytpv_cc,paytpv_brand FROM '._DB_PREFIX_.'paytpv_customer WHERE not paytpv_cc="" and id_customer = '.(int)$this->context->customer->id . ' order by date desc';

		$assoc = Db::getInstance()->executeS($sql);

		foreach ($assoc as $key=>$row) {

			$res[$key]['IDUSER']= $row['paytpv_iduser'];
			$res[$key]['TOKEN_USER']= $row['paytpv_tokenuser'];
			$res[$key]['CC'] = $row['paytpv_cc'];
			$res[$key]['BRAND'] = $row['paytpv_brand'];
		}

		return  $res;

	}
	public function getDataToken($token){

		$res = array();

		$sql = 'SELECT paytpv_iduser,paytpv_tokenuser,paytpv_cc FROM '._DB_PREFIX_.'paytpv_customer WHERE paytpv_tokenuser="'.$token.'"';

		$assoc = Db::getInstance()->executeS($sql);

		foreach ($assoc as $key=>$row) {
			$res['IDUSER']= $row['paytpv_iduser'];
			$res['TOKEN_USER']= $row['paytpv_tokenuser'];
			$res['CC'] = $row['paytpv_cc'];
		}

		return  $res;

	}
	public function hookPaymentReturn($params) {

		if (!$this->active)
			return;
		$this->context->smarty->assign(array(
			'this_path' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		$this->context->smarty->assign('base_dir',__PS_BASE_URI__);
		return $this->display(__FILE__, 'payment_return.tpl');

	}
	private function getConfigValues(){
		return Configuration::getMultiple(array('PAYTPV_USERCODE', 'PAYTPV_PASS', 'PAYTPV_TERM', 'PAYTPV_CLIENTCODE', 'PAYTPV_OPERATIVA', 'PAYTPV_3DFIRST', 'PAYTPV_TERMINALES', 'PAYTPV_IFRAME','PAYTPV_SUSCRIPTIONS','PAYTPV_REG_ESTADO'));
	}
	
	public function saveCard($id_customer,$paytpv_iduser,$paytpv_tokenuser,$paytpv_cc,$paytpv_brand){

		// Datos usuario
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_customer where id_customer = ' . $id_customer .' AND paytpv_cc="'.$paytpv_cc.'"';	
		$result = Db::getInstance()->getRow($sql);

		// Si no existe el token lo insertamos
		if (empty($result) === true){
			$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_customer (`paytpv_iduser`, `paytpv_tokenuser`, `paytpv_cc`,`paytpv_brand`,`id_customer`) VALUES('.$paytpv_iduser.',"'.$paytpv_tokenuser.'","'.$paytpv_cc.'","'.$paytpv_brand.'",'.$id_customer.')';
			Db::getInstance()->Execute($sql);
		}else{
			// Si existe actualizamos la fecha de uso para mostrarla luego la primera
			$sql = 'UPDATE '. _DB_PREFIX_ .'paytpv_customer set date = \''.pSQL(date('Y-m-d H:i:s')).'\' where id_customer = '.intval($id_customer).' and paytpv_cc="'.$paytpv_cc.'"';
			Db::getInstance()->Execute($sql);

			// Eliminamos el usuario creado en paytpv. (NO DEJAMOS CREAR DOS TARJETAS IGUALES AL MIMSO USUARIO)
			$result = $this->remove_user($paytpv_iduser,$paytpv_tokenuser);
		}
	}

	public function savePayTpvOrder($paytpv_iduser,$paytpv_tokenuser,$id_suscription,$id_customer,$id_order,$price){

		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_order where id_order='.$id_order. ' and paytpv_iduser='.$paytpv_iduser;
		$result = Db::getInstance()->getRow($sql);
		if (empty($result) === true){
			$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_order (`paytpv_iduser`,`paytpv_tokenuser`,`id_suscription`, `id_customer`, `id_order`,`price`) VALUES('.$paytpv_iduser.',"'.$paytpv_tokenuser.'",'.$id_suscription.','.$id_customer.','.$id_order.',"'.$price.'")';
			Db::getInstance()->Execute($sql);
		}
		
	}


	public function saveSuscription($id_customer,$id_order,$paytpv_iduser,$paytpv_tokenuser,$periodicity,$cycles,$importe){
		// Datos usuario
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_suscription where id_customer = ' . $id_customer .' AND id_order="'.$id_order.'"';	
		$result = Db::getInstance()->getRow($sql);

		// Si no existe la suscripcion la creamos
		if (empty($result) === true){
			$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_suscription(`id_customer`, `id_order`, `paytpv_iduser`,`paytpv_tokenuser`,`periodicity`,`cycles`,`price`) VALUES('.$id_customer.','.$id_order.','.$paytpv_iduser.',"'.$paytpv_tokenuser.'",'.$periodicity.','.$cycles.','.$importe.')';
			Db::getInstance()->Execute($sql);
		}
	}

	public function subcriptionFromOrder($id_customer,$id_order){
		// Datos usuario
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_suscription where id_customer = ' . $id_customer .' AND id_order="'.$id_order.'"';	
		$result = Db::getInstance()->getRow($sql);
		return $result;
	}

	
	
	/* Obtener las suscripciones del usuario */
	public function getSuscriptions(){
		global $cookie;
		$ps_language = new Language(intval($cookie->id_lang));

		$res = array();
		$sql = 'select ps.*,count(po.id_order) as pagos FROM '._DB_PREFIX_.'paytpv_suscription ps
LEFT OUTER JOIN '._DB_PREFIX_.'paytpv_order po on ps.id_suscription = po.id_suscription 
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
			$res[$key]['CYCLES'] = $row['cycles'];
			$res[$key]['PRICE'] = number_format(Tools::convertPrice($row['price'], $currency), 2, '.', '');		
			$res[$key]['DATE'] = $row['date'];
			$res[$key]['DATE_YYYYMMDD'] = ($ps_language->iso_code=="es")?date("d-m-Y",strtotime($row['date'])):date("Y-m-d",strtotime($row['date']));

			$num_pagos = $row['pagos'];
			
			$status = $row['status'];
			if ($row['status']==1)
				$status = $row['status'];  // CANCELADA
			else if ($num_pagos==$row['cycles'])
				$status = 2; // FINALIZADO
							

			$res[$key]['STATUS'] = $status;
		}
		
		return  $res;
	}

	
	/* Obtener los pagos de una suscripcion */
	public function getSuscriptionPay($id_suscription){
		global $cookie;
		$ps_language = new Language(intval($cookie->id_lang));
		$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
		$currency = new Currency(intval($id_currency));

		// Si no existe la suscripcion la creamos
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_order where id_suscription = ' . $id_suscription;	
		$assoc = Db::getInstance()->executeS($sql);
		$res = array();
		foreach ($assoc as $key=>$row) {
			$res[$key]["ID"] = $row["id"];
			$order = new Order($row['id_order']);
			$res[$key]['ID_ORDER']= $row['id_order'];
			$res[$key]['ORDER_REFERENCE']= $order->reference;
			$res[$key]["PRICE"] = number_format(Tools::convertPrice($row['price'], $currency), 2, '.', '');	
			$res[$key]['DATE'] = $row['date'];
			$res[$key]['DATE_YYYYMMDD'] = ($ps_language->iso_code=="es")?date("d-m-Y",strtotime($row['date'])):date("Y-m-d",strtotime($row['date']));
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


	public function removeCard($paytpv_cc){
		include_once(_PS_MODULE_DIR_.'/paytpv/ws_client.php');

		$client = new WS_Client(
			array(
				'clientcode' => $this->clientcode,
				'term' => $this->term,
				'pass' => $this->pass,
			)
		);
		// Datos usuario

		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_customer where id_customer = '.(int)$this->context->customer->id . ' and paytpv_cc="'.$paytpv_cc .'"';
		$result = Db::getInstance()->getRow($sql);
		$paytpv_iduser = $result["paytpv_iduser"];
		$paytpv_tokenuser = $result["paytpv_tokenuser"];

		$result = $client->remove_user( $paytpv_iduser, $paytpv_tokenuser);
		
		$sql = 'DELETE FROM '. _DB_PREFIX_ .'paytpv_customer where id_customer = '.(int)$this->context->customer->id . ' and `paytpv_cc`="'.$paytpv_cc.'"';
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

		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_suscription where id_suscription = '.$id_suscription;
		$result = Db::getInstance()->getRow($sql);
		$paytpv_iduser = $result["paytpv_iduser"];
		$paytpv_tokenuser = $result["paytpv_tokenuser"];

		$result = $client->remove_subscription( $paytpv_iduser, $paytpv_tokenuser);
		if ( ( int ) $result[ 'DS_RESPONSE' ] == 1 ) {
			$sql = 'DELETE FROM '. _DB_PREFIX_ .'paytpv_suscription where id_suscription = '.$id_suscription;
			Db::getInstance()->Execute($sql);
			return true;
		}
		return false;
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

		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_suscription where id_suscription = '.$id_suscription;
		$result = Db::getInstance()->getRow($sql);
		$paytpv_iduser = $result["paytpv_iduser"];
		$paytpv_tokenuser = $result["paytpv_tokenuser"];

		$result = $client->remove_subscription( $paytpv_iduser, $paytpv_tokenuser);

		if ( ( int ) $result[ 'DS_RESPONSE' ] == 1 ) {
			$sql = 'UPDATE '. _DB_PREFIX_ .'paytpv_suscription set status=1 where id_suscription = '.$id_suscription;
			Db::getInstance()->Execute($sql);
			return true;
		}
		return false;

		

	}


	public function save_paytpv_order_info($id_customer,$id_cart,$paytpvagree,$suscription,$peridicity,$cycles){
		// Eliminamos la orden si existe.
		$sql = 'DELETE FROM '. _DB_PREFIX_ .'paytpv_order_info where id_customer = '.$id_customer .' and id_cart= "'. $id_cart .'"';
		Db::getInstance()->Execute($sql);

		// Insertamos los datos de la orden
		$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_order_info (`id_customer`,`id_cart`,`paytpvagree`,`suscription`,`periodicity`,`cycles`) VALUES('.$id_customer.',"'.$id_cart.'",'.$paytpvagree.','.$suscription.','.$peridicity.','.$cycles.')';
		Db::getInstance()->Execute($sql);
		
		return true;

	}


	public function get_paytpv_order_info($id_customer,$id_cart){
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_order_info where id_customer = '.$id_customer . ' and id_cart="'.$id_cart.'"';
		$result = Db::getInstance()->getRow($sql);
		return $result;
	}


	public function validPassword($id_customer,$passwd){
		$sql = 'select * from ' . _DB_PREFIX_ .'customer where id_customer = '.$id_customer . ' and passwd="'. md5(pSQL(_COOKIE_KEY_.$passwd)) . '"';
		$result = Db::getInstance()->getRow($sql);
		return (empty($result) === true)?false:true;
	}


	/*

	Datos cuenta
	*/

	public function hookDisplayCustomerAccount($params)
	{
		$this->smarty->assign('in_footer', false);
		return $this->display(__FILE__, 'my-account.tpl');
	}
}

?>