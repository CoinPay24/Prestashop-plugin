
<?php
class CoinPay24CallbackModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $postData = $_POST;

        if (!isset($postData['verify_hash'])) {
            http_response_code(400);
            die('Invalid callback');
        }

        $apiKey = Configuration::get('COINPAY24_API_KEY');
        $verifyHash = $postData['verify_hash'];
        unset($postData['verify_hash']);
        ksort($postData);

        $generatedHash = hash_hmac('sha256', http_build_query($postData), $apiKey);

        if ($verifyHash !== $generatedHash) {
            http_response_code(400);
            die('Invalid hash');
        }

        if ($postData['status'] === 'completed') {
            $order = new Order((int)$postData['order_id']);
            $order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
        }
    }
}
?>
