<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3 class="mb-4">Data Employee</h3>

        <?php 
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'sukses') {
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        Data berhasil diproses!
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
            } else {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        Gagal memproses data!
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
            }
        }
        ?>

        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalEmployee" onclick="resetForm()">
            + Add Employee
        </button>

        <div class="card">
            <div class="card-header bg-white">
                Daftar Karyawan
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th> 
                            <th>Address</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql    = "SELECT employee.*, dept.deptname FROM employee LEFT JOIN dept ON employee.id_dept = dept.id ORDER BY employee.id DESC";
                        $query  = mysqli_query($koneksi, $sql);
                        $urut   = 1;
                        
                        while ($row = mysqli_fetch_array($query)) {
                        ?>
                        <tr>
                            <th scope="row"><?php echo $urut++; ?></th>
                            <td><?php echo $row['empname']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['address']; ?></td>
                            <td><?php echo $row['deptname']; ?></td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm text-white"
                                        onclick="viewData(
                                            '<?php echo $row['empname'] ?>', 
                                            '<?php echo $row['phone'] ?>', 
                                            '<?php echo $row['address'] ?>', 
                                            '<?php echo $row['id_dept'] ?>'
                                        )">
                                    View
                                </button>
                                
                                <button type="button" class="btn btn-success btn-sm" 
                                        onclick="editData(
                                            '<?php echo $row['id'] ?>', 
                                            '<?php echo $row['empname'] ?>', 
                                            '<?php echo $row['phone'] ?>', 
                                            '<?php echo $row['address'] ?>', 
                                            '<?php echo $row['id_dept'] ?>'
                                        )">
                                    Edit
                                </button>
                                
                                <a href="proses.php?aksi=hapus&id=<?php echo $row['id'] ?>" 
                                   onclick="return confirm('Yakin Ingin hapus data ini?')" 
                                   class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEmployee" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="proses.php" method="POST">
                        <input type="hidden" name="id" id="id"> 
                        
                        <div class="mb-3">
                            <label class="form-label">Employee Name</label>
                            <input type="text" class="form-control" name="empname" id="empname" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="number" class="form-control" name="phone" id="phone" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="address" id="address" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="id_dept" id="id_dept" required>
                                <option value="">- Pilih Departemen -</option>
                                <?php
                                $q_dept = mysqli_query($koneksi, "SELECT * FROM dept");
                                while($d = mysqli_fetch_array($q_dept)){
                                    echo "<option value='".$d['id']."'>".$d['deptname']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" name="simpan" id="tombolSimpan" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function enableForm() {
            document.getElementById('empname').disabled = false;
            document.getElementById('phone').disabled = false;
            document.getElementById('address').disabled = false;
            document.getElementById('id_dept').disabled = false;
            document.getElementById('tombolSimpan').style.display = 'block';
        }

        function disableForm() {
            document.getElementById('empname').disabled = true;
            document.getElementById('phone').disabled = true;
            document.getElementById('address').disabled = true;
            document.getElementById('id_dept').disabled = true;
            document.getElementById('tombolSimpan').style.display = 'none';
        }

        function resetForm(){
            enableForm();
            document.getElementById('modalTitle').innerText = "Add Employee";
            document.getElementById('id').value = "";
            document.getElementById('empname').value = "";
            document.getElementById('phone').value = "";
            document.getElementById('address').value = "";
            document.getElementById('id_dept').value = "";
        }

        function editData(id, empname, phone, address, id_dept){
            enableForm();
            document.getElementById('modalTitle').innerText = "Edit Employee";
            document.getElementById('id').value = id;
            document.getElementById('empname').value = empname;
            document.getElementById('phone').value = phone;
            document.getElementById('address').value = address;
            document.getElementById('id_dept').value = id_dept;
            
            var myModal = new bootstrap.Modal(document.getElementById('modalEmployee'));
            myModal.show();
        }

        function viewData(empname, phone, address, id_dept){
            disableForm();
            document.getElementById('modalTitle').innerText = "View Employee";
            
            document.getElementById('empname').value = empname;
            document.getElementById('phone').value = phone;
            document.getElementById('address').value = address;
            document.getElementById('id_dept').value = id_dept;
            
            var myModal = new bootstrap.Modal(document.getElementById('modalEmployee'));
            myModal.show();
        }
    </script>
</body>
</html>