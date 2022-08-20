<?php

// Data Stored into Database
$host = 'localhost';
$username = '<UserName>';
$password = '<UserPassword>';
$db = '<DataBaseName>';

$connect = new mysqli($host, $username, $password, $db);
if ($connect->connect_error) {
    die('Connection failed: ' . $connect->connect_error);
}

// date_default_timezone_set("Asia/Karachi");

function filterData(&$str)
{
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if (strstr($str, '"')) {
        $str = '"' . str_replace('"', '""', $str) . '"';
    }
}

// Excel File name For Download
$fileName = 'Export_Data' . date('Ymd') . '.xls';

// Column Names
$fields = [
    'SR.No',
    'First Name',
    'Last Name',
    'Phone Number',
    'Email Address',
    'Lead Status',
];

// Display Column Name as First Row
$excelData = implode("\t", array_values($fields)) . "\n";

// Get Records From DataBase
$query = $connect->query('Your Query');

if ($query->num_rows > 0 && $query->num_rows != 0) {
    $i = 0;
    while ($row = $query->fetch_assoc()) {
        $i++;
        $Name = explode(' ', $row['Client_Name']);
        $First_Name = $Last_Name = '';
        $rowData = [
            $i,
            !empty($Name[0]) ? $Name[0] : 'Null',
            !empty($Name[1]) ? $Name[1] : 'Null',
            $row['Phone_Number'] ? $row['Phone_Number'] : 'Null',
            $row['Email_address'] ? $row['Email_address'] : 'Null',
            $row['Status'],
        ];
        array_walk($rowData, 'filterData');
        $excelData .= implode('/t', array_values($excelData)) . "\n";
    }
} else {
    $excelData .= 'No Records Found' . "\n";
}

// Headers For Download
header('Content-Type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=\"$fileName\"");

// Render Excel Data
echo $excelData;

exit();
?>