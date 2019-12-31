<?php

namespace Drupal\commerce_payabbhi\PluginForm\OffsiteRedirect;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm as BasePaymentOffsiteForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\ExpressionLanguage\Tests\Node\Obj;

/**
 * Provides the Off-site payment form.
 */
class PayabbhiForm extends BasePaymentOffsiteForm {


  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    global $base_url;

    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $this->entity;

    /** @var \Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface $payment_gateway_plugin */
    $payment_gateway_plugin = $payment->getPaymentGateway()->getPlugin();

    $owner = \Drupal::routeMatch()->getParameter('commerce_order')->getCustomer();
    $order_id = \Drupal::routeMatch()->getParameter('commerce_order')->id();
    $order = Order::load($order_id);

    $billing_profile = $order->getBillingProfile();

    $address = $order->getBillingProfile()->address->first();

    $amount = ($payment->getAmount()->getNumber()) * 100;

    $key_id = $payment_gateway_plugin->getConfiguration()['key_id'];
    $key_secret = $payment_gateway_plugin->getConfiguration()['key_secret'];
    $currency = $payment->getAmount()->getCurrencyCode();
    $receipt = $order_id;
    $payment_capture = FALSE;
    $client = new \Payabbhi\Client($key_id, $key_secret);
//    $api = new Api($key_id, $key_secret);
    $razorpay_order = $client->order->create(array('merchant_order_id' => $order_id,
                     'amount' => $amount,
                     'currency' => 'INR',
                     'notes' => array('item_count' => '2',
                                      'sales_channel' => 'website')));


    
    $merchant_order_id = $razorpay_order->id;
    $order->setData('merchant_order_id', $merchant_order_id);
    $order->save();

    $payment_method =$payment_gateway_plugin->getConfiguration();

    // Attach library.
    $form['#attached']['library'][] = 'commerce_payabbhi/commerce_payabbhi.payment';
    $form['#attached']['drupalSettings']['commerce_razorpay'] = array(
      'amount' => $amount,
      'key' => $key_id,
      'logo' => $base_url . "/" . drupal_get_path('module', 'commerce_payabbhi') . '/paylogo.svg',
      'order_id' => $merchant_order_id,
      'commerce_order_id' => $order_id,
      'payment_settings' => $payment_method,
      'name' => $address->getGivenName(). " " . $address->getFamilyName(),
      'address' => $address->getAddressLine1() . " " . $address->getLocality() . " " . $address->getAdministrativeArea(),
      'email' => $order->getEmail(),
    );

    return $this->buildRedirectForm($form, $form_state);
  }

  protected function buildRedirectForm(array $form, FormStateInterface $form_state) {

    return $form;
  }

}
