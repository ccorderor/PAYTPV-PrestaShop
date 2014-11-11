<?php
/**
* www.paytpv.com Module Copyright (c) 2013 PayTPV Software
*  
*  info@paytpv.com -- http://paytpv.com/
*
*  Released under the GNU General Public License
*
*  Autor: Mikel Martin
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
		$this->version = '4.0.0';

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
		if (isset($config['PAYTPV_3DMIN']))
			$this->tdmin = $config['PAYTPV_3DMIN'];
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
		//Instala la tabla para almacenar las operaciones en la pasarela
		if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return (false);
		else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return (false);
		$sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
		$sql = preg_split("/;\s*[\r\n]+/",$sql);
		foreach ($sql AS $k=>$query)
			Db::getInstance()->Execute(trim($query));

		$sql = 'SHOW COLUMNS from '._DB_PREFIX_.'customer where field=\'paytpv_cc\';';
		$assoc = Db::getInstance()->executeS($sql);
		if(count($assoc)>0){
			$sql = 'ALTER TABLE `'._DB_PREFIX_.'customer` ADD  `paytpv_iduser` INT NOT NULL ,
    		ADD  `paytpv_tokenuser` VARCHAR( 64 ) NULL DEFAULT NULL ,
    		ADD  `paytpv_cc` VARCHAR( 32 ) NULL DEFAULT NULL ;';
			Db::getInstance()->executeS($sql);
		}

		$sql = 'SHOW COLUMNS from '._DB_PREFIX_.'orders where field=\'paytpv_cc\';';
		$assoc = Db::getInstance()->executeS($sql);
		if(count($assoc)>0){
			$sql = 'ALTER TABLE `'._DB_PREFIX_.'orders` ADD  `paytpv_iduser` INT NOT NULL ,
    		ADD  `paytpv_tokenuser` VARCHAR( 64 ) NULL DEFAULT NULL ,
    		ADD  `paytpv_cc` VARCHAR( 32 ) NULL DEFAULT NULL ;';
			Db::getInstance()->executeS($sql);
		}

		Configuration::updateValue('PAYTPV_CLIENTCODE', 'Escribe el código cliente');
		Configuration::updateValue('PAYTPV_USERCODE', 'PayTPV');
		
		// Valores por defecto al instalar el módulo
		if (!parent::install() OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}

	public function uninstall() {
		// Valores a quitar si desinstalamos el módulo
		if (!Configuration::deleteByName('PAYTPV_USERCODE')
				OR !Configuration::deleteByName('PAYTPV_CLIENTCODE')
				OR !Configuration::deleteByName('PAYTPV_OPERATIVA')
				OR !Configuration::deleteByName('PAYTPV_3DMIN')
				OR !Configuration::deleteByName('PAYTPV_3DFIRST')
				OR !Configuration::deleteByName('PAYTPV_IFRAME')
				OR !Configuration::deleteByName('PAYTPV_TERM')
				OR !Configuration::deleteByName('PAYTPV_REG_ESTADO')
				OR !Configuration::deleteByName('PAYTPV_PASS')
				OR !parent::uninstall())
			return false;
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
			Configuration::updateValue('PAYTPV_3DMIN', $_POST['3dmin']);
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
		$this->context->smarty->assign('tdmin', $conf_values['PAYTPV_3DMIN']);
		$this->context->smarty->assign('tdfirst', $conf_values['PAYTPV_3DFIRST']);
		$this->context->smarty->assign('OK',Context::getContext()->link->getModuleLink($this->name, 'url',array(),$ssl));
		$this->context->smarty->assign('KO',Context::getContext()->link->getModuleLink($this->name, 'url',array(),$ssl));
		$this->context->smarty->assign('base_dir', __PS_BASE_URI__);
		return $this->display(__FILE__, 'views/admin.tpl');
	}

	public function hookPayment($params) {
        // Variables necesarias de fuera		
		global $smarty, $cookie, $cart;
					
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
		if($this->operativa=='0'){
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
				'3DSECURE' => '1'
			);
			$tmpl_vars = $this->getToken($fields);
		}else{
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
			'this_path' => $this->_path),
			$tmpl_vars
		);

		$smarty->assign($tmpl_vars);
		if($this->iframe=='1')
			if($this->operativa=='1')
				return $this->display(__FILE__, 'payment_iframe.tpl');
			else
				return $this->display(__FILE__, 'payment_bsiframe.tpl');
		return $this->display(__FILE__, 'payment.tpl');
	}

	public function getToken(){
		$res = array();
		$sql = 'SELECT paytpv_iduser,paytpv_tokenuser,paytpv_cc FROM '._DB_PREFIX_.'customer WHERE not paytpv_cc="" and id_customer = '.(int)$this->context->customer->id;
		$assoc = Db::getInstance()->executeS($sql);
		foreach ($assoc as $row) {
			$res['IDUSER']= $row['paytpv_iduser'];
			$res['TOKENUSER']= $row['paytpv_tokenuser'];
			$res['CC'] = $row['paytpv_cc'];
			$res['capture_url'] = Context::getContext()->link->getModuleLink($this->name, 'capture',$values,$ssl);
		}
		return  $res;
	}
	public function hookPaymentReturn($params) {
		if (!$this->active)
			return;

		$this->context->smarty->assign(array(
			'this_path' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));

		return $this->display(__FILE__, 'payment_return.tpl');
	}

	private function getConfigValues(){
		return Configuration::getMultiple(array('PAYTPV_USERCODE', 'PAYTPV_PASS', 'PAYTPV_TERM', 'PAYTPV_CLIENTCODE', 'PAYTPV_OPERATIVA', 'PAYTPV_3DMIN', 'PAYTPV_3DFIRST','PAYTPV_IFRAME','PAYTPV_REG_ESTADO'));
	}
}
?>
