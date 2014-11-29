<?php

/**
* www.paytpv.com Module Copyright (c) 2014 PayTPV Software
*  
*  info@paytpv.com -- http://paytpv.com/
*
*  Released under the GNU General Public License
*
*  Autor: Jose Ramón Garcia
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
		$this->version = '5.0.0';
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
		if (isset($config['PAYTPV_IFRAME']))
			$this->iframe = $config['PAYTPV_IFRAME'];			
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
			return $false;
		}

		$paypal_install->updateConfiguration();
		// Valores por defecto al instalar el módulo
		if (!parent::install() OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn'))
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
				 	$this->paytpvagree((int)$this->context->customer->id,$_POST["paytpv_agree"]);
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
		$OPERATION = "1";
		$URLOK=$URLKO=Context::getContext()->link->getModuleLink($this->name, 'url',$values,$ssl);
		$ps_language = new Language(intval($cookie->id_lang));
		
		if($this->operativa=='0'){ // BANKSTORE
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

			//$tmpl_vars = $this->getToken($fields);

			$saved_card = $this->getToken($fields);
			
			foreach ($saved_card as $key=>$val){
				$values_aux = array_merge($values,array("TOKENUSER"=>$val["TOKENUSER"]));
				$saved_card[$key]['url'] = Context::getContext()->link->getModuleLink($this->name, 'capture',$values_aux,$ssl);	
			}

			$tmpl_vars['capture_url'] = Context::getContext()->link->getModuleLink($this->name, 'capture',$values,$ssl);
			$smarty->assign('saved_card',$saved_card);
			$smarty->assign('base_dir', __PS_BASE_URI__);

		}else{  // TPV WEB

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
				'URLKO' => $URLKO,
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
			$res[$key]['TOKENUSER']= $row['paytpv_tokenuser'];
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
			$res['TOKENUSER']= $row['paytpv_tokenuser'];
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
		return Configuration::getMultiple(array('PAYTPV_USERCODE', 'PAYTPV_PASS', 'PAYTPV_TERM', 'PAYTPV_CLIENTCODE', 'PAYTPV_OPERATIVA', 'PAYTPV_3DFIRST','PAYTPV_IFRAME','PAYTPV_REG_ESTADO'));
	}
	
	public function saveCard($idorder,$idcustomer,$paytpv_iduser,$paytpv_tokenuser,$paytpv_cc,$paytpv_brand){

		// Datos usuario
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_customer where id_customer = ' . $idcustomer .' AND paytpv_cc="'.$paytpv_cc.'"';	

		$result = Db::getInstance()->getRow($sql);

		// Si no existe el token lo insertamos
		if (empty($result) === true){
			$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_customer (`paytpv_iduser`, `paytpv_tokenuser`, `paytpv_cc`,`paytpv_brand`,`id_customer`) VALUES('.$paytpv_iduser.',"'.$paytpv_tokenuser.'","'.$paytpv_cc.'","'.$paytpv_brand.'",'.$idcustomer.')';
			Db::getInstance()->Execute($sql);

		// Si existe actualizamos la fecha de uso para mostrarla luego la primera

		}else{
			$sql = 'UPDATE '. _DB_PREFIX_ .'paytpv_customer set date = \''.pSQL(date('Y-m-d H:i:s')).'\' where id_customer = '.intval($idcustomer).' and paytpv_cc="'.$paytpv_cc.'"';
			Db::getInstance()->Execute($sql);
		}
		// Datos order

		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_order where id_order='.$idorder. ' and paytpv_iduser='.$paytpv_iduser;
		$result = Db::getInstance()->getRow($sql);
		if (empty($result) === true){
			$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_order (`id_order`, `paytpv_iduser`) VALUES('.$idorder.','.$paytpv_iduser.')';
			Db::getInstance()->Execute($sql);
		}
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

		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_customer where paytpv_cc="'.$paytpv_cc .'"';
		$result = Db::getInstance()->getRow($sql);
		$paytpv_iduser = $result["paytpv_iduser"];
		$paytpv_tokenuser = $result["paytpv_tokenuser"];

		$result = $client->remove_user( $paytpv_iduser, $paytpv_tokenuser, $_SERVER['REMOTE_ADDR']);
		if ($result["DS_RESPONSE"]==1){
			$sql = 'DELETE FROM '. _DB_PREFIX_ .'paytpv_customer where id_customer = '.(int)$this->context->customer->id . ' and `paytpv_cc`="'.$paytpv_cc.'"';
			Db::getInstance()->Execute($sql);
			return true;
		}

		return false;

	}


	public function paytpvagree($idcustomer,$paytpv_agree){
		
		// Eliminamos el acuerdo si existe.
		$sql = 'DELETE FROM '. _DB_PREFIX_ .'paytpv_agree where id_customer = '.$idcustomer;
		Db::getInstance()->Execute($sql);

		if ($paytpv_agree){
			// Insertamos el acuerdo
			$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_agree (`id_customer`, `paytpvagree`) VALUES('.$idcustomer.','.$paytpv_agree.')';
			Db::getInstance()->Execute($sql);
		}
		return true;

	}


	public function paytpvagree_save($idcustomer){
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_agree where id_customer = '.$idcustomer . ' and paytpvagree=1';
		$result = Db::getInstance()->getRow($sql);
		return (empty($result) === true)?false:true;
	}


	public function validPassword($idcustomer,$passwd){
		$sql = 'select * from ' . _DB_PREFIX_ .'customer where id_customer = '.$idcustomer . ' and passwd="'. md5(pSQL(_COOKIE_KEY_.$passwd)) . '"';
		$result = Db::getInstance()->getRow($sql);
		return (empty($result) === true)?false:true;
	}
}

?>

