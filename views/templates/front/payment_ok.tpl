{capture name=path}{l s='Payment completed' mod='paytpv'}{/capture}

{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Payment completed' mod='paytpv'}</h2>
	<img src="{$base_dir}img/admin/icon-valid.png"/>&nbsp;&nbsp;   
	{l s='Thank you for trusting us. Your purchase has been formalized correctly and soon we will process your order.'  mod='paytpv'}
	

<ul class="footer_links">    
	<li>    	
		<a href="{$link->getPageLink('my-account')}" title="{l s='Go to your account'  mod='paytpv'}">    		
			<img src="{$base_dir}img/admin/nav-user.gif" alt="{l s='Go to your account' mod='paytpv'}" class="icon" />&nbsp;{l s='Go to your account'  mod='paytpv'}    	
		</a>
	</li>   
	<li>&nbsp;&nbsp;</li>    
	<li>    	
		<a href="{$base_dir}">    		
			<img src="{$base_dir}img/admin/home.gif" alt="{l s='Home' mod='paytpv'}" class="icon" />&nbsp;{l s='Home'  mod='paytpv'}    	
		</a>    
	</li>
</ul>