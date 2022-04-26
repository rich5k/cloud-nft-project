<?php
require("../settings/db_class.php");

//require(dirname(__FILE__).'/../settings/db_class.php');

class User extends db_connection
{
    
    function adduser($fname, $lname, $username, $email, $pass,  $gender, $twitter, $instagram, $class, $sexual_orientation, $dob, $major, $phone)
    {
        return $this->query("INSERT INTO `users`(fname, lname, username, email, pass, gender, twitter, insta, class, sexual_orientation, dob, major, phone)
                                                    values ('$fname', '$lname', '$username', '$email', '$pass','$gender', '$twitter','$instagram','$class', '$sexual_orientation', '$dob', '$major',
                                                    '$phone')");
    }

    function edituser($id, $fname, $lname, $username, $email, $pass, $twitter, $instagram, $gender, $class, $sexual_orientation, $dob, $major, $phone)
    {
        return $this->query("UPDATE `users`
                            SET fname = '$fname', lname='$lname', email = '$email', pass= '$pass', gender='$gender', class = '$class',
                            major='$major', phone = '$phone' , username = '$username', twitter = '$twitter', insta = '$instagram', sexual_orientation = '$sexual_orientation' , dob = '$dob'
                            WHERE Uid = '$id'");
    }

    function deleteuser($id)
    {
        return $this->query("DELETE FROM 'users' WHERE Uid = '$id'");
    }

    function addImage($Uid, $image_1)
    {
        return $this->query("INSERT INTO pictures(Uid, pic_name) values ('$Uid','$image_1')");
    }

    function updateImage($id, $image_1)
    {
        return $this->query("UPDATE pictures
                                SET pic_name = '$image_1', 
                                WHERE pic_id = '$id' ");
    }

    function getAllUserImages($id){
        return $this->fetch("SELECT * FROM pictures where Uid = '$id'");
    }


    function finduser($email)
    {
        return $this->fetchOne("SELECT Uid, email, pass FROM `users` WHERE email = '$email'");
    }

    function findEmail($email)
    {
        return $this->fetchOne("SELECT email FROM `users` WHERE email = '$email'");
    }

    function findID($email)
    {
        return $this->fetchOne("SELECT Uid FROM `users` WHERE email = '$email'");
    }

    function getUser($id)
    {
        return $this->fetchOne("SELECT * 
                                FROM users
                                INNER JOIN pictures
                                ON pictures.Uid = users.Uid
                                INNER JOIN courses
                                ON courses.course_id = users.major
                                INNER JOIN sexual_orientation
                                ON sexual_orientation.id = users.sexual_orientation
                                WHERE users.Uid = '$id'");
    }

    function getPartner($id, $gender, $sexual_orientation)
    {
        if ($sexual_orientation === '1') {
            if ($gender === 'm') {
                return $this->fetch("SELECT *
                                    FROM users
                                    INNER JOIN pictures
                                    ON pictures.Uid = users.Uid
                                    WHERE gender = 'f' and sexual_orientation = '$sexual_orientation' ");
            }else if($gender === 'f') {
                return $this->fetch("SELECT *
                                    FROM users
                                    INNER JOIN pictures
                                    ON pictures.Uid = users.Uid
                                    WHERE gender = 'm' and sexual_orientation = '$sexual_orientation' ");
            }
        } elseif ($sexual_orientation === '2') {
            return $this->fetch('SELECT *
                                FROM users
                                INNER JOIN pictures
                                ON pictures.Uid = users.Uid
                                WHERE gender = "m" or gender = "f" ');
        } elseif ($sexual_orientation === '3') {
            return $this->fetch('SELECT *
                                FROM users
                                INNER JOIN pictures
                                ON pictures.Uid = users.Uid
                                WHERE gender = ' . $gender . ' and sexual_orientation = ' . $sexual_orientation . '');
        } elseif ($sexual_orientation === '4') {
            return $this->fetch('SELECT *
                                FROM users
                                INNER JOIN pictures
                                ON pictures.Uid = users.Uid');
        }
    }
    function getUserMessages($id){
        return $this->fetch("SELECT * 
                             FROM conversation 
                             WHERE sender_id = '$id' or receiver_id = '$id'
                             ORDER BY mess_time DESC ");
    }

    function getInterests(){
        return $this->fetch("SELECT * FROM interest");
    }

    function deleteInterests($id){
        return $this->query("DELETE FROM user_interest WHERE Uid = '$id' ");
    }

    function add_user_interest($id, $Iid){
        return $this->query("INSERT INTO user_interest(Uid, Iid) values('$id', '$Iid')");
    }
}
