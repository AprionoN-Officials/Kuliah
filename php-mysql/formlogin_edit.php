<?php 
include 'config.php';
$id = $_GET['id'];

$stmt = $mysqli->prepare("SELECT * FROM  login WHERE ID=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $d = $result->fetch_assoc();
    //header('Location: form_login.php');
   //  while ($row = $result->fetch_assoc()) {
   //      echo "id: " . $row["id"] . " - Name: " . $row["username"] . " " . $row["passwd"] . "<br>";
   //  }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
</head>
<style>
.box {
    width: 600px;
    margin: 50px auto;
    padding: 30px;
    border: 2px solid #333;
    border-radius: 5px;
    background-color: #fff;
}

.box h2 {
    text-align: center;
    margin-bottom: 30px;
    letter-spacing: 2px;
}

.box table {
    margin: auto;
}

.box input[type="text"],
.box input[type="password"] {
    width: 230px;
    padding: 6px;
    border: 1px solid #999;
    border-radius: 3px;
}

.box button {
    padding: 6px 15px;
    border: 1px solid #666;
    background-color: #f2f2f2;
    cursor: pointer;
}

.box button:hover {
    background-color: #ddd;
}

</style>

<body>
<div class="box">
<h2>Edit User</h2>

<form action="formlogin_update.php" method="post">
    <input type="hidden" name="id" value="<?= $d['id'] ?>">
    <table border="0">
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Username</td>
            <td>:</td>
            <td><input type="text" name="username" value="<?= $d['username'] ?>"></td>
        </tr>
        <tr>
            <td>Password</td>
            <td>:</td>
            <td><input type="password" name="pwd">Password (kosongkan jika tidak diganti)</td>
        </tr>
        <tr>
            <td>Name</td>
            <td>:</td>
            <td><input type="text" name="name" value="<?= $d['name'] ?>"></td>
        </tr>
        <tr>
            <td colspan="3">
            <button type="submit">Update</button>
            <button onclick="window.location.href='formlogin.php'">Cancel</button>
        </td>
        </tr>    
    </table>
</form>
</div>
</body>
</html>
