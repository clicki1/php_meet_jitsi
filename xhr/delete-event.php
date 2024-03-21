<?php 
if ($f == "delete-event") {

    $data = array(
        'status' => 500
    );
    if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0 && !empty($_GET['delete_desc'])) {
        

        if(Wo_DeleteEvent_Cansel($_GET['id'], $_GET['delete_desc'])){
             $data['status'] = 200;
             Wo_AddPayForSysWallet($_GET['id'], "event_close" , 0, 0.5);
        }

    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
