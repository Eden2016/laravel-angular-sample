<?php

namespace App\Http\Controllers\Clients;

use App\Http\Requests\BlogPostCreateRequest;
use Auth;
use Datatables;
use App\Game;
use App\Individual;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogPostOption;
use App\TeamAccount;
use App\Tournament;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected $_blogController;

    public function __construct()
    {
        $this->_blogController = new \App\Http\Controllers\BlogController();
    }

    public function create()
    {
        return view('client.blog.add');
    }

    public function store(BlogPostCreateRequest $request)
    {
        $this->_blogController->store($request);
        return redirect(groute('client.blog.manage'));
    }

    public function edit($id)
    {
        $post = BlogPost::where('client_id', Auth::guard('client')->user()->id)->where('id', $id)->first();
        return view('client.blog.edit', compact('post'));
    }

    public function getTranslations($id)
    {
        return $this->_blogController->getTranslations($id);
    }

    public function getTranslation($id, $lang)
    {
        return $this->_blogController->getTranslation($id, $lang);
    }

    public function setTranslation(Request $request, $id, $lang)
    {
        return $this->_blogController->setTranslation($request, $id, $lang);
    }


    public function update(Request $request, $id)
    {
        $post = BlogPost::where('client_id', Auth::guard('client')->user()->id)->where('id', $id)->first();
        if($post)
            $this->_blogController->update($request, $id);
        return redirect(route('client.blog.manage'));
    }

    public function getPosts()
    {
        return view('client.blog.manage');
    }

    public function delete($id)
    {
        $post = BlogPost::where('client_id', Auth::guard('client')->user()->id)->where('id', $id)->first();
        if(!$post)
            return abort(404);
        BlogPostOption::where('post_id', $id)->delete();
        BlogPost::findOrFail($id)->delete();
        return redirect(route('client.blog.manage'));
    }

    public function tagSearch(Request $request)
    {
        return $this->_blogController->tagSearch($request);
    }

    public function getClientImageConfig(Request $request, $id)
    {
        $client = Auth::guard('client')->user();
        return response()->json([
            'headline' => $client->getHeadlineDimension(),
            'thumb' => $client->getThumbDimension()
        ]);
    }

    public function dataquery(Request $request)
    {
        return Datatables::eloquent(BlogPost::query()->where('client_id', Auth::guard('client')->user()->id)->orderBy('is_draft', 'desc'))
            ->filter(function ($query) use ($request) {
                if($request->get('withunpublished', false) == 0) {
                    $query->where('is_draft', 0);
                }
                if($request->get('searchtext') && $request->get('searchtext') != '') {
                    $query->where('title', 'like', "%{$request->get('searchtext')}%")
                        ->orWhere('summary','like', "%{$request->get('searchtext')}%");
                }
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
                return '<a class="btn btn-xs btn-success" href="'.route('client.blog.edit', [$post->id]).'"><i class="fa fa-pencil"></i> edit</a>
                        <a class="btn btn-xs btn-danger" href="'.route('client.blog.delete', [$post->id]).'"><i class="fa fa-trash"></i> delete</a>';
            })
            ->make(true);
    }
}
