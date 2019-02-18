<?php

include 'conn.php';

// *** Function to get data from Auris START ***
function GetData($url, $options) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $options);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $retValue = curl_exec($ch);
    curl_close($ch);
    return $retValue;
}

// *** Function to get data from Auris END ***
// *** Function to Display Access Numbers START ***
function DisplayAccessNumbers($array) {
    foreach ($array as $arr_value) {
        foreach ($arr_value as $key => $value) {
            if ($key == 'AccessNumber') {
                echo $key . " = ";
                echo $value . "<br/>";
            }
        }
    }
}

// *** Function to Display Access Numbers END ***
// *** Function to Display Rates START ***
function DisplayRates($array) {
    foreach ($array as $arr_value) {
        foreach ($arr_value as $key => $value) {
            echo $arr_value['RegionName'] . " = ";
            echo $arr_value['RegionRate'] . "<br/>";
        }
    }
}

// *** Function to Display Rates END ***
// *** Function to encode POST fields START ***
function preparePostFields($array) {
    $params = array();

    foreach ($array as $key => $value) {
        $params[] = $key . '=' . urlencode($value);
    }

    return implode('&', $params);
}

// *** Function to encode POST fields END ***
// *** Start definition Get Access numbers ***
function GetAccess($card, $db_name, $db_host, $db_user, $db_pwd) {


    $sql = "SELECT Area, City, State, Number, PinRequired, Surcharge FROM AccessNumbers WHERE ProductID=" . $card . " ORDER BY Area ASC";

    $result = mysql_query($sql);

    $Records = array();

    while ($row = mysql_fetch_assoc($result)) {
        $Records[] = array("Area" => $row['Area'], "City" => $row['City'], "State" => $row['State'], "Number" => $row['Number'], "PIN" => $row['PinRequired'], "Surcharge" => $row['Surcharge']);
    }
    return $Records;
}

// *** END definition Get Access numbers ***
// *** Start definition Get Rates ***
function GetRates($url, $OptArr, $prod_id, $region) {
    $OptArr['transaction_type'] = 129;
    $OptArr['product_id'] = $prod_id;
    $OptArr['region_filter'] = $region;

// Prepares the Post fields
    $PostOptions = preparePostFields($OptArr);

// Gets data from Auris
    $raw_data = GetData($url, $PostOptions);

// Converts XML result into array
    $xmlArr = json_decode(json_encode((array) simplexml_load_string($raw_data)), 1);

// Trim Array with results
    $Records = $xmlArr['RecordList']['Record'];

// Display Rates
//DisplayRates($Records);

    return $Records;
}

// *** END definition Get Rates ***
// *** START definition Free PIN registration ***
function RegisterFreePIN($ani) {
    $url = 'https://auristechnology.com/webservices/testtransactions.asp';
    $OptArr = array('user_id' => 'scretail');
    $OptArr['user_pw'] = 'scr1111';
//    $OptArr['response_type'] = '03';
    $OptArr['response_type'] = '02';
    $OptArr['transaction_type'] = 118;
    $OptArr['product_id'] = 10056;
    $OptArr['transaction_amount'] = 200;
    $OptArr['ani_number'] = $ani;

// Prepares the Post fields
    $PostOptions = preparePostFields($OptArr);

// Gets data from Auris
    $raw_data = GetData($url, $PostOptions);

    $Records = array();
    foreach (explode('&', $raw_data) as $chunk) {
        $param = explode("=", $chunk);
        $Records[urldecode($param[0])] = urldecode($param[1]);
    }

    return $Records;
}

function RedFreePIN($ani) {
    $url = 'https://auristechnology.com/webservices/testtransactions.asp';
    $OptArr = array('user_id' => 'scretail');
    $OptArr['user_pw'] = 'scr1111';
//    $OptArr['response_type'] = '03';
    $OptArr['response_type'] = '02';
    $OptArr['transaction_type'] = 118;
    $OptArr['product_id'] = 10060;
    $OptArr['transaction_amount'] = 50;
    $OptArr['ani_number'] = $ani;

// Prepares the Post fields
    $PostOptions = preparePostFields($OptArr);

// Gets data from Auris
    $raw_data = GetData($url, $PostOptions);

    $Records = array();
    foreach (explode('&', $raw_data) as $chunk) {
        $param = explode("=", $chunk);
        $Records[urldecode($param[0])] = urldecode($param[1]);
    }

    return $Records;
}

// *** END definition Free PIN registration ***
// Get Access numbers
//GetAccessNumbers($URL,$OptArr,'10031');
// Get product rates
//GetRates($URL,$OptArr,'10023','C');
?>
