<?php
//
//var_dump(time());
//exit();
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

$category_id = $_GET['category_id'] ?? 0;
$e_type = $_GET['e_type'] ?? "all";

if (isset($_GET['ID']) && isset($_GET['STATUS'])) {
    $result = getOrderRNCB($_GET['ID']);
    if($result['status'] && $result['status'] === 'FullyPaid'){
        Wo_AddMoneyForUserWallet($result['amount'], $_GET['ID']);
    }
}

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords'] = $wo['config']['siteKeywords'];
$wo['page'] = 'events';
$wo['active'] = 7;
$wo['title'] = $wo['lang']['events'] . ' | ' . $wo['config']['siteTitle'];
$wo['events'] = Wo_GetEventsSearch($category_id, $e_type);
$wo['category_id'] = $category_id;
$wo['e_type'] = $e_type;
$wo['content'] = Wo_LoadPage('events/events-search');
