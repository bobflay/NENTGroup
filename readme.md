# NENT Trailer Fetcher

This is a simple Laravel project created as  test project for a backend-developer.
To run this project you need
### Installation
  - Running PHP evironment, preferably version 7.1 (use WAMP or LAMP)
  - Composer: [download-here]
  - Redis: In memory database [download-redis]
  - Valid Movie DB API Key: [moviedb-api]

```sh
$ git clone https://github.com/bobflay/NENTGroup.git
$ cd NENTGroup
$ composer install
$ php artisan run serve
```
if everything run successfully you should receive a message saying that the project is running on the port 8000 by defaut;

```sh
Laravel development server started: <http://127.0.0.1:8000>
```
### Requests
This project one sample request which is a **GET** request to the following Route: **Base_url +"/api/trailer?"url=_{movie_resource_url_from_viaplay}_**

| Request URL | Youtube URL |
| ------ | ------ |
| http://localhost:8000/api/trailer?url=https://content.viaplay.se/pc-se/film/a-star-is-born-2018| https://www.youtube.com/watch?v=nSbzyEJ8X9E|
| http://localhost:8000/api/trailer?url=https://content.viaplay.se/pc-se/film/avatar-2009| https://www.youtube.com/watch?v=5PSNL1qE6VY |
| http://localhost:8000/api/trailer?url=https://content.viaplay.se/pc-se/film/titanic-1997 | https://www.youtube.com/watch?v=CHekzSiZjrY |
| http://localhost:8000/api/trailer?url=https://content.viaplay.se/pc-se/film/mad-max-fury-road-2015| https://www.youtube.com/watch?v=akX3Is3qBpw |


### Development
Platform: I decided to work with Laravel, since I have a lot of experience with Laravel
**Target: create REST API for providing client with Trailer URL**
Movie Resource Link as Input: https://content.viaplay.se/pc-se/film/arrival-2016 
**Return Trailer as output**
Within the movie resource, the IMDb information can be found at the following path:
_embedded[“viaplay:blocks”][0]._embedded[“viaplay:product”].content.imdb
Using the get rquest of the provided link doesn't return  with a movie resource!
The output of the GET request of  https://content.viaplay.se/pc-se/film/arrival-2016
```json
{
    "code": 5100,
    "redirectPath": "/pc-se/film",
    "url": "/pc-se/film/arrival-2016"
}
```
- I decided to use **"https://content.viaplay.se/pcdash-se/store/_{PublicPath}_?partial=true"** to get movie resource instead.
After getting the movie resource I extracted the IMDB id;
- I called THE MOVIE DB API to get the movie id from IMDB id:
https://api.themoviedb.org/3/find/{external_id}?api_key=<<api_key>>&language=en-US&external_source=imdb_id
- I called the Movie DB API to get the trailler using themoviedb id:
https://api.themoviedb.org/3/movie/603/videos?api_key=cf59e3834d8b8ef500ff73aa09dec848&language=en-US
- After getting Youtube key, I store the viaplay movie resource url with youtube url in Redis for faster retrieval for the next call.

### Todos

 - Write MORE Tests
 - Support trailer for series
 - shorten the response for the first time api call



   [download-redis]: <https://redis.io/download>
   [moviedb-api]: <https://www.themoviedb.org/documentation/api>
   [download-here]: <https://getcomposer.org/download/>

