<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
// temporarly extend time limit
set_time_limit(300);
error_reporting(0);

if (isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {

//include('data6rst.php');
include('settings.php');

if($nylas_accesstoken ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Please Ask Admin to Set Nylas API Access Token  at <b>settings.php</b> File</div><br>";
exit();

}



if($admin_name ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Please Ask Admin to Set Site Admin Name at <b>settings.php</b> File</div><br>";
exit();

}


if($admin_email ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Please Ask Admin to Set Site Admin Email at <b>settings.php</b> File</div><br>";
exit();

}






$url ="https://api.nylas.com/contacts?limit=10&phone_number=08064242019";



$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
//curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', "Authorization: Bearer $nylas_accesstoken"));  
//curl_setopt($ch, CURLOPT_POSTFIELDS, $data_param);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
 $output = curl_exec($ch); 

if($output == ''){
echo "<div style='background:red;color:white;padding:10px;border:none;'>API Call to Nylas API Failed. Ensure there is an Internet  Connections...</div><br>";
exit();
}




$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// catch error message before closing
if (curl_errno($ch)) {
   // echo $error_msg = curl_error($ch);
}

curl_close($ch);


$json = json_decode($output, true);
$account_id = $json[0]["account_id"];


$mx_error = $json["message"];
if($mx_error != ''){
echo "<div style='background:red;color:white;padding:10px;border:none;'>Nylas API Error Message: $mx_error</div><br>";
exit();
}



if($account_id != ''){

//echo "<div style='color:white;background:green;padding:10px;'>&nbsp;&nbsp;&nbsp;&nbsp;<h3>Business Contacts Successfully Loaded</h3></div>";

}
else {
echo "<div style='color:white;background:red;padding:10px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<h3>Email Cannot be Sent. No  Contacts  Found  Via Nylas API. Please Create New Business Contacts</h3></div>";
exit();

}   


foreach($json as $row){


ini_set('max_execution_time', 300); //300 seconds = 5 minutes
// temporarly extend time limit
set_time_limit(300);


$account_id = $row['account_id'];
$given_name = $row['given_name'];
$id = $row['id'];
$middle_name = $row['middle_name'];
$surname = $row['surname'];
$job_title = $row['job_title'];
$email = $row['emails'][0]['email'];
$fullname= "$surname $given_name $middle_name";





$subject= strip_tags($_POST['em_title']);

$message= $_POST['email_message'];
$message1 = str_replace("�", '', $message);
$message = str_replace("'", '', $message1);
$message_send = trim(str_replace("\r\n", "", $message1));


$data_param= '{
  "subject": "'.$subject.'",
  "to": [
    {
       "email": "'.$email.'",
      "name": "'.$fullname.'"
    }
  ],
  "from": [
    {
      "name": "'.$admin_name.'",
      "email": "'.$admin_email.'"
    }
  ],
  "reply_to": [
    {
      "name": "'.$admin_name.'",
      "email": "'.$admin_email.'"
    }
  ],
  "body": "'.$message_send.'"
}';


$url ="https://api.nylas.com/send";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', "Authorization: Bearer $nylas_accesstoken"));  
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_param);
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$output = curl_exec($ch); 


$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// catch error message before closing
if (curl_errno($ch)) {
    //echo $error_msg = curl_error($ch);
}

curl_close($ch); 


$json = json_decode($output, true);
$account_id = $json["account_id"];
$id = $json["id"];

$mx_error = $json["message"];
if($mx_error != ''){
echo "<div style='background:red;color:white;padding:10px;border:none;'>API Error Message: $mx_error</div><br>";
exit();
}



if($account_id != ''){

echo "<div style='background:green;color:white;padding:10px;border:none;'> Email successful Sent Via Nylas Rest API</div><br>";

//echo "<script> alert('Email successfully Sent Via Nylas Rest API');</script>";
}else{


echo "<div style='background:red;color:white;padding:10px;border:none;'> Email sending Via Nylas Rest API Failed. Ensure that everything is set </div><br>";

}







}




}
else{
echo "<div id='' style='background:red;color:white;padding:10px;border:none;'>
Direct Page Access not Allowed<br></div>";
}


?>