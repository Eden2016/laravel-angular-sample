<?php
namespace App\Http\Controllers;

use DB;
use PDO;
use App\Models\BlogPost;
use App\Services\GameServices;
use Illuminate\Http\Request;

class BlogController extends Controller {

    public function index(Request $request, $game, $client_id = null)
    {
        $posts = BlogPost::query()->select('blog_posts.*');
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $games = DB::table('games')->select('id', 'name', 'slug')->get();
        DB::setFetchMode(PDO::FETCH_CLASS);
        if($client_id) {
            $posts->where('client_id', $client_id);
        }
        if($game != 'all') {
            if($gameId = GameServices::getGameId($game)) {
                $posts->leftJoin('blog_post_options', function($join) use ($gameId)
                {
                    $join->on('blog_post_options.post_id', '=', 'blog_posts.id');
                    $join->where('blog_post_options.option', '=', 'game');
                    $join->where('blog_post_options.value', '=', $gameId);
                });
                $posts->where('blog_post_options.value', '=', $gameId);
            }
        }
        $result = $posts->paginate($request->get('size', 15));
        foreach($result as &$res) {
            $res->languages = $res->takeTranslations();
            $gamesIds = $res->takeGamesIds();
            $res->games = array_values(array_filter( $games, function($item) use ($gamesIds) {
                return in_array($item['id'], $gamesIds);
            }));
            if($headline = $res->getHeadlineImage())
                $res->headline = 'http://static.esportsconstruct.com/'.$headline;
            if($thumb = $res->getThumbImage())
                $res->thumb = 'http://static.esportsconstruct.com/'.$thumb;
        }
        return $result;
    }

    public function show(Request $request, $game, $client_id, $post_id)
    {
        $lang = $request->get('lang', 'en');
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $games = DB::table('games')->select('id', 'name', 'slug')->get();
        DB::setFetchMode(PDO::FETCH_CLASS);
        if($post = BlogPost::where('id', $post_id)->where('client_id', $client_id)->first()) {
            if($lang != 'en') {
                if($option = $post->options()->where('option', 'translation_'.$lang)->first()) {
                    $translation = json_decode($option->value);
                    $post->title = $translation->title;
                    $post->summary = $translation->summary;
                    $post->post = $translation->post;
                }
            }
            $post->tags = $post->takeTags();
            $post->translations = $post->takeTranslations();
            if($headline = $post->getHeadlineImage())
                $post->headline = 'http://static.esportsconstruct.com/'.$headline;
            if($thumb = $post->getThumbImage())
                $post->thumb = 'http://static.esportsconstruct.com/'.$thumb;
            $gamesIds = $post->takeGamesIds();
            $post->games = array_values(array_filter( $games, function($item) use ($gamesIds) {
                return in_array($item['id'], $gamesIds);
            }));
            return response()->json($post->toArray());
        }
        return abort(404, "Post not found");
    }

    public function edit($game, $id) {
        abort(501, 'Not Implemented');
    }

    public function update($game, $id) {
        abort(501, 'Not Implemented');
    }

    public function destroy($game, $id) {
        abort(501, 'Not Implemented');
    }

    public function create($game) {
        abort(501, 'Not Implemented');
    }

    public function store($game) {
        abort(501, 'Not Implemented');
    }
}