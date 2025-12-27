<?php
include('header.php');
?>

<div class="modal fade" id="courseAddModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add Course</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="saveCourse">
            <div class="modal-body">
                <div id="errorMessage" class="alert alert-warning d-none"></div>

                <div class="mb-3">
                    <label for="">Course Name</label>
                    <input type="text" name="course_name" class="form-control" />
                </div>
                <div class="mb-3">
                    <label for="">Duration</label>
                    <input type="text" name="duration" class="form-control" />
                </div>
                <div class="mb-3">
                    <label for="">Level</label>
                    <input type="text" name="level" class="form-control" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Course</button>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="courseEditModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit Course</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="updateCourse">
            <div class="modal-body">
                <div id="errorMessageUpdate" class="alert alert-warning d-none"></div>

                <input type="hidden" name="course_id" id="course_id" >

                <div class="mb-3">
                    <label for="">Course Name</label>
                    <input type="text" name="course_name" id="edit_course_name" class="form-control" />
                </div>
                <div class="mb-3">
                    <label for="">Duration</label>
                    <input type="text" name="duration" id="edit_duration" class="form-control" />
                </div>
                <div class="mb-3">
                    <label for="">Level</label>
                    <input type="text" name="level" id="edit_level" class="form-control" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update Course</button>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="courseViewModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">View Course</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="">Course Name</label>
                    <p id="view_course_name" class="form-control"></p>
                </div>
                <div class="mb-3">
                    <label for="">Duration</label>
                    <p id="view_duration" class="form-control"></p>
                </div>
                <div class="mb-3">
                    <label for="">Level</label>
                    <p id="view_level" class="form-control"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>
                        <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#courseAddModal">
                            Add Course
                        </button>
                    </h4>
                </div>
                <div class="card-body">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Course Name</th>
                                <th>Duration</th>
                                <th>Level</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require_once 'config.php';

                            $query = "SELECT * FROM course";
                            $query_run = mysqli_query($mysqli, $query);

                            if(mysqli_num_rows($query_run) > 0)
                            {
                                foreach($query_run as $course)
                                {
                                    ?>
                                    <tr>
                                        <td><?= $course['id'] ?></td>
                                        <td><?= $course['course_name'] ?></td>
                                        <td><?= $course['duration'] ?></td>
                                        <td><?= $course['level'] ?></td>
                                        <td>
                                            <button type="button" value="<?=$course['id'];?>" class="viewCourseBtn btn btn-info btn-sm">View</button>
                                            <button type="button" value="<?=$course['id'];?>" class="editCourseBtn btn btn-success btn-sm">Edit</button>
                                            <button type="button" value="<?=$course['id'];?>" class="deleteCourseBtn btn btn-danger btn-sm">Delete</button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

<script>
    // --- SAVE COURSE ---
    $(document).on('submit', '#saveCourse', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append("save_course", true);

        $.ajax({
            type: "POST",
            url: "sc_course.php",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                
                var res = jQuery.parseJSON(response);
                if(res.status == 422) {
                    $('#errorMessage').removeClass('d-none');
                    $('#errorMessage').text(res.message);

                }else if(res.status == 200){

                    $('#errorMessage').addClass('d-none');
                    // Perbaikan: ID Modal yang benar
                    $('#courseAddModal').modal('hide');
                    $('#saveCourse')[0].reset();

                    alertify.set('notifier','position', 'top-right');
                    alertify.success(res.message);

                    $('#myTable').load(location.href + " #myTable");

                }else if(res.status == 500) {
                    alert(res.message);
                }
            }
        });
    });

    // --- KLIK TOMBOL EDIT (FETCH DATA) ---
    $(document).on('click', '.editCourseBtn', function () {

        var course_id = $(this).val();
        
        $.ajax({
            type: "GET",
            // Perbaikan: URL harus ke sc_course.php, bukan s_code.php
            url: "sc_course.php?course_id=" + course_id,
            success: function (response) {

                var res = jQuery.parseJSON(response);
                if(res.status == 404) {
                    alert(res.message);
                }else if(res.status == 200){

                    // Perbaikan: Memasukkan data ke ID input yang benar
                    $('#course_id').val(res.data.id);
                    $('#edit_course_name').val(res.data.course_name);
                    $('#edit_duration').val(res.data.duration);
                    $('#edit_level').val(res.data.level);

                    $('#courseEditModal').modal('show');
                }
            }
        });
    });

    // --- UPDATE COURSE (SUBMIT FORM) ---
    $(document).on('submit', '#updateCourse', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append("update_course", true);
        
        $.ajax({
            type: "POST",
            url: "sc_course.php",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                
                var res = jQuery.parseJSON(response);
                if(res.status == 422) {
                    $('#errorMessageUpdate').removeClass('d-none');
                    $('#errorMessageUpdate').text(res.message);

                }else if(res.status == 200){

                    $('#errorMessageUpdate').addClass('d-none');

                    alertify.set('notifier','position', 'top-right');
                    alertify.success(res.message);
                    
                    // Perbaikan: ID Modal yang benar
                    $('#courseEditModal').modal('hide');
                    $('#updateCourse')[0].reset();

                    $('#myTable').load(location.href + " #myTable");

                }else if(res.status == 500) {
                    alert(res.message);
                }
            }
        });
    });

    // --- VIEW COURSE ---
    $(document).on('click', '.viewCourseBtn', function () {

        var course_id = $(this).val();
        $.ajax({
            type: "GET",
            // Perbaikan: URL ke sc_course.php
            url: "sc_course.php?course_id=" + course_id,
            success: function (response) {

                var res = jQuery.parseJSON(response);
                if(res.status == 404) {
                    alert(res.message);
                }else if(res.status == 200){

                    // Perbaikan: ID HTML dan key JSON yang benar
                    $('#view_course_name').text(res.data.course_name);
                    $('#view_duration').text(res.data.duration);
                    $('#view_level').text(res.data.level);

                    $('#courseViewModal').modal('show');
                }
            }
        });
    });

    // --- DELETE COURSE ---
    $(document).on('click', '.deleteCourseBtn', function (e) {
        e.preventDefault();

        if(confirm('Are you sure you want to delete this data?'))
        {
            var course_id = $(this).val();
            $.ajax({
                type: "POST",
                url: "sc_course.php",
                data: {
                    'delete_course': true,
                    'course_id': course_id
                },
                success: function (response) {

                    var res = jQuery.parseJSON(response);
                    if(res.status == 500) {
                        alert(res.message);
                    }else{
                        alertify.set('notifier','position', 'top-right');
                        alertify.success(res.message);

                        $('#myTable').load(location.href + " #myTable");
                    }
                }
            });
        }
    });

</script>
<?php
include('footer.php');
?>