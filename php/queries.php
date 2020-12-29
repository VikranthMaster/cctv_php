<?php

// Get all available dates for footage
$get_dates_query = "
			select d.date as date
			from cctv2.CameraDate as cd
			join cctv2.Date as d on d.UID=cd.dateID
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
            from (select dt.date as date , count(d.objectID) as GatePersonCount, count(p.filepath) as GateTotalCount
                    from cctv2.Photo as p
                    join cctv2.CameraDate as cd on cd.UID = cameraDateID
                    join cctv2.Date as dt on dt.UID=cd.dateID
                    join cctv2.Camera as c on c.UID = cd.cameraID
                    left join cctv2.Detection as d on d.photoID=p.UID
                    where c.UID = 1 group by date order by date) as Gate
            natural join
                 (select dt.date as date , count(d.objectID) as StairsPersonCount, count(p.filepath) as StairsTotalCount
                    from cctv2.Photo as p
                    join cctv2.CameraDate as cd on cd.UID = cameraDateID
					join cctv2.Date as dt on dt.UID=cd.dateID
                    join cctv2.Camera as c on c.UID = cd.cameraID
                    left join cctv2.Detection as d on d.photoID=p.UID
                    where c.UID = 2 group by date order by date) as Stairs
	        order by date desc;
		   ";

// Get Temperature report
$get_temperature_query = "
            select date as Date,
                   MIN(temp) as MinTemperature,
                   MAX(temp) as MaxTemperature,
                   FORMAT(AVG(temp),2) as AvgTemperature
            from cctv2.Temperature
	    join cctv2.Date on dateID=UID
            group  by Date
            order by Date desc;
           ";

// Get Temperature report
$get_datetemp_query = "
            select hour(time) as Hour, 
                      MIN(temp) as MinTemperature, 
                      MAX(temp) as MaxTemperature, 
                      FORMAT(AVG(temp),2) as AvgTemperature 
                from cctv2.Temperature
	            where dateID = (select UID from cctv2.Date where date=?)
                group by Hour;
           ";

// Get Photos
$get_photos_query = "
            select hour(p.time) as Hour, count(d.objectID) as PersonCount, count(p.filepath) as TotalCount, c.cachedir, p.time
	        from cctv2.Photo as p
                join cctv2.CameraDate as cd on cd.UID=cameraDateID
                join cctv2.Date dt on cd.dateID = dt.UID
                join cctv2.Camera as c on c.UID = cd.cameraID
                left join cctv2.Detection as d on d.photoID=p.UID
                where c.name = ? and date = ?
	        group by hour order by hour desc;
            ";

// Get All photos
$get_allphotos_query = "
            select c.rootdir as RootDir, c.cachedir as CacheDir , p.filepath as FilePath, p.time as Time
                from cctv2.Photo as p
                join cctv2.CameraDate as cd on cd.UID=cameraDateID
                join cctv2.Date dt on cd.dateID = dt.UID
                join cctv2.Camera as c on c.UID = cd.cameraID
                where c.name = ? and date = ? and hour(p.time)=?;
            ";

// Get Person photos
$get_personphotos_query = "
            select c.rootdir as RootDir, c.cachedir as CacheDir , p.filepath as FilePath, p.time as Time
                from cctv2.Photo as p
                join cctv2.CameraDate as cd on cd.UID=cameraDateID
                join cctv2.Date dt on cd.dateID = dt.UID
                join cctv2.Camera as c on c.UID = cd.cameraID
                join cctv2.Detection as d on d.photoID=p.UID
                where c.name = ? and date = ? and hour(p.time)=?;
            ";

// Get Other photos
$get_otherphotos_query = "
            select c.rootdir as RootDir, c.cachedir as CacheDir , p.filepath as FilePath, p.time as Time
                from cctv2.Photo as p
                join cctv2.CameraDate as cd on cd.UID=cameraDateID
                join cctv2.Date dt on cd.dateID = dt.UID
                join cctv2.Camera as c on c.UID = cd.cameraID
                left join cctv2.Detection as d on d.photoID=p.UID
                where c.name = ? and date = ? and hour(p.time)=? and d.objectID is NULL;
            ";

// Get bounding box for a photo
$get_bounding_box = "
            select concat(d.x1,\" \",d.y1,\" \",d.x2,\" \",d.y2,\" \",d.probability) as Box
                from cctv2.Photo as p
                join cctv2.CameraDate as cd on cd.UID=cameraDateID
                join cctv2.Date dt on cd.dateID = dt.UID
                join cctv2.Camera as c on c.UID = cd.cameraID
                join cctv2.Detection as d on d.photoID=p.UID
	        where c.name = ? and dt.date=? and p.time=?;
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
