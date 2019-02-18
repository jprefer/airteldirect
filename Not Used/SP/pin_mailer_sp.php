<?php

session_start();
include 'cgi-bin/conn.php';

if (isset($_POST['Submit'])) {
    if ($_SESSION['chapcha_code'] == $_POST['chapcha_code'] && !empty($_SESSION['chapcha_code'])) {
        $to = $_POST['email'];

        $check_email_query = "SELECT ";
        $check_email_query .= "id ";
        $check_email_query .= "FROM free_pins ";
        $check_email_query .= "WHERE ";
        $check_email_query .= "email = '$to'";

        $check = mysql_query($check_email_query);
//        $row = mysql_fetch_array($check);
        $num_check = mysql_num_rows($check);
        if ($num_check > 0) {
            echo "Ya hemos enviado un PIN a ese correo electronico anteriormente!";
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
                $set_pin_query = "UPDATE free_pins SET date='$date', free='N', email='$to' WHERE id='$id'";
                mysql_query($set_pin_query) or die(mysql_error());

                $headers = array();
                $headers[] = "MIME-Version: 1.0";
                $headers[] = "Content-type: text/plain; charset=iso-8859-1";
                $headers[] = "From: Airtel Direct <customerservice@airteldirect.com>";
                $headers[] = "Reply-To: Airtel Direct Customer Service <customerservice@airteldirect.com>";
                $headers[] = "X-Mailer: PHP/" . phpversion();

                $subject = "Solicitud de PIN de prueba de Airtel Direct";
                $body = "Gracias por probar nuestro producto.\n\n
Su PIN es: $pin.\n
Numero de acceso: (800) 706-5369\n\n
Simplemente marque el numero de acceso listado arriba, seleccione el idioma de preferencia, introduzca su PIN y siga las instrucciones.\n\n
Una vez que haya usado su PIN de prueba, vaya a http://www.airteldirect.com para registrar su nueva cuenta.\n\n
Esperamos poderle proveer el mejor servicio al mejor precio del mercado.\n\n\n
Sinceramente,\n
Servicio al Cliente de Airtel Direct";

                //echo "Gracias por probar nuestro producto. En un momento estara recibiendo un email con su PIN e instrucciones de uso.";
                mail($to, $subject, $body, implode("\r\n", $headers));
                unset($_SESSION['chapcha_code']);
                header("Location: conf_try_sp.html");
                
                } else {
                echo "Error ocurred getting test PIN, please try again.";
            }
        }
    } else {
        echo 'Disculpe, no coloco el codigo de seguridad correctamente, favor intente de nuevo!';
    }
}
?>