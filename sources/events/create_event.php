<?php

if ($wo['loggedin'] == false) {
    header("Location: " . Wo_SeoLink('index.php?link1=welcome'));
    exit();
}

if ($wo['user']['verified'] == 0) {
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
    if ($result['status'] && $result['status'] === 'FullyPaid') {
        Wo_AddMoneyForUserWallet($result['amount'], $_GET['ID']);
//        header("Location: " . Wo_SeoLink('index.php?link1=create-event'));
//        exit();
    }
}


$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords'] = $wo['config']['siteKeywords'];
$wo['page'] = 'events';
$wo['active'] = 6;
$wo['title'] = $wo['lang']['create_events'] . ' | ' . $wo['config']['siteTitle'];
$wo['content'] = Wo_LoadPage('events/create-event');
