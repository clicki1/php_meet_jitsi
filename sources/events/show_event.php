<?php

if ($wo['loggedin'] == true) {
    Wo_UpdateEventActive($_GET['eid']);
}

if ($wo['config']['events_visibility'] == 1) {
    if ($wo['loggedin'] == false) {
        //ОТКРЫВАЕМ СТРАНИЦУ
//	  header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
//	  exit();
    }
}

if ($wo['config']['events'] == 0) {
    header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}

if (isset($_GET['ID']) && isset($_GET['STATUS'])) {
    $result = getOrderRNCB($_GET['ID']);
    if($result['status'] && $result['status'] === 'FullyPaid'){
        Wo_AddMoneyForUserWallet($result['amount'], $_GET['ID']);
    }
}

if (isset($_GET['eid']) && is_numeric($_GET['eid'])) {
//     Wo_UpdateEventActive($_GET['eid']);
    $event = Wo_EventData($_GET['eid']);

    if ($event && !empty($event)) {

        $wo['description'] = strip_tags($event['description']);
        $wo['keywords'] = $wo['config']['siteKeywords'];
        $wo['page'] = 'events';
        $wo['event'] = $event;
        $wo['result'] = Wo_UserData($wo['event']['poster_id']);
        if (!empty($wo['result']['avatar_full'])) {
            $wo['result']['avatar'] = Wo_GetMedia($wo['result']['avatar_full']);
        }
        $wo['event']['going'] = Wo_TotalGoingUsers($event['id']);
        $wo['event']['interested'] = Wo_TotalInterestedUsers($event['id']);
        $wo['event']['invited'] = Wo_TotalInvitedUsers($event['id']);
        $wo['events'] = Wo_GetSuggestedEvents(array("limit" => 5));
        $wo['title'] = $event['name'];
        $wo['content'] = Wo_LoadPage('events/content');
    }
}
