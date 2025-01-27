
<?php
class CoinPay24ValidationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cart = $this->context->cart;
        $apiKey = Configuration::get('COINPAY24_API_KEY');

        $data = array(
            'api_key' => $apiKey,
            'order_id' => $cart->id,
            'price_amount' => $cart->getOrderTotal(true, Cart::BOTH),
            'price_currency' => $this->context->currency->iso_code,
            'callback_url' => $this->context->link->getModuleLink('coinpay24', 'callback', [], true),
            'success_url' => $this->context->link->getPageLink('order-confirmation', true),
            'cancel_url' => $this->context->link->getPageLink('order', true),
        );

        $ch = curl_init('https://api.coinpay24.com/v1/invoices/create');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if (isset($responseData['payment_url'])) {
            Tools::redirect($responseData['payment_url']);
        } else {
            die('Error creating invoice: ' . $responseData['error_message']);
        }
    }
}
?>
