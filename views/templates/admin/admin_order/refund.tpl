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

{if $smarty.const._PS_VERSION_ >= 1.6}
<div class="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-heading"><img src="{$base_url}modules/{$module_name}/logo.gif" alt="" /> {l s='PayTPV Refund' mod='paytpv'}</div>
			<form method="post" action="{$smarty.server.REQUEST_URI|escape:htmlall}">
				<input type="hidden" name="id_order" value="{$params.id_order|intval}" />
				<p><b>{l s='Information:' mod='paytpv'}</b> {l s='Payment accepted' mod='paytpv'}</p>
				<ul>
					<li>
					{l s='"Standard Refund" or "Refund product" options: a partial refund to customer credit card is made unless you select "Generate a voucher".' mod='paytpv'}</li>
					<li>
					{l s='"Partitial Refund" does not return amount to the credit card customer.' mod='paytpv'}
					</li>
				</ul>
				<p><b>{l s='Outstanding amount:' mod='paytpv'}</b> {$amount}</p>
				<p class="center">
					<button type="submit" class="btn btn-default" name="submitPayTpvRefund" onclick="if (!confirm('{l s='Are you sure?' mod='paytpv'}'))return false;">
						<i class="icon-undo"></i>
						{l s='Refund total transaction' mod='paytpv'}
					</button>
				</p>
			</form>	
		</div>
	</div>
</div>
{else}
<br />
<fieldset {if isset($ps_version) && ($ps_version < '1.5')}style="width: 400px"{/if}>
	<legend><img src="{$base_url}modules/{$module_name}/logo.gif" alt="" />{l s='PayTPV Refund' mod='paytpv'}</legend>
	<p><b>{l s='Information:' mod='paytpv'}</b> {l s='Payment accepted' mod='paytpv'}</p>
	<ul>
		<li>
		{l s='"Standard Refund" or "Refund product" options: a partial refund to customer credit card is made unless you select "Generate a voucher".' mod='paytpv'}</li>
		<li>
		{l s='"Partitial Refund" does not return amount to the credit card customer.' mod='paytpv'}
		</li>
	</ul>
	<p><b>{l s='Outstanding amount:' mod='paytpv'}</b> {$amount}</p>
	<form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}">
		<input type="hidden" name="id_order" value="{$params.id_order|intval}" />
		<p class="center">
			<input type="submit" class="button" name="submitPayTpvRefund" value="{l s='Refund total transaction' mod='paytpv'}" onclick="if (!confirm('{l s='Are you sure?' mod='paytpv'}'))return false;" />
		</p>
	</form>
</fieldset>

{/if}
