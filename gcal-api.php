<?php
require_once __DIR__.'/vendor/autoload.php';
// $aimJsonPath = __DIR__ . '/key/my-project-81076-portfoolio-575b7e3dcecd.json';
$client = new Google_Client();
$client->setApplicationName('portfolio');
// 予定を取得する時は Google_Service_Calendar::CALENDAR_READONLY
// 予定を追加する時は Google_Service_Calendar::CALENDAR_EVENTS
// $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
$client->setScopes(Google_Service_Calendar::CALENDAR_EVENTS);
$client->setAuthConfig($_ENV['ENV_KEY']);
$service = new Google_Service_Calendar($client);
$calendarId = '68lj7einrd9os6a2i0i1fn2oak@group.calendar.google.com';
?>