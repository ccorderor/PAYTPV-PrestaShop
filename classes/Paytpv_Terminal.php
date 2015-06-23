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

class Paytpv_Terminal extends ObjectModel
{
	public $id;
	public $idterminal;
	public $password;
	public $currency_iso_code;
	public $terminales;
	public $tdfirst;
	public $tdmin;
	


	public static function exist_Terminal(){
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_terminal order by id';
		$result = Db::getInstance()->getRow($sql);
		if (empty($result) === true)
			return false;

		return true;
	}


	public static function remove_Terminals(){
		Db::getInstance()->Execute('DELETE FROM '. _DB_PREFIX_ .'paytpv_terminal');
	}

	public static function add_Terminal($id,$idterminal,$password,$currency_iso_code,$terminales,$tdfirst,$tdmin){
		$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_terminal (id,idterminal,password,currency_iso_code,terminales,tdfirst,tdmin) VALUES('.$id.','.$idterminal.',"'.$password.'","'.$currency_iso_code.'",'.$terminales.','.$tdfirst.','.$tdmin.')';
		Db::getInstance()->Execute($sql);
	}

	public static function get_Terminals(){
		return Db::getInstance()->executeS("SELECT idterminal, password, currency_iso_code, terminales, tdfirst, tdmin FROM " . _DB_PREFIX_ . "paytpv_terminal");
	}

	public static function get_Terminal_Currency($currency_iso_code){
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_terminal where currency_iso_code="'.$currency_iso_code. '"';
		$result = Db::getInstance()->getRow($sql);
		return $result;
	}

	public static function get_First_Terminal(){
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_terminal order by id';
		$result = Db::getInstance()->getRow($sql);
		return $result;
	}

	public static function get_TerminalById(){
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_terminal order by id';
		$result = Db::getInstance()->getRow($sql);
		return $result;
	}


	public static function getTerminalByCurrency($currency_iso_code){
		$result2 = self::get_Terminal_Currency($currency_iso_code);

		// Select first termnial defined
		if (empty($result2) === true){
			// Search for terminal in merchant default currency
			$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
			$currency = new Currency($id_currency);

			$result2 = self::get_Terminal_Currency($currency->iso_code);

			// If not exists terminal in default currency. Select first terminal defined
			if (empty($result2) === true){
				$result2 = self::get_First_Terminal();
			}
		}

		$arrDatos["idterminal"] = $result2["idterminal"];
		$arrDatos["password"] = $result2["password"];
		$arrDatos["terminales"] = $result2["terminales"];
		$arrDatos["tdfirst"] = $result2["tdfirst"];
		$arrDatos["tdmin"] = $result2["tdmin"];

		return $arrDatos;
	}

	public static function getTerminalByIdTerminal($idterminal){
		$sql = 'select * from ' . _DB_PREFIX_ .'paytpv_terminal where idterminal='.$idterminal;
		$result2 = Db::getInstance()->getRow($sql);

		$arrDatos["idterminal"] = $result2["idterminal"];
		$arrDatos["password"] = $result2["password"];
		$arrDatos["terminales"] = $result2["terminales"];
		$arrDatos["tdfirst"] = $result2["tdfirst"];
		$arrDatos["tdmin"] = $result2["tdmin"];

		return $arrDatos;
	}

}
