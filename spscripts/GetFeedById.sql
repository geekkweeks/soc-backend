DELIMITER  //

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetFeedById`(
   In ID char(36) COLLATE utf8mb4_unicode_ci
)
BEGIN

SELECT 
	f.id,
    c.name client_name,
    F.title, 
    f.content,
    f.permalink,
    f.thumblink,
    m.name media_name,
    f.caption,
    taken_date,
    posted_date,
    keyword,
    replies,
    views,
    favs,
    likes,
    comment,
    S.title subject_name,
    FA.talk_about,
    fa.conversation_type,
    fa.tags, 
    fa.corporate,
    CASE
		WHEN fa.gender = 'm' THEN 'Male'
		WHEN fa.gender = 'f' THEN 'Female'
		ELSE ''
	END as gender,    
    FA.age,
    fa.education,
    FA.location,
    spam,
    f.is_active    
FROM FEEDS F
LEFT JOIN FEED_ANALYSIS FA ON F.ID = FA.FEED_ID 
LEFT JOIN  SUBJECTS S ON FA.SUBJECT_ID = S.ID
LEFT JOIN MEDIAS M ON F.MEDIA_ID = M.ID
LEFT JOIN CLIENTS C ON F.CLIENT_ID = C.ID
where f.id = id;

END //


DELIMITER ;