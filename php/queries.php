<?php
$rpt1_query = "
            select manu.name
            , count(productID) as ProductCount
            , avg(price) as AveragePrice
            , max(price) as MaxPrice
            , min(price) as MinPrice
            , max(maxDiscount) as MaxDiscount
            , manu.manufacturerID
            from pricepalace.manufacturer as manu
            join pricepalace.product as p on p.manufacturerID = manu.manufacturerID
            group by manu.name
            order by avg(price) desc
            limit 100;
            ";

$rpt2_query = "
            select
            cat.name
            , count(distinct(prod.productid)) as ProductCount
            , count(distinct(manufacturerid)) as ManufacturerCount
            , avg(price) as AveragePrice
            from pricepalace.category as cat
            join pricepalace.productcategory as pc on pc.categoryID = cat.categoryID
            join pricepalace.product as prod on prod.productID = pc.productID
            group by cat.name
            order by cat.name asc
            ;
            ";

$rpt3_query = "
              with salesdata as (
                select
                cat.name
                , cat.categoryID
                ,prod.productid
                , prod.name as productname
                , sold.quantity
                , case when sales.salePrice is not Null then sold.quantity
                else 0 end as DiscountSold
                , price
                , sales.salePrice
                , ifnull(sales.salePrice, price)*sold.quantity as ActualRevenue
                , (case when sales.salePrice is Null then sold.quantity
                else sold.quantity * .75 end) * price as PredictedRevenue
                from pricepalace.category as cat
                join pricepalace.productcategory as pc on pc.categoryID = cat.categoryID
                join pricepalace.product as prod on prod.productid = pc.productid
                join pricepalace.sold as sold on sold.productID =prod.productID
                left join pricepalace.sales as sales on sales.productID = sold.productID and sales.saleDate = sold.soldDate
                where cat.name = 'GPS'
                 )
                 , summary as (
                 select productid
                 , productname
                 , price
                 , sum(quantity) as TotalUnitsSold
                 , sum(discountSold) as DiscountSold
                 , sum(PredictedRevenue) as PredictedRevenue
                 , sum(ActualRevenue) as ActualRevenue
                 , sum(ActualRevenue) - sum(PredictedRevenue) as Delta
                 From salesdata
                 group by productid, productname, price)
                 select productid
                 , productname
                 , price
                 , TotalUnitsSold
                 , DiscountSold
                 , PredictedRevenue
                 , ActualRevenue
                 , Delta
                 from summary
                 where abs(delta) >5000
                 order by delta desc
                 ;
                 ";

$rpt4_query = "
            Select
            state
            , store.storeID
            , address
            , city
            , year(soldDate) as SalesYear
            , sum(ifnull(sales.salePrice, price)*sold.quantity) as TotalRevenue
            from pricepalace.city
            join pricepalace.store on store.cityid = city.cityid
            left join pricepalace.sold on sold.storeid = store.storeid
            left join pricepalace.product on product.productID = sold.productid
            left join pricepalace.sales as sales on sales.productID = sold.productID and sales.saleDate =
            sold.soldDate
            where state = ?
            group by state, storeID, address, city, year(solddate)
            order by SalesYear asc, TotalRevenue desc
            ;
            ";

$rpt5_query = "
            with GHD as (
              select year(soldDate) as SalesYear
                , sum(quantity) as GHDUnitsSold
                from pricepalace.sold
                join pricepalace.productcategory as pc on pc.productid = sold.productid and pc.categoryid IN
                (select categoryID from pricepalace.category where name = 'Air Conditioner')
                where month(soldDate) = 2 and day(soldDate) = 2
                group by year(soldDate)
                )
                select
                year(soldDate) as SalesYear
                , sum(quantity) as TotalUnitsSold
                , sum(quantity)/365 as AvgDailySales
                , ifnull(GHDUnitsSold,0) as GHDUnitsSold
                from pricepalace.sold
                join pricepalace.productcategory as pc on pc.productid = sold.productid and pc.categoryid IN
                (select categoryID from pricepalace.category where name = 'Air Conditioner')
                left join GHD on GHD.SalesYear = year(soldDate)
                group by year(soldDate), GHDUnitsSold
		order by year(soldDate) asc
                ;
            ";

$rpt6_query = "
        with catsales as (
            Select
            state
            , cat.name as CatName
            , sum(sold.quantity) as TotalUnitsSold
            , dense_rank () over (partition by cat.name order by sum(sold.quantity) desc) as salerank
            from pricepalace.city
            join pricepalace.store on store.cityid = city.cityid
            join pricepalace.sold on sold.storeid = store.storeid and year(soldDate) = ? and month(soldDate) = ?
            join pricepalace.productcategory as pc on pc.productid = sold.productid
            join pricepalace.category as cat on cat.categoryID = pc.categoryid
            group by state, cat.name, year(solddate)
            order by CatName asc
            )
            select state
            , CatName
            , TotalUnitsSold
            from catsales where salerank = 1
                ;
            ";

$rpt7_query = "
            with rev2 as (
                Select
                    year(soldDate) as SalesYear
                    , population
                    ,city.cityID
                    , Sum(ifnull(sales.salePrice, price)*sold.quantity) as TotalRevenue
                    from pricepalace.city
                    join pricepalace.store on store.cityid = city.cityid
                    join pricepalace.sold on sold.storeid = store.storeid
                    left join pricepalace.sales as sales on sales.productID = sold.productID and sales.saleDate =
                    sold.soldDate
                    join pricepalace.product on product.productID = sold.productid
                    group by year(soldDate), city.cityid
                     ORDER BY SalesYear, City
                     )
                     select SalesYear,
                     SUM(CASE WHEN population < 3700000 THEN TotalRevenue END) as Small,
                     SUM(CASE WHEN population >= 3700000 and population < 6700000 THEN TotalRevenue END) as Medium,
                     SUM(CASE WHEN population >=6700000 and population < 9000000 THEN TotalRevenue END) as Large,
                     SUM(CASE WHEN population > 9000000 THEN TotalRevenue END) as ExtraLarge
                     from rev2
                     GROUP BY SalesYear
                     ORDER BY SalesYear;
                ";

$get_states_query = "
            select distinct c.state from pricepalace.sold sd
            join pricepalace.store st
            on sd.storeID = st.storeID
            join pricepalace.city c
            on st.cityID = c.cityID
            order by c.state
            ;
            ";

$get_dates_query = "
            select distinct
            year(soldDate) as year
            , monthname(solddate) as 'Month'
            from pricepalace.sold
            ;
            ";

$rpt1_detail_query = "
            select prod.ProductID, prod.name, prod.price as price, REPLACE(TRIM(TRAILING ', ' FROM Group_concat(cat.name, ', ')), \", \", \"\") as CategoryList
            from pricepalace.category as cat
            join pricepalace.productcategory as pc on pc.categoryID = cat.categoryID
            join pricepalace.product as prod on prod.productID = pc.productID
            where manufacturerID = ?
            group by prod.ProductID
            order by price desc
            ;
            ";

$member_trend_query = "
            select year(signupdate) as Year
            , count(distinct(membershipID)) as NewMembers
            from pricepalace.membership group by year(signupdate) order by year desc
            ;
            ";

$member_trend_top25_query = "
            select count(distinct(membershipID)) as NewMembers
            , count(distinct(store.storeid)) as StoreCount
            , city
            , state
            , 'top25' as position
            from pricepalace.membership as mem
            join pricepalace.store on store.storeid = mem.storeid
            join pricepalace.city on city.cityid = store.cityid
            where year(signupdate) = ?
            group by year(signupdate), city, state
            order by NewMembers Desc
            limit 25
            ;
            ";

$member_trend_bottom25_query = "
            SELECT count(distinct(membershipID)) as NewMembers
            , count(distinct(store.storeid)) as StoreCount
            , city
            , state
            FROM pricepalace.city
            join pricepalace.store on store.cityid = city.cityID
            LEFT join pricepalace.membership as mem on mem.storeID = store.storeID
            where year(signupdate) = ?
            OR signUpDate IS NULL
            group by year(signupdate), city, state
            order by NewMembers Asc
            limit 25
            ;
            ";

$member_drill_thru_query = "
            select count(distinct(membershipID)) as NewMembers
            , store.StoreID
            , address
            , city
            , state
            from pricepalace.store
            left join pricepalace.membership as mem on store.storeid = mem.storeid
            join pricepalace.city on city.cityid = store.cityid
            where (year(signupdate) = ? or signupdate is null)
            and city = ?
            and state = ?
            group by year(signupdate), city, state, store.storeID, address
            order by NewMembers Asc
            ;
            ";

$summary_query = "SELECT
            (SELECT COUNT(productID) from pricepalace.product) as product_count,
            (SELECT COUNT(name) from pricepalace.manufacturer) as manufacturer_count,
            (SELECT COUNT(storeID) from pricepalace.store) as store_count,
            (SELECT COUNT(membershipid) from pricepalace.membership) as member_count
            FROM DUAL;
            ";

$get_holidays_query = "
            SELECT `date`, `name` from pricepalace.holidays;
            ";

$add_date_query = "
            INSERT IGNORE INTO pricepalace.date(date) VALUES (?);
            ";

$get_dates_query = "
			select cd.date as date
			from cctv.CameraDate as cd
			group by date
			order by date desc;
		   ";

$get_summary_query = "
			select c.name as Camera, cd.date as Date, count(*) as PhotoCount
            from cctv.Photo
            join cctv.CameraDate as cd on cameraDateID=cd.UID
            join cctv.Camera as c on cameraID=c.UID
            group by cameraDateID
			order by date DESC;
		   ";

$add_holiday_query = "
            INSERT into pricepalace.holidays (date,name)
            VALUES (?, ?) as new
            ON DUPLICATE KEY UPDATE
            name = CONCAT(holidays.name, ', ', new.name);
            ";

$get_population_query = "
            SELECT cityID, city, state, population from pricepalace.city;
            ";
$update_pop_query = "
            UPDATE pricepalace.city
            SET population = ? where cityID = ?;
            ";

?>
