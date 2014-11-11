<p class="payment_module">
    {if $cc!=''}
    {l s='Si continua realizará el pago con la tarjeta %s.' mod='paytpv'}
        <button type="button" onclick="jQuery('#iframe_container').show();">{l s='Usar otra tarjeta' mod='paytpv'}</button>
    {/if}
    <div id="iframe_container">
    <iframe src="https://secure.paytpv.com/gateway/bnkgateway.php?{$query}"
            name="paytpv" style="width: 670px; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-style: initial; border-color: initial; border-image: initial; height: 322px; " marginheight="0" marginwidth="0" scrolling="no"></iframe>
    </div>
</p>
{if $cc!=''}
<script>
    jQuery('#iframe_container').hide();
</script>
{/if}