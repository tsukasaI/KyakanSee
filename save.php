<?php
  require_once 'functions.php';
  $year = !empty($_POST['selectYear']) ? $_POST['selectYear'] : "";
  $month = !empty($_POST['selectMonth']) ? $_POST['selectMonth'] : "";
  $day = !empty($_POST['selectDay']) ? $_POST['selectDay'] : "";
  $thisDay = $year . "年" . $month . "月" . $day . "日";
  $title = !empty($_POST['plan']) ? $_POST['plan'] : "";
  $name = !empty($_POST['task']) ? $_POST['task'] : "";
  $startTime = !empty($_POST['startTime']) ? $_POST['startTime'] : "";
  $endTime = !empty($_POST['endTime']) ? $_POST['endTime'] : "";
  $memo = !empty($_POST['memo']) ? $_POST['memo'] : "";
  $memoBr = nl2br($memo);
  $desc = $title;
  if ( !empty($memo) ) {
    $desc .= "\n" . $memo;
  }
  $events = get_google_calendar_this_event($year, $month, $day);
  $url = 'error-booking.php?date=' . $thisDay;
  judge_close_redirect($events, $url);
  $event = insert_google_calendar_event($name, $desc, $year, $month, $day, $startTime, $endTime);
  if ( $event ) {
    $url = 'comfirm.php';
    header('Location: ' . $url, true, 307);
    exit;
  }else{
    $url = 'error-reservation.php';
    header('Location: ' . $url, true, 307);
    exit;  
  }
?>