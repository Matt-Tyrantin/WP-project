<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/src/controllers/activity_controller.php');

    use \App\Controllers\ActivityController as ActivityController;

    $activity_controller = new ActivityController();

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        echo json_encode($activity_controller->Get($_REQUEST));

    } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] == 'PUT') {
            unset($_REQUEST['_method']);
            echo json_encode($activity_controller->Put($_REQUEST));

        } else if (isset($_REQUEST['_method']) && $_REQUEST['_method'] == 'DELETE') {
            unset($_REQUEST['_method']);
            echo json_encode($activity_controller->Delete($_REQUEST));

        } else {
            echo json_encode($activity_controller->Post($_REQUEST));
        }
    } 
?>