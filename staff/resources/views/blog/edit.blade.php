@extends('layouts.default')

@section('content')

    @include('blog._partials.summernote_widget')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Edit post</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li class="active">
                    <strong>Edit post</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (Session::has('success'))
                <div class="alert alert-success">{!! session('success') !!}</div>
            @endif
            <form method="POST" enctype="multipart/form-data">
                {{csrf_field()}}

                <div class="col-sm-8">

                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" maxlength="60" class="form-control" id="title" name="title" value="{{$post->title}}" required />
                    </div>

                    <label for="game">Which game is related to ?</label>
                    <div class="form-group">
                        <select class="select2" name="games[]" id="games" style="width: 100%" multiple>
                            @foreach(\App\Game::allCached() as $game)
                                <option value="{{$game->id}}" {{in_array($game->id, $post->takeGamesIds()) ? 'selected' : ''}}>{{$game->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label for="client">Client</label>
                    <div class="form-group">
                        <select class="select2" name="client" id="client" style="width: 100%">
                            @foreach(\App\Client::all() as $client)
                                <option value="{{$client->id}}" {{$client->id == $post->client_id ? 'selected' : ''}}>{{$client->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="summary">Post summary</label>
                        <textarea class="form-control" rows="5" name="summary" id="summary" maxlength="300">{{$post->summary}}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="post">Post</label>
                        <textarea class="form-control" name="post" rows="5" id="post">{{$post->post}}</textarea>
                    </div>

                    <label for="client">Post type</label>
                    <div class="form-group">
                        <select class="form-control" name="type" id="type" style="width: 100%">
                            @foreach(\App\Models\BlogPost::BLOG_TYPES as $key => $blogType)
                                <option value="{{$key}}" {{$key == $post->type ? 'selected' : ''}}>{{$blogType}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tags">Tags</label>
                        <input type="text" class="select2" value="{{implode(',',$post->takeTags())}}" name="tags" id="tags" style="width: 100%"/>
                    </div>

                    <div class="form-group">
                        <label for="is_highlight">
                            <input type="checkbox" name="is_highlight" id="is_highlight" value="1" {{($post->is_highlight) ? 'checked' : ''}}> Highlight post
                        </label>
                    </div>

                    <a href="{{groute('blog.delete', [$post->id])}}" class="btn btn-danger">
                        <i class="fa fa-trash-o"></i> Remove
                    </a>
                    <button type="submit" name="save_as_draft" value="1" class="btn btn-default">
                        <i class="fa fa-floppy-o"></i> Save as Draft
                    </button>
                    <button type="submit" name="publish" value="1" class="btn btn-primary">
                        <i class="fa fa-upload"></i> Publish
                    </button>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <h4>Change headline image:</h4>
                        <img id="headline_preview" src="http://static.esportsconstruct.com/{{$post->getHeadlineImage()}}" class="img-rounded block" alt="{{$post->title}}" height="170">
                        <br/>
                        <input type="file" id="headline" name="headline" style="display: block">
                    </div>
                    <div class="alert alert-warning" id="headline-image-config">
                    </div>
                    <div class="form-group">
                        <h4>Change post thumb</h4>
                        <img id="thumb_preview" src="http://static.esportsconstruct.com/{{$post->getThumbImage()}}" class="img-rounded block" alt="{{$post->title}}" height="170">
                        <br/>
                        <input type="file" id="thumb" name="thumb" style="display: block">
                    </div>
                    <div class="alert alert-warning" id="thumb-image-config">
                    </div>
                    <div class="form-group">
                        <h4>Translations</h4>
                    </div>
                    <ul class="list-group" id="translation-list">

                    </ul>
                    <a class="btn btn-primary" href="#add-edit-translation" data-target="#add-edit-translation" data-toggle="modal"> + Add translation </a>
                </div>
            </form>

            <div class="modal fade" tabindex="-1" role="dialog" id="add-edit-translation">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Translation</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="title">Language</label>
                                <input type="text" name="lang" id="lang" style="width: 100%" required/>
                            </div>
                            <div class="form-group">
                                <label for="title">Translated Title</label>
                                <input type="text" maxlength="60" class="form-control" id="t_title" name="t_title" value="" required />
                            </div>
                            <div class="form-group">
                                <label for="summary">Translated post summary</label>
                                <textarea class="form-control" rows="5" name="t_summary" id="t_summary" maxlength="300"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="post">Translated post</label>
                                <textarea class="form-control" name="t_post" rows="5" id="t_post"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="saveTranslation" data-dismiss="modal" type="button" class="btn btn-primary dim">Save translation</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <div class="modal fade" tabindex="-1" role="dialog" id="edit-translation">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Edit Translation</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" maxlength="60" class="form-control" id="te_lang" name="te_lang" value="" required />
                            <div class="form-group">
                                <label for="title">Translated Title</label>
                                <input type="text" maxlength="60" class="form-control" id="te_title" name="te_title" value="" required />
                            </div>
                            <div class="form-group">
                                <label for="summary">Translated post summary</label>
                                <textarea class="form-control" rows="4" name="te_summary" id="te_summary" maxlength="300"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="post">Translated post</label>
                                <textarea class="form-control" name="te_post" rows="15" id="te_post"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="updateTranslation" data-dismiss="modal" type="button" class="btn btn-primary dim">Update translation</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            @endsection

            @section('scripts')
                @parent
                <script type="text/javascript">
                  $(document).ready(function() {
                    var isoLangsS2 = [];

                    function getTranslation(lang) {
                      $('#te_lang').val(lang);
                      $.ajax({
                        url: '{{groute('blog.post.translation', [$post->id, ''])}}/' + lang,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data)
                        {
                          console.log(data);
                          $('#edit-translation').modal('show');
                          $('#te_title').val(data.title);
                          $('#te_summary').val(data.summary);
                          $('#te_post').summernote('code',data.post);
                        }
                      });
                    }

                    function drawTranslations(list) {
                      var $list = $('#translation-list');
                      $list.empty();
                      for(var key in list) {
                        var $element = $('<li></li>');
                        $element.addClass('list-group-item link-like');
                        $element.html('<b>' + list[key] + '</b> translation - <u>edit</u>');
                        $element.data('lang', list[key]);
                        $list.prepend($element);
                        $element.click(function (e) {
                          getTranslation($(this).data('lang'));
                        });
                      }
                    }

                    $.ajax({
                      url: 'http://static.esportsconstruct.com/langs.json',
                      dataType: 'jsonp',
                      jsonp: false,
                      jsonpCallback: 'callback',
                      context: this,
                      complete: function(data) {
                        for(key in data.responseJSON) {
                          isoLangsS2.push({ id: key, text: data.responseJSON[key].name});
                        }
                      }
                    });

                    function drawPreview(input, target) {

                      if (input.files && input.files[0]) {
                        var reader = new FileReader();

                        reader.onload = function (e) {
                          $(target).attr('src', e.target.result);
                          $(target).show();
                        }

                        reader.readAsDataURL(input.files[0]);
                      }
                    }

                    $('#headline').change(function(){
                      drawPreview(this, '#headline_preview');
                    });
                    $('#thumb').change(function(){
                      drawPreview(this, '#thumb_preview');
                    });

                    $('#add-edit-translation').on('show.bs.modal', function(e){
                      $('#lang').select2({
                        data: isoLangsS2,
                        placeholder: 'Select language'
                      });
                      $('#t_title').val($('#title').val());
                      $('#t_summary').val($('#summary').val());
                      $('#t_post').summernote('code',($('#post').summernote('code')));
                    });

                    $('#saveTranslation').click(function(){
                      $.ajax({
                        url: '{{groute('blog.post.translationset', [$post->id, ''])}}/' + $('#lang').val(),
                        type: 'POST',
                        dataType: 'json',
                        data: {
                          title: $('#t_title').val(),
                          summary: $('#t_summary').val(),
                          post: $('#t_post').val(),
                          _token: '{{csrf_token()}}'
                        },
                        success: function(data, textStatus, jqXHR)
                        {
                          drawTranslations(data);
                        }
                      });
                    });

                    $('#updateTranslation').click(function(){
                      $.ajax({
                        url: '{{groute('blog.post.translationset', [$post->id, ''])}}/' + $('#te_lang').val(),
                        type: 'POST',
                        dataType: 'json',
                        data: {
                          title: $('#te_title').val(),
                          summary: $('#te_summary').val(),
                          post: $('#te_post').val(),
                          _token: '{{csrf_token()}}'
                        },
                        success: function(data, textStatus, jqXHR)
                        {
                          drawTranslations(data);
                        }
                      });
                    });

                    $('#games').select2();
                    $('#client').select2();
                    $('#client').on("select2-selecting", function(e) {
                      if(e.val == undefined) {
                        e.val = $('#client').val();
                      }
                      $.ajax({
                        url: '/editorial/image-config/' + e.val,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data, textStatus, jqXHR)
                        {
                          $('#headline-image-config').html('Headline image must be not less than ' + data.headline.width + 'x' + data.headline.height + ', also image must have proportions with same ratio');
                          $('#thumb-image-config').html('Thumb image must be not less than ' + data.thumb.width + 'x' + data.thumb.height + ', also image must have proportions with same ratio');
                        }
                      });
                    }).trigger("select2-selecting");
                    $('#tags').select2({
                      placeholder: "Type letters for tags search",
                      multiple: true,
                      ajax: {
                        url: "{{route('blog.tagsearch')}}",
                        dataType: 'json',
                        delay: 250,
                        data: function(term, page) {
                          return {
                            q: term
                          };
                        },
                        cache: true,
                        processResults: function (data, params) {
                          return {results: data.results, more: false};
                        }
                      },
                      initSelection: function (element, callback) {
                        var $values = $(element).val().split(',');
                        var elementText = $values.map(function(elem) {
                          return { id: elem, text: elem};
                        });
                        callback(elementText);
                      },
                      formatResult:  function (data) {
                        return data.text + ' [' + data.from + ']';
                      },
                      tags: true,
                      escapeMarkup: function (markup) { return markup; },
                      minimumInputLength: 2
                    });

                    $.ajax({
                      url: '{{groute('blog.post.translations', [$post->id])}}',
                      type: 'GET',
                      dataType: 'json',
                      success: function(data)
                      {
                        drawTranslations(data);
                      }
                    });
                  });
                </script>
@endsection