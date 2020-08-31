<?php
  require_once 'functions.php';
  $year = isset($_POST['selectYear']) ? $_POST['selectYear'] : "";
  $month = isset($_POST['selectMonth']) ? $_POST['selectMonth'] : "";
  $day = isset($_POST['selectDay']) ? $_POST['selectDay'] : "";
  $thisDay = $year . "年" . $month . "月" . $day . "日";
  $title = isset($_POST['plan']) ? $_POST['plan'] : "";
  $name = isset($_POST['yourName']) ? $_POST['yourName'] : "";
  $tel = isset($_POST['yourTel']) ? $_POST['yourTel'] : "";
  $mail = isset($_POST['yourMail']) ? $_POST['yourMail'] : "";
  $startTime = isset($_POST['startTime']) ? $_POST['startTime'] : "";
  $endTime = isset($_POST['endTime']) ? $_POST['endTime'] : "";
  $memo = isset($_POST['memo']) ? $_POST['memo'] : "";
  $memo = nl2br($memo);
  require_once 'header.php';
?>
<section class="thanks">
  <div class="container text-center">
    <h2 class="mb-4">登録しました</h2>
    <div class="card">
      <div class="card-header bg-wine">登録内容</div>
      <div class="card-body">
        <table class="thanks table table-borderess">
          <tr>
            <th>日時</th><td><?php echo $thisDay; ?> <?php echo $startTime; ?>～<?php echo $endTime; ?></td>
          </tr>
          <tr>
            <th>メニュー</th><td><?php echo $title; ?></td>
          </tr>
          <tr>
            <th>備考</th><td><?php echo $memo; ?></td>
          </tr>
        </table>
      </div>
    </div>
    <div class="mt-5">
    	<a href="index.php" class="btn btn-secondary">タイムテーブルに戻る</a>
    </div>
  </div>
</section>

<script>localStorage.clear();</script>

<?php require_once 'footer.php'; ?>