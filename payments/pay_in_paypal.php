<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Payment</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AYGxwiqeoV9znqgDbxAjdoTdbdGdFbYyKgkI8s9ByOB9IuslYvKIVlIqgfA2k9EEwnRKCXBQACuyHBm6&currency=PHP"></script>
</head>
<body>
    <h1>Pay with PayPal</h1>
    <div id="paypal-button-container"></div>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            currency_code: 'PHP', // Philippine Peso
                            value: '100.00'      // Total amount
                        },
                        description: 'Payment for Sample Product'
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Transaction completed by ' + details.payer.name.given_name);
                    console.log('Transaction details:', details);
                    // // Redirect or handle success here
                    // window.location.href = '/success.html';
                });
            },
            onCancel: function(data) {
                alert('Payment cancelled');
                // // Redirect or handle cancellation here
                // window.location.href = '/cancel.html';
            },
            onError: function(err) {
                console.error('Error during payment:', err);
                alert('An error occurred during payment.');
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>