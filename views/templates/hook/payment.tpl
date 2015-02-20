{*
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
*}

<p class="payment_module">

	<a href="javascript:$('#paytpv_form').submit();" title="{l s='Connect with TPV' mod='paytpv'}">

		<img src="{$module_dir}/views/img/tarjetas.png" alt="{l s='Connect with TPV' mod='paytpv'}" />

		{l s='Secure payment by credit card' mod='paytpv'}

	</a>

</p>
<form action="https://www.paytpv.com/gateway/fsgateway.php" method="post" id="paytpv_form" class="hidden">

{foreach from=$fields key=k item=v}

    <input type="hidden" name="{$k}" value="{$v}" />

{/foreach}

</form>