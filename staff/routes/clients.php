<?php

Route::auth();

Route::get('/', ['as' => 'client.home', 'uses' => 'HomeController@index']);

Route::group(['prefix' => 'editorial'], function(){
    Route::get('/add', ['as' => 'client.blog.create', 'uses' => 'BlogController@create']);
    Route::post('/add', ['as' => 'client.blog.store', 'uses' => 'BlogController@store']);
    Route::get('/{id}/edit', ['as' => 'client.blog.edit', 'uses' => 'BlogController@edit']);
    Route::post('/{id}/edit', ['as' => 'client.blog.update', 'uses' => 'BlogController@update']);
    Route::get('/{id}/delete', ['as' => 'client.blog.delete', 'uses' => 'BlogController@delete']);
    Route::get('/tag-search', ['as' => 'client.blog.tagsearch', 'uses' => 'BlogController@tagSearch']);
    Route::get('/data-query', ['as' => 'client.blog.posts.dataquery', 'uses' => 'BlogController@dataquery']);
    Route::get('/image-config/{id}', ['as' => 'client.blog.imageconfig', 'uses' => 'BlogController@getClientImageConfig']);
    Route::get('/{id}/translations', ['as' => 'client.blog.post.translations', 'uses' => 'BlogController@getTranslations']);
    Route::get('/{id}/translations/{lang}', ['as' => 'client.blog.post.translation', 'uses' => 'BlogController@getTranslation']);
    Route::post('/{id}/translations/{lang}', ['as' => 'client.blog.post.translationset', 'uses' => 'BlogController@setTranslation']);
    Route::get('/manage', ['as' => 'client.blog.manage', 'uses' => 'BlogController@getPosts']);
});