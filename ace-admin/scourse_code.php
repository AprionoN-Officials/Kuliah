<?php

require_once 'config.php';


if(isset($_POST['save_student']))
{
    $sname = mysqli_real_escape_string($mysqli, $_POST['snameid']);
    $cname = mysqli_real_escape_string($mysqli, $_POST['course_id']);
    $cdate = mysqli_real_escape_string($mysqli, $_POST['cdate']);
    //$address = mysqli_real_escape_string($mysqli, $_POST['address']);

    if($sname == NULL || $cname == NULL || $cdate == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'All fields are mandatory'
        ];
        echo json_encode($res);
        return;
    }

    $query = "INSERT INTO scourses (student_id,course_id,course_date) VALUES ('$sname','$cname','$cdate')";
    //echo $query;
    $query_run = mysqli_query($mysqli, $query);

    if($query_run)
    {
        $res = [
            'status' => 200,
            'message' => 'Student Created Successfully'
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'Student Not Created'
        ];
        echo json_encode($res);
        return;
    }
}


// search
if (! empty($_POST["keyword"])) {
    $sql = $mysqli->prepare("SELECT * FROM students WHERE name LIKE  ? ORDER BY name LIMIT 0,6");
    //var_dump($sql);
    $search = "{$_POST['keyword']}%";
    $sql->bind_param("s", $search);
    $sql->execute();
    $result = $sql->get_result();
    if (! empty($result)) {
        ?>
    <ul id="auto-complete-list">
    <?php
        foreach ($result as $student) {
    ?>
        <li
        onClick="selectName('<?php echo $student["name"]; ?>','<?php echo $student["id"]; ?>');">
      <?php echo $student["name"]; ?>
        </li>
    <?php
        } // end for
    ?>
    </ul>
<?php
    } // end if not empty
}

if (! empty($_POST["term"])) {
    $term = $_POST['term'];
    $query = $mysqli->prepare("SELECT id, course_name FROM courses WHERE course_name LIKE ? LIMIT 10");
    $search = "%".$term."%";
    $query->bind_param("s", $search);
    $query->execute();
    $result = $query->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "label" => $row['course_name'], // tampil di dropdown
            "value" => $row['id']   // isi ke hidden input
            //"harga" => $row['harga']
        ];
    }

    echo json_encode($data);
}
?>