<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

changeCover($group_id, $change_interval);

function changeCover($group_id, $change_interval = 30) {
    while (1) {
        $upload_url = getCoverUploadServer($group_id);
        $cover = uploadCover($upload_url);
        $res = saveCover($cover);
        if ($res['response']) {
            verbose('Group cover successfully changed. Waiting for '.$change_interval." seconds to next cover change\n");
        }

        sleep ($change_interval);
        changeCover($group_id, $change_interval);
    } 
}

function getCoverUploadServer($group_id) {
    $upload_url = vkRequest("photos.getOwnerCoverPhotoUploadServer", ['group_id' => $group_id, 'crop_x2'=>1590]);
    if ($upload_url['response']) verbose('Upload server received');

    return $upload_url['response']['upload_url'];
}

function saveCover($cover) {
    $res = vkRequest("photos.saveOwnerCoverPhoto", ["hash"=>$cover['hash'], "photo"=>$cover['photo']]);
    if ($res['response']) verbose('Cover photo saved');
    
    return $res;
}

function uploadCover($url) {
    global $covers_dir;

    $covers = scandir(__DIR__.'/'.$covers_dir); 

    if (count($covers) > 2) {
        $cover_path = __DIR__.'/'.$covers_dir.'/'.$covers[mt_rand(2,count($covers)-1)];
        $photo_file = new CURLFile($cover_path,'multipart/form-data');
        $res = json_decode(curlPost($url, ['photo' => $photo_file]), true);
        if ($res['hash'] && $res['photo']) verbose('Photo uploaded');

        return $res;        
    } else {
        error('Covers directory is empty. Is no photos to upload');
    }

}

function curlPost($url, $params) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

    $res = curl_exec($ch);

    return $res;
}

function vkRequest($method, $params) {
    global $access_token;

    $params['access_token'] = $access_token;
    $res = json_decode(curlPost('https://api.vk.com/method/'.$method, $params), true);

    if (isset($res['error'])) {
        if ($res['error']['error_msg'] == 'Captcha needed') {
            warning('Captcha needed! Trying to solve captcha by antigate...');
            $params['captcha_sid'] = $res['error']['captcha_sid'];
            $params['captcha_key'] = solveCaptcha($res['error']['captcha_img']);
            
            return vkRequest($method, $params);
        }
    } else {
        return $res;
    }
}

function solveCaptcha($img) {
    $antigate = new NekoWeb\AntigateClient();
    $antigate->setApiKey('bc557b2f0e211930e17c8736dd5751fa');
    $res = $antigate->recognizeByUrl($img);
    warning('Captcha successfully solved! Resending request...');

    return $res;
}

function verbose($msg) {
    global $log_level;
    if ($log_level == 'verbose') echo "[\033[32m OK \033[0m] ".$msg. "\n";
}

function warning($msg) {
    global $log_level;
    if ($log_level == 'verbose' || $log_level == 'warning' ) echo "[\033[33m WARNING \033[0m] ".$msg. "\n";
}

function error($msg) {
    global $log_level;
    echo "[\033[31m ERROR \033[0m] ".$msg. "\n";
    die();
}