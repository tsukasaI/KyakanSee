<?php
  require_once "functions.php";
  get_header();
  $thisDay = !empty($_GET['date']) ?  $_GET['date'] : "";
?>
<section class="py-4">
  <div class="container">
    <div class="card">
      <div class="card-body text-center">
        <div class="h4"><?php echo $thisDay; ?><span class="small">の</span></div>
        <div class="h5 mb-5">登録できません</div>
        <a href="schedule.php" class="btn btn-danger">カレンダーへ戻る</a>
      </div>
    </div>
  </div>
</section>
<?php get_footer(); ?>