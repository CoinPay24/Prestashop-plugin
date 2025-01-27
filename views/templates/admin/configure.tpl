
<form action="" method="post">
    <label for="COINPAY24_API_KEY">API Key</label>
    <input type="text" name="COINPAY24_API_KEY" value="{$COINPAY24_API_KEY}" />
    <button type="submit" name="submitCoinPay24" class="btn btn-primary">Save</button>
</form>
{if isset($confirmation)}<p>Settings updated successfully!</p>{/if}
