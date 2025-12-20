<?php
require_once 'config.php';

$id=$_POST['id'];
$user=$_POST['username'];
$pwd=$_POST['pwd'];
$name=$_POST['name'];
if($pwd==""){
    $sql = "UPDATE LOGIN SET username = ?, name=? WHERE id = ?";
}else{
    $sql = "UPDATE LOGIN SET username = ?, passwd=?, name=? WHERE id = ?";
}
$stmt = $mysqli->prepare($sql);

// 3. Bind parameters
// The "s" and "i" specify the types of data:
// s = string, i = integer
if($pwd==""){
    $stmt->bind_param("ssi", $user,$name,$id);
}else{
    $stmt->bind_param("sssi", $user, $pwd,$name,$id);
}
// 4. Execute the statement
$stmt->execute();

// Check the number of affected rows
if ($stmt->affected_rows > 0) {
    echo "Record updated successfully.";
    header('Location:form_login.php');
} else {
    echo "No records updated.";
}
?>