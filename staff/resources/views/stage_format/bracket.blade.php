@extends('layouts.default')

@section('content')

    <div class="match-wrapp">
    <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>All Tournaments</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('tournaments.list')}}">Tournaments</a>
                    </li>
                    <li class="active">
                        <strong>Bracket</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">

            </div>
        </div>
    <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-md-8">
                    <div class="ibox">
                        <div class="ibox-title text-center">
                            <h3>Tournament</h3>
                           
                        </div>
                        <div class="ibox-content">
                            <div id="minimal">
                                <div class="bracket"></div>
                            </div> 
                        </div>
                    </div>
                    
                </div>

                @endsection

                @section('scripts')
                    @parent

                    <script type="text/javascript">
                      const ROUND_TYPE_GROUP = 0;
                      const ROUND_TYPE_UPPER_BRACKET = 1;
                      const ROUND_TYPE_LOWER_BRACKET = 2;
                      const ROUND_TYPE_FINAL = 3;
                      const ROUND_TYPE_THIRD_PLACE_PLAYOFF = 4;
                      const ROUND_TYPE_DBL_ELIM_GROUP = 5;

                      var calculateMatchScore = function(single_round, callback) {
                        var scores = [];
                        $.each(single_round.dummy_matches, function(dummy_index, dummy) {
                          var opponent1score = null,
                            opponent2score = null;

                          if (dummy.match_games.length > 0) {
                            opponent1score = 0;
                            opponent2score = 0;

                            $.each(dummy.match_games, function(match_index, game) {
                              opponent1score += game.opponent1_score;
                              opponent2score += game.opponent2_score;
                            });
                          }

                          scores[dummy_index] = [opponent1score, opponent2score];
                        });

                        callback(scores);
                      }

                      var getBracket = function (callback) {
                        var newURL = window.location.protocol + "://" + window.location.host;
                        var pathArray = window.location.pathname.split( '/' );
                        var sfID = pathArray[6];

                        if (sfID == 'stage_format')
                          sfID = pathArray[7];

                        $.get('/stage_format/' + sfID, function(data) {
                          if (data.status == "success") {
                            var teamsList   = [],
                              result      = [],
                              round       = [];

                            $.each(data.stage_format[0].rounds[0].dummy_matches, function(index, dummys) {
                              teamsList[index] = [dummys.opponent1_details.name, dummys.opponent2_details.name];
                            });

                            //Get upper bracket results
                            $.each(data.stage_format[0].rounds, function(index, single_round) {
                              if (single_round.type === ROUND_TYPE_UPPER_BRACKET) {
                                calculateMatchScore(single_round, function (scores) {
                                  round[index] = scores;
                                });
                              }
                            });

                            if (round.length > 0) {
                              result[0] = round;
                              round = [];
                            }

                            //Get lower bracket results
                            $.each(data.stage_format[0].rounds, function(index, single_round) {
                              if (single_round.type === ROUND_TYPE_LOWER_BRACKET) {
                                calculateMatchScore(single_round, function (scores) {
                                  round[index] = scores;
                                });
                              }
                            });

                            if (round.length > 0) {
                              result[1] = round;
                              round = [];
                            }

                            // bracket code
                            var minimalData = {
                              teams : teamsList,
                              results : result
                            }

                            callback(minimalData);
                          } else {
                            console.log(data);
                          }
                        });
                      }

                      $(document).ready(function() {
                        getBracket(function (minimalData) {
                          console.log(minimalData);
                          $('#minimal .bracket').bracket({
                            init: minimalData
                          });
                        });
                      });
                    </script>
@endsection