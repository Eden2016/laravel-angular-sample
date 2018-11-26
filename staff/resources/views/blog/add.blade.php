@extends('layouts.default')

@section('content')

    @include('blog._partials.summernote_widget')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Add</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li class="active">
                    <strong>Add post</strong>
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
                        <input type="text" maxlength="60" class="form-control" id="title" name="title" required />
                    </div>

                    <label for="game">Which game is related to ?</label>
                    <div class="form-group">
                        <select class="select2" name="games[]" id="games" style="width: 100%" multiple>
                            @foreach(\App\Game::allCached() as $game)
                                <option value="{{$game->id}}" {{$game->id == request()->currentGame->id ? 'selected' : ''}}>{{$game->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label for="client">Client</label>
                    <div class="form-group">
                        <select class="select2" name="client" id="client" style="width: 100%">
                            @foreach(\App\Client::all() as $client)
                                <option value="{{$client->id}}">{{$client->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="summary">Post summary</label>
                        <textarea class="form-control" rows="5" name="summary" id="summary" maxlength="300"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="post">Post</label>
                        <textarea class="form-control" name="post" rows="5" id="post"></textarea>
                    </div>

                    <label for="client">Post type</label>
                    <div class="form-group">
                        <select class="form-control" name="type" id="type" style="width: 100%">
                            @foreach(\App\Models\BlogPost::BLOG_TYPES as $key => $blogType)
                                <option value="{{$key}}">{{$blogType}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tags">Tags</label>
                        <input type="text" class="select2" name="tags" id="tags" style="width: 100%"/>
                    </div>

                    <div class="form-group">
                        <label for="is_highlight">
                            <input type="checkbox" name="is_highlight" id="is_highlight" value="1"> Highlight post
                        </label>
                    </div>
                        <button type="submit" name="save_as_draft" value="1" class="btn btn-default">
                            <i class="fa fa-floppy-o"></i> Save as Draft
                        </button>
                        <button type="submit" name="publish" value="1" class="btn btn-primary">
                            <i class="fa fa-upload"></i> Publish
                        </button>
                    <br/>
                    <br/>
                    <!-- Some pushdown -->
                    <br/>
                    <br/>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <h4>Add headline image:</h4>
                        <img src="" id="headline_preview" class="img-rounded block" style="display: none !important"  height="170">
                        <br/>
                        <input type="file" id="headline" name="headline" style="display: block">
                    </div>
                    <div class="alert alert-warning" id="headline-image-config">
                    </div>
                    <div class="form-group">
                    <h4>Add post thumb</h4>
                        <img src="" id="thumb_preview" class="img-rounded block" style="display: none !important" height="170">
                        <br/>
                    <input type="file" id="thumb" name="thumb" style="display: block">
                    </div>
                    <div class="alert alert-warning" id="thumb-image-config">
                    </div>
                    <div class="form-group">
                        <h4>Add translation</h4>
                    </div>
                    <div class="alert alert-warning" id="thumb-image-config">
                        You can add translations only after post creation
                    </div>
                </div>
            </form>

                <div class="modal fade" tabindex="-1" role="dialog" id="add-widget">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Add esportsconstruct widget</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="title">Widget:</label>
                                    <select class="form-control" id="widget_type" name="widget_type">
                                        <option value="dota2draft">Full dota2 draft embed</option>
                                        <option value="dota2matchgamedraft">Dota2 draft embed for single match</option>
                                        <option value="dota2scoreboard">Dota2 single game scoreboard</option>
                                        <option value="matchdetails">Upcoming match details</option>
                                        <option value="player">Player details</option>
                                        <option value="team">Team details</option>
                                        <option value="tournament">Tournament details</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="title">Widget value id:</label>
                                    <input type="text" name="widget_options" id="widget_options" class="form-control" required/>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button id="insert-widget-button" data-dismiss="modal" type="button" class="btn btn-primary dim">Insert widget</button>
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

                    $('#games').select2();
                    $('#client').select2();
                    $('#headline').change(function(){
                      drawPreview(this, '#headline_preview');
                    });
                    $('#thumb').change(function(){
                      drawPreview(this, '#thumb_preview');
                    });
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
                      tags: true,
                      formatResult:  function (data) {
                        return data.text + ' [' + data.from + ']';
                      },
                      escapeMarkup: function (markup) { return markup; },
                      minimumInputLength: 2
                    });
                  });


                </script>
@endsection