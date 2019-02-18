<?php

session_start();
include 'conn.php';
include 'webservices.php';

function SendEMail($to, $body, $subject) {
    $headers = array();
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-type: text/plain; charset=iso-8859-1";
    $headers[] = "From: Airtel Direct <customerservice@airteldirect.com>";
    $headers[] = "Reply-To: Airtel Direct Customer Service <customerservice@airteldirect.com>";
    $headers[] = "X-Mailer: PHP/" . phpversion();

    mail($to, $subject, $body, implode("\r\n", $headers));
}

function Email_FreePIN($to) {
    $check_email_query = "SELECT ";
    $check_email_query .= "id ";
    $check_email_query .= "FROM free_pins ";
    $check_email_query .= "WHERE ";
    $check_email_query .= "email = '$to'";

    $check = mysql_query($check_email_query);
//        $row = mysql_fetch_array($check);
    $num_check = mysql_num_rows($check);
    if ($num_check > 0) {
        $_SESSION['msg'] = "We have sent a PIN to this email address already!";
        header("Location: ../try_us_form.php");
    } else {
        $get_pin_query = "SELECT ";
        $get_pin_query .= "id, pin ";
        $get_pin_query .= "FROM free_pins ";
        $get_pin_query .= "WHERE free='Y' ";
        $get_pin_query .= "ORDER BY id ASC ";
        $get_pin_query .= "LIMIT 1";

        $get_pin = mysql_query($get_pin_query);
        if (mysql_num_rows($get_pin) > 0) {
            $row = mysql_fetch_array($get_pin);
            $id = $row['id'];
            $pin = $row['pin'];
            $date = date("Y-m-d H:i:s");
            $ip = $_SERVER['REMOTE_ADDR'];
            $set_pin_query = "UPDATE free_pins SET date='$date', free='N', email='$to', origin_ip='$ip' WHERE id='$id'";
            mysql_query($set_pin_query) or die(mysql_error());

            $subject = "Airtel Direct PIN request";
            $body = "Thank you for giving us a try.\n\n
Your PIN is: $pin.\n
Access number: (800) 706-5369\n\n
Simply dial the access number listed above, select the language of your choice, enter your PIN and follow the instructions.\n\n
Once you have used your test PIN, go to http://www.airteldirect.com to register your new account.\n\n
We look forward to providing you with the best quality and rates on the market.\n\n\n
Sincerely,\n
Airtel Direct Customer Service";

            SendEMail($to, $body, $subject);
            $_SESSION['msg'] = "Thank you for giving us a try. You will receive an email with your PIN and instructions shortly.";
            header("Location: ../try_us_form.php");
//                echo "Thank you for giving us a try. You will receive an email with your PIN and instructions shortly.";
        } else {
            $_SESSION['msg'] = "Error ocurred getting test PIN, please try again.";
            header("Location: ../try_us_form.php");
        }
    }
}

function SendSMS($number, $msg) {
    echo "SendSMS <br>";
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
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);

    $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo $URL;
    return $response;
}

function GeneratePIN($phone, $date, $salesrep, $lang) {
    if ($lang == 'SP') {
        $msg = "Disfrute de su cuenta gratis de $2. Para llamar: marque 305-938-5392 y siga las instrucciones. ";
        $msg .= "Necesita ayuda? Llame a 800-460-2855 o en www.airteldirect.com";
    } else {
        $msg = "Enjoy your $2 free account. To call: Dial 305-938-5392 and follow prompts. ";
        $msg .= "Need help? Call 800-460-2855 or go to www.airteldirect.com";
    }

    $SMS_log_query = "INSERT INTO SMS_pins_log (PhoneNumber, Date, SalesRep, Language) VALUES ('$phone', '$date', '$salesrep', '$lang')";
    $result = mysql_query($SMS_log_query);
    if ($result) {
        echo "Insert success <br>";
//        $Data = RegisterFreePIN($URL, $OptArr, $phone);
        $resp_code = '1';
//        $resp_code = $Data['response_code'];
        $pin = $Data['account_number'];
        $serial = $Data['serial_number'];
        $amount = $Data['transaction_amount'];
        $trans_id = $Data['transaction_id'];

        if ($resp_code == '1') {
            $update_sms_log = "UPDATE SMS_pins_log SET PIN=$pin, SerialNumber=$serial, ";
            $update_sms_log .= "Amount=$amount, TransactionId=$trans_id, ResponseCode=$resp_code ";
            $update_sms_log .= "WHERE PhoneNumber = $phone";
            if (mysql_query($update_sms_log)) {
                SendSMS(substr($phone, 1), $msg);
                $notify = "$phone has just signed up for demo PIN";
                $ph = '9546443027';
                SendSMS($ph, $notify);
            }
        } else {
            $update_sms_log = "UPDATE SMS_pins_log SET TransactionId=$trans_id, ResponseCode=$resp_code ";
            $update_sms_log .= "WHERE PhoneNumber = $phone";
//            if (mysql_query($update_sms_log)) {
//                $msg = "Creation failed";
////                SendSMS(substr($phone, 1), $msg);
//            }
        }
    } else {
        echo "Insert failed";
        if ($lang == 'SP') {
            $msg = "Ya su telefono se encuentra en nuestro sistema de pruebas. Vaya a www.airteldirect.com y registrese para llamar con las mejores tarifas y la mejor calidad.";
        } else {
            $msg = "Your phone number is already regiestered. Go to www.airteldirect.com and sign up to call with the best rates and quality.";
        }
        SendSMS(substr($phone, 1), $msg);
    }
}

function CheckRep($rep) {
    $get_salesrep_qry = "SELECT SalesRepLanguage FROM SalesRepInfo WHERE SalesRepCode = '$rep'";
    $get_salesrep = mysql_query($get_salesrep_qry);
    //echo "$get_salesrep_qry <br>";
    if (mysql_num_rows($get_salesrep) == 0) {
        $result = FALSE;
    } else {
        $result = TRUE;
    }
    return $result;
}

function RegisterPhone($phone, $rep, $lang, $type) {
    $date = date("Y-m-d H:i:s");
    $SMS_log_query = "INSERT INTO SMS_pins_log (PhoneNumber, Date, SalesRep, Language, Type) VALUES ('$phone', '$date', '$rep', '$lang', '$type')";
    $result = mysql_query($SMS_log_query);
    if ($result) {
        echo "Insert success <br>";
        $Data = RegisterFreePIN($phone);
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
                if ($lang == 'SP') {
                    $msg = "Disfrute de su cuenta gratis de $2. Para llamar: marque 305-938-5392 y siga las instrucciones. ";
                    $msg .= "Necesita ayuda? Llame a 800-460-2855 o en www.airteldirect.com";
                } else {
                    $msg = "Enjoy your $2 free account. To call: Dial 305-938-5392 and follow prompts. ";
                    $msg .= "Need help? Call 800-460-2855 or go to www.airteldirect.com";
                }
                SendSMS(substr($phone, 1), $msg);
                $_SESSION['msg'] = $msg;
                header("Location: ../try_us_form.php");
//                $msg2 = "$phone has just signed up for demo PIN";
//                $ph = '9546443027';
//                //$ph = '3053388118';
//                SendSMS($ph, $msg2);
            }
        } else {
            $update_sms_log = "UPDATE SMS_pins_log SET TransactionId=$trans_id, ResponseCode=$resp_code ";
            $update_sms_log .= "WHERE PhoneNumber = $phone";
            if (mysql_query($update_sms_log)) {
                $msg = "There was an error creating your test account. Please try again.";
                $_SESSION['msg'] = $msg;
                header("Location: ../try_us_form.php");
                SendSMS(substr($phone, 1), $msg);
            }
        }
    } else {
        echo "Insert failed";
        if ($lang == 'SP') {
            $msg = "Ya su telefono se encuentra en nuestro sistema de pruebas. Vaya a www.airteldirect.com y registrese para llamar con las mejores tarifas y la mejor calidad.";
        } else {
            $msg = "Your phone number is already registered. Go to www.airteldirect.com and sign up to call with the best rates and quality.";
        }
        SendSMS(substr($phone, 1), $msg);
        $_SESSION['msg'] = $msg;
        header("Location: ../try_us_form.php");
    }
}

function RegisterLandPhone($phone, $rep, $lang, $type, $email) {
    $date = date("Y-m-d H:i:s");
    $SMS_log_query = "INSERT INTO SMS_pins_log (PhoneNumber, Date, SalesRep, Language, Type, Email) VALUES ('$phone', '$date', '$rep', '$lang', '$type', '$email')";
    $result = mysql_query($SMS_log_query);
    if ($result) {
//        echo "Insert success <br>";
        $Data = RegisterFreePIN($phone);
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
                if ($lang == 'SP') {
                    $msg = "Disfrute de su cuenta gratis de $2. Para llamar: marque 305-938-5392 y siga las instrucciones. ";
                    $msg .= "Necesita ayuda? Llame a 800-460-2855 o en www.airteldirect.com";
                } else {
                    $msg = "Enjoy your $2 free account. To call: Dial 305-938-5392 and follow prompts. ";
                    $msg .= "Need help? Call 800-460-2855 or go to www.airteldirect.com";
                }
                $_SESSION['msg'] = $msg;
                $subject = "Free PIN from Airtel Direct";
                SendEMail($email, $msg, $subject);
                header("Location: ../try_us_form.php");
//                $msg2 = "$phone has just signed up for demo PIN";
//                $ph = '9546443027';
//                //$ph = '3053388118';
//                SendSMS($ph, $msg2);
            }
        } else {

            $update_sms_log = "UPDATE SMS_pins_log SET TransactionId=$trans_id, ResponseCode=$resp_code ";
            $update_sms_log .= "WHERE PhoneNumber = $phone";
            if (mysql_query($update_sms_log)) {
                $msg = "There was an error creating your test account. Please try again.";
                $_SESSION['msg'] = $msg;
                $subject = "Error creating your free PIN from Airtel Direct";
                SendEMail($email, $msg, $subject);
                header("Location: ../try_us_form.php");
//                SendSMS(substr($phone, 1), $msg);
            }
        }
    } else {
//        echo "Insert failed";
        if ($lang == 'SP') {
            $msg = "Ya su telefono y/o email se encuentran en nuestro sistema de pruebas. Vaya a www.airteldirect.com y registrese para llamar con las mejores tarifas y la mejor calidad.";
        } else {
            $msg = "Your phone number and/or email are already registered. Go to www.airteldirect.com and sign up to call with the best rates and quality.";
        }
//        SendSMS(substr($phone, 1), $msg);
        $_SESSION['msg'] = $msg;
        $subject = "Error creating your free PIN from Airtel Direct";
        SendEMail($email, $msg, $subject);
        header("Location: ../try_us_form.php");
    }
}

$option = $_POST['selection'];

if (isset($_POST['Submit'])) {
    if ($option === '1') {
        $email = $_POST['email'];
        Email_FreePIN($email);
    } elseif ($option === '2') {
        $phone = preg_replace('/[^0-9]+/', '', $_POST['mob_phone']);
        $phone_type = 'M';
        RegisterPhone($phone, 'web', 'EN', $phone_type);
    } elseif ($option === '3') {
        $phone = preg_replace('/[^0-9]+/', '', $_POST['land_phone']);
        $phone_type = 'L';
        $email = $_POST['email2'];
        RegisterLandPhone($phone, 'web', 'EN', $phone_type, $email);
    }
}
?>
