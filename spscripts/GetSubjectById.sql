DELIMITER //

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetSubjectById`(
  In ID char(36) COLLATE utf8mb4_unicode_ci   
)
BEGIN
SELECT
	s.id,
    c.id client_id,
    c.name client_name,
    s.title,
    s.color,
    s.order_no,
    s.is_active,
    s.created_at
	FROM SUBJECTS S
	JOIN CLIENTS C ON S.CLIENT_ID = C.ID
	where s.id = id;

END //

DELIMITER ;