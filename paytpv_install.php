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

class PayTpvInstall
{
	/**
	 * Create PayTpv tables
	 */
	public function createTables()
	{
		if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paytpvregistro` (
			  `id_registro` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `id_customer` int(10) unsigned NOT NULL,
			  `id_cart` int(10) unsigned NOT NULL,
			  `amount` decimal(13,6) unsigned NOT NULL,
			  `date_add` datetime NOT NULL,
			  `error_code` varchar(64) character set utf8 NOT NULL,
			  PRIMARY KEY  (`id_registro`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1'))
		return false;

		if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paytpv_order_info` (
				`id_customer` int(10) unsigned NOT NULL,
				`id_cart` int(10) unsigned NOT NULL,
				`paytpvagree` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
				`suscription` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
				`periodicity` INT(3) NOT NULL,
				`cycles` INT(2) NOT NULL,
				`date` DATETIME NOT NULL,
				PRIMARY KEY (`id_customer`, `id_cart`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
		return false;

		if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paytpv_customer` (
				`paytpv_iduser` int NOT NULL,
				`paytpv_tokenuser` VARCHAR(64) NOT NULL,
				`paytpv_cc` VARCHAR(32) NOT NULL,
				`paytpv_brand` VARCHAR(32) NULL,
				`id_customer` int(10) unsigned NOT NULL,
				`date` DATETIME NOT NULL,
				PRIMARY KEY (`paytpv_iduser`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
		return false;

		if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paytpv_suscription` (
				`id_suscription` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_customer` int(10) unsigned NOT NULL,
				`id_order` INT(10) UNSIGNED NOT NULL,
				`paytpv_iduser` INT(11) NOT NULL,
				`paytpv_tokenuser` VARCHAR(64) NOT NULL,
				`periodicity` INT(3) NOT NULL,
				`cycles` INT(2) NOT NULL,
				`price` DECIMAL(20,6) NOT NULL DEFAULT 0,
				`date` DATETIME NOT NULL,
				`status` SMALLINT(1) NOT NULL DEFAULT 0,
				PRIMARY KEY (`id_suscription`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
		return false;

		if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paytpv_order` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`paytpv_iduser` INT(11) NOT NULL,
				`paytpv_tokenuser` VARCHAR(64) NOT NULL,
				`id_suscription` INT(10) UNSIGNED NOT NULL,
				`id_customer` INT(10) UNSIGNED NOT NULL,
				`id_order` INT(10) UNSIGNED NOT NULL,
				`price` DECIMAL(20,6) NOT NULL DEFAULT 0,
				`date` DATETIME NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
		return false;

		return true;
	}
	/**
	 * Set configuration table
	 */

	public function updateConfiguration()
	{
		Configuration::updateValue('PAYTPV_CLIENTCODE', 'Escribe el código cliente');
		Configuration::updateValue('PAYTPV_USERCODE', 'PayTPV');
	}
	/**
	 * Delete PayTpv configuration
	 */

	public function deleteConfiguration()
	{
		// Valores a quitar si desinstalamos el módulo
		Configuration::deleteByName('PAYTPV_USERCODE');
		Configuration::deleteByName('PAYTPV_CLIENTCODE');
		Configuration::deleteByName('PAYTPV_OPERATIVA');
		Configuration::deleteByName('PAYTPV_3DFIRST');
		Configuration::deleteByName('PAYTPV_TERMINALES');
		Configuration::deleteByName('PAYTPV_IFRAME');
		Configuration::deleteByName('PAYTPV_TERM');
		Configuration::deleteByName('PAYTPV_REG_ESTADO');
		Configuration::deleteByName('PAYTPV_PASS');
		Configuration::deleteByName('PAYTPV_SUSCRIPTIONS');
		
	}
}