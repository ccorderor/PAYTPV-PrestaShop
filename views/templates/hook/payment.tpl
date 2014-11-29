<p class="payment_module">

	<a href="javascript:$('#paytpv_form').submit();" title="{l s='Conectar con el TPV' mod='paytpv'}">

		<img src="{$module_dir}/views/img/tarjetas.png" alt="{l s='Conectar con el TPV' mod='paytpv'}" />

		{l s='Pago seguro con tarjeta de crédito' mod='paytpv'}

	</a>

</p>
<form action="https://www.paytpv.com/gateway/fsgateway.php" method="post" id="paytpv_form" class="hidden">

{foreach from=$fields key=k item=v}

    <input type="hidden" name="{$k}" value="{$v}" />

{/foreach}

</form>