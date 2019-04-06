<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use HttpClient;
use Redis;

class TrailerController extends Controller
{
    
	public function index()
	{	
		//get the url from the post request
		$url = request()->url;
		//check this url is previously exists in Redis;
		$trailer_url = Redis::get("trailer:$url");
		if(is_null($trailer_url))
		{
			//if the url is not set in Redis;
			//get the url structure
			$response = HttpClient::get($url)->json();

			if(isset($response->code) && $response->code = 5100)
			{
				$prefix = $response->redirectPath;
				$url = $response->url;
				//get the movie name from the url
				$url = explode("$prefix/", $url);
				//make sure that the movie name was extracted properly
				if(sizeof($url)==2)
				{
					$movie_name = $url[1];

					//after getting the movie name we will lookup for the movie resource from viaplay api

					$request = env('VIAPLAY_API').'/pcdash-se/store/'.$movie_name.'?partial=true';
					$movie_resource = HttpClient::get($request)->json();
					$imdb_id =  $this->getIMDBId($movie_resource);
					
				}

			}else{

				$imdb_id = $this->getIMDBId($response);
			}

			$moviedb_id = $this->getMovieDBId($imdb_id);
			$youtube_id = $this->getYoutubeId($moviedb_id);
			$trailer_url = $this->formatYoutubeURL($youtube_id);
			Redis::set('trailer:'.request()->url,$trailer_url);



		}


		return response($trailer_url);

	}


	/**
	 * Retrieve imdb id from viaplay movie resource
	 * Argument: viaplay movie resource object
	 * Response: string (imdb_id)
	 */
	public function getIMDBId($movie_resource)
	{
		$content = $movie_resource->_embedded;
		$content = get_object_vars($content);
		if(isset($content['viaplay:product']))
		{
			$viaplay_product=$content['viaplay:product']->content;
		}elseif(isset($content['viaplay:blocks']))
		{
			$blocks = collect($content['viaplay:blocks']);
			$block = $blocks->where('type','product')->first();
			$embedded = $block->_embedded;
			$embedded = get_object_vars($embedded);
			$viaplay_product=$embedded['viaplay:product']->content;
		}
		$id = $viaplay_product->imdb->id;
		return $id;
	}

	/**
	 * Get TheMovieDB id from imdb id
	 * Argument: String (imdb_id)
	 * Response: string (the movie db id)
	 */
	public function getMovieDBId($imdb_id)
	{
		$request = [
			'url'=>env('MOVIEDB_API').'/find/'.$imdb_id,
			'params'=>[
				'external_source'=>'imdb_id',
				'api_key'=>env('MOVIEDB_KEY')
			]
		];

		$response = HttpClient::get($request)->json();
		$id = $response->movie_results[0]->id;
		return $id;
	}


	/**
	 * Retrieve youtube id from the movie db id
	 * Argument: (String) the movie db id
	 * Response: (string) Youtube Key
	 */
	public function getYoutubeId($moviedb_id)
	{
		$request = [
			'url'=>env('MOVIEDB_API').'/movie/'.$moviedb_id.'/videos',
			'params'=>[
				'api_key'=>env('MOVIEDB_KEY')
			]
		];

		$response = HttpClient::get($request)->json();
		$response = $response->results;
		$response = collect($response);
		$trailer = $response->where('type','Trailer')->first();
		return $trailer->key;
	}


	/**
	 * Fromat Youtube URL
	 * Argument: (string) Youtube Id
	 * Response: (string) Youtube URL
	 */
	public function formatYoutubeURL($id)
	{
		return 'https://www.youtube.com/watch?v='.$id;
	}


}
