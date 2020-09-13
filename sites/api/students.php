<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/src/controllers/student_controller.php');

    use \App\Controllers\StudentController as StudentController;

    $student_controller = new StudentController();

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        echo json_encode($student_controller->Get($_REQUEST));

    } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] == 'PUT') {
            unset($_REQUEST['_method']);
            echo json_encode($student_controller->Put($_REQUEST));

        } else if (isset($_REQUEST['_method']) && $_REQUEST['_method'] == 'DELETE') {
            unset($_REQUEST['_method']);
            echo json_encode($student_controller->Delete($_REQUEST));

        } else {
            echo json_encode($student_controller->Post($_REQUEST));
        }
    } 
?>