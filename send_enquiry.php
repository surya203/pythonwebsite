<?php
require_once('config.php');
if($_POST){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $course = $_POST['course'];
    $date = date('Y-m-d');
    $ins = mysqli_query($conn,"insert into enquires(name,email,mobile,course,created_on) values('$name','$email','$mobile','$course','$date')") or die(mysqli_error($conn));
    if($ins){
        $res['status'] = 'success';
        $res['message'] = 'Thank You for contacting us, We will get back to you soon';
    }else{
        $res['status'] = 'error';
        $res['message'] = 'Something went wrong, please try again later';
    }
    echo json_encode($res);
}