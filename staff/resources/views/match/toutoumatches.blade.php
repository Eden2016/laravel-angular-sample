@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Available TouTou Matches</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{groute('toutou.matches')}}">TouTou Match List</a>
                </li>
                <li class="active">
                    <strong>Available TouTou Matches</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight" data-ng-app="App"
         data-ng-controller="TouTouMatchesController">
        <div class="row">
            <div class="col-md-9">

                <div class="col-md-12">
                    <h3>Available TouTou Matches</h3>
                    <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true"> Available <span class="badge badge-primary"> {{ count($matches) }} </span></a></li>

                        </ul>
                        <div class="tab-content">
                            <div id="tab-1" class="tab-pane active">
                                <div class="panel-body">
                                    <table class="footable table table-stripped" data-page-size="20">
                                        <thead>
                                        <tr>
                                            <th>Match ID</th>
                                            <th>Event ID</th>
                                            <th>Competition Name</th>
                                            <th>Home Team</th>
                                            <th>Away Team</th>
                                            <th>Start Date</th>
                                            <th>Linked With</th>
                                            <th>Auto-link?</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (isset($matches) && count($matches))
                                            @foreach ($matches as $match)
                                                <tr>
                                                    <td>{{ $match->id }}</td>
                                                    <td>{{ $match->event_id }}</td>
                                                    <td>{{ $match->competition_name }}</td>
                                                    <td>{{ isset($match->homeTeam->team_name) ? $match->homeTeam->team_name : '' }}</td>
                                                    <td>{{ isset($match->awayTeam->team_name) ? $match->awayTeam->team_name : '' }}</td>
                                                    <td>{{ $match->event_date }}</td>
                                                    <td>
                                                        <?php if ($match->dummy_match) { ?>
                                                        <a href="{{ groute('match.view', 'current', ['tournamentId' => 0, 'stageId' => 0, 'sfId' => 0, 'matchId' => $match->dummy_match]) }}">EC # {{ $match->dummy_match }}</a>
                                                        <?php } else { ?>
                                                        No link
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                    @if ($match->automatic_assigment > -1)
                                                        {{ $match->automatic_assigment ? 'Yes' : 'No' }}
                                                    @else
                                                        Manual overwrite
                                                    @endif
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-xs btn-success"
                                                                data-ng-click="editMatch(<?=$match->id?>)">
                                                            <i class="fa fa-pencil"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>

                                        <tfoot>
                                        <tr>
                                            <td colspan="6">
                                                <ul class="pagination pull-right"></ul>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editMatch">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                    aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Edit TouTou Match</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="game_id">Game</label>
                                <select name="game_id" id="game_id" class="form-control"
                                        data-ng-model="match.game_id">
                                    @foreach(\App\Game::allCached() as $game)
                                        <option value="{{$game->id}}"  data-ng-selected="match.game_id==<?=$game->id?>">{{$game->name}}</option>
                                    @endforeach
                                </select>
                                {{-- If you need to add more fields for edit - just create the field and set data-ng-model="match.field_name".
Then in MatchController@postSingleToutouMatch method allow it in request()->only() --}}
                            </div>
                            <div class="form-group">
                                <label for="dummy_match">DummyMatch ID</label>
                                <input type="text" id="dummy_match" name="dummy_match" class="form-control" data-ng-model="match.dummy_match" />
                            </div>
                            <div class="form-group">
                                <label for="game_number">Game Number</label>
                                <input type="text" id="game_number" name="game_number" class="form-control" data-ng-model="match.game_number" />
                            </div>
                            <div class="form-group">
                                <label for="game_number">Streams <span style="font-size:10px;">(not mandatory)</span></label>
                                <input type="text" id="streams" name="streams[]" class="form-control" data-ng-model="match.streams" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" data-ng-click="saveMatch()">Save changes
                            </button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            @endsection

            @section('scripts_head')
                @parent
                <script src="/bower_components/angular/angular.min.js"></script>
                <script>
                  var app = angular.module('App', []);
                  app.controller('TouTouMatchesController', function ($scope, $http) {
                    $scope.match = {};
                    $scope.editMatch = function (id) {
                      $http.get('/toutou/matches/' + id)
                        .then(function (response) {
                          $scope.match = response.data.event;
                          $scope.match.automatic_assigment = -1;
                          $('#game_id').val($scope.match.game_id);

                          $('#editMatch').modal('show');

                          setTimeout(function () {
                                $('#streams').select2('data', response.data.streams);
                            }, 500);
                        });
                    };
                    $scope.saveMatch = function () {
                      $scope.match.streams = $('#editMatch [name="streams[]"]').val();
                      $http.post('/toutou/matches/' + $scope.match.id, $scope.match)
                        .then(function (response) {
                          $('#editMatch').modal('hide');
                        });
                    };
                  })
                </script>
@endsection
@section('scripts')
<script src="/js/jquery-2.1.1.js"></script>
<!-- select2 -->
<script src="/bower_components/select2/select2.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/main.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#editMatch').on('shown.bs.modal', function() {
            invoke_select2();
        });
    });

    var invoke_select2 = function () {
        $('#streams').select2({
            ajax: {
                url: gameUrl + "/streams/getStreamByTitle/",
                dataType: 'json',
                delay: 250,
                data: function (term) {
                    return {
                        title: term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: $.map(data.streams, function (item) {
                            return {
                                text: item.title + " (" + item.lang + ")",
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 2,
            placeholder: 'Search for a stream',
            multiple: true
        }).parent().find('.select2-container').css({
            "width": "100%"
        });

    };
</script>
@endsection