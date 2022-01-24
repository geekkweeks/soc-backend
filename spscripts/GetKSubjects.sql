CREATE DEFINER=`root`@`localhost` PROCEDURE `GetSubjects`(
   In search longtext COLLATE utf8mb4_unicode_ci,
   In pageSize int,
   In pageNo int
   
)
BEGIN
SET @total_rows = (
	SELECT COUNT(*) 
    FROM SUBJECTS S
	JOIN CLIENTS C ON S.CLIENT_ID = C.ID
	where (search is not null && search != '' && 
					C.name like  concat('%', search ,'%') ||
					S.title like  concat('%', search ,'%')
	   )		
); 
SELECT
	@total_rows total_rows,
	s.id,
    c.name client_name,
    s.title,
    s.color,
    s.order_no,
    s.is_active,
    s.created_at
	FROM SUBJECTS S
	JOIN CLIENTS C ON S.CLIENT_ID = C.ID
	where (search is not null && search != '' && 
					C.name like  concat('%', search ,'%') ||
					S.title like  concat('%', search ,'%')
	   )		
LIMIT pageSize OFFSET pageNo;

END