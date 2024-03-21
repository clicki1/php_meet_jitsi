<?php

//var_dump($_POST);
//var_dump("****");
//var_dump($_FILES['passport']['name']);
//var_dump("****");
//var_dump(Wo_CheckVerifcateFiles($_FILES['passport']['name']));
//
//exit();
if ($f == 'verificate-user') {
    $data = array(
        'status' => 304,
        'message' => ($error_icon . $wo['lang']['please_check_details'])
    );
    $error = false;
//    if (!isset($_POST['name']) || !isset($_POST['text']) || !isset($_FILES['passport']) || !isset($_FILES['photo'])) {
    if (!isset($_POST['type_ver']) || !isset($_POST['fio'])  || !isset($_POST['ogrnip']) || !isset($_POST['inn']) || !isset($_POST['org']) || !isset($_POST['ogrn']) || !isset($_POST['bank']) || !isset($_POST['bik']) || !isset($_POST['rs']) || !isset($_POST['kor']) || !isset($_POST['tel']) || !isset($_POST['email'])) {
//      
        $error = true;
    } else {
        if (strlen($_POST['type_ver']) < 1) {
            $error = true;
        }

        if ($_POST['type_ver'] != 1 && empty($_POST['fio'])) {
            $error = true;
        }
        if ($_POST['type_ver'] == 0 && empty($_POST['ogrnip'])) {
            $error = true;
        }

        if ($_POST['type_ver'] == 1 && empty($_POST['org'])) {
            $error = true;
        }
        if ($_POST['type_ver'] == 1 && empty($_POST['ogrn'])) {
            $error = true;
        }
        
        if (empty($_POST['inn'])) {
            $error = true;
        }

        if (empty($_POST['bank'])) {
            $error = true;
        }
        if (empty($_POST['bik'])) {
            $error = true;
        }
        if (empty($_POST['rs'])) {
            $error = true;
        }
        if (empty($_POST['kor'])) {
            $error = true;
        }
        if (empty($_POST['tel'])) {
            $error = true;
        }
        if (empty($_POST['email'])) {
            $error = true;
        }
//        if (strlen($_POST['name']) < 5 || strlen($_POST['name']) > 50) {
//            $error           = true;
//            $data['message'] = $error_icon . $wo['lang']['username_characters_length'];
//        }
//        if (!file_exists($_FILES['passport']['tmp_name']) || !file_exists($_FILES['photo']['tmp_name'])) {
//            $error = true;
//            $data['message'] = $error_icon . $wo['lang']['please_select_passport_id'];
//        }
        if (file_exists($_FILES["passport"]["tmp_name"])) {
          
//            $image = getimagesize($_FILES["passport"]["tmp_name"]);
            
            if(!Wo_CheckVerifcateFiles($_FILES['passport']['name'])){
                 $error = true;
                $data['message'] = $error_icon . ' Тип файла может быть: jpeg, jpg, png, bmp, gif, doc, pdf, webp';
            }
            
//            if (!in_array($image[2], array(
//                        IMAGETYPE_GIF,
//                        IMAGETYPE_JPEG,
//                        IMAGETYPE_PNG,
//                        IMAGETYPE_BMP
//                    ))) {
//                $error = true;
//                $data['message'] = $error_icon . $wo['lang']['passport_id_invalid'];
//            }
            
        }
//        if (file_exists($_FILES["photo"]["tmp_name"])) {
//            $image = getimagesize($_FILES["photo"]["tmp_name"]);
//            if (!in_array($image[2], array(
//                        IMAGETYPE_GIF,
//                        IMAGETYPE_JPEG,
//                        IMAGETYPE_PNG,
//                        IMAGETYPE_BMP
//                    ))) {
//                $error = true;
//                $data['message'] = $error_icon . $wo['lang']['user_picture_invalid'];
//            }
//        }
    }
    if (!$error) {
        $ver = Get_VerificationDoc();
        if ($ver) {
            Wo_DelVerificationDoc($ver->id);
        };
        $registration_data = array(
            'user_id' => $wo['user']['id'],
            'message' => Wo_Secure($_POST['text']) ?? "",
            'user_name' => Wo_Secure($_POST['name']) ?? "",
            'passport' => '',
            'photo' => '',
            'type' => 'User',
            'seen' => 0,
            'type_ver' => (int) $_POST['type_ver'],
            'fio' => Wo_Secure($_POST['fio'] ?? 0),
            'inn' => $_POST['inn'] ?? 0,
            'org' => Wo_Secure($_POST['org'] ?? 0),
            'ogrn' => Wo_Secure($_POST['ogrn'] ?? 0),
            'ogrnip' => Wo_Secure($_POST['ogrnip'] ?? 0),
            'bank' => Wo_Secure($_POST['bank']),
            'bik' => $_POST['bik'] ?? '',
            'rs' => $_POST['rs'] ?? '',
            'kor' => $_POST['kor'] ?? '',
            'tel' => $_POST['tel'] ?? '',
            'address' => $_POST['address'] ?? '',
            'email' => $_POST['email'] ?? "",
        );
       
        $last_id = Wo_SendVerificationRequest($registration_data);
//         var_dump($last_id);
//        exit();
        if ($last_id && is_numeric($last_id)) {
            $files = array(
                'passport' => $_FILES,
//                'photo' => $_FILES
            );
            $update_data = array();
            foreach ($files as $key => $file) {
                $fileInfo = array(
                    'file' => $file[$key]["tmp_name"],
                    'name' => $file[$key]['name'],
                    'size' => $file[$key]["size"],
                    'type' => $file[$key]["type"],
//                    'types' => 'jpg,png,bmp,gif',
//                    'types' => 'all'
                );
                $media = Wo_ShareFile($fileInfo);

                if (!empty($media)) {
                    $update_data[$key] = $media['filename'];
                }
            }
//                      var_dump($update_data);
//        exit();
            $data['status'] = 200;
            $data['message'] = $success_icon . $wo['lang']['verification_request_sent'];
            $data['url'] = $wo['config']['site_url'] . '/setting/verification';
            //
            if (Wo_UpdateVerificationRequest($last_id, $update_data)) {
                $data['status'] = 200;
                $data['message'] = $success_icon . $wo['lang']['verification_request_sent'];
                $data['url'] = $wo['config']['site_url'] . '/setting/verification';
//                $data['url'] = $wo['config']['site_url'];
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
