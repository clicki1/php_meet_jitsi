<?php

if ($f == "insert-event") {
    if (Wo_CheckSession($hash_id) === true) {
        $check_pay = 0; 
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

            //ВАЛИДАЦИЯ НА БАЛЛАНС
            if ((int) $wo['user']['wallet'] < (int)$_POST['time-pay']) {
                $error = $error_icon . $wo['lang']['please_add_money'] ?? "NO MONEY";
                $check_pay = 1;
            }
//            var_dump($_POST['time-pay']);
//            var_dump($error);
//            exit();
            //ВАЛИДАЦИЯ PRICE
            if (empty($_POST['event-price'])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            //ВАЛИДАЦИЯ PRICE COAST
            if ((int) $_POST['event-price'] < 100) {
                $error = $error_icon . $wo['lang']['please_check_min_price'] ?? "NO MONEY";
            }

            //ВАЛИДАЦИЯ STATUS
            if (empty($_POST['status']) && count((array) $_POST['status']) <= 0) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }

            //ВАЛИДАЦИЯ ДЛИТЕЛЬНОСТИ МЕРОПРИЯТИЯ
            if (empty($_POST['event-time'])) {
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
            //ПРОВЕРКА ВРЕМЕНИ СОЗДАНИЯ МЕРОПРИЯТИЯ И ОКОНЧАНИЯ МЕРОПРИЯТИЯ
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

        if (empty($error)) {
            $registration_data = array(
                'name' => Wo_Secure($_POST['event-name'], 1),
                'category_id' => Wo_Secure($_POST['event-category']),
//                'location' => Wo_Secure($_POST['event-locat']),
                'description' => Wo_Secure($_POST['event-description'], 1),
                'start_date' => Wo_Secure($_POST['event-start-date']),
                'start_time' => Wo_Secure($_POST['event-start-time']),
                'end_date' => Wo_Secure($_POST['event-end-date']),
                'end_time' => Wo_Secure($_POST['event-end-time']),
                'poster_id' => $wo['user']['id'],
                'event_price' => Wo_Secure($_POST['event-price']),
                'event_create_price' => Wo_Secure((int)$_POST['time-pay']),
                'status' => (int) Wo_Secure($_POST['status']),
                'event_active' => 0,
                'event_key' => hash('md5', $_POST['event-name'] . time()),
                'start_timestamp' => Wo_DateToTimestamp($_POST['event-start-date'], $_POST['event-start-time']),
                'end_timestamp' => Wo_DateToTimestamp(Wo_Secure($_POST['event-end-date']), Wo_Secure($_POST['event-end-time'])),
            );
            $last_id = Wo_InsertEvent($registration_data);
            //   ДАБАВЛЯЕМ ТЭГИ
            if (!empty($_POST['event-tags'])) {
                Wo_AddTagsEvents($last_id, $_POST['event-tags']);
            }
            //ПОЛУЧАЕМ СТОИМОСТЬ МЕРОПРИЯТИЯ
//            $event_create_price = EventsCoastsTime((int) $_POST['event-time']);
            $event_create_price = (int)$_POST['time-pay'];
           
            //СНИМАЕМ СО СЧЕТА ДЕНЬГИ И ПЕРЕВОДИМ НА СЧЕТ МЕРОПРИЯТИЯ
            Wo_EventPayUser($event_coast, $last_id);
            //ДОБАВЛЯЕМ ДЕНЬГИ НА СИСТЕМНЫЙ СЧЕТ
            Wo_AddPayForSysWallet($last_id, 'event_create', $event_create_price);
            //СОЗДАЕМ ГРУППОВОЙ ЧАТ
            Wo_AddGroupChatForEvent($last_id);

            if ($last_id && is_numeric($last_id)) {
                if (!empty($_FILES["event-cover"]["tmp_name"])) {
                    $temp_name = $_FILES["event-cover"]["tmp_name"];
                    $file_name = $_FILES["event-cover"]["name"];
                    $file_type = $_FILES['event-cover']['type'];
                    $file_size = $_FILES["event-cover"]["size"];
                    Wo_UploadImage($temp_name, $file_name, 'cover', $file_type, $last_id, 'event');
                }
                $data = array(
                    'message' => $success_icon . $wo['lang']['event_added'],
                    'status' => 200,
                    'url' => Wo_SeoLink("index.php?link1=show-event&eid=" . $last_id)
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
