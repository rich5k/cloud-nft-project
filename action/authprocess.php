<?php

require('../controllers/UserController.php');
session_start();

if(isset($_POST['register'])){
    $email = $_POST['email'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $pass = $_POST['password'];
    $username = $_POST['username'];
    $class = $_POST['class'];
    $confirmPass = $_POST['cpass'];
    $dob = $_POST['dob'];
    $instagram = $_POST['instagram'];
    $twitter = $_POST['twitter'];
    $gender = $_POST['gender'];
    $sexual_orientation = $_POST['sexual_orientation'];
    $contact = $_POST['phone'];
    $major = $_POST['major'];
    $role = 1;

    if(find_email($email) === true){
        header("Location: register.php?error=Email already exists!");
        die;
    }
    else if($pass !== $confirmPass){
        header("Location: register.php?error=Passwords do not match!");
        die;
    }
    else{
        $pass = password_hash($pass, PASSWORD_DEFAULT);
        // echo $email ."<br>";
        // echo $fname ."<br>";
        // echo $lname ."<br>";
        // echo $pass ."<br>";
        // echo $address ."<br>";
        // echo $confirmPass ."<br>";
        // echo $country ."<br>";
        // echo $city ."<br>";
        // echo $contact ."<br>";
        // var_dump(add_user_controller($fname, $lname, $username, $email, $pass, $twitter, $instagram, $gender, $class, $sexual_orientation, $major, $contact, $role));
        // die;
        if( add_user_controller($fname, $lname, $username, $email, $pass, $twitter, $instagram, $gender, $class, $sexual_orientation, $dob, $major, $contact, $role) !== true) 
            header('Location: ../view/auth/register.php?error=Data could not be inserted');
        //$_SESSION['Uid'] = find_user_id($email);
        header("Location: ../view/auth/login.php");
        
    }   
}
die("ERROR: Could not execute");


if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $result = find_user_controller($email);
    
    // die();

    if(isset($result)){
        if(password_verify($password, $result['user_password'])){
            $user = find_user_controller($email);
            $_SESSION['Uid'] = $user['Uid'];
            $role = $user['user_role'];
            //var_dump($role);
            if($role === '1' ){
                header("Location: ../index.php");
            }else if ($role === '0'){
                $_SESSION['user_role'] = 0;
                header("Location: ../admin/index.php");
            }
        }else{
            $_SESSION['errors'] = 'Email or password is incorrect'; 
            
            header("Location: ../view/auth/login.php");
        }
    }else{
        $_SESSION['errors'] = 'Email or password is incorrect'; 
        header("Location: ../view/auth/login.php");
    }

}
    

?>