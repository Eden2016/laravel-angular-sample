<?php

namespace App\Http\Controllers;

use Auth;
use Datatables;
use Image;
use App\Client;
use App\Http\Requests\BlogPostCreateRequest;
use App\Game;
use App\Individual;
use App\Models\BlogPost;
use App\Models\BlogPostOption;
use App\TeamAccount;
use App\Tournament;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function create()
    {
        $data = [];
        return view('blog.add', $data);
    }

    public function store(BlogPostCreateRequest $request)
    {

        $client = Client::findOrFail($request->get('client'));

        $post = new BlogPost();
        $post->title = $request->get('title');
        $post->summary = $request->get('summary', '');
        $post->post = $request->get('post', '');
        $post->type = $request->get('type');
        $post->author_id = Auth::user()->id;
        $post->client_id = $client->id;
        $post->is_highlight = $request->get('is_highlight', 0);
        if($request->get('save_as_draft')) {
            $post->is_draft = 1;
            $post->published_at = null;
        } else {
            $post->is_draft = 0;
            $post->published_at = Carbon::now();
        }
        $post->save();

        $s3 = \Storage::disk('s3');
        if($headlineImage = $request->file('headline')) {
            $headlinePath = 'blog/'.$client->name.'/'.$headlineImage->getBasename($headlineImage->getClientOriginalExtension()) . '-'. str_random(5) .'.' . $headlineImage->getClientOriginalExtension();
            $_headlineImage = Image::make($headlineImage->getRealPath());
            if($client->isImageFitHeadline($_headlineImage->width(), $_headlineImage->height())) {
                $_headlineImage->resize($client->getHeadlineDimension()->width, $client->getHeadlineDimension()->height);
                $s3->put($headlinePath, $_headlineImage->stream()->__toString(), 'public');
                $post->setHeadlineImage($headlinePath);
            } else {
                $post->delete();
                return redirect()->back()->withErrors('Headline image doesnt fit requirements');
            }
        }

        if($thumbImage = $request->file('thumb')) {
            $thumbPath = 'blog/'.$client->name.'/thumb_'.$thumbImage->getBasename($thumbImage->getClientOriginalExtension()) . '-' . str_random(5) . '.' . $thumbImage->getClientOriginalExtension();
            $_thumbImage = Image::make($thumbImage->getRealPath());
            if($client->isImageFitThumb($_thumbImage->width(), $_thumbImage->height())) {
                $_thumbImage->resize($client->getThumbDimension()->width, $client->getThumbDimension()->height);
                $s3->put($thumbPath, $_thumbImage->stream()->__toString(), 'public');
                $post->setThumbImage($thumbPath);
            }
        }

        $games = $request->get('games', []);
        foreach($games as $game) {
            $option = new BlogPostOption();
            $option->post_id = $post->id;
            $option->option = 'game';
            $option->value = $game;
            $option->save();
        }

        $tags = explode(',', $request->get('tags', ''));

        foreach($tags as $tag) {
            if($tag != '') {
                $option = new BlogPostOption();
                $option->post_id = $post->id;
                $option->option = 'tag';
                $option->value = $tag;
                $option->save();
            }
        }

        return redirect(groute('blog.manage'));
    }

    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);
        return view('blog.edit', compact('post'));
    }

    public function getTranslations($id)
    {
        $post = BlogPost::findOrFail($id);
        return response()->json($post->takeTranslations());
    }

    public function getTranslation($id, $lang)
    {
        $post = BlogPost::findOrFail($id);
        if($translation = $post->options()->where('option', 'translation_'.$lang)->first())
            return response()->json(json_decode($translation->value));
        return abort(404);
    }

    public function setTranslation(Request $request, $id, $lang)
    {
        $post = BlogPost::findOrFail($id);
        $translation = $post->options()->where('option', 'translation_'.$lang)->first();
        if(!$translation) {
            $translation = new BlogPostOption();
            $translation->post_id = $id;
            $translation->option = 'translation_'.$lang;
        }
        $translation->value = json_encode([
            'title' => $request->get('title', ''),
            'summary' => $request->get('summary', ''),
            'post' => $request->get('post', ''),
        ]);
        $translation->save();
        return $this->getTranslations($id);
    }

    public function update(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);
        $client = Client::findOrFail($request->get('client'));
        $s3 = \Storage::disk('s3');
        if($headlineImage = $request->file('headline')) {
            $headlinePath = 'blog/'.$client->name.'/'.$headlineImage->getBasename($headlineImage->getClientOriginalExtension()) . '-'. str_random(5) .'.' . $headlineImage->getClientOriginalExtension();
            $_headlineImage = Image::make($headlineImage->getRealPath());
            if($client->isImageFitHeadline($_headlineImage->width(), $_headlineImage->height())) {
                $_headlineImage->resize($client->getHeadlineDimension()->width, $client->getHeadlineDimension()->height);
                $s3->delete($post->getHeadlineImage());
                $s3->put($headlinePath, $_headlineImage->stream()->__toString(), 'public');
                $post->setHeadlineImage($headlinePath);
            } else {
                return redirect()->back()->withErrors('Headline image doesnt fit requirements');
            }
        }

        if($thumbImage = $request->file('thumb')) {
            $thumbPath = 'blog/'.$client->name.'/thumb_'.$thumbImage->getBasename($thumbImage->getClientOriginalExtension()) . '-' . str_random(5) . '.' . $thumbImage->getClientOriginalExtension();
            $_thumbImage = Image::make($thumbImage->getRealPath());
            if($client->isImageFitThumb($_thumbImage->width(), $_thumbImage->height())) {
                $_thumbImage->resize($client->getThumbDimension()->width, $client->getThumbDimension()->height);
                $s3->delete($post->getThumbImage());
                $s3->put($thumbPath, $_thumbImage->stream()->__toString(), 'public');
                $post->setThumbImage($thumbPath);
            } else {
                return redirect()->back()->withErrors('Thumb image doesnt fit requirements');
            }
        }

        $post->title = $request->get('title');
        $post->summary = $request->get('summary', '');
        $post->post = $request->get('post', '');
        $post->type = $request->get('type');
        $post->client_id = $request->get('client');
        $post->is_highlight = $request->get('is_highlight', 0);
        if($request->get('save_as_draft') && $post->is_draft == 0) {
            $post->is_draft = 1;
            $post->published_at = null;
        } else {
            if($post->is_draft == 1) {
                $post->is_draft = 0;
                $post->published_at = Carbon::now();
            }
        }
        $post->save();

        BlogPostOption::where('post_id', $post->id)->where('option', 'game')->delete();
        $games = $request->get('games', []);
        foreach($games as $game) {
            $option = new BlogPostOption();
            $option->post_id = $post->id;
            $option->option = 'game';
            $option->value = $game;
            $option->save();
        }

        BlogPostOption::where('post_id', $post->id)->where('option', 'tag')->delete();
        $tags = explode(',', $request->get('tags', ''));
        foreach($tags as $tag) {
            if($tag != '') {
                $option = new BlogPostOption();
                $option->post_id = $post->id;
                $option->option = 'tag';
                $option->value = $tag;
                $option->save();
            }
        }
        return redirect(groute('blog.manage'));
    }

    public function getPosts()
    {
        return view('blog.manage');
    }

    public function delete($id)
    {
        BlogPostOption::where('post_id', $id)->delete();
        BlogPost::findOrFail($id)->delete();
        return redirect(groute('blog.manage'));
    }

    public function tagSearch(Request $request)
    {
        $tags = [];
        $games = Game::where('name', 'like', "%{$request->q}%")->get();
        $tournaments = Tournament::where('name', 'like', "%{$request->q}%")->get();
        $teams = TeamAccount::where('name', 'like',"%{$request->q}%")->get();
        $players = Individual::where('nickname', 'like',"%{$request->q}%")->get();
        foreach($games as $game) {
            $tags[] = [
                'id' => $game->name,
                'text' => $game->name,
                'from' => 'game'
            ];
        }
        foreach($tournaments as $tournament) {
            $tags[] = [
                'id' => $tournament->name,
                'text' => $tournament->name,
                'from' => Game::allCached($tournament->game_id)->name.' tournament'
            ];
        }
        foreach($teams as $team) {
            $tags[] = [
                'id' => $team->name,
                'text' => $team->name,
                'from' => Game::allCached($team->game_id)->name.' team'
            ];
        }
        foreach($players as $player) {
            $tags[] = [
                'id' => $player->nickname,
                'text' => $player->nickname,
                'from' => Game::allCached($player->game_id)->name.' player'
            ];
        }
        return response()->json(['results' => $tags, 'more' => false]);
    }

    public function getClientImageConfig(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        return response()->json([
            'headline' => $client->getHeadlineDimension(),
            'thumb' => $client->getThumbDimension()
        ]);
    }

    public function dataquery(Request $request)
    {
        return Datatables::eloquent(BlogPost::query()->orderBy('is_draft', 'desc'))
            ->filter(function ($query) use ($request) {
                if($request->get('withunpublished', false) == 0) {
                    $query->where('is_draft', 0);
                }
                if($request->get('searchtext') && $request->get('searchtext') != '') {
                    $query->where('title', 'like', "%{$request->get('searchtext')}%")
                        ->orWhere('summary','like', "%{$request->get('searchtext')}%");
                }
                if($request->get('only_my', false)) {
                    $query->where('author_id', Auth::user()->id);
                }
            })
            ->addColumn('client', function(BlogPost $post) {
                return $post->client->name;
            })
            ->addColumn('author', function(BlogPost $post) {
                return $post->author->email;
            })
            ->editColumn('type', function (BlogPost $post) {
                return \App\Models\BlogPost::BLOG_TYPES[$post->type];
            })
            ->addColumn('games', function(BlogPost $post) {
                return implode(' ', array_map(function($item) {
                    return '<i class="game-icon '.$item->slug.'"></i>';
                }, $post->takeGames()));
            })
            ->addColumn('translations', function(BlogPost $post) {
                return implode('-', $post->takeTranslations());
            })
            ->addColumn('actions', function (BlogPost $post) {
                return '<a class="btn btn-xs btn-success" href="'.groute('blog.edit', [$post->id]).'"><i class="fa fa-pencil"></i> edit</a>
                        <a class="btn btn-xs btn-danger" href="'.groute('blog.delete', [$post->id]).'"><i class="fa fa-trash"></i> delete</a>';
            })
            ->make(true);
    }
}
