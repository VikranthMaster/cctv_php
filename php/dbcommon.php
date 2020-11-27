<?php

$get_summary_query = "
			select Cam.name as camName, CamDate.date as date, count(*) as count                                                          
                        from cctv.Photo                                                
                        join cctv.CameraDate as CamDate on cameraDateID=CamDate.UID                    
                        join cctv.Camera as Cam on cameraID=Cam.UID              
                        group by cameraDateID
			order by date DESC;
			";

$get_dates_query = "
			select cd.date as date
			from cctv.CameraDate as cd
			group by date
			order by date desc; 
		   ";

function getConn() {
    $servername = "localhost";
    $username = "root";
    $password = "password";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password);
    
    // Check connection
    if (! $conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

?>
