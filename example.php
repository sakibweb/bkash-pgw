
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>SakibWeb</title>
    <meta name="viewport" content="width=device-width", initial-scale="1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrom=1">
    <script src="jquery-1.8.3.min.js"></script>
    <script id="myScript" src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script>
</head>

<body>
    <button id="bKash_button">Pay With bKash</button>

    <script type="text/javascript">
        var accessToken = "<?php echo $idtoken; ?>";

        $(document).ready(function(){
            var paymentConfig = {
                createCheckoutURL: "index.php?createpayment",
                executeCheckoutURL: "index.php?executepayment",
            };

            var paymentRequest = { amount: '100', invoice: 'sakib' };

            bKash.init({
                paymentMode: 'checkout',
                paymentRequest: paymentRequest,
                createRequest: function(request){
                    $.ajax({
                        url: paymentConfig.createCheckoutURL + "&amount=" + paymentRequest.amount + "&invoice=" + paymentRequest.invoice,
                        type: 'POST',
                        contentType: 'application/json',
                        success: function(data) {
                            var obj = JSON.parse(data);
                            if(data && obj.paymentID != null){
                                paymentID = obj.paymentID;
                                bKash.create().onSuccess(obj);
                            }
                            else {
                                console.log('error');
                                bKash.create().onError();
                            }
                        },
                        error: function(){
                            console.log('error');
                            bKash.create().onError();
                        }
                    });
                },
                
                executeRequestOnAuthorization: function(){
                    $.ajax({
                        url: paymentConfig.executeCheckoutURL + "&paymentID=" + paymentID,
                        type: 'POST',
                        contentType: 'application/json',
                        success: function(data){
                            data = JSON.parse(data);
                            if(data && data.paymentID != null){
                                let payOK = JSON.stringify(data);
                                console.log(payOK);
                                alert('[SUCCESS] data : ' + payOK);                            
                            }
                            else {
                                bKash.execute().onError();
                            }
                        },
                        error: function(){
                            bKash.execute().onError();
                        }
                    });
                }
            });
        });

        function callReconfigure(val){
            bKash.reconfigure(val);
        }

        function clickPayButton(){
            $("#bKash_button").trigger('click');
        }
    </script>
</body>
</html>
