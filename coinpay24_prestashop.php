
<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class CoinPay24_Prestashop extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'coinpay24_prestashop';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Your Name';
        $this->need_instance = 0;

        $this->controllers = array('validation');
        $this->is_eu_compatible = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CoinPay24 Payment Gateway');
        $this->description = $this->l('Accept payments using CoinPay24 cryptocurrency gateway.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('COINPAY24_API_KEY')) {
            $this->warning = $this->l('API Key must be configured before using this module.');
        }
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('paymentOptions') &&
            $this->registerHook('paymentReturn') &&
            Configuration::updateValue('COINPAY24_API_KEY', '');
    }

    public function uninstall()
    {
        return parent::uninstall() && Configuration::deleteByName('COINPAY24_API_KEY');
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitCoinPay24')) {
            Configuration::updateValue('COINPAY24_API_KEY', Tools::getValue('COINPAY24_API_KEY'));
            $this->context->smarty->assign('confirmation', 'ok');
        }

        $this->context->smarty->assign('COINPAY24_API_KEY', Configuration::get('COINPAY24_API_KEY'));
        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        $payment_options = [];

        $newOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $newOption->setCallToActionText($this->l('Pay with CoinPay24'))
            ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
            ->setAdditionalInformation($this->context->smarty->fetch('module:coinpay24_prestashop/views/templates/front/payment_info.tpl'));

        $payment_options[] = $newOption;
        return $payment_options;
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        $this->smarty->assign([
            'status' => 'ok',
            'id_order' => Tools::getValue('id_order')
        ]);

        return $this->fetch('module:coinpay24_prestashop/views/templates/front/payment_return.tpl');
    }
}
?>
