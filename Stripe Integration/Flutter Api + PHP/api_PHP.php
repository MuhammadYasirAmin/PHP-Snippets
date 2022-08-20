<?php
    include_once 'dbConfig.php';
    $response = "";
    $user_row = "";
    
        $card_owner_name = $_POST["card_owner_name"];
        $card_owner_number = $_POST["card_owner_number"];
        $card_MM = $_POST["card_MM"];
        $card_DD = $_POST["card_DD"];
        $card_owner_CVC = $_POST["card_owner_CVC"];
        
        $InstallmentAmount = $_POST["emiAmount"];
        
       
        $query = "SELECT * FROM `tbl_registration` WHERE `unique_id` = '".$Unique_Id."'";
        $queryresult = mysqli_query($conn, $query);
        $count = mysqli_num_rows($queryresult);
        if($count != 0 || $count > 0 || $count == 1){
            $user_row = mysqli_fetch_assoc($queryresult);
         
            $Reg_Email = $user_row["user_Email"];
            $Reg_Name = $user_row["user_Name"];
            
            $Pyament_response = '';
            
            //include Stripe PHP library
            include_once('stripe-php/init.php');  
                
                //set stripe secret key and publishable key
                $stripe = array(
                "secret_key"      => "sk_test_",
                "publishable_key" => "pk_test_"
                );    
            
                \Stripe\Stripe::setApiKey($stripe['secret_key']); 
                
                $stripe = new \Stripe\StripeClient(
                      'sk_test_'
                    );
                    
                    
                $customer = \Stripe\Customer::create(array(
                    'email'			=>	$Reg_Email,
                    'source'		=>	$stripe->tokens->create([
                                              'card' => [
                                                'number' => $card_owner_number,
                                                'exp_month' => intval($card_DD),
                                                'exp_year' => intval($card_MM),
                                                'cvc' => $card_owner_CVC,
                                              ],
                                            ]),
                    'name'			=>	$Reg_Name,
                    'address'		=>	array(
                        'line1'			=>	'$_POST["Reg_Address"]',
                        'postal_code'	=>	'$_POST["Reg_ZipCode"]',
                        'city'			=>	'$_POST["Reg_City"]',
                        'country'		=>	'US'
                    )
                ));
            
                $charge = \Stripe\Charge::create(array(
                    'customer'		=>	$customer->id,
                    'amount'		=>	intval($InstallmentAmount) * 100,
                    'currency'		=>	'USD',
                    'description'	=>	$Installment. "Installment Date = ". $InstallmentDate,
                    'metadata'		=> array(
                        'user_id'		=>	$Unique_Id
                    )
                ));
    
                $Pyament_response = $charge->jsonSerialize();
            
            if ($Pyament_response["amount_refunded"] == 0 && empty($Pyament_response["failure_code"]) && $Pyament_response['paid'] == 1 && $Pyament_response["captured"] == 1 && $Pyament_response['status'] == 'succeeded') {    
                
                $query = "";
                
                if (mysqli_query($conn, $query)) {
                    $response = $Pyament_response['balance_transaction'];
                    return $response;
                } else {
                    $response = mysqli_error($conn);
                    return $response;
                }
            }
        }
           echoData($response);

function echoData($result) {
    echo json_encode($result);
}
?>