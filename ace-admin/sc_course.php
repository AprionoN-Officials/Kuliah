<?php
// sc_course.php
require_once 'config.php';

// --- SAVE COURSE ---
if(isset($_POST['save_course']))
{
    $course_name = mysqli_real_escape_string($mysqli, $_POST['course_name']);
    $duration = mysqli_real_escape_string($mysqli, $_POST['duration']);
    $level = mysqli_real_escape_string($mysqli, $_POST['level']);

    if($course_name == NULL || $duration == NULL || $level == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'All fields are mandatory'
        ];
        echo json_encode($res);
        return;
    }

    // Perbaikan: Nama tabel diganti menjadi 'course' (sesuai SQL)
    $query = "INSERT INTO course (course_name, duration, level) VALUES ('$course_name','$duration','$level')";
    $query_run = mysqli_query($mysqli, $query);

    if($query_run)
    {
        $res = [
            'status' => 200,
            'message' => 'Course Created Successfully'
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'Course Not Created'
        ];
        echo json_encode($res);
        return;
    }
}

// --- UPDATE COURSE ---
if(isset($_POST['update_course']))
{
    $course_id = mysqli_real_escape_string($mysqli, $_POST['course_id']);
    $course_name = mysqli_real_escape_string($mysqli, $_POST['course_name']);
    $duration = mysqli_real_escape_string($mysqli, $_POST['duration']);
    $level = mysqli_real_escape_string($mysqli, $_POST['level']);

    if($course_name == NULL || $duration == NULL || $level == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'All fields are mandatory'
        ];
        echo json_encode($res);
        return;
    }

    // Perbaikan: Nama tabel 'course'
    $query = "UPDATE course SET course_name='$course_name', duration='$duration', level='$level' 
                WHERE id='$course_id'";
    $query_run = mysqli_query($mysqli, $query);

    if($query_run)
    {
        $res = [
            'status' => 200,
            'message' => 'Course Updated Successfully'
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'Course Not Updated'
        ];
        echo json_encode($res);
        return;
    }
}

// --- GET COURSE BY ID ---
if(isset($_GET['course_id']))
{
    $course_id = mysqli_real_escape_string($mysqli, $_GET['course_id']);

    // Perbaikan: Nama tabel 'course'
    $query = "SELECT * FROM course WHERE id='$course_id'";
    $query_run = mysqli_query($mysqli, $query);

    if(mysqli_num_rows($query_run) == 1)
    {
        $course = mysqli_fetch_array($query_run);

        $res = [
            'status' => 200,
            'message' => 'Course Fetch Successfully by id',
            'data' => $course
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 404,
            'message' => 'Course Id Not Found'
        ];
        echo json_encode($res);
        return;
    }
}

// --- DELETE COURSE ---
if(isset($_POST['delete_course']))
{
    $course_id = mysqli_real_escape_string($mysqli, $_POST['course_id']);

    // Perbaikan: Nama tabel 'course'
    $query = "DELETE FROM course WHERE id='$course_id'";
    $query_run = mysqli_query($mysqli, $query);

    if($query_run)
    {
        $res = [
            'status' => 200,
            'message' => 'Course Deleted Successfully'
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'Course Not Deleted'
        ];
        echo json_encode($res);
        return;
    }
}
?>