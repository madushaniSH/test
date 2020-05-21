<?php

/*
    Filename: forgot_password.php
    Author: Malika Liyanage
    Created: 16/07/2019
    Purpose: Sends the user a password reset link to his email
*/
session_start();
// Redirects the user if the username was not set
// if(!isset($_SESSION['id'])){
//     header("location: login_auth_one.php");
//     exit();
// }
// Current settings to connect to the user account database
require('user_db_connection.php');

// Setting up the DSN
$dsn = 'mysql:host='.$host.';dbname='.$dbname;
/*
    Attempts to connect to the databse, if no connection was estabishled
    kills the script
*/
try{
    // Creating a new PDO instance
    $pdo = new PDO($dsn, $user, $pwd);
    // setting the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    // throws error message
    echo "<p>Connection to database failed<br>Reason: ".$e->getMessage().'</p>';
    exit();
}
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\SMTP;
        use PHPMailer\PHPMailer\Exception;
        
        require 'styles/PHPMailer/src/Exception.php';
        require 'styles/PHPMailer/src/PHPMailer.php';
        require 'styles/PHPMailer/src/SMTP.php';

        if(isset($_SESSION['id'])){
            $display_message='';
            $sql='SELECT account_id,account_email,account_password_reset_request FROM accounts WHERE account_id=:id';
            $stmt=$pdo->prepare($sql);
            $stmt-> execute(['id'=>$_SESSION['id']]);
            $user_information = $stmt ->fetch(PDO::FETCH_OBJ);

            if($user_information->account_password_reset_request== 0){

                $uniqidstr=md5(uniqid(mt_rand()));;
                $sql = 'UPDATE accounts SET account_password_reset_request = :reset_value, account_password_reset_identity = :reset_identity WHERE account_id = :id';
                $stmt = $pdo->prepare($sql);
                $reset_state =1;
                $stmt->execute(['reset_value'=>$reset_state,'id'=>$_SESSION['id'],'reset_identity'=>$uniqidstr]);
                $reset_password_link='http://www.gssdataoperationsdepartment.space/gssdataoperationsdep/reset_password.php?fp_code='.$uniqidStr;
                 
                $emailTo=$user_information->account_email;
                // Instantiation and passing `true` enables exceptions
                $mail = new PHPMailer;
            
            try {
                //Server settings
                // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      
                $mail->isSMTP();                                            
                $mail->Host       = 'smtp.gmail.com';                    
                $mail->SMTPAuth   = true;                                   
                $mail->Username   = 'gssdataoperationsdepartment@gmail.com';                     
                $mail->Password   = '0115930027';                               
                $mail->SMTPSecure = 'tsl';        
                $mail->Port       = 587;                                    
            
                //Recipients
                $mail->setFrom('gssdataoperationsdepartment@gmail.com');
                $mail->addAddress($emailTo );     
                 
            
                // Content
                $mail->isHTML(true);                                  
                $mail->Subject = "Password Update Request";
                $mail->Body    = 'Dear '.$_SESSION['first_name'].',
                <br/>Recently a request was submitted to reset a password for your account.If this was a mistake, just ignore
                this email and nothing will happen.
                <br/>To reset your password visit the following link: <a href="'.$reset_password_link.'">'.$reset_password_link.'</a>
                <br/><br/>Regards,
                <br/>GSS Data Operations Department';
               
            
                $mail->send();
                $display_message = 'A new reset password link has been sent to your email address '.$user_information->account_email;
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            }else{
                $display_message = 'Reset Password link has already been sent to your email address';
            }
           
        
        }else{
            header('location: login_auth_one.php');
        }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='icon' href='favicon.ico' type='image/x-icon' />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="styles/login.css"/>
    <script src="scripts/transition.js"></script>
    <title>Forgot Password</title>
</head>
<body>
<svg id="fader"></svg>
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="index.php" class="navbar-brand">Data Operations</a>
        </div>
    </div>
</nav>
<?php
    if($display_message != ''){
        echo "<h1 class=\"text-center jumbotron\">$display_message</h1>";
    }
?>
</body>
</html>
