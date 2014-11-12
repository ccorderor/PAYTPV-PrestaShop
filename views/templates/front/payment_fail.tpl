{capture name=path}{l s='Pago no completado' mod='paytpv'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Pago no completado' mod='paytpv'}</h2>

<table width="100%" border="0">
	<tr><td><img src="img/admin/icon-cancel.png"/></td>
    <td>{l s='Lo sentimos. Su pago no se ha completado. Puede intentarlo de nuevo o escoger otro medio de pago. Recuerde que puede usar tarjetas adheridas al sistema de pago seguro de Visa, denominado "Verified by Visa", o de MasterCard, denominado "MasterCard SecureCode".'  mod='paytpv'}</td></tr>
</table>
<ul class="footer_links">
    <li><a href="{$link->getPageLink('my-account')}"><img src="{$this_path}views/img/my-account.gif" alt="" class="icon" /></a><a href="{$link->getPageLink('my-account')}">{l s='Volver a su cuenta'  mod='redsys'}</a></li>
    <li><a href="{$link->getPageLink('order',false, NULL,'step=3')}" title="{l s='Pagos'  mod='paytpv'}"><img src="{$this_path}views/img/cart.gif" alt="{l s='Pagos' mod='redsys'}" class="icon" /></a><a href="{$link->getPageLink('order',false, NULL,'step=3')}" title="{l s='Pagos'  mod='paytpv'}">{l s='Volver a elegir medio de pago'  mod='paytpv'}</a></li>
    <li><a href="{$base_dir}"><img src="{$this_path}views/img/home.gif" alt="" class="icon" /></a><a href="{$base_dir}">{l s='Inicio'  mod='redsys'}</a></li>
</ul>