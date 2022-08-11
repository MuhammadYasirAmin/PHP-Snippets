Upload Library & Make sure that your php file same here

// ================================  HTML Form Code ================

<input type="hidden" name="currency_code" value="USD" />
<input type="hidden" name="token" id="token" value="" />

// ================================  JS Code ================

<script src="https://js.stripe.com/v2/"></script>

<script> 
	Stripe.setPublishableKey('published_key');

            function stripeResponseHandler(status, response) {
                if (response.error) {
                    $('#Estimete_Buy_btn').attr('disabled', false);
                    $('#message').html(response.error.message).show();
                } else {
                    var token = response['id'];
                    $('#token').val(token);
                    document.getElementById("token").value = token;

                    $("form").submit();
                }
            }

            function stripePay(event) {
                event.preventDefault();

                let expDate = $('#card_expiry_month').val();
                let expMonth = expDate.slice(0, 2);
                let expYear = expDate.slice(5, 7);

                $('button').attr('disabled', 'disabled');
                $('button').val('Form Processing....');
                $('button').html('Form Processing....');
                Stripe.createToken({
                    number: $('#card_holder_number').val(),
                    cvc: $('#card_cvc').val(),
                    exp_month: expMonth,
                    exp_year: expYear
                }, stripeResponseHandler);
                return false;
            }
</script>

// ======================== PHP Code =======================

<?php
    session_start();
    require_once 'dbh.inc.php';
    
    if(isset($_POST["token"])){
        
        $card_owner_name = $_POST["card_owner_name"];
        $card_owner_number = $_POST["card_owner_number"];
        $card_MM = $_POST["card_MM"];
        $card_DD = $_POST["card_DD"];
        $card_owner_CVC = $_POST["card_owner_CVC"];
        
        $InstallmentAmount = $_POST["InstallmentAmount"];
        $Installment = $_POST["Installment"];
        $InstallmentDate = $_POST["InstallmentDate"];
        $Unique_Id = intval($_POST["UniqueID"]);
        $PlanID = $_POST["PlanID"];
        $SelectedPlan = $_POST["SelectedPlan"];
        
        $Reg_Email = isset($_POST["Reg_Email"]);
        $Reg_Name = $_POST["Reg_Name"];
        
        $Pyament_response = '';
        
        //include Stripe PHP library
        include_once('stripe-php/init.php');  
            
            //set stripe secret key and publishable key
            $stripe = array(
            "secret_key"      => "seceret_Key",
            "publishable_key" => "published_Key"
            );    
        
            \Stripe\Stripe::setApiKey($stripe['secret_key']); 
        
            $customer = \Stripe\Customer::create(array(
                'email'			=>	$_POST["Reg_Email"],
                'source'		=>	$_POST["token"],
                'name'			=>	$_POST["Reg_Name"],
                'address'		=>	array(
                    'line1'			=>	'$_POST["Reg_Address"]',
                    'postal_code'	=>	'$_POST["Reg_ZipCode"]',
                    'city'			=>	'$_POST["Reg_City"]',
                    'country'		=>	'US'
                )
            ));
        
            $charge = \Stripe\Charge::create(array(
                'customer'		=>	$customer->id,
                'amount'		=>	intval($_POST["InstallmentAmount"]) * 100,
                'currency'		=>	$_POST["currency_code"],
                'description'	=>	$_POST["Installment"]. "Installment Date = ". $_POST["InstallmentDate"],
                'metadata'		=> array(
                    'user_id'		=>	$Unique_Id
                )
            ));

            $Pyament_response = $charge->jsonSerialize();
        
        if ($Pyament_response["amount_refunded"] == 0 && empty($Pyament_response["failure_code"]) && $Pyament_response['paid'] == 1 && $Pyament_response["captured"] == 1 && $Pyament_response['status'] == 'succeeded') {    
            
            $query = "";
            
            if (mysqli_query($conn, $query)) {
                header('location: /yourpath?response='.$Pyament_response["balance_transaction"]);
                exit();
            } else {
                echo  $query." => ".mysqli_error($conn);
            }
        }
        
    }
?>