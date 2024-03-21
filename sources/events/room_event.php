<?php

if ($wo['loggedin'] == false) {

    header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}

if ($wo['config']['events'] == 0) {

    header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}
if (!$wo['config']['can_use_events']) {

    header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}

if (isset($_GET['eid']) && is_numeric($_GET['eid'])) {
    //ОБНОВЛЕНИЕ ДАННЫХ ПО МЕРОПРИЯТИЯМ
    Wo_UpdateEventActive($_GET['eid']);

    $event = Wo_EventData($_GET['eid']);


    if ($event && !empty($event) && (Is_EventOwner($event['id']) || Wo_IsAdmin() || Is_EventGoungUser($event['id']) )) {


        $wo['description'] = $wo['config']['siteDesc'];
        $wo['keywords'] = $wo['config']['siteKeywords'];
        $wo['page'] = 'events';
        $wo['active'] = null;
        $wo['event'] = $event;
        $wo['group'] = Wo_GroupChatEvent($event['id']) ?? array();
        $wo['title'] = $wo['lang']['room_event'] . ' | ' . $wo['config']['siteTitle'];
        $wo['content'] = Wo_LoadPage('events/room-event');
    }
}


