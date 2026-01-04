<?php
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $id         = $_POST['id'];
    $empname    = $_POST['empname'];
    $phone      = $_POST['phone'];
    $address    = $_POST['address'];
    $id_dept    = $_POST['id_dept'];

    if ($id != "") {
        $sql = "UPDATE employee SET empname='$empname', phone='$phone', address='$address', id_dept='$id_dept' WHERE id='$id'";
    } else {
        $sql = "INSERT INTO employee (empname, phone, address, id_dept) VALUES ('$empname', '$phone', '$address', '$id_dept')";
    }

    $query = mysqli_query($koneksi, $sql);

    if ($query) {
        header("Location: index.php?status=sukses");
    } else {
        header("Location: index.php?status=gagal");
    }
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    $sql = "DELETE FROM employee WHERE id = '$id'";
    $query = mysqli_query($koneksi, $sql);

    if ($query) {
        header("Location: index.php?status=sukses");
    } else {
        header("Location: index.php?status=gagal");
    }
}
?>