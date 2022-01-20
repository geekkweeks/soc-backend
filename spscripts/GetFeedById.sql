DELIMITER  //

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetFeedById`(
    In ID char(36) COLLATE utf8mb4_unicode_ci
)
BEGIN

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
where f.id = id;

END //


DELIMITER ;