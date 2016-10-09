<html>
<head>
	<title>
	</title>
</head>
	<body>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
	$( function() {
		$( "#date" ).datepicker({
		  showWeek: true,
		  firstDay: 1,
		  dateFormat: "yy-mm-dd",
		  showOtherMonths: true,
     	  selectOtherMonths: true
		});
	} );
	</script>
</body>
<body>
<div id="container">
	<h1>Todays Shifts</h1>
	<form method='get' action='index.php' name='date'>
	</form>
	</div>
	<div id="left">

<?php
 
// require the ShiftPlanning SDK class
require('src/shiftplanning.php');

/* set the developer key on class initialization */
$shiftplanning = new shiftplanning(
	array(
		'key' => 'a78dcab4104ccd7338d83b0ebcc40692aed9f782'
	)
);

// check for a current active session
// if a session exists, $session will now hold the user's information
$session = $shiftplanning->getSession( );

if( !$session )
{// if a session hasn't been started, create one
	// perform a single API call to authenticate a user
	$response = $shiftplanning->doLogin(
		array(// these fields are required to login
			'username' => 'mladenurosevic',
			'password' => 'Sinhro16031984',
		)
	);
	if( $response['status']['code'] == 1 )
	{// check to make sure that login was successful
		$session = $shiftplanning->getSession( );	// return the session data after successful login
		$loged_in=1;
		echo "logged in";
	}
	else
	{// display the login error to the user
		echo "You are not logged in, Please contact admin";
		echo $response['status']['text'] . "--" . $response['status']['error'];
		$loged_in=0;
	}
}
else
{
	//session already set and user logged in
	$loged_in=1;
}


if($loged_in==1)
{ // assume session has been established and customer logged in

	if (!isset($_GET['date']))
	{
		$today = date("Y-m-d",strtotime("today"));
	} 
	else
	{
		
		if (check_your_datetime($_GET['date'])==true){
			$today = $_GET['date'];
		}
		else{
			$today = date("Y-m-d",strtotime("today"));
		}

	}



	$week=date("D",strtotime($today));
	
	echo "<table class='shift'><caption>Schedule for " .$today." ".$week. "</caption>";
	$todays_shifts = $shiftplanning->getShifts('overview', array('start_date' => $today ,'end_date' => $today) );
	$i=0;
	foreach($todays_shifts['data'] as $shift){
		foreach($shift['employees'] as $employee){
			//store each employees data in array
			$employees_shift[$i]['name']=$employee["name"];	
			$employees_shift[$i]['schedule_name']=$shift["schedule_name"];
			$employees_shift[$i]['time']=$shift["start_time"]["time"]." - ".$shift["end_time"]["time"];	
			$i++;	
		}						
	}

	foreach($employees_shift as $employee_shift){
		//go throguh each employee and display data
		echo "<tr><td class='employee'>".$employee_shift['name']."</td></tr>";
		echo "<tr><td class='position'>Possition: ".$employee_shift['schedule_name']." ". $employee_shift['time'] ."</td></tr>";
	}
	echo "</table>";
}

function check_your_datetime($x) {
    return (date('Y-m-d', strtotime($x)) == $x);
}

?>
	</div>
</div>	
</body>
</html>