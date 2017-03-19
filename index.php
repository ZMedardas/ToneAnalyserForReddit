<html>
  <head>
	  <?php include "layout/head.php"; ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Date', 'Occurences','<?php echo $_GET['emot']?>']
			<?php
			
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
				
				$wor=$_GET['word'];
				
				require "database.php";
				
				$conn = getConnection();
				
				$sql_start="SELECT COUNT(commID) as num, ROUND(UNIX_TIMESTAMP(time)/(15 * 60)) AS timekey FROM comments ";
				$sql_end=" GROUP BY timekey ORDER BY timekey";
				$sql_start2="SELECT AVG(anger) as anger,AVG(disgust) as disgust,AVG(fear) as fear,AVG(joy) as joy,AVG(sadness) as sadness,AVG(analytical) as analytical,AVG(confident) as confident,AVG(tentative) as tentative,AVG(openness) as openness,AVG(conscientiousness) as conscientiousness,AVG(extraversion) as extraversion,AVG(agreeableness) as agreeableness,AVG(emotional) as emotional, ROUND(UNIX_TIMESTAMP(time)/(15 * 60)) AS timekey FROM tones WHERE commID IN (SELECT commID FROM comments WHERE INSTR(LOWER(body), ' ".$wor." ') > 0) ";
				$sql_end2=" GROUP BY timekey ORDER BY timekey";
				
				$stmt = $conn->prepare($sql_start."WHERE INSTR(LOWER(body), ' ".$wor." ') > 0".$sql_end);
				$stmt->execute();
				$res = $stmt->get_result();
				
				$stmt2 = $conn->prepare($sql_start2.$sql_end2);
				$stmt2->execute();
				$res2 = $stmt2->get_result();
				$tk=1653285;
				$tot=0;
				$row2 = $res2->fetch_assoc();
				while($row = $res->fetch_assoc()) {
					while($tk!=$row['timekey'])
					{
						$dt = new DateTime("@".$tk*15*60);
						echo ",['".$dt->format('Y-m-d H:i:s')."',0,0]";
						$tk+=1;
					}
					$dt = new DateTime("@".$row['timekey']*15*60);
					if($row['timekey']==$row2['timekey'])
					{
						echo ",['".$dt->format('Y-m-d H:i:s')."',".$row['num'].",".(60*$row2[$_GET['emot']])."]";
						$row2 = $res2->fetch_assoc();
					}
					else
					{
						echo ",['".$dt->format('Y-m-d H:i:s')."',".$row['num'].",0]";
					}
					$tot+=$row['num'];
					$tk+=1;
				}
			?>
        ]);
        var options = {
          title: '',
          vAxis: {title:'Occurences'},
          hAxis: {title:'Dates'},
          legend: { position: 'bottom' },
        };
        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
	  <?php include "layout/nav.php"; ?>
	  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
					<div class="panel-heading">
						<h3>Trends and Emotions</h3>
					</div>
					<div class="panel-body">
						<div class="center-block" id="curve_chart" style="width: 900px; height: 500px"></div>
					</div>
					<form id="queryForm" action="index.php" method="get" novalidate>
						<div class="control-group form-group">
							<div class="controls">
								<label>Topic</label>
								<input type="text" class="form-control" id="word" name="word" value=<?php if(!empty($_GET['word']))echo '"'.$_GET['word'].'"'; else echo ""; ?> >
								
							</div>
						</div>
						<div class="control-group form-group">
							<div class="controls">
								<label>Emotion</label>
								<select name="emot" for="queryForm">
								  <option value="anger">anger</option>
								  <option value="disgust">disgust</option>
								  <option value="fear">fear</option>
								  <option value="joy">joy</option>
								  <option value="sadness">sadness</option>
								  <option value="analytical">analytical</option>
								  <option value="confident">confident</option>
								  <option value="tentative">tentative</option>
								  <option value="openness">openness</option>
								  <option value="conscientiousness">conscientiousness</option>
								  <option value="extraversion">extraversion</option>
								  <option value="agreeableness">agreeableness</option>
								  <option value="emotional">emotional</option>
								</select>
							</div>
						</div>
						<input type="submit" value="Query" class="btn btn-primary"></button>
					</form>
					<div>
						<?php
							echo "Total occurences: ".$tot."<br><br>";
						
							$sql_start="SELECT time, body FROM comments ";
							$sql_end=" ORDER BY time LIMIT 20";
							
							if(true)
							{
								$stmt = $conn->prepare($sql_start."WHERE INSTR(LOWER(body), ' ".$wor." ') > 0".$sql_end);
								$stmt->execute();
							}
							else
							{
								$stmt = $conn->prepare($sql_start.$sql_end);
								$stmt->execute();
							}
							$res = $stmt->get_result();
							$d=24;
							$h=22;
							$m=0;
							while($row = $res->fetch_assoc()) {
								echo "<p>".$row['time']." ".$row['body']."</p><br>";
							}
						?>
					</div>
				</div>
			</div>
		</div>
  </body>
</html>
