<?php

if ($f == 'update_user_information_startup' && Wo_CheckSession($hash_id) === true) {
//    var_dump($_GET);
//    var_dump($_POST);
//    exit();
    if (isset($_POST['user_id']) && is_numeric($_POST['user_id']) && $_POST['user_id'] > 0) {
        $Userdata = Wo_UserData($_POST['user_id']);
        if (!empty($Userdata['user_id'])) {
            $age_data = '00-00-0000';
            if (!empty($_POST['birthday']) && preg_match('@^\s*(3[01]|[12][0-9]|0?[1-9])\-(1[012]|0?[1-9])\-((?:19|20)\d{2})\s*$@', $_POST['birthday'])) {
                $newDate = date("Y-m-d", strtotime($_POST['birthday']));
                $age_data = $newDate;
            } else {
                if (!empty($_POST['age_year']) || !empty($_POST['age_day']) || !empty($_POST['age_month'])) {
                    if (empty($_POST['age_year']) || empty($_POST['age_day']) || empty($_POST['age_month'])) {
                        $errors[] = $error_icon . $wo['lang']['please_choose_correct_date'];
                    } else {
                        $age_data = $_POST['age_year'] . '-' . $_POST['age_month'] . '-' . $_POST['age_day'];
                    }
                }
            }
            $Update_data = array(
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'country_id' => $_POST['country'] ?? 0,
                'city_id' => (int) $_POST['city_id'],
                'birthday' => $age_data,
                'start_up_info' => 1
            );
            if (!empty($_GET['a']) && $_GET['a'] == 'activation') {
                $Update_data['category_id'] = (int) $_POST['user_category_id'] ?? null;
                //ДОБАВЛЯЕМ ТЕГИ К ПОЛЬЗОВАТЕЛЮ
                if (isset($_POST['tag']) && !empty($_POST['tag'])) {
                    Wo_AddUserCategoryTags($_POST['user_id'], (int) $_POST['user_category_id'], $_POST['tag']);
                } else {
                    Wo_AddUserCategoryTags($_POST['user_id'], (int) $_POST['user_category_id']);
                }
            }
            if (Wo_UpdateUserData($_POST['user_id'], $Update_data)) {
                $data = array(
                    'status' => 200
                );
                //ADD FIND TAGS_OR_UPDATE
                if (isset($_POST['find_tag'])) {
                    Wo_AddUserFindTags($_POST['user_id'], $_POST['find_tag']);
                }
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
