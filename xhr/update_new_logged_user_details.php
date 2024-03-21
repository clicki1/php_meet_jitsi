<?php

if ($f == 'update_new_logged_user_details') {
    if (empty($_POST['new_password']) || empty($_POST['username']) || empty($_POST['repeat_new_password']) || (empty($_POST['user_category_id']) || $_POST['user_category_id'] == 0) || (!isset($_POST['tag']) || (empty($_POST['tag'])) || $_POST['user_category_id'] == 0) || Wo_CheckSession($hash_id) === false) {
        $errors[] = $error_icon . $wo['lang']['please_check_details'];
    } else {
        if ($_POST['new_password'] != $_POST['repeat_new_password']) {
            $errors[] = $error_icon . $wo['lang']['password_mismatch'];
        }
        if (strlen($_POST['new_password']) < 6) {
            $errors[] = $error_icon . $wo['lang']['password_short'];
        }
        if (strlen($_POST['username']) > 32) {
            $errors[] = $error_icon . $wo['lang']['username_characters_length'];
        }
        if (strlen($_POST['username']) < 5) {
            $errors[] = $error_icon . $wo['lang']['username_characters_length'];
        }
        if (!preg_match('/^[\w]+$/', $_POST['username'])) {
            $errors[] = $error_icon . $wo['lang']['username_invalid_characters'];
        }
        if (Wo_UserExists($_POST['username']) === true) {
            $errors[] = $error_icon . $wo['lang']['username_exists'];
        }
        if (empty($errors)) {
            $Update_data = array(
                'password' => password_hash($_POST['new_password'], PASSWORD_DEFAULT),
                'username' => $_POST['username'],
                'category_id' => (int) $_POST['user_category_id'],
                'social_login' => 0
            );
            if (Wo_UpdateUserData($_POST['user_id'], $Update_data)) {
                $get_user = Wo_UserData($_POST['user_id']);
                //ДОБАВЛЯЕМ ТЕГИ К ПОЛЬЗОВАТЕЛЮ
                if (isset($_POST['tag']) && !empty($_POST['tag'])) {
                    Wo_AddUserCategoryTags($_POST['user_id'], (int) $_POST['user_category_id'], $_POST['tag']);
                } else {
                    Wo_AddUserCategoryTags($_POST['user_id'], (int) $_POST['user_category_id']);
                }
                $data = array(
                    'status' => 200,
                    'message' => $success_icon . $wo['lang']['setting_updated'],
                    'url' => $get_user['url']
                );
            }
        }
    }
    header("Content-type: application/json");
    if (isset($errors)) {
        echo json_encode(array(
            'errors' => $errors
        ));
    } else {
        echo json_encode($data);
    }
    exit();
}
