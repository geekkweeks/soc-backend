DELIMITER  //

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetFeedById`(
   In ID char(36) COLLATE utf8mb4_unicode_ci
)
BEGIN

SELECT 
	f.id,
    FA.feed_id ,
    c.name client_name,
    c.id client_id,
    F.title, 
    f.content,
    f.permalink,
    f.thumblink,
    m.id media_id,
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
    S.id subject_id,
    S.title subject_name,
    TA.ID talk_about_id,
    TA.NAME talk_about,
    ct.id conversation_type_id,
    ct.name conversation_type,
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
LEFT JOIN CONVERSATION_TYPES CT ON CT.ID = FA.conversation_type 
LEFT JOIN TALK_ABOUTS TA ON FA.TALK_ABOUT = TA.ID
where f.id = id;

END //


DELIMITER ;