<?php
if ($wo['loggedin'] == false) {
  header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
  exit();
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

$category_id = $_GET['category_id'] ?? 0;
$e_type = $_GET['e_type'] ?? "like";

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'events';
$wo['active']      = 4;
//$wo['events']      = Wo_GetInterestedEvents();
$wo['events']      = Wo_GetEventsSearch($category_id, $e_type);
$wo['title']       = 'Избранные мероприятия';
//$wo['title']       = $wo['lang']['event_intersted'] . ' | ' . $wo['config']['siteTitle'];
//var_dump($wo['title'] );
//exit();
$wo['content']     = Wo_LoadPage('events/events-interested');