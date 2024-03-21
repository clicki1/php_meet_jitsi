<?php

if ($wo['config']['events_visibility'] == 1) {
	if ($wo['loggedin'] == false) {
	  header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
	  exit();
	}
}

if ($wo['config']['events'] == 0) {
	header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}
$category_id = $_GET['category_id'] ?? 0;
$e_type = $_GET['e_type'] ?? "all";
//ОБНОВЛЕНИЕ ДАННЫХ ПО МЕРОПРИЯТИЯМ
//Wo_UpdateEventActive($event_id = null);
//ПОПОЛНЯЕМ КОШЕЛЕК ЧЕРЕЗ ВИДЖЕТ RNCB
if (isset($_GET['ID']) && isset($_GET['STATUS'])) {
    $result = getOrderRNCB($_GET['ID']);
    if($result['status'] && $result['status'] === 'FullyPaid'){
        Wo_AddMoneyForUserWallet($result['amount'], $_GET['ID']);
        
    }
    header("Location: " . Wo_SeoLink('index.php?link1=events'));
    exit();
}


$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'events';
$wo['active']      = 1;
$wo['events']      =  Wo_GetEventsSearch($category_id, $e_type);
$wo['title']       = $wo['lang']['events_upcoming'] . ' | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('events/events-upcomming');