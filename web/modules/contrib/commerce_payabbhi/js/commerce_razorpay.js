(function($) {
  Drupal.behaviors.commerce_razorpay = {
    attach: function(context, settings) {

      var amount = settings.commerce_razorpay.amount;
      var key = settings.commerce_razorpay.key;
      var logo = settings.commerce_razorpay.logo;
      var merchant_order_id = settings.commerce_razorpay.order_id;
      var commerce_order_id = settings.commerce_razorpay.commerce_order_id;
      var payment_settings = JSON.stringify(settings.commerce_razorpay.payment_settings);
      var name = settings.commerce_razorpay.name;
      var address = name + ' ' + settings.commerce_razorpay.address;
      var email = settings.commerce_razorpay.email;
      var opt = {
        'access_id': key,
        'order_id': merchant_order_id,
        'amount': amount, // 100 paise = INR 1
        'description': "Purchase of Kryptonite",
        'image': logo,
        'handler': function(response) {
          window.location = '/srmsteel/web/capture-payment?amount=' + amount + '&order_id=' + commerce_order_id + '&payment_settings=' + payment_settings + '&response=' + JSON.stringify(response);
        },
        
        'prefill': {
          'name': name,
          'email': email,
          'contact': '',
          'method': '' // eg: card, etc.
        },
        'notes': {
          'merchant_order_id': merchant_order_id
        },
        'theme': {
          'color': '#fd698c',
          'emi_mode': true
        }
      };

 $('#edit-actions-next').click(function() {
        var rzp1 = new Payabbhi(opt);
        rzp1.open();
      });

      var rzp1 = new Payabbhi(opt);
      rzp1.open();
    }
  };
}(jQuery));
