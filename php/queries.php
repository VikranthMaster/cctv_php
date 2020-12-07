<?php

// Get all available dates for footage
$get_dates_query = "
			select cd.date as date
			from cctv.CameraDate as cd
			group by date
			order by date desc;
		   ";

// Get summary of count of photos on all dates
$get_summary_query = "
			select date as Date, 
                    GatePersonCount as GatePerson, 
                    GateTotalCount as GateTotal, 
                    StairsPersonCount as StairsPerson, 
                    StairsTotalCount as StairsTotal
            from (select cd.date as date , count(d.objectID) as GatePersonCount, count(p.filepath) as GateTotalCount
                    from cctv.Photo as p
                    join cctv.CameraDate as cd on cd.UID = cameraDateID
                    join cctv.Camera as c on c.UID = cd.cameraID
                    left join cctv.Detection as d on d.photoID=p.UID
                    where c.UID = 1 group by date order by date) as Gate
            natural join
                 (select cd.date as date , count(d.objectID) as StairsPersonCount, count(p.filepath) as StairsTotalCount
                    from cctv.Photo as p
                    join cctv.CameraDate as cd on cd.UID = cameraDateID
                    join cctv.Camera as c on c.UID = cd.cameraID
                    left join cctv.Detection as d on d.photoID=p.UID
                    where c.UID = 2 group by date order by date) as Stairs
	        order by date desc;
		   ";

// Get Temperature report
$get_temperature_query = "
            select date(time) as Date,
                   MIN(temp) as MinTemperature,
                   MAX(temp) as MaxTemperature,
                   AVG(temp) as AvgTemperature
            from cctv.Temperature
            group  by Date
            order by Date desc;
           ";

// Get available hour for given Camera and Date.
$get_hours = "
            select hour(p.time) from Photo as p
	        join CameraDate as cd on cd.UID = cameraDateID
            join Camera as c on c.UID = cd.cameraID
            where cameraDateID IN (
							select UID from CameraDate
							where cameraID=2 and date='2020-11-25')
	        group by hour(p.time);
           ";

// Get Total count and person count for all hours given Camera and Date
$get_count_images = "
            select c.name as Camera, hour(p.time) as hour, count(d.objectID) as PersonCount, count(p.filepath) as TotalCount, p.filepath, d.objectID
            from Photo as p
            join CameraDate as cd on cd.UID = cameraDateID
            join Camera as c on c.UID = cd.cameraID
            left join Detection as d on d.photoID=p.UID
            where cameraDateID IN (
            select UID from CameraDate
            where cameraID=1 and date='2020-11-26')
            group by hour order by hour desc
           ";

// Get other images for given camera, date, hour
$get_other_images = "
            select c.name, cd.date, p.time, p.filepath, p.thumbnail, d.objectID from Photo as p
	        join CameraDate as cd on cd.UID = cameraDateID
            join Camera as c on c.UID = cd.cameraID
            left join Detection as d on d.photoID=p.UID
            where cameraDateID IN (
							select UID from CameraDate 
							where cameraID=2 and date='2020-11-25') and d.objectID is null and hour(time)=20
	        order by p.time desc;
           ";


// Get other images for given camera, date, hour
$get_person_images = "
            select c.name, cd.date, p.time, p.filepath, p.thumbnail, d.objectID from Photo as p
	        join CameraDate as cd on cd.UID = cameraDateID
            join Camera as c on c.UID = cd.cameraID
            join Detection as d on d.photoID=p.UID
            where cameraDateID IN (
							select UID from CameraDate
							where cameraID=2 and date='2020-11-25') and hour(time)=20
	        order by p.time desc;
           ";

// Get All images for given camera, date, hour
$get_all_images = "
            select c.name, cd.date, p.time, p.filepath, p.thumbnail from Photo as p
	        join CameraDate as cd on cd.UID = cameraDateID
            join Camera as c on c.UID = cd.cameraID
            where cameraDateID IN (
							select UID from CameraDate
							where cameraID=2 and date='2020-11-25') and hour(time)=20
	        order by p.time desc;
           ";



?>
