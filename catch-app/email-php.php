<?php
if ($_POST['name']=='') {
    
    echo json_encode(array('success'=>0,'message'=>'This field is required', 'type'=>4));
    exit;
}
if ($_POST['email']=='') {
    
    echo json_encode(array('success'=>0,'message'=>'This field is required', 'type'=>1));
    exit;
}
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
 
    echo json_encode(array('success'=>0,'message'=>'Invalid email format','type'=>2));
    exit;
}



if(isset($_POST['name'],$_POST['email']))
{
    $name = isset($_POST['name'])?$_POST['name']:'';
    $email = isset($_POST['email'])?$_POST['email']:'';

    $phone = isset($_POST['phone'])?$_POST['phone']:'';
    if($phone==''){$phone='......';}

    // $number = isset($_POST['number'])?$_POST['number']:'';
    // if($number==''){$number='......';}

    // $subject = isset($_POST['subject'])?$_POST['subject']:'';
    // if($subject==''){$subject='......';}

    $message = isset($_POST['message'])?$_POST['message']:'';
    if($message==''){$message='......';}

            //$to = "beautybidllcdeveloper@gmail.com";
            //$to = "hardeep.s@iapptechnologiesllp.com";
            $to = "catchapp.ug@gmail.com";
            $email_subject='Catch app contact form';

            $headers = "From: support@catchapp.live\r\n";
            $headers .= "Reply-To: support@catchapp.live\r\n";
            //$headers .= "Return-Path: iappphp18@gmail.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
            $headers .= "X-Priority: 3\r\n";
            $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;
           // $headers .= "CC: sombodyelse@example.com\r\n";
            //$headers .= "BCC: hidden@example.com\r\n";


            //$headers  = 'MIME-Version: 1.0' . "\r\n";
           // $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
           // $headers .= 'From: iappphp18@gmail.com' . "\r\n".
            //'Reply-To: iappphp18@gmail.com'."\r\n" . 
           // 'X-Mailer: PHP/' . phpversion();
            //$headers .= 'CC: lakhwinder.r@iapptechnologies.com\r\n';

            $message = '<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="HandheldFriendly" content="true" />
  <meta name="MobileOptimized" content="320" />
  <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, width=device-width, user-scalable=no" />
  <title>Sportifi</title>
  <link rel="icon" type="image/png" href="images/fav.png">
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet"> </head>
<body style="background-color:#eff2f7;
       font-family: "Roboto", sans-serif;
       padding:0;
       margin:0;
       font-weight: normal;
       font-size: 15px;
       color: #2e2e2e;
       line-height: 1.6em;
       vertical-align:middle;
       padding:20px;">
  <table style="width:100%;
            max-width:505px;
            margin:0px auto;
            background-color:#fff;
            border-collapse: collapse;
            box-sizing: border-box;
          display:block;
          padding:30px;
          border-radius:5px;
          text-align:left;">
      <thead style="display:block">
      <tr  style="display:block">
        <th colspan="1"
          style="font-weight: normal;
               text-align:left;
               display:block">
          <img style="width:235px;
                margin:10px auto 30px;"
             src="">
        </th>
      </tr>
      </thead>
      <tbody  style="display:block">
      <tr  style="display:block">
        <td colspan="1"
          style="font-weight: normal;
               display:block">
          <h1 style="font-size: 15px;
                margin:0 0 20px;
                 text-align:left;
                 ">Name:<span style="margin-left:15px;">'.$name.'</span>
          </h1>
          <p style="font-size: 15px;
                               margin:0 0 20px;
                 font-weight: normal;
                 text-align:left;"><span >E-mail:</span><span style="margin-left:15px;">'.$email.'</span>
          </p>
          <p style="font-size: 15px;
                               margin:0 0 30px;
                 font-weight: normal;
                 text-align:left;"><span >Message:</span> <span style="margin-left:11px;">'.$message.'</p>
          
        </td>
      </tr>
      </thead>
  </table>
</body>
</html>';
        

    // Sending email
    mail($to, $email_subject, $message, $headers);
        
      
    echo json_encode(array('success'=>1,'message'=>'Your mail has been sent successfully.'));
    exit;
      
}

?>
