<?php
if($_POST){
    $name        = $_POST['name'];
    $email       = $_POST['email'];
    $number      = $_POST['mobile'];
    $msg         = $_POST['message'];
    
    $mailSubject = "You Have Recieved An Enquiry on ".date('d-m-Y');
    $sendMessage = '<html><head></head><body>
    <table border="1"><tr><td>Name : </td><td>'.$name.'</td></tr>
    <tr><td>Email : </td><td>'.$email.'</td></tr>
    <tr><td>Number : </td><td>'.$number.'</td></tr>
    <tr><td>Message : </td><td>'.$msg.'</td></tr>
    </table></body></html>';
    
	$fromEmail = $email;
	$toEmail = 'info@pythonlife.in';
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= "From: <$email>" . "\r\n";
	if(mail($toEmail , $mailSubject , $sendMessage , $headers )){
	    $res['status'] = 'success';
	    $res['message'] = 'Thank You for contacting us. We Will get back to you soon.';
	}else{
	    $res['status'] = 'error';
	    $res['message'] = 'Something went wrong, please try again later';
	}
	echo json_encode($res);
}