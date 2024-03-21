<?php

if ($f == "update-event") {
    $check_pay = 0;
    if (true) {
        if (empty($_POST['event-name'])  || empty($_POST['event-description'])) {
            $error = $error_icon . $wo['lang']['please_check_details'];
        } else {
            if (strlen($_POST['event-name']) < 10) {
                $error = $error_icon . $wo['lang']['title_more_than10'];
            }
            //ВАЛИДАЦИЯ КАТЕГОРИИ
            if (empty($_POST['event-category'])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            //       ВАЛИДАЦИЯ TAGS
            if (empty($_POST['event-tags'])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            //ВАЛИДАЦИЯ PRICE
            if (empty($_POST['event-price'])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            //ВАЛИДАЦИЯ PRICE COAST
            if ((int) $_POST['event-price'] < 100) {
                $error = $error_icon . $wo['lang']['please_check_min_price'];
            }
            //ВАЛИДАЦИЯ НА БАЛЛАНС
            if ((int) $wo['user']['wallet'] < (int) $_POST['time-pay']) {
                
                if (Wo_ValidationEventPayUser($_GET['eid'], (int) $_POST['time-pay'])) {
                    $error = $error_icon . $wo['lang']['please_add_money'] ?? "NO MONEY";
                    $check_pay = 1;
//                    var_dump(222);
                }
            }
//            var_dump((int) $wo['user']['wallet'] < (int) $_POST['time-pay']);
//            var_dump(Wo_ValidationEventPayUser($_GET['eid'], (int) $_POST['time-pay']));
//            exit();
            //ВАЛИДАЦИЯ STATUS
            if (empty($_POST['status']) && count((array) $_POST['status']) <= 0) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            if (strlen($_POST['event-description']) < 32) {
                $error = $error_icon . $wo['lang']['desc_more_than32'];
            }
            if (empty($_POST['event-start-date'])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            if (empty($_POST['event-end-date'])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            if (empty($_POST['event-start-time'])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            if (empty($_POST['event-end-time'])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            if (empty($error)) {
                $date_start = explode('-', $_POST['event-start-date']);
                $date_end = explode('-', $_POST['event-end-date']);
                if ($date_start[0] < $date_end[0]) {
                    
                } else {
                    if ($date_start[0] > $date_end[0]) {
                        $error = $error_icon . $wo['lang']['please_check_details'];
                    } else {
                        if ($date_start[1] < $date_end[1]) {
                            
                        } else {
                            if ($date_start[1] > $date_end[1]) {
                                $error = $error_icon . $wo['lang']['please_check_details'];
                            } else {
                                if ($date_start[2] < $date_end[2]) {
                                    
                                } else {
                                    if ($date_start[2] > $date_end[2]) {
                                        $error = $error_icon . $wo['lang']['please_check_details'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //ПРОВЕРКА ВРЕМЕНИ СОЗДАНИЯ МЕРОПРИЯТИЯ
            if (empty($error)) {
                if (Wo_ValidationEventDateStart($_POST['event-start-date'], $_POST['event-end-date'], $_POST['event-start-time'], $_POST['event-end-time'])) {
                    $error = $error_icon . $wo['lang']['please_check_date_start_end'];
                }
            }
            //ПРОВЕРКА НАЧАЛА ВРЕМЕНИ СОЗДАНИЯ МЕРОПРИЯТИЯ И ТЕКУЩЕГО ВРЕМЕНИ
            if (empty($error)) {
                if (time() > Wo_DateToTimestamp($_POST['event-start-date'], $_POST['event-start-time'])) {
                    $error = $error_icon . $wo['lang']['please_check_date_start_end'];
                }
            }
        }
        if (empty($error) && isset($_GET['eid']) && is_numeric($_GET['eid'])) {
            $registration_data = array(
                'name' => $_POST['event-name'],
                'category_id' => Wo_Secure($_POST['event-category']),
//                'location' => $_POST['event-locat'],
                'description' => $_POST['event-description'],
                'start_date' => $_POST['event-start-date'],
                'start_time' => $_POST['event-start-time'],
                'end_date' => $_POST['event-end-date'],
                'end_time' => $_POST['event-end-time'],
                'event_price' => $_POST['event-price'],
                'event_create_price' => Wo_Secure((int)$_POST['time-pay']),
                'status' => (int) Wo_Secure($_POST['status']),
                'start_timestamp' => Wo_DateToTimestamp($_POST['event-start-date'], $_POST['event-start-time']),
                'end_timestamp' => Wo_DateToTimestamp(Wo_Secure($_POST['event-end-date']), Wo_Secure($_POST['event-end-time'])),
            );
            $result = Wo_UpdateEvent($_GET['eid'], $registration_data);
            //   ДАБАВЛЯЕМ ТЭГИ
            if (!empty($_POST['event-tags']) && $result) {
                Wo_UpdateTagsEvents($_GET['eid'], $_POST['event-tags']);
            }
            //ИЗМЕНЯЕМ ИМЯ ГРУППОВОГО ЧАТА
            Wo_UpdateGroupChatForEvent($_GET['eid'], $_POST['event-name']);
            //ПОЛУЧАЕМ СТОИМОСТЬ МЕРОПРИЯТИЯ
//              $event_create_price = EventsCoastsTime((int) $_POST['event-time']);
            $event_create_price = (int)$_POST['time-pay'];
            //ДОБАВЛЯЕМ/ИЗМЕНЯЕМ ДЕНЬГИ НА СИСТЕМНЫЙ СЧЕТ
            Wo_AddPayForSysWallet($_GET['eid'], 'event_update', $event_create_price);

            if ($result) {
                if (!empty($_FILES["event-cover"]["tmp_name"])) {
                    $temp_name = $_FILES["event-cover"]["tmp_name"];
                    $file_name = $_FILES["event-cover"]["name"];
                    $file_type = $_FILES['event-cover']['type'];
                    $file_size = $_FILES["event-cover"]["size"];
                    Wo_UploadImage($temp_name, $file_name, 'cover', $file_type, $_GET['eid'], 'event');
                }
                $data = array(
                    'message' => $success_icon . $wo['lang']['event_saved'],
                    'status' => 200,
                    'url' => Wo_SeoLink("index.php?link1=show-event&eid=" . $_GET['eid'])
                );
            }
        } else {
            $data = array(
                'status' => 500,
                'pay' => $check_pay,
                'message' => $error
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
