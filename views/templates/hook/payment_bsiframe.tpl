<div class="col-xs-12 col-md-6">
    {if isset($CC)}
    <p class="payment_module paytpv_capture">
        <a href="{$capture_url}" class="bankwire">
            {l s='Si continua realizará el pago con la tarjeta %s.'  sprintf=$CC mod='paytpv'}
        </a>
    </p>
    <p class="paytpv_button">
    <a href="javascript:void(0);" onclick="jQuery('p.paytpv_iframe').show();" title="{l s='Usar los datos de otra tarjeta de crédito' mod='paytpv'}" class="button button-small btn btn-default">
        <span>{l s='Usar otra tarjeta' mod='paytpv'}<i class="icon-chevron-right right"></i></span>
    </a>
    </p>
    {/if}
    <br class="clear"/>
    <p class="payment_module paytpv_iframe">
        <iframe src="https://secure.paytpv.com/gateway/bnkgateway.php?{$query}"
            name="paytpv" style="width: 670px; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-style: initial; border-color: initial; border-image: initial; height: 322px; " marginheight="0" marginwidth="0" scrolling="no"></iframe>
    </p>
</div>

{if isset($CC)}
<script>
    jQuery('p.paytpv_iframe').hide();
</script>
{/if}