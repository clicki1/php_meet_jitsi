<?php
if ($f == 'go_event') {

    global $sqlConnect, $wo;
    $user_id = $wo["user"]["id"];
    $user_wallet = $wo["user"]["wallet"];
    
    $result = array(
                    'status' => 200,
                    'error' => 0,
                    'message' => 'Ok',
                    'button' => 'on',
                    
                );
    
    if (!empty($_GET['event_id']) && Wo_CheckMainSession($hash_id) === true) {
        //ВЫХОД ИЗ МЕРОПРИЯТИЯ
        if (Wo_EventGoingExists($_GET['event_id']) === true) {
            if (Wo_UnsetEventGoingUsers($_GET['event_id'])) {
                $data = array(
                    'status' => 200,
                    'html' => Wo_GetGoingButton($_GET['event_id'])
                );
                //ВОЗВРАЩАЕМ ДЕНЬГИ УЧАСТНИКУ
                Wo_RepaymentEventUsers ($_GET['event_id']);
               
                 $result = array(
                    'status' => 200,
                    'error' => 0,
                    'message' => 'Ok',
                    'button' => 'off',
                );
            
            }
        } else {
            if (Wo_AddEventGoingUsers($_GET['event_id']) && !Wo_PaymentEventUsers ($_GET['event_id'])) {
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                //СНИМАЕМ ДЕНЬГИ СО СЧЕТА УЧАСТНИКА
                if(Wo_PaymentEventUsers ($_GET['event_id']) === false){
                    if (Wo_EventGoingExists($_GET['event_id']) === true) {
                        if (Wo_UnsetEventGoingUsers($_GET['event_id'])) {
                            $data = array(
                                'status' => 200,
                                'html' => Wo_GetGoingButton($_GET['event_id'])
                            );
                        }
                    }
                    
                    $result = array(
                        'status' => 200,
                        'error' => 1,
                        'message' => 'not enough money on balance',
                        'button' => 'off',
                    );
                    
                }

                $data = array(
                    'status' => 200,
                    'html' => Wo_GetGoingButton($_GET['event_id'])
                );
                if (Wo_CanSenEmails()) {
                    $data['can_send'] = 1;
                }
            }
        }
         if($result['error'] == 0){
             Wo_AddMemberGroupChat($_GET['event_id']);
         }
        
        
    }
    header("Content-type: application/json");
    echo json_encode($result);
    exit();
}
