-- Objectif : % regardé par série suivis pour un utilisateur

select count(*) as nb_ep_watched from user_episode
inner join episode on episode.id = user_episode.episode_id
inner join season on season.id = episode.season_id
inner join series on series.id = season.id
where user_id = 15 and series.id = 1

select count(*) as nb_ep_total from episode
inner join season on season.id = episode.season_id
inner join series on series.id = season.series_id
where series.id = 1

-- series avec nb ttal d'ep par serie
select *, count(episode.id) as nb_ep_total from episode
inner join season on season.id = episode.season_id
inner join series on series.id = season.series_id
group by series.id

-- nb total d'ep regardés par serie pour un user
select series.id , count(episode.id) as nb_ep_total from episode
inner join season on season.id = episode.season_id
inner join series on series.id = season.series_id
inner join user_episode on user_episode.episode_id = episode.id
where user_episode.user_id = 15
group by series.id


select s_id, nb_ep_watched.total / 2
from (
    select series.id as s_id , count(episode.id) as total from episode
    inner join season on season.id = episode.season_id
    inner join series on series.id = season.series_id
    inner join user_episode on user_episode.episode_id = episode.id
    where user_episode.user_id = 15
    group by series.id
) as nb_ep_watched



select user_s_id, total_s_id, 100 * nb_ep_watched.total / total_eps.total as percent_watched
from (
    select series.id as user_s_id , count(episode.id) as total from episode
    inner join season on season.id = episode.season_id
    inner join series on series.id = season.series_id
    inner join user_episode on user_episode.episode_id = episode.id
    where user_episode.user_id = 15
    group by series.id
) as nb_ep_watched
join (
    select series.id as total_s_id , count(episode.id) as total from episode
    inner join season on season.id = episode.season_id
    inner join series on series.id = season.series_id
    group by series.id
) as total_eps
group by user_s_id, total_s_id

select series.id as user_s_id, count(episode.id) from user_series
inner join series on series.id = user_series.series_id
inner join season on season.series_id = series.id
inner join episode on episode.season_id = season.id
inner join user_episode on user_episode.episode_id = episode.id
where user_series.user_id = 15
group by series.id

select user_s_id, nb_ep_watched.total / 1
from (
 	select series.id as user_s_id, count(episode.id) as total 
    from user_series
    inner join series on series.id = user_series.series_id
    inner join season on season.series_id = series.id
    inner join episode on episode.season_id = season.id
    inner join user_episode on user_episode.episode_id = episode.id
    where user_series.user_id = 15
    group by series.id
) as nb_ep_watched

-- nb ep par serie
select series.id, count(episode.id) as nb_ep_total from episode
inner join season on season.id = episode.season_id
inner join series on series.id = season.series_id
group by series.id

select user_s_id, all_s_id, 100 * nb_ep_watched.total / nb_ep_total.total
from (
 	select series.id as user_s_id, count(episode.id) as total 
    from user_series
    inner join series on series.id = user_series.series_id
    inner join season on season.series_id = series.id
    inner join episode on episode.season_id = season.id
    inner join user_episode on user_episode.episode_id = episode.id
    where user_series.user_id = 15
    group by series.id
) as nb_ep_watched
join (
    select series.id as all_s_id, count(episode.id) as total from episode
    inner join season on season.id = episode.season_id
    inner join series on series.id = season.series_id
    group by series.id
) as nb_ep_total
group by user_s_id
