<?php
$wo['description'] = '';
$wo['keywords']    = '';
$wo['page']        = 'search';
$wo['title']       = $wo['lang']['search'] . ' | ' . $wo['config']['siteTitle'];
if ($wo['config']['website_mode'] == 'linkedin') {
    $wo['content'] = Wo_LoadPage('search/linkedin');
} else {

    if(Is_ActivationForSearch()){
        $wo['content'] = Wo_LoadPage("search/activation-page");
    }else{
          $wo['content'] = Wo_LoadPage('search/content');
    };
//  
    
}
