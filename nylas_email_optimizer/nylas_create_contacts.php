
<?php


ini_set('max_execution_time', 300); //300 seconds = 5 minutes
// temporarly extend time limit
set_time_limit(300);

//error_reporting(0);


if (isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {

include('settings.php');



if($nylas_accesstoken ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Please Ask Admin to Set Nylas API Access Token  at <b>settings.php</b> File</div><br>";
exit();

}


if($chatgpt_accesstoken ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Please Ask Admin to Set Chatgpt Access Token at <b>settings.php</b> File</div><br>";
exit();

}

$given_name= strip_tags($_POST['given_name']);
$middle_name= strip_tags($_POST['middle_name']);
$surname= strip_tags($_POST['surname']);
$contact_email= strip_tags($_POST['contact_email']);
$job_title= strip_tags($_POST['job_title']);

// Make API Call to NYLAS AI
$url ="https://api.nylas.com/contacts";
$data_param = '{
  "emails": [
    {
      "email": "'.$contact_email.'",
      "type": "Customer"
    }
  ],
  "given_name": "'.$given_name.'",
  "job_title": "'.$job_title.'",
  "middle_name": "'.$middle_name.'",
  "surname": "'.$surname.'",
 "phone_numbers": [
    {
      "number": "08064242019",
      "type": "business"
    }
  ]
}';


/*
$data_param = '{
  "emails": [
    {
      "email": "'.$contact_email.'",
      "type": "Customer"
    }
  ],
  "given_name": "'.$given_name.'",
  "job_title": "'.$job_title.'",
  "middle_name": "'.$middle_name.'",
  "surname": "'.$surname.'",
 "phone_numbers": [
    {
      "number": "08064242019",
      "type": "business"
    }
  ]
}';

*/


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', "Authorization: Bearer $nylas_accesstoken"));  
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_param);
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
echo $output = curl_exec($ch); 

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
$account_id = $json["account_id"];
$id = $json["id"];


$mx_error = $json["message"];
if($mx_error != ''){
echo "<div style='background:red;color:white;padding:10px;border:none;'>Nylas API Error Message: $mx_error</div><br>";
exit();
}



if($account_id != ''){

echo "<div style='color:white;background:green;padding:10px;'>Contacts Successfully Created</div>";
echo "<script>alert('Contacts Successfully Created'); location.reload();</script>";

}
else {
echo "<div style='color:white;background:red;padding:10px;'>There is an Issue creating Via Nylas AI. Please Check Internet Connections</div>";
exit();

}   




}
else{
echo "<div id='' style='background:red;color:white;padding:10px;border:none;'>
Direct Page Access not Allowed<br></div>";
}


?>
