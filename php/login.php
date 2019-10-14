<?php
    include("_con.php");
    include('../configurations/constants.php');
    session_start();
    $error="";
    //mysqli_select_db($database,$GLOBALS['db_connection']);
    if(isset($_POST['Username']) && isset($_POST['password'])){
        $email = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['Username']);
        $password = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['password']);
        $pass=md5($password);

        //checking for the email an password in DB
        $sql_q="SELECT u.id,u.profile,u.name FROM login AS l JOIN users AS u ON l.ref_id= u.id WHERE l.email='$email' AND l.password='$password' AND l.type='user'";
        $res_q=mysqli_query($GLOBALS['db_connection'], $sql_q);
        if(mysqli_num_rows($res_q)==1){
            $row = mysqli_fetch_assoc($res_q);

            $id = $row['id'];
            $_SESSION['login_time']=date('H:i:s');
            
            $_SESSION['user']=$email;
            $_SESSION['user_id']=$id;
            $_SESSION['user_image']= $row['profile'];
            $_SESSION['user_name']=$row['name'];

            //unset($_SESSION['logout_flag']);
        //move to homepage
            // header('location:employeeInfo.php');
            header("location:".BASE_URL."user/home.php");

        }else{
            //new login 

            /*$sql="INSERT INTO login(email,password) VALUES('$username','$pass')";
            if(!mysqli_query($GLOBALS['db_connection'],$sql)){
                echo "Error: " . $sql . "<br>" . mysqli_error($GLOBALS['db_connection']);
            }else{
                header("location:employeeInfo.php");
            } */

            //$error="Your email/password is wrong.";
            //$_SESSION['error']=$error;
            $username = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['Username']);
            $password = mysqli_real_escape_string($GLOBALS['db_connection'],$_POST['password']);
            $pass=md5($password);
            $sql_qu="SELECT * FROM login WHERE email='$username' AND password='$pass' AND type='admin'";
            $res_qu=mysqli_query($GLOBALS['db_connection'],$sql_qu);
            if(mysqli_num_rows($res_qu)==1){
                $_SESSION['user']=$username;
                $_SESSION['user_id']=$id;
                unset($_SESSION['logout_flag']);
                header('location:../admin/ui/index.php');
            }else{
                
                echo "Wrong email/password.";
                $error="Wrong email/password.";
                header('refresh:1;url='.BASE_URL.'index.php');
                
            }    
        } 
    } 
?>
