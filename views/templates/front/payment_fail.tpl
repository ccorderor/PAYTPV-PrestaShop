{capture name=path}{l s='Pago no completado' mod='paytpv'}{/capture}

{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Pago no completado' mod='paytpv'}</h2>


	{if isset($msg_paytpv_contrasena) && ($msg_paytpv_contrasena!="")}
	<img src="{$base_dir}img/admin/icon-cancel.png"/> &nbsp;&nbsp; {$msg_paytpv_contrasena}
	</tr>
	{else}
	<img src="{$base_dir}img/admin/icon-cancel.png"/>&nbsp;&nbsp;   
	{l s='Lo sentimos. Su pago no se ha completado. Puede intentarlo de nuevo o escoger otro medio de pago. Recuerde que puede usar tarjetas adheridas al sistema de pago seguro de Visa, denominado "Verified by Visa", o de MasterCard, denominado "MasterCard SecureCode".'  mod='paytpv'}
	{/if}

<ul class="footer_links">    
	<li>    	
		<a href="{$link->getPageLink('my-account')}" title="{l s='Mi cuenta'  mod='paytpv'}">    		
			<img src="{$base_dir}img/admin/nav-user.gif" alt="{l s='Mi cuenta' mod='paytpv'}" class="icon" />&nbsp;{l s='Volver a su cuenta'  mod='paytpv'}    	
		</a>
	</li>
	<li>&nbsp;&nbsp;</li>    
	<li>    	
		<a href="{$link->getPageLink('order',false, NULL,'step=3')}" title="{l s='Pagos'  mod='paytpv'}">    		
			<img src="{$base_dir}img/admin/cart.gif" alt="{l s='Pagos' mod='paytpv'}" class="icon" />&nbsp;{l s='Volver a elegir medio de pago'  mod='paytpv'}    	
	    </a>    
	</li>    
	<li>&nbsp;&nbsp;</li>    
	<li>    	
		<a href="{$base_dir}">    		
			<img src="{$base_dir}img/admin/home.gif" alt="{l s='Inicio' mod='paytpv'}" class="icon" />&nbsp;{l s='Inicio'  mod='paytpv'}    	
		</a>    
	</li>
</ul>