<?php
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Online Quiz  - Result </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="quiz.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
		include("header.php");
		include("database.php");
		extract($_SESSION);
		$con=mysqli_connect("localhost","root","","quiz_new");
				if(!$con)
				{
						die("connection failed!!".mysqli_connect_error());
				}
		$sql="select t.test_name,t.total_que,r.test_date,r.score,r.grade from mst_test t, mst_result1 r where
		t.test_id=r.test_id and r.login='$login'";
		$rs=mysqli_query($con,$sql);// or die(mysqli_error());

		echo "<h1 class=head1> Result </h1>";
		if(mysqli_num_rows($rs)<1)
		{
			echo "<br><br><h1 class=head1> You have not given any quiz</h1>";
			exit;
		}
		echo "<table border=1 align=center><tr class=style2><td width=300>Test Name<td> Total<br> Question <td>Date  <td> Score <td> Grade";
		while($row=mysqli_fetch_row($rs))
		{
		echo "<tr class=style8><td>$row[0] <td align=center> $row[1]<td align=center> $row[2]  <td align=center> $row[3]<td align=center> $row[4]";
		}
		echo "</table>";
		echo "
		<br>
		<br>
		<table align='Center' cellspacing='0' border='1'>
		<tr>
			<td>GRADE</td>
			<td>Range</td>
		</tr>
		<tr>
			<td>A+</td>
			<td> >=90</td>
		</tr>
		<tr>
			<td>A</td>
			<td> >=80 and < 90 </td>
		</tr>
		<tr>
			<td>B+</td>
			<td> >=70 and < 80 </td>
		</tr>
		<tr>
			<td>B</td>
			<td> >=60 and < 70 </td>
		</tr>
		<tr>
			<td>C</td>
			<td> >=50 and < 60 </td>
		</tr>
		<tr>
			<td>D</td>
			<td> >=40 and < 50 </td>
		</tr>
		<tr>
			<td>A</td>
			<td> >=30 and < 40 </td>
		</tr>
		<tr>
			<td>A]Fail</td>
			<td>  < 30 </td>
		</tr>
	</table>";

?>
</body>
</html>
