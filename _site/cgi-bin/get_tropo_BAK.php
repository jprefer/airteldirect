<?php

include '../cgi-bin/conn.php';
require('../webservices.php');

function SendSMS($number, $msg) {

    $orig_message = preg_replace('/[^\x20-\x7E]/', '', $msg);
    $message = urlencode($orig_message);
    $token = '1d997e0de1568648977b9a21489f86ce8d81e738cbc1488cee0728830911f8cd46ea3e7be79ffa297f35c0e8';
    $URL = "https://api.tropo.com/1.0/sessions?action=create&token=" . $token . "&numberToDial=" . $number . "&msg=" . $message;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);

    $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    return $response;
}

$phone = preg_replace('/[^0-9]+/', '', $_POST['Phone']);
$language = $_POST['Language'];
$date = date("Y-m-d H:i:s");

echo "Orig phone = $phone <br>";

if (substr($phone, 0, 1) == '1' && strlen($phone) == '11') {
////    $new_phone = substr($phone, 1);
    $SMS_log_query = "INSERT INTO SMS_pins_log (PhoneNumber, Date) VALUES ('$phone', '$date')";
    $result = mysql_query($SMS_log_query);
    if ($result) {
        echo "Insert success <br>";
        $Data = RegisterFreePIN($URL, $OptArr, $phone);
//        $resp_code = '1';
        $resp_code = $Data['response_code'];
        $pin = $Data['account_number'];
        $serial = $Data['serial_number'];
        $amount = $Data['transaction_amount'];
        $trans_id = $Data['transaction_id'];

        if ($resp_code == '1') {
            $update_sms_log = "UPDATE SMS_pins_log SET PIN=$pin, SerialNumber=$serial, ";
            $update_sms_log .= "Amount=$amount, TransactionId=$trans_id, ResponseCode=$resp_code ";
            $update_sms_log .= "WHERE PhoneNumber = $phone";
            if (mysql_query($update_sms_log)) {
                if ($language == 'SP') {
                    $msg = "Disfrute de su cuenta gratis de $2. Para llamar: marque 305-938-5392 y siga las instrucciones. ";
                    $msg .= "Necesita ayuda? Llame a 800-460-2855 o en www.airteldirect.com";
                } else {
                    $msg = "Enjoy your $2 free account. To call: Dial 305-938-5392 and follow prompts. ";
                    $msg .= "Need help? Call 800-460-2855 or go to www.airteldirect.com";
                }
                SendSMS(substr($phone, 1), $msg);
                $msg2 = "$phone has just signed up for demo PIN";
                $ph = '9546443027';
                SendSMS($ph, $msg2);
            }
        } else {
            $update_sms_log = "UPDATE SMS_pins_log SET TransactionId=$trans_id, ResponseCode=$resp_code ";
            $update_sms_log .= "WHERE PhoneNumber = $phone";
            if (mysql_query($update_sms_log)) {
                $msg = "Creation failed";
//                SendSMS(substr($phone, 1), $msg);
            }
        }
    } else {
        echo "Insert failed";
        if ($language == 'SP') {
        $msg = "Ya su telefono se encuentra en nuestro sistema de pruebas. Vaya a www.airteldirect.com y registrese para llamar con las mejores tarifas y la mejor calidad.";
        } else {
            $msg = "Your phone number is already regiestered. Go to www.airteldirect.com and sign up to call with the best rates and quality.";
        }
        SendSMS(substr($phone, 1), $msg);
    }
}
?>
