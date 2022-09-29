<?php
/**
 * Include script with class implementation
 */
require('GetFromTikTok.php');

$url = 'https://www.tiktok.com/@anbrey757/video/7024376296608189697';

/**
 * Create class object
 */
$tiktok = new GetFromTikTok($url);

/**
 * Create file with video from TikTok or print error and exit
 */
if ($tiktok->get_error() == 0) {
    file_put_contents('video.' . $tiktok->get_format(), $tiktok->get_video());
} else {
    die($tiktok->get_error_msg());
}