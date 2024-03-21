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

//ПОПОЛНЯЕМ КОШЕЛЕК ЧЕРЕЗ ВИДЖЕТ RNCB
if (isset($_GET['ID']) && isset($_GET['STATUS'])) {
    $result = getOrderRNCB($_GET['ID']);
    if($result['status'] && $result['status'] === 'FullyPaid'){
        Wo_AddMoneyForUserWallet($result['amount'], $_GET['ID']);
    }
}
if (isset($_GET['eid']) && is_numeric($_GET['eid'])) {
		$event             = Wo_EventData($_GET['eid']);
	if ($event  && !empty($event) && (Is_EventOwner($event['id']) || Wo_IsAdmin())) {
		$wo['description'] = $wo['config']['siteDesc'];
		$wo['keywords']    = $wo['config']['siteKeywords'];
		$wo['page']        = 'events';
		$wo['active']      = null;
		$wo['event']       = $event;
		$wo['title']       = $wo['lang']['edit_event'] . ' | ' . $wo['config']['siteTitle'];
		$wo['content']     = Wo_LoadPage('events/edit-event');
	}

}
