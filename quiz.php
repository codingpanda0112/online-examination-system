<?php
	session_start();
	error_reporting(1);
	include("database.php");
	extract($_POST);
	extract($_GET);
	extract($_SESSION);
	/*$rs=mysqli_query("select * from mst_question where test_id=$tid",$cn) or die(mysqli_error());
	if($_SESSION[qn]>mysqli_num_rows($rs))
	{
	unset($_SESSION[qn]);
	exit;
	}*/
	if(isset($subid) && isset($testid))
	{
		$_SESSION[sid]=$subid;
		$_SESSION[tid]=$testid;
		header("location:quiz.php");
	}
	if(!isset($_SESSION[sid]) || !isset($_SESSION[tid]))
	{
		header("location: index.php");
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
		<head>
		<title>Online Quiz</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="quiz.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
		include("header.php");
		$con=mysqli_connect("localhost","root","","quiz_new");
		if(!$con)
		{
			die("connection failed!!".mysqli_connect_error());
		}
		$query="select * from mst_question";
		$sql="select * from mst_question where test_id=$tid";
		$rs=mysqli_query($con,$sql);
		$row = mysqli_fetch_assoc($rs);
		$timervalue = $row['timer'];

		if($timervalue > 0){
			
			$_SESSION["subject"] = $newsubject;
			$_SESSION["username"] = $user_data['username'];
			$_SESSION["class"] = $user_data['class'];

			$_SESSION["duration"] =  $timervalue;
			$_SESSION["start_time"] = date("Y-m-d H:i:s");

			$end_time =  $end_time= date("Y-m-d H:i:s", 
			strtotime('+'.$_SESSION["duration"].'seconds',
			strtotime($_SESSION["start_time"])));


			$_SESSION["end_time"] = $end_time;
				
		}

		//or die(mysqli_error());
?>

<script>

		var count = <?php echo"time left". $timervalue ?>;
		var counter = setInterval(timer, 1000); //1000 will  run it every 1 second

		function timer() {
		    count = count - 1;
		    if (count == -1) {
		        clearInterval(counter);
				document.getElementById("submittime").click();
		        return;
		    }
		}
</script>

<script type="text/javascript">
		setInterval(function()
		{
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open("GET","system/response.php",false);
		xmlhttp.send(null);
		document.getElementById("response").innerHTML=xmlhttp.responseText;
		},1000);
</script>


<?php
		$con=mysqli_connect("localhost","root","","quiz_new");
		if(!$con)
		{
			die("connection failed!!".mysqli_connect_error());
		}

		if(!isset($_SESSION[qn]))
		{
			$_SESSION[qn]=0;
			mysqli_query("delete from mst_useranswer where sess_id='" . session_id() ."'") or die(mysqli_error());
			$_SESSION[trueans]=0;
			
		}
		else
		{	
				if($submit=='Next Question' && isset($ans))
				{
						mysqli_data_seek($rs,$_SESSION[qn]);
						$row= mysqli_fetch_row($rs);	
						$sql2="insert into mst_useranswer(sess_id, test_id, que_des, ans1,ans2,ans3,ans4,true_ans,your_ans) values ('".session_id()."', $tid,'$row[2]','$row[3]','$row[4]','$row[5]', '$row[6]','$row[7]','$ans')";

						mysqli_query($con,$sql2);//or die(mysqli_error());
						if($ans==$row[7])
						{
									$_SESSION[trueans]=$_SESSION[trueans]+1;
						}
						$_SESSION[qn]=$_SESSION[qn]+1;
						$qno=$_SESSION[qn];

						$sqls="insert into active(session_id,userid,attempted,testid) values('".session_id()."','$login',$qno,$tid)";
						mysqli_query($con,$sqls);
				}
				else if($submit=='Get Result' && isset($ans))
				{
						mysqli_data_seek($rs,$_SESSION[qn]);
						$row= mysqli_fetch_row($rs);	
						$sql3=
						mysqli_query($con,"insert into mst_useranswer(sess_id, test_id, que_des, ans1,ans2,ans3,ans4,true_ans,your_ans) values ('".session_id()."', $tid,'$row[2]','$row[3]','$row[4]','$row[5]', '$row[6]','$row[7]','$ans')");
						// or die(mysqli_error());
						if($ans==$row[7])
						{
									$_SESSION[trueans]=$_SESSION[trueans]+1;
						}

						echo "<h1 class=head1> Result</h1>";
						$_SESSION[qn]=$_SESSION[qn]+1;
						echo "<Table align=center><tr class=tot><td>Total Question<td> $_SESSION[qn]";
						$total= $_SESSION[qn];
						$percent=($_SESSION[trueans]/$total)*100;
						if($percent>=90)
						{
							$grade='A+';
						}
						elseif ($percent>=80 && $percent<90) {
							$grade='A';
						}
						elseif ($percent>=70 && $percent<80) {
							$grade='B+';
						}
						elseif ($percent>=60 && $percent<70) {
							$grade='B';
						}
						elseif ($percent>=50 && $percent<60) {
							$grade='C';
						}
						elseif ($percent>=40 && $percent<50) {
							$grade='D';
						}
						elseif ($percent>=30 && $percent<40) {
							$grade='E';
						}
						else 
						{
							$grade='FAIL';
						}
						
						echo "<tr class=tans><td>True Answer<td>".$_SESSION[trueans];
						$w=$_SESSION[qn]-$_SESSION[trueans];
						echo "<tr class=fans><td>Wrong Answer<td> ". $w;
						if($grade=='FAIL')
						{
							echo "<tr class=fans><td>Grade:<td> ". $grade;
						}
						else
							echo "<tr class=tans><td>Grade:<td> ". $grade;

						echo "</table>";
						

						$sql4="insert into mst_result1(login,test_id,test_date,score,grade) values('$login',$tid,'".date("Y-m-d")."',$_SESSION[trueans],'$grade')";
						$sql6="delete from active where session_id='".session_id()."'";
						mysqli_query($con,$sql6);
						
						mysqli_query($con,$sql4);// or die(mysqli_error());
						echo "<h1 align=center><a href=review.php> Review Question</a> </h1>";
						unset($_SESSION[qn]);
						unset($_SESSION[sid]);
						unset($_SESSION[tid]);
						unset($_SESSION[trueans]);
						exit;
				}
		}
		$sql5="select * from mst_question where test_id=$tid";
		$rs=mysqli_query($con,$sql5);// or die(mysqli_error());
		if($_SESSION[qn]>mysqli_num_rows($rs)-1)
		{
		unset($_SESSION[qn]);
		echo "<h1 class=head1>Some Error  Occured</h1>";
		session_destroy();
		echo "Please <a href=index.php> Start Again</a>";

		exit;
		}
		mysqli_data_seek($rs,$_SESSION[qn]);
		$row= mysqli_fetch_row($rs);
		echo "<form name=myfm method=post action=quiz.php>";
		echo "<table width=100%> <tr> <td width=30>&nbsp;<td> <table border=0>";
		$n=$_SESSION[qn]+1;
		echo "<tR><td><span class=style2>Que ".  $n .": $row[2]</style>";
		echo "<tr><td class=style8><input type=radio name=ans value=1>$row[3]";
		echo "<tr><td class=style8> <input type=radio name=ans value=2>$row[4]";
		echo "<tr><td class=style8><input type=radio name=ans value=3>$row[5]";
		echo "<tr><td class=style8><input type=radio name=ans value=4>$row[6]";

		if($_SESSION[qn]<mysqli_num_rows($rs)-1)
		echo "<tr><td><input type=submit name=submit value='Next Question'></form>";
		else
		echo "<tr><td><input type=submit name=submit value='Get Result'></form>";
		echo "</table></table>";
?>
</body>
</html>