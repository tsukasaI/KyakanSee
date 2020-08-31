<?php
require_once 'functions.php';
require_once 'gcal-api.php';
// $span を $revSpan にして スタートとエンドを反対にする
$revSpan = $span * (-1);
date_default_timezone_set('Asia/Tokyo');
$now = date('Y-m-d');
$past = date('Y-m-d', strtotime('-7 day', time()));
$today = date('Ymd' . convert2dig($iniStart) . '0000');
$todayUnix = strtotime($today);
$todayLast = date('Ymd' . convert2dig($iniEnd) . '0000');
$todayLastUnix = strtotime($todayLast);
$timeMin = $past . 'T00:00:00+0900';
$timeMinUnix = strtotime($timeMin);
$timeMaxUnix = strtotime('+' . $span . ' day', $timeMinUnix);
$timeMax = date('Y-m-d', $timeMaxUnix); //マイナスに変更したため timeMax < timeMin
$timeMax = $timeMax . 'T23:59:00+0900';
$optParams = array(
	// 'maxResults' => 10,
	'timeZone' => 'Asia/Tokyo',
	'orderBy' => 'startTime',
	'singleEvents' => true,
	'timeMin' => $timeMin,
	'timeMax' => $timeMax
);
$results = $service->events->listEvents($calendarId, $optParams);
$events = $results->getItems();
if (isset($events)) :
	$ngsArr = array();
	$titleArr = array();
	foreach ($events as $k => $v) {
		$title = empty($v->getSummary()) ? "no title" : $v->getSummary();
		$titleArr[] = $title;
		if ($title === $closeKey) {
			$start = $v->start->date; // 2020-03-10
			$ngStart = $start . 'T' . convert2dig($iniStart) . ':00:00+0900';
			$ngEnd = $start . 'T' . convert2dig($iniEnd) . ':00:00+0900';
			$ngStartUnix = strtotime($ngStart);
			$ngEndUnix = strtotime($ngEnd);
			$ngDiff = $ngEndUnix - $ngStartUnix;
			$ngDiffMin = $ngDiff / 60;
			$ngDiffNum = $ngDiffMin / $divideMin;
			$ngDiffArr = array();
			for ($i = 0; $i < $ngDiffNum; $i++) {
				$minute = $divideMin * $i;
				$ngDiffArr[] = strtotime('+' . $minute . ' minute', $ngStartUnix);
			}
			$ngsArr[] = $ngDiffArr;
		} else {
			$start = $v->start->dateTime; // 2020-03-10T10:00:00+09:00
			$end = $v->end->dateTime; // 2020-03-10T11:00:00+09:00
			$ngStartUnix = strtotime($start);
			$ngEndUnix = strtotime($end);
			$ngDiff = $ngEndUnix - $ngStartUnix;
			$ngDiffMin = $ngDiff / 60;
			$ngDiffNum = $ngDiffMin / $divideMin;
			$ngDiffArr = array();
			for ($i = 0; $i < $ngDiffNum; $i++) {
				$minute = $divideMin * $i;
				$ngDiffArr[] = strtotime('+' . $minute . ' minute', $ngStartUnix);
			}
			$ngsArr[] = $ngDiffArr;
		}
	}
	$ngsArr2 = array();
	foreach ($ngsArr as $k => $v) {
		foreach ($v as $k2 => $v2) {
			$ngsArr2[] = $v2;
		}
	}
	for ($i = $revSpan; $i < 1; $i++) {
		$iniDayLastUnix = strtotime('+' . $i . ' day', $todayLastUnix);
		$ngsArr2[] = $iniDayLastUnix;
	}
endif;
require_once 'header.php';
?>
<div id="app">

	<section class="selectMenu">
		<div class="container">
			<div class="card border-top">
				<div class="card-body">
					<div class="step">Step.1</div>
					<h2>タスクの選択</h2>
					<p>クリックすると選択状態になります</p>
					<div class="row row-cols-1 row-cols-lg-3">
						<div class="col py-1" v-for="v in plan">
							<div class="card card-hover" :class="activeMenu(v.slug)" @click="clickMenu(v.slug)">
								<div class="card-header d-flex justify-content-between">
									<span v-text="v.title"></span><span v-text="v.status"></span>
								</div>
								<div class="card-body" v-text="v.desc"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="timetable">
		<div class="container">
			<div class="card border-top">
				<div class="card-body">
					<div class="step">Step.2</div>
					<h2>開始時間を選択</h2>
					<p>ピンク色の時間帯はすでにデータが入っています</p>
					<div class="table-responsive">
						<table class="table table-bordered table-timetable" :class="{ 'disabled': judgePlanActive }">
							<thead>
								<tr>
									<th class="th1"></th>
									<?php
									$todayYmd = date('m/d', $todayUnix);
									$idArr = array($todayUnix);
									$dateArr = array($todayYmd);
									for ($i = 0; $i < $iniDivide; $i++) {
										$todayUnix = strtotime('+' . $divideMin . ' minute', $todayUnix);
										$todayYmd = date('m/d', $todayUnix);
										$idArr[] = $todayUnix;
										$dateArr[] = $todayYmd;
									}
									?>
									<?php for ($i = $revSpan; $i < 1; $i++) { ?>
										<?php
										$iniDayUnix = strtotime('+' . $i . ' day', $todayUnix);
										$iniDayYm = date('n/j', $iniDayUnix);
										$iniDayW = date('w', $iniDayUnix);
										$iniDayW = get_week_kanji($iniDayW);
										?>
										<th class="th2">
											<div><?php echo $iniDayYm; ?></div>
											<small><?php echo $iniDayW; ?></small>
										</th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($idArr as $k => $v) :
									if (array_key_last($idArr) == $k) { ?>
										<tr>
											<?php $thisTime = date('H:i', $v); ?>
											<th class="th3">
												<div class="thTime"><?php echo $thisTime; ?></div>
											</th>
											<?php for ($i = $revSpan; $i < 1; $i++) : ?>
												<td v-on:mouseover="hoverEffect(<?php echo $nextDayUnix; ?>)" 
													v-on:mouseleave="hoverLeave()" :class="{ 'okTd': hoverJudge(<?php echo $nextDayUnix; ?>), 'okClick': clickJudge(<?php echo $nextDayUnix; ?>) }" 
													id="<?php echo $nextDayUnix; ?>" 
													class="td1">
												</td>
											<?php endfor; ?>
										</tr>
									<?php } else { ?>
										<tr>
											<?php $thisTime = date('H:i', $v); ?>
											<th class="th3">
												<div class="thTime"><?php echo $thisTime; ?></div>
											</th>
											<?php for ($i = $revSpan; $i < 1; $i++) :
												$nextDayUnix = strtotime('+' . $i . 'day', $v);
												$flagOpenClose = 1;
												foreach ($ngsArr as $v2) {
													foreach ($v2 as $v3) {
														if ($v3 == $nextDayUnix) {
															$flagOpenClose = 0; //false
														}
													}
												}
												if ($flagOpenClose) { ?>
													<td v-on:mouseover="hoverEffect(<?php echo $nextDayUnix; ?>)" 
														v-on:mouseleave="hoverLeave()" 
														v-on:click="setStarting(<?php echo $nextDayUnix; ?>)" 
														:class="{ 'okTd': hoverJudge(<?php echo $nextDayUnix; ?>), 'okClick': clickJudge(<?php echo $nextDayUnix; ?>) }" 
														id="<?php echo $nextDayUnix; ?>" 
														class="td2">
														<button class="timeSubmit"></button>
														<input type="hidden" name="selectedPlan" v-model="planActive">
														<input type="hidden" name="unixTime" value="<?php echo $nextDayUnix; ?>">
													</td>
												<?php } else { ?>
													<td class="cellClose"></td>
													<?php } ?>
											<?php 
											endfor; ?>
										</tr>
								<?php }
								endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="reserveForm">
		<div class="container">
			<div class="card border-top">
				<div class="card-body">
					<div class="step">Step.3</div>
					<h2>時間の確認</h2>
					<div class="row mt-2 pb-3">
						<div class="col-12 col-md-3">
							<h3>開始<span class="required"></span></h3>
						</div>
						<div class="col-12 col-md-9">
							<h4 v-html="startTime"></h4>
						</div>
					</div>
					<div class="row mt-2 pb-3">
						<div class="col-12 col-md-3">
							<h3>終了時間<span class="required"></span></h3>
						</div>
						<div class="col-12 col-md-9">
							<select name="endTime" class="custom-select" v-model="selectedEndTime">
								<option v-for="time in endTimeOption">{{ time }}</option>
							</select>
						</div>
					</div>
					<div class="row mt-2">
						<div class="col-12 col-md-3">
							<h3>コメント</h3>
						</div>
						<div class="col-12 col-md-9">
							<div class="form-group">
								<textarea name="memo" v-model="memo" rows="1" @change="changeInput('memo')" class="form-control"></textarea>
							</div>
						</div>
					</div>
					<div class="d-flex justify-content-center my-4">
						<form action="save.php" method="post">
							<a href="" class="btn btn-secondary text-white mr-5">クリア</a>
							<button type="submit" class="btn btn-danger" :disabled="reserveActive">登録する</button>
							<input type="hidden" name="selectYear" v-model="year">
							<input type="hidden" name="selectMonth" v-model="month">
							<input type="hidden" name="selectDay" v-model="day">
							<input type="hidden" name="plan" v-model="planActive">
							<input type="hidden" name="startTime" v-model="startTimeMin">
							<input type="hidden" name="endTime" v-model="selectedEndTime">
							<input type="hidden" name="task" v-model="planActive">
							<input type="hidden" name="memo" v-model="memo">
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>

</div><!-- /#app -->

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script>
	var app = new Vue({
		el: '#app',
		data: {
			plan: <?php include_once 'plan.json'; ?>,
			planActive: '',
			hoverActive: [],
			closed: <?php echo json_encode($ngsArr2); ?>,
			selectedPlanTime: 0,
			startTime: '',
			endTime: '',
			judgePlanActive: true,
			reserveActive: true,
			memo: '',
			startTimeMin: '',
			endTimeMin: '',
			endTimeOption: [],
			selectedEndTime: '',
			year: '',
			month: '',
			day: '',
			task: ''
		},
		mounted: function() {
			// localStorage.clear();
			this.setData();
			this.setPlanActive();
		},
		methods: {
			setData() {
				var setData = ['planActive'];
				for (var k in setData) {
					var t = setData[k];
					if (localStorage.getItem(t)) {
						this[t] = localStorage.getItem(t);
					}
				}
			},
			setPlanActive() {
				if (localStorage.getItem('planActive')) {
					this.judgePlanActive = false;
					this.setPlanTime();
				}
			},
			hoverEffect(e) {
				var planTime = ((this.selectedPlanTime / <?php echo $divideMin; ?>) - 1) * 60 * <?php echo $divideMin; ?>;
				var start = new Date(e * 1000);
				var end = new Date((e + planTime) * 1000);
				var diff = end - start;
				var diffNum = diff / 1000 / 60 / <?php echo $divideMin; ?>;
				var diffArr = [e];
				for (var i = 0; i < diffNum; i++) {
					var minute = <?php echo $divideMin; ?> * (i + 1);
					var pushTime = (e * 1000) + (minute * 60 * 1000);
					pushTime = pushTime / 1000;
					diffArr.push(pushTime);
				}
				this.hoverActive = diffArr;
			},
			hoverLeave() {
				this.hoverActive = '';
			},
			hoverJudge(e) {
				if (this.hoverActive.indexOf(e) >= 0) {
					for (var key in this.hoverActive) {
						if (this.closed.indexOf(this.hoverActive[key]) >= 0) {
							return false;
						}
					}
					return true;
				}
				return false;
			},
			clickJudge(e) {
				if (this.judgePlanActive) {
					return false;
				}
				var planTime = ((this.selectedPlanTime / <?php echo $divideMin; ?>) - 1) * 60 * <?php echo $divideMin; ?>;
				var start = new Date(e * 1000);
				var end = new Date((e + planTime) * 1000);
				var diff = end - start;
				var diffNum = diff / 1000 / 60 / <?php echo $divideMin; ?>;
				var diffArr = [e];
				for (var i = 0; i < diffNum; i++) {
					var minute = <?php echo $divideMin; ?> * (i + 1);
					var pushTime = (e * 1000) + (minute * 60 * 1000);
					pushTime = pushTime / 1000;
					diffArr.push(pushTime);
				}
				for (var key in diffArr) {
					if (this.closed.indexOf(diffArr[key]) >= 0) {
						return false;
					}
				}
				return true;
			},
			activeMenu: function(v) {
				if (this.planActive === v) {
					return 'active';
				}
				return false;
			},
			clickMenu: function(v) {
				this.planActive = v;
				this.judgePlanActive = false;
				this.setPlanTime();
				localStorage.setItem('planActive', v);
			},
			setPlanTime: function() {
				for (var k in this.plan) {
					if (this.planActive === this.plan[k].slug) {
						this.selectedPlanTime = this.plan[k].time;
					}
				}
				this.setEndTime();
			},
			setEndTime: function() {
				var startDateHour = this.startTime.split(":")[0];
				if (startDateHour == '') {
					return false;
				}
				var startDateMinute = this.startTime.split(":")[1];
				var startDateTime = new Date(2000, 1, 1, startDateHour, startDateMinute, 0);
				var endDateTime = startDateTime.setMinutes(startDateTime.getMinutes() + this.selectedPlanTime);
				var endDateHour = new Date(endDateTime).getHours();
				endDateHour = ('0' + endDateHour).slice(-2);
				var endDateMinute = new Date(endDateTime).getMinutes();
				endDateMinute = ('0' + endDateMinute).slice(-2);
				this.endTime = endDateHour + ':' + endDateMinute;
			},
			setStarting: function(e) {
				// 開始時間のセット
				this.year = new Date(e * 1000).getFullYear();
				this.month = new Date(e * 1000).getMonth() + 1;
				this.day = new Date(e * 1000).getDate();
				this.startTime = this.convertUnixToYmdHm(e);
				let startTimeHour = new Date(e * 1000).getHours();
				startTimeHour = ('0' + startTimeHour).slice(-2);
				let startTimeMin = new Date(e * 1000).getMinutes();
				startTimeMin = ('0' + startTimeMin).slice(-2);
				this.startTimeMin = startTimeHour + ':' + startTimeMin;
				this.endTime = this.convertUnixToYmd(e);
				let endTimeHour = new Date(e * 1000).getHours();
				let endTimeMin = new Date(e * 1000).getMinutes();
				this.endTimeMin = endTimeHour + ':' + endTimeMin;

				// 終了時間のオプションを配列でセット
				this.endTimeOption = [];
				if (endTimeMin === 30) {
					for (let i = endTimeHour + 1; i < 24; i++) {
						this.endTimeOption.push(i + ':00');
						this.endTimeOption.push(i + ':30');
					}
				} else {
					this.endTimeOption.push(endTimeHour + ':30');
					for (let i = endTimeHour + 1; i < 24; i++) {
						this.endTimeOption.push(i + ':00');
						this.endTimeOption.push(i + ':30');
					}
				}
				this.endTimeOption.push(24 + ':00');
			},
			switchReserveActive: function() {
				if (this.planActive != "" && this.startTime != "" && this.endTime != "" && this.yourName != "") {
					this.reserveActive = false;
				} else {
					this.reserveActive = true;
				}
			},
			convertUnixToYmdHm: function(e) {
				let dateTime = new Date(e * 1000);
				var year = dateTime.getFullYear();
				var month = dateTime.getMonth() + 1;
				var day = dateTime.getDate();
				var hour = dateTime.getHours();
				hour = ('0' + hour).slice(-2);
				var minute = dateTime.getMinutes();
				minute = ('0' + minute).slice(-2);
				return year + '年' + month + '月' + day + '日 ' + hour + ':' + minute;
			},
			convertUnixToYmd: function(e) {
				let dateTime = new Date(e * 1000);
				var year = dateTime.getFullYear();
				var month = dateTime.getMonth() + 1;
				var day = dateTime.getDate();
				return year + '年' + month + '月' + day + '日';
			},
			changeInput: function(c) {
				localStorage.setItem(c, this[c]);
			}
		},
		watch: {
			planActive: function(v) {
				this.switchReserveActive();
				if (v.length > 0) {
					this.isStartDisabled = false;
				} else {
					this.isStartDisabled = true;
				}
			},
			startTime: function(v) {
				this.switchReserveActive();
			},
			endTime: function(v) {
				this.switchReserveActive();
			},
			yourName: function(v) {
				this.switchReserveActive();
			}
		}
	})
</script>

<?php require_once 'footer.php'; ?>