<?php

namespace Drupal\commerce_payabbhi\Controller;
use Drupal\commerce_order\Entity\Order;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Controller
 * @package Drupal\commerce_payabbhi\Controller
 */
class Controller extends ControllerBase{

  public function capturePayment() {
    $amount = $_GET['amount'];
    $commerce_order_id = $_GET['order_id'];
    $payment_settings = json_decode($_GET['payment_settings']);
    $response = json_decode($_GET['response']);
    $razorpay_signature = $response->payment_signature;
    $razorpay_payment_id = $response->payment_id;
    $razorpay_order_id = $response->order_id;
    $key_id = $payment_settings->key_id;
    $key_secret = $payment_settings->key_secret;
    $client = new \Payabbhi\Client($key_id, $key_secret);
    if($payment->status == 'authorized') {
      $payment->capture(array('amount' => $amount));
    }
    // @TODO Save payment method details in order object.

    // Validating  Signature.
    $success = true;
    $error = "Payment Failed";

   
      $client = new \Payabbhi\Client($key_id, $key_secret);
        $attributes = array(
          'payment_id' => $razorpay_payment_id,
          'order_id' => $razorpay_order_id,
          'razorpay_signature' => $razorpay_signature
        );
      try
      {

        $client->utility->verifyPaymentSignature($attributes);
      }
      catch (\Payabbhi\Error\SignatureVerification $e)
      {
        $success = false;
        $error = 'Payabbhi Error : ' . $e->getMessage();

      }
    

    // If Payment is successfully captured at razorpay end.
    if ($response->is_error === true) {
      $message = "Payment ID: {$razorpay_payment_id}";
      drupal_set_message(t($message));
    }
    else {
      $message = "Your payment failed " . $error;
      drupal_set_message(t($message), 'error');
    }
    $url =  Url::fromRoute('commerce_payment.checkout.return', [
      'commerce_order' => $commerce_order_id,
      'step' => 'payment',
    ], ['absolute' => TRUE])->toString();
    return new RedirectResponse($url);

  }
}
