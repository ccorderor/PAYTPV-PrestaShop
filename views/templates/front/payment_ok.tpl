{capture name=path}{l s='Pago no completado' mod='paytpv'}{/capture}

{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Pago completado' mod='paytpv'}</h2>
	<img src="{$base_dir}img/admin/icon-cancel.png"/>&nbsp;&nbsp;   
	{l s='Gracias por confiar en nosotros. Su compra se ha formalizado correctamente y en breve procesaremos su pedido.'  mod='paytpv'}
	

<ul class="footer_links">    
	<li>    	
		<a href="{$link->getPageLink('my-account')}" title="{l s='Mi cuenta'  mod='paytpv'}">    		
			<img src="{$base_dir}img/admin/nav-user.gif" alt="{l s='Mi cuenta' mod='paytpv'}" class="icon" />&nbsp;{l s='Ir a su cuenta'  mod='paytpv'}    	
		</a>
	</li>   
	<li>&nbsp;&nbsp;</li>    
	<li>    	
		<a href="{$base_dir}">    		
			<img src="{$base_dir}img/admin/home.gif" alt="{l s='Inicio' mod='paytpv'}" class="icon" />&nbsp;{l s='Inicio'  mod='paytpv'}    	
		</a>    
	</li>
</ul>