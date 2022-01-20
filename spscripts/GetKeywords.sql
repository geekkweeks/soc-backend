DELIMITER //

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetKeywords`(
   In search longtext COLLATE utf8mb4_unicode_ci,
   In pageSize int,
   In pageNo int
   
)
BEGIN
SET @total_rows = (SELECT COUNT(*) 
		FROM KEYWORDS K
		JOIN MEDIAS M ON K.MEDIA_ID = M.ID
		where (search is not null && search != '' && 
				k.keyword like  concat('%', search ,'%') ||
                m.name like  concat('%', search ,'%')
		   )); 
    
   SELECT 
		@total_rows total_rows,
		K.id,
    keyword,
    sequence,
    M.NAME media_name,
    K.is_active    
FROM KEYWORDS K
JOIN MEDIAS M ON K.MEDIA_ID = M.ID
where (search is not null && search != '' && 
				k.keyword like  concat('%', search ,'%') ||
                m.name like  concat('%', search ,'%')
	   )
LIMIT pageSize OFFSET pageNo;
END //

DELIMITER ;