DELIMITER //

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetFeeds`(
   In search longtext COLLATE utf8mb4_unicode_ci,
   In pageSize int,
   In pageNo int
   
)
BEGIN
SET @total_rows = (
	SELECT COUNT(*) 
    FROM FEEDS F
	JOIN MEDIAS M ON F.MEDIA_ID = M.ID
	JOIN CLIENTS C ON F.CLIENT_ID = C.ID
	where (search is not null && search != '' && 
					keyword like  concat('%', search ,'%') ||
					M.name like  concat('%', search ,'%') ||
					C.name like  concat('%', search ,'%') ||
					F.title like  concat('%', search ,'%') ||
					F.caption like  concat('%', search ,'%') ||
					F.content like  concat('%', search ,'%') ||
					F.edu like  concat('%', search ,'%')
	   )		
); 
SELECT 
	f.id,
    c.name client_name,
    title, 
    m.name media_name,
    taken_date,
    posted_date,
    keyword,
    replies,
    views,
    favs,
    likes,
    comment,
    age,
    edu,
    spam,
    f.is_active    
FROM FEEDS F
JOIN MEDIAS M ON F.MEDIA_ID = M.ID
JOIN CLIENTS C ON F.CLIENT_ID = C.ID
where (search is not null && search != '' && 
				keyword like  concat('%', search ,'%') ||
                M.name like  concat('%', search ,'%') ||
                C.name like  concat('%', search ,'%') ||
                F.title like  concat('%', search ,'%') ||
                F.caption like  concat('%', search ,'%') ||
                F.content like  concat('%', search ,'%') ||
                F.edu like  concat('%', search ,'%')
	   )
LIMIT pageSize OFFSET pageNo;

END //

DELIMITER ;