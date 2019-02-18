<?php

include 'conn.php';
require('webservices.php');
// Info for Auris web access


function SendSMS($number, $msg) {
    echo "SendSMS <br>";
    $orig_message = preg_replace('/[^\x20-\x7E]/', '', $msg);
    $message = urlencode($orig_message);
    $token = '245cbac491c87740a6c34512e75eef04f108511b796ed4a1405ae890072d3714ca027436757dc0213ff1e8f7';
    $URL = "https://api.tropo.com/1.0/sessions?action=create&token=" . $token . "&numberToDial=" . $number . "&msg=" . $message;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);

    $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo $URL;
    return $response;
}

function CheckRep($rep) {
    $get_salesrep_qry = "SELECT SalesRepLanguage FROM Red_SalesRepInfo WHERE SalesRepCode = '$rep'";
    $get_salesrep = mysql_query($get_salesrep_qry);
    //echo "$get_salesrep_qry <br>";
    if (mysql_num_rows($get_salesrep) == 0) {
        $result = FALSE;
    } else {
        $result = TRUE;
    }
    return $result;
}

$phone = preg_replace('/[^0-9]+/', '', $_POST['Phone']);
if (substr($phone, 0, 1) == '1') {
    $phone = substr($phone, 1);
}
$orig_text = $_POST['Text'];
$date = date("Y-m-d H:i:s");

//error_log("$phone -> $orig_text\n", 3, "/tmp/my-errors.log");

//echo "Orig phone = $phone <br>";

$text = explode(" ", $orig_text);
//echo "Text= $orig_text <br>";
$fields = count($text);
//echo "Fields= $fields <br>";

if(empty($text[1])) {
    $fields = 1;
}

// Check if number is valid
//if (substr($phone, 0, 1) == '1' && strlen($phone) == '10') {
if (strlen($phone) == '10') {    
    // Check if text contains 1 or more words
    if ($fields === 1) {
        $rep = 'None';
        $keyword = strtolower($text[0]);
//        error_log("Orig Keyword is $keyword\n", 3, "/tmp/my-errors.log");
        if ($keyword == 'llamar') {
            $lang = 'SP';
        } elseif (strpos($keyword,'rmw168') !== false) {
            $lang = 'EN';
//            error_log("Keyword is $keyword\n", 3, "/tmp/my-errors.log");
        } else {
            $msg = 'Invalid option, please try again!';
            SendSMS($phone, $msg);
            exit();
        }
    } elseif ($fields > 1) {
        $rep = strtolower($text[0]);
        // Check if Rep is valid
        $GoodRep = CheckRep($rep);
        if ($GoodRep == FALSE) {
            $msg = 'Invalid representative, please try again!';
            SendSMS($phone, $msg);
            exit();
        } else {
            $keyword = strtolower($text[1]);
            // Check if keyword is valid
            if ($keyword == 'llamar') {
                $lang = 'SP';
            } elseif ($keyword == 'call') {
                $lang = 'EN';
            } else {
                $msg = 'Invalid option, please try again!';
                SendSMS($phone, $msg);
                exit();
            }
        }
    }
    // We got valid rep and/or keyword...  

    $SMS_log_query = "INSERT INTO Red_SMS_pins_log (PhoneNumber, Date, SalesRep, Language, Keyword, Type) VALUES ('$phone', '$date', '$rep', '$lang', '$keyword', 'M')";
    $result = mysql_query($SMS_log_query);
    if ($result) {
//        error_log("$SMS_log_query\n", 3, "/tmp/my-errors.log");
//        echo "Insert success <br>";
        $Data = RedFreePIN($phone);
//        $resp_code = '1';
        $resp_code = $Data['response_code'];
        $pin = $Data['account_number'];
        $serial = $Data['serial_number'];
        $amount = $Data['transaction_amount'];
        $trans_id = $Data['transaction_id'];

        if ($resp_code == '1') {
            $update_sms_log = "UPDATE Red_SMS_pins_log SET PIN=$pin, SerialNumber=$serial, ";
            $update_sms_log .= "Amount=$amount, TransactionId=$trans_id, ResponseCode=$resp_code ";
            $update_sms_log .= "WHERE PhoneNumber = '$phone'";
            if (mysql_query($update_sms_log)) {
                if ($lang == 'SP') {
                    $msg = "Disfrute de su cuenta gratis de $2. Para llamar: marque 305-938-5392 y siga las instrucciones. ";
                    $msg .= "Necesita ayuda? Llame a 800-460-2855 o en www.airteldirect.com";
                } else {
                    $msg = "Enjoy a few minutes to try our service. To call: Dial 626-802-4456 and follow prompts. ";
                    $msg .= "Need help? Call 800-535-3080 or go to www.redmobilepinless.com";
                }
                SendSMS($phone, $msg);
//                $msg2 = "$phone has just signed up for demo PIN";
//                $ph = '9546443027';
//                //$ph = '3053388118';
//                SendSMS($ph, $msg2);
            }
        } else {
            $update_sms_log = "UPDATE Red_SMS_pins_log SET TransactionId=$trans_id, ResponseCode=$resp_code ";
            $update_sms_log .= "WHERE PhoneNumber = '$phone'";
            if (mysql_query($update_sms_log)) {
                $msg = "There was an error creating your account. Please try again.";
                SendSMS($phone, $msg);
            }
        }
    } else {
//        $error = mysql_error();
//        error_log("$error\n", 3, "/tmp/my-errors.log");
//        echo "Insert failed";
        if ($lang == 'SP') {
            $msg = "Ya su telefono se encuentra en nuestro sistema de pruebas. Vaya a www.airteldirect.com y registrese para llamar con las mejores tarifas y la mejor calidad.";
        } else {
            $msg = "Your phone number is already registered. Go to www.redmobilepinless.com and sign up to call with the best rates and quality";
        }
        SendSMS($phone, $msg);
    }
}
?>
