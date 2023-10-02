
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

//$nylas= strip_tags($_POST[nylas']);

// Make API Call to NYLAS AI
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

echo "<div style='color:white;background:green;padding:10px;'>&nbsp;&nbsp;&nbsp;&nbsp;<h3>Business Contacts List Successfully Loaded</h3></div>";

}
else {
echo "<div style='color:white;background:red;padding:10px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<h3>No  Contacts List  Found  Via Nylas API. Please Create New Business Contacts</h3></div>";
exit();

}   

echo "
<br><div class='well'>

<b>Send Bulk Email to all Users/Customers in Your Contact List at once....</b><br>

<button type='button'  class='btn btn-danger btn-sm' data-toggle='modal' data-target='#myModal_em_bulk'>Send Bulk EMail Via Nylas</button>


</div><br>";


echo '<div class="row"><div class="col-sm-1"></div>
<div class="col-sm-10">
<table border="0" cellspacing="2" cellpadding="2" class="table table-striped_no table-bordered table-hover"> 
      <tr> 
<th> <font face="Arial">SurName</font> </th> 
          <th> <font face="Arial">Middle Name</font> </th> 
          <th> <font face="Arial">Given Name</font> </th> 
          <th> <font face="Arial">Email</font> </th> 
<th> <font face="Arial">Job Title</font> </th> 
<th> <font face="Arial">Action</font> </th> 


      </tr>';

foreach($json as $row){

$account_id = $row['account_id'];
$given_name = $row['given_name'];
$id = $row['id'];
$middle_name = $row['middle_name'];
$surname = $row['surname'];
$job_title = $row['job_title'];
$email = $row['emails'][0]['email'];

$fullname ="$surname $middle_name $given_name";



 echo "<tr class='rec_$id' > 

<td>

<b>$surname</b>
</td>

         
                  
                  <td>$middle_name</td>



                  <td>$given_name</td> 

                  <td>$email</td> 
 <td>$job_title</td>  
                 
 <td>
<button type='button'  class='btn btn-primary btn-xs btn_call' data-toggle='modal' data-target='#myModal_em'
data-id='$id'
data-fullname='$fullname'
data-email='$email'

>Send EMail Individual Via Nylas</button>


   <div style='display:none' class='loader-delete_$id'></div>
   <div style='display:none' class='result-delete_$id'></div>
<button style='display:none' class='btn btn-danger delete_btn' data-id='$id'  title='Delete User' disabled>Delete Contact</button>


</td>
              </tr>";





}

echo "</div><div class='col-sm-1'></div></div>";







}
else{
echo "<div id='' style='background:red;color:white;padding:10px;border:none;'>
Direct Page Access not Allowed<br></div>";
}


?>
