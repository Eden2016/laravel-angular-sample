<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    const BLOG_TYPES = [
      0 => 'Undefined',
      1 => 'Announcement',
      2 => 'Preview',
      3 => 'Recap',
      4 => 'Analysis',
      5 => 'Interview',
      6 => 'Blog'
    ];

    public function client()
    {
        return $this->hasOne('App\Client', 'id', 'client_id');
    }

    public function author()
    {
        return $this->hasOne('App\User', 'id', 'author_id');
    }

    public function options()
    {
        return $this->hasOne('App\Models\BlogPostOption', 'post_id', 'id');
    }

    public function getHeadlineImage()
    {
        return $this->options()->where('option', 'headline_image')->first()->value;
    }

    public function getThumbImage()
    {
        return $this->options()->where('option', 'thumb_image')->first()->value;
    }

    public function getHeadlineImageUrl()
    {
        return \Storage::disk('s3')->url($this->getHeadlineImage());
    }

    public function getThumbImageUrl()
    {
        return \Storage::disk('s3')->url($this->getThumbImage());
    }

    public function setHeadlineImage($path)
    {
        $option = $this->options()->where('option', 'headline_image')->first();
        if(!$option) {
            $option = new BlogPostOption();
            $option->post_id = $this->id;
            $option->option = 'headline_image';
        }
        $option->value = $path;
        $option->save();
    }

    public function setThumbImage($path)
    {
        $option = $this->options()->where('option', 'thumb_image')->first();
        if(!$option) {
            $option = new BlogPostOption();
            $option->post_id = $this->id;
            $option->option = 'thumb_image';
        }
        $option->value = $path;
        $option->save();
    }

    public function takeTranslations()
    {
        return array_map(function($item) {
            return str_replace('translation_', '', $item);
            },
            $this->options()->where('option', 'like' ,'translation_%')->get()->pluck('option')->toArray()
        );
    }

    public function takeGamesIds()
    {
        return $this->options()->where('option', 'game')->get()->pluck('value')->toArray();
    }

    public function takeTags()
    {
        return $this->options()->where('option', 'tag')->get()->pluck('value')->toArray();
    }

    public function takeGames()
    {
        $ids = $this->takeGamesIds();
        $selected = [];
        foreach(\App\Game::allCached() as $game) {
            if(in_array($game->id, $ids) !== false) {
                $selected[] = $game;
            }
        }
        return $selected;
    }
}
