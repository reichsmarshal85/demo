<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://paypage-uat.ngenius-payments.com/hosted-sessions/sdk.js"></script>
    </head>
    <body>

    <center>
        <div>
            <div id="loader"><img src="loding.gif"/></div>
            <div id="mount-id" style="max-width: 500px;"></div>
            <div><button onclick="createSession()" class="checkoutButton">PAY NOW</button></div>
            <div id="error-response"></div>
        </div>



        <div id="3ds_iframe"></div>
    </center>
</body>
<script>
	var validationMsg;

    window.NI.mountCardInput('mount-id', {
        style: {
            /*main: {
             padding: '0px'
             },
             base: {
             backgroundColor: '#FFFFFF',
             fontSize: '16px'
             },*/
            input: {
                borderWidth: '1px',
                borderRadius: '5px',
                borderStyle: 'solid',
                backgroundColor: '#FFFFFF',
                borderColor: '#DDDDDD',
                color: '#000000',
                padding: '5px'
            },
            invalid: {
                borderColor: 'red'
            }
        },
        apiKey: 'ZDJkOGJlY2EtYzBiZS00MTQ1LWFiZjYtYzg1MjVlODUxMGI3OjM2NDgyZWRmLTM1NmEtNGRlYy04YjcwLWE4ZTUzMTA5NzljMg==',
        outletRef: '5edab6d7-5946-43f4-b8c7-06b29c272bdd',
        onSuccess: function () {
            $('#loader').hide();
            console.log('Success');
        },
        onFail: function () {
            console.log('Failed');
        },
        onChangeValidStatus: function (statusObj) { //console.log(statusObj);
            validate(statusObj);
        },
    });

    function validate(statusObj) {
    	$('#error-response').html('');
    	validationMsg = '';

        if (statusObj.isPanValid === false) {
            validationMsg = 'PAN Invalid';
        } else if (statusObj.isExpiryValid === false) {
            validationMsg = 'Expiry Invalid';
        } else if (statusObj.isCVVValid === false) {
            validationMsg = 'CVV Invalid';
        } else if (statusObj.isNameValid === false) {
            validationMsg = 'Name Invalid';
        }

        if (validationMsg !== '') {
            $('#error-response').html(validationMsg);
        }
    }

    let sessionId;
    async function createSession() {
    	$('#error-response').html('');

        try {
            const response = await window.NI.generateSessionId();
            //sessionId = JSON.stringify(response.session_id);
            console.log(response.session_id);
            $.ajax({
                dataType: 'json',
                url: "create_payment.php",
                type: "post",
                data: {session_id: response.session_id},
                success: function (data) {
                    //console.log(data);
                    check3ds(data);
                }

            });
        } catch (err) {
            validate(err.payload);
        }
    }
    async function check3ds(data) {
        const {status, error} = await window.NI.handlePaymentResponse(
                data,
                {
                    mountId: '3ds_iframe',
                    style: {width: 500, height: 500}
                }
        );
        console.log(status);
        //console.log(error);
        if (status === window.NI.paymentStates.AUTHORISED || status === window.NI.paymentStates.CAPTURED) {
            // Same as before this signals successful payment
        } else if (status === window.NI.paymentStates.FAILED || status === window.NI.paymentStates.THREE_DS_FAILURE) { // A new state to look out for is 3DS Challenge failure
            // payment failure signal
        }
    }

</script>
</html>



