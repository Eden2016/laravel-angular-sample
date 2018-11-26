@extends('layouts.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Tournament</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('tournaments.list')}}">Tournaments</a>
                    </li>
                    <li>
                        <a href="{{groute('stage', 'current', ['tournamentId' => $stage->tournament_id, 'stageId' => $stage->id])}}">Stage</a>
                    </li>
                    <li class="active">
                        <strong>Create Stage Format</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">

            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-sm-8">
                   <div class="ibox">
                       <div class="ibox-title">
                        <h3>Create Stage Format</h3>
                       </div>
                       <div class="ibox-content">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Create Post Form -->
                           <form action="{{groute('stage_format.save')}}" method="post" id="addSF">
                                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                                <input type="hidden" name="stage" id="stage" value="{{ $stage->id }}" />
                              <input type="hidden" name="tournament" value="{{ $stage->tournament_id }}" />

                              <div class="form-group">
                                <label for="format">Format</label>
                                <select class="form-control" name="format" id="format">
                                  @foreach ($types as $k=>$type)
                                  <option value="{{ $k }}">{{ $type }}</option>
                                  @endforeach
                                </select>
                              </div>
                              <div class="form-group">
                                <label for="name">Stage Format Name</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Stage 1" />
                              </div>
                              <div class="form-group" id="partNumHolder">
                                <label for="participants">Number of Participants</label>
                                <input type="text" class="form-control" name="participants" id="participants" />
                              </div>
                              <div class="form-group" id="qualifyingNumberHolder">
                                <label for="qualifingParticipants">Number of Qualifing Participants</label>
                                <input type="text" class="form-control" name="qualifingParticipants" id="qualifingParticipants" value="1" />
                              </div>
                                <div class="form-group" id="pointsDistribution">
                                    <label for="points_distribution">Points distribution type</label>
                                    <select name="points_distribution" id="points_distribution"
                                            class="form-control">
                                        <option value="per_match">Per match</option>
                                        <option value="per_game">Per game</option>
                                    </select>
                                </div>
                                <div class="form-group" id="points_per_win_holder">
                                    <label for="points_per_win">Points per win</label>
                                    <input type="text" class="form-control" name="points_per_win" id="points_per_win" value="0" />
                                </div>
                                <div class="form-group" id="points_per_draw_holder">
                                    <label for="points_per_draw">Points per draw</label>
                                    <input type="text" class="form-control" name="points_per_draw" id="points_per_draw" value="0" />
                                </div>
                              <div class="checkbox" id="invitationalHolder">
                                <label>
                                  <input type="checkbox" name="invitational" value="1" id="invitational"> Invitational
                                </label>
                              </div>
                              <div class="checkbox" id="doubleHolder">
                                <label>
                                  <input type="checkbox" name="double_rounds" value="1" id="double_rounds"> Double Rounds
                                </label>
                              </div>
                              <div class="checkbox" id="playoffsHolder">
                                <label>
                                  <input type="checkbox" name="disable_playoffs" value="1" id="disable_playoffs"> Disable Playoffs
                                </label>
                              </div>

                                <div class="form-group" id="elimination_playoffs_container">
                                    <label for="elimination_playoffs">Elimination playoffs</label>
                                    <select name="elimination_playoffs" id="elimination_playoffs" class="form-control">
                                        <option value="single">Single elimination</option>
                                        <option value="double">Double elimination</option>
                                    </select>
                                </div>
                              <div class="checkbox" id="thirdPlaceHolder">
                                <label>
                                  <input type="checkbox" name="third_place" value="1" id="third_place"> Third Place Match
                                </label>
                              </div>
                              <div class="checkbox" id="urlPrefillHolder">
                                <label>
                                  <input type="checkbox" name="url_prefill" value="1" id="url_prefill"> URL Prefill?
                                </label>
                              </div>
                              <div class="form-group" id="urlHolder">
                                <label for="groupsNum">Scrape URL</label>
                                <input type="text" class="form-control" name="url" id="url" placeholder="http://..." />
                              </div>
                              <div class="form-group" id="groupsNumHolder">
                                <label for="groupsNum">Number of groups</label>
                                <input type="text" class="form-control" name="groupsNum" id="groupsNum" value="1" />
                              </div>
                              <div class="form-group" id="invitedQualifiersHolder">
                                <label for="invitedQualifiers">Number of invited qualifiers</label>
                                <input type="text" class="form-control" name="invitedQualifiers" id="invitedQualifiers" value="0" />
                              </div>
                              <div class="form-group" id="upperBracketNumHolder">
                                <label for="participantsUpperBracket">Number of Participants in Upper Bracket</label>
                                <input type="text" class="form-control" name="participantsUpperBracket" id="participantsUpperBracket" value="1" />
                              </div>
                              <div class="form-group" id="lowerBracketNumHolder">
                                <label for="participantsLowerBracket">Number of Participants in Lower Bracket</label>
                                <input type="text" class="form-control" name="participantsLowerBracket" id="participantsLowerBracket" value="1" />
                              </div>
                                  <label for="start">Start Date</label>
                              <div class='input-group date' id='startHolder'>
                                  <input type="text" class="form-control" name="start" id="start" value="{{$stage->start}}" />
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                              </div>
                              <input type="hidden" name="end" id="end" value="2018-12-31 08:00:00" />

                              <div class="checkbox">
                                <label>
                                  <input type="checkbox" name="active" value="1" id="active" checked> Is active
                                </label>
                              </div>
                              <div class="checkbox">
                                <label>
                                  <input type="checkbox" name="hidden" value="1" id="hidden"> Is hidden
                                </label>
                              </div>
                              <button type="submit" class="btn btn-primary dim" id="addSFButton">Add stage format</button>
                            </form>
                       </div>
                   </div>
                </div>

                <!-- Add Participants Modal -->
                <div class="modal fade" id="addParticipantsModal" role="dialog" aria-labelledby="addParticipantsModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Select Participants</h4>
                      </div>
                      <div class="modal-body">
                        <form id="participantsSelect">
                          <input type="hidden" name="sfid" id="sfid">
                          <input type="hidden" name="gslIds" id="gslIds">
                          <input type="hidden" name="sftype" id="sftype">
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="saveParticipants">Save</button>
                      </div>
                    </div>
                  </div>
                </div>

                @endsection

                @section('scripts')
                    @parent

                    <script type="text/javascript">
                      $(document).ready(function () {
                        $('#disable_playoffs').on('change', function(){
                          if($(this).is(':checked')){
                            $('#elimination_playoffs_container').hide();
                          } else{
                            $('#elimination_playoffs_container').show();
                          }
                        });
                        $('#upperBracketNumHolder').hide();
                        $('#lowerBracketNumHolder').hide();
                        $('#invitedQualifiersHolder').hide();
                        $('#invitationalHolder').hide();
                        $('#doubleHolder').hide();
                        $('#playoffsHolder').hide();
                        $('#groupsNumHolder').hide();
                        $('#thirdPlaceHolder').hide();
                        $('#urlHolder').hide();
                        $('#urlPrefillHolder').hide();


                        if ($('#format').val() == {{ \App\StageFormat::TYPE_SINGLE_ELIM }}) {
                          $('#invitationalHolder').show();
                          $('#thirdPlaceHolder').show();

                          $('#urlPrefillHolder').show();
                          $('#qualifyingNumberHolder').show()
                            .find('#qualifingParticipants').val(function(){
                            if($('#qualifyingNumberHolder').data('old')){
                              return $('#qualifyingNumberHolder').data('old');
                            }
                            return 1;
                          });
                          $('#elimination_playoffs_container').hide();
                        }

                        if ($('#format').val() == {{ \App\StageFormat::TYPE_ROUND_ROBIN }}) {
                          $('#doubleHolder').show();
                          $('#groupsNumHolder').show();

                          $('#urlPrefillHolder').show();
                          $('#qualifyingNumberHolder').show()
                            .find('#qualifingParticipants').val(function(){
                            if($('#qualifyingNumberHolder').data('old')){
                              return $('#qualifyingNumberHolder').data('old');
                            }
                            return 1;
                          });
                          $('#elimination_playoffs_container').hide();

                        }

                        if ($('#format').val() == {{ \App\StageFormat::TYPE_GSL_FORMAT }}) {
                          $('#playoffsHolder').hide();
                          $('#groupsNumHolder').show();
                          $('#qualifyingNumberHolder').hide().data('old', $('#qualifingParticipants').val())
                            .find('#qualifingParticipants').val('2');
                          $('#elimination_playoffs_container').show();
                          $('#upperBracketNumHolder').show();
                          $('#lowerBracketNumHolder').show();
                          $('#upperBracketNumHolder').hide();
                          $('#lowerBracketNumHolder').hide();
                        }

                        $('#format').change(function () {
                          var format = $(this).val();

                          if (format == {{ \App\StageFormat::TYPE_DOUBLE_ELIM }}) {
                            var participants = $('#participants').val();
                            $('#elimination_playoffs_container').hide();

                            $('#participantsUpperBracket').val(participants);
                            $('#participantsLowerBracket').val(participants / 2);

                            $('#upperBracketNumHolder').show();
                            $('#lowerBracketNumHolder').show();

                            $('#urlPrefillHolder').show();

                            $('#invitedQualifiersHolder').hide();
                            $('#invitationalHolder').hide();
                            $('#doubleHolder').hide();
                            $('#playoffsHolder').hide();
                            $('#groupsNumHolder').hide();
                            $('#thirdPlaceHolder').hide();
                            $('#urlPrefillHolder').hide();
                            $('#qualifyingNumberHolder').show()
                              .find('#qualifingParticipants').val(function(){
                              if($('#qualifyingNumberHolder').data('old')){
                                return $('#qualifyingNumberHolder').data('old');
                              }
                              return 1;
                            });
                          } else if (format == {{ \App\StageFormat::TYPE_GSL_FORMAT }}) {
                            var participants = $('#participants').val();

                            $('#playoffsHolder').show();
                            $('#groupsNumHolder').show();

                            $('#participantsUpperBracket').val(participants);
                            $('#participantsLowerBracket').val(participants / 2);

                            $('#upperBracketNumHolder').hide();
                            $('#lowerBracketNumHolder').hide();

                            $('#invitedQualifiersHolder').hide();
                            $('#invitationalHolder').hide();
                            $('#doubleHolder').hide();
                            $('#thirdPlaceHolder').hide();
                            $('#urlPrefillHolder').hide();
                            $('#elimination_playoffs_container').show();

                            $('#qualifyingNumberHolder').hide().data('old', $('#qualifingParticipants').val())
                              .find('#qualifingParticipants').val('2');
                          } else if (format == {{ \App\StageFormat::TYPE_SINGLE_ELIM }}) {
                            $('#invitationalHolder').show();
                            $('#thirdPlaceHolder').show();
                            $('#elimination_playoffs_container').hide();

                            $('#upperBracketNumHolder').hide();
                            $('#lowerBracketNumHolder').hide();
                            $('#doubleHolder').hide();
                            $('#playoffsHolder').hide();
                            $('#groupsNumHolder').hide();
                            $('#urlPrefillHolder').hide();
                            $('#qualifyingNumberHolder').show()
                              .find('#qualifingParticipants').val(function(){
                              if($('#qualifyingNumberHolder').data('old')){
                                return $('#qualifyingNumberHolder').data('old');
                              }
                              return 1;
                            });
                          } else if (format == {{ \App\StageFormat::TYPE_ROUND_ROBIN }}) {
                            $('#doubleHolder').show();
                            $('#groupsNumHolder').show();
                            $('#urlPrefillHolder').show();
                            $('#elimination_playoffs_container').hide();
                            $('#upperBracketNumHolder').hide();
                            $('#lowerBracketNumHolder').hide();
                            $('#invitationalHolder').hide();
                            $('#playoffsHolder').hide();
                            $('#thirdPlaceHolder').hide();
                            $('#qualifyingNumberHolder').show()
                              .find('#qualifingParticipants').val(function(){
                              if($('#qualifyingNumberHolder').data('old')){
                                return $('#qualifyingNumberHolder').data('old');
                              }
                              return 1;
                            });
                          }
                          else if (format == {{ \App\StageFormat::TYPE_SWISS_FORMAT }}) {
                            $('#upperBracketNumHolder').hide();
                            $('#lowerBracketNumHolder').hide();
                            $('#elimination_playoffs_container').hide();

                            $('#invitedQualifiersHolder').hide();
                            $('#invitationalHolder').hide();
                            $('#doubleHolder').hide();
                            $('#groupsNumHolder').hide();
                            $('#playoffsHolder').hide();
                            $('#thirdPlaceHolder').hide();
                            $('#urlPrefillHolder').hide();
                            $('#qualifyingNumberHolder').hide();
                          } else {
                            $('#upperBracketNumHolder').hide();
                            $('#lowerBracketNumHolder').hide();
                            $('#elimination_playoffs_container').hide();

                            //$('#invitedQualifiersHolder').hide();
                            $('#invitationalHolder').hide();
                            $('#doubleHolder').hide();
                            $('#groupsNumHolder').hide();
                            $('#playoffsHolder').hide();
                            $('#thirdPlaceHolder').hide();
                            $('#urlPrefillHolder').hide();
                            $('#qualifyingNumberHolder').show()
                              .find('#qualifingParticipants').val(function(){
                              if($('#qualifyingNumberHolder').data('old')){
                                return $('#qualifyingNumberHolder').data('old');
                              }
                              return 1;
                            });
                          }
                        });

                        $('#url_prefill').on('click', function ( e ) {
                          if ($(this).is(':checked')) {
                            $('#urlHolder').show();

                            $('#qualifyingNumberHolder').hide();
                            $('#partNumHolder').hide();
                            $('#groupsNumHolder').hide();
                          } else {
                            $('#urlHolder').hide();

                            $('#qualifyingNumberHolder').show();
                            $('#partNumHolder').show();
                            $('#groupsNumHolder').show();
                          }
                        });

                        $('#addSF').on('submit', function (e) {
                          if ($('#url_prefill').is(':checked')) {
                            return;
                          }

                          e.preventDefault();

                          $('#addSFButton').attr('disabled', true);

                          var isHidden = 0,
                            isActive = 0,
                            grNum = $('#groupsNum').val();

                          if (grNum == 0)
                            grNum = 1;

                          var isHidden = 0,
                            isActive = 0;

                          if ($('#active').is(':checked'))
                            isActive = 1;

                          if ($('#hidden').is(':checked'))
                            isHidden = 1;

                          if ($('#format').val() == {{ \App\StageFormat::TYPE_ROUND_ROBIN }}) {
                            $.post('{{route('stage_format.add_ajax')}}', {
                              _token : $('#token').val(),
                              stage : $('#stage').val(),
                              name : $('#name').val(),
                              format : $('#format').val(),
                              start : $('#start').val(),
                              participants : $('#participants').val(),
                              qualifingParticipants : $('#qualifingParticipants').val(),
                              groupsNum : grNum,
                              hidden : isHidden,
                              elimination_playoffs: $('#elimination_playoffs').val(),
                              disable_playoffs: $('#disable_playoffs').is(':checked'),
                              active : isActive,
                              points_distribution: $('#points_distribution').val(),
                              points_per_win: $('#points_per_win').val(),
                              points_per_draw: $('#points_per_draw').val()
                            }, function (data) {
                              $('#addSFButton').attr('disabled', false);

                              if (data.status == "success") {
                                var first_div = $(document.createElement('div')).addClass('col-xs-6');

                                var second_div = $(document.createElement('div')).addClass('col-xs-6');
                                var row = $(document.createElement('div')).addClass('row');

                                for (var i = 0; i < data.participants; i++) {
                                  if (i % $('#participants').val() == 0)
                                    $('<br/><br/>').appendTo('#participantsSelect');

                                  $('<div class="form-group"><input name="participants[]"  data-index="' + i + '" class="select2-participants" /></div>').appendTo(first_div);
                                  $('<div class="form-group">'
                                    + '<input class="select-team-members" name="team_members[]" data-index="' + i + '" />'
                                    + '</div>').appendTo(second_div);
                                }
                                first_div.appendTo(row);
                                second_div.appendTo(row);
                                row.appendTo('#participantsSelect');

                                $('#addParticipantsModal').modal('show');
                                $('#sfid').val(data.id);
                                $('#sftype').val(data.format);
                              } else {
                                console.log(data);
                              }
                            });
                          }
                          else if ($('#format').val() == {{ \App\StageFormat::TYPE_GSL_FORMAT }}) {
                            $.post('{{route('stage_format.add_ajax')}}', {
                              _token : $('#token').val(),
                              stage : $('#stage').val(),
                              name : $('#name').val(),
                              format : $('#format').val(),
                              start : $('#start').val(),
                              participants : $('#participants').val(),
                              qualifingParticipants : $('#qualifingParticipants').val(),
                              groupsNum : grNum,
                              hidden : isHidden,
                              elimination_playoffs: $('#elimination_playoffs').val(),
                              disable_playoffs: $('#disable_playoffs').is(':checked'),
                              active : isActive,
                              points_distribution: $('#points_distribution').val(),
                              points_per_win: $('#points_per_win').val(),
                              points_per_draw: $('#points_per_draw').val()
                            }, function (data) {
                              $('#addSFButton').attr('disabled', false);

                              if (data.status == "success") {
                                var first_div = $(document.createElement('div')).addClass('col-xs-6');

                                var second_div = $(document.createElement('div')).addClass('col-xs-6');
                                var row = $(document.createElement('div')).addClass('row');

                                for (var i = 0; i < data.participants; i++) {
                                  if (i % (data.participants / grNum) == 0) {
                                    $('<br/><br/>').appendTo(first_div);
                                    $('<br/><br/>').appendTo(second_div);
                                  }

                                  $('<div class="form-group"><input name="participants[]"  data-index="' + i + '" class="select2-participants" /></div>').appendTo(first_div);
                                  $('<div class="form-group">'
                                    + '<input class="select-team-members" name="team_members[]" data-index="' + i + '" />'
                                    + '</div>').appendTo(second_div);
                                }
                                first_div.appendTo(row);
                                second_div.appendTo(row);
                                row.appendTo('#participantsSelect');

                                $('#addParticipantsModal').modal('show');
                                $('#sfid').val(data.id);
                                $('#gslIds').val(data.stage_formats);
                                $('#sftype').val(data.format);
                              }
                              else {
                                console.log(data);
                              }
                            });
                          }
                          else {
                            $.post('{{route('stage_format.add_ajax')}}', $(this).serialize(),
                              function (data) {
                                $('#addSFButton').attr('disabled', false);

                                if (data.status == "success") {
                                  if ($('#invitational').is(':checked')) {
                                    data.participants = parseInt(data.participants) + (data.participants / 2);
                                  }
                                  var first_div = $(document.createElement('div')).addClass('col-xs-6');

                                  var second_div = $(document.createElement('div')).addClass('col-xs-6');
                                  var row = $(document.createElement('div')).addClass('row');
                                  for (var i = 0; i < data.participants; i++) {
                                    $('<div class="form-group"><input name="participants[]"  data-index="' + i + '" class="select2-participants" /></div>').appendTo(first_div);
                                    $('<div class="form-group">'
                                      + '<input class="select-team-members" name="team_members[]" data-index="' + i + '" />'
                                      + '</div>').appendTo(second_div);
                                  }
                                  first_div.appendTo(row);
                                  second_div.appendTo(row);
                                  row.appendTo('#participantsSelect');
                                  $('#addParticipantsModal').modal('show');
                                  $('#sfid').val(data.id);
                                  $('#sftype').val(data.format);
                                } else {
                                  console.log(data);
                                }
                              });
                          }
                        });

                        $('#saveParticipants').on('click', function (e) {
                          e.preventDefault();

                          var sfType = $('#sftype').val(),
                            url = '{{groute('stage_format.add_participants')}}';

                          var participants  = "",
                            drounds       = 0,
                            grNum         = $('#groupsNum').val();

                          if (grNum == 0)
                            grNum = 1;
                          participants = [];
                          $.each($('.select2-participants'), function () {
                            participants.push({
                              id: $(this).val(),
                              members: $('[name="team_members[]"][data-index="' + $(this).data('index') + '"]').val().split(',')
                            });
                          });

                          if ($('#double_rounds').is(':checked'))
                            drounds = 1;

                          if (sfType != {{ \App\StageFormat::TYPE_ROUND_ROBIN }})
                            url = '{{groute('stage_format.add_opponents')}}';

                          $.post(url, {
                            _token : $('#token').val(),
                            id : $('#sfid').val(),
                            type : sfType,
                            data : participants,
                            groupsNum : grNum,
                            double_rounds : drounds,
                            gslIds : $('#gslIds').val()
                          }, function (data) {

                            if (data.status == "success") {
                              window.location.href = data.location;
                            } else {
                              console.log(data);
                            }
                          });
                        });

                        $('#addParticipantsModal').on('shown.bs.modal', function() {
                          invoke_select2();
                        });

                      });

                      var invoke_select2 = function () {

                        //Selecting participants for round robin stage format
                        $('.select-team-members').select2({
                          placeholder: 'Select a team first',
                          data: {},
                          multiple: true
                        }).parent().find('.select2-container').css({"width": "100%"});
                        $('.select2-participants').select2({
                          ajax: {
                            url: gameUrl + "/team/getTeamByNameNew/",
                            dataType: 'json',
                            delay: 250,
                            data: function (term) {
                              return {
                                name: term
                              };
                            },
                            processResults: function (data, params) {
                              return {
                                results: $.map(data.teams, function (item) {
                                  return {
                                    text: item.name,
                                    id: item.id
                                  }
                                })
                              };
                            },
                            cache: true
                          },
                          escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                          minimumInputLength: 1,
                          placeholder: 'Search for a team'
                        }).parent().find('.select2-container').css({"width": "100%"});

                      };
                      $('body').on('change', '[name="participants[]"]', function (e) {
                        var el = $(this);
                        var index = $(this).data('index');
                        $.get('/api/team/members', {
                          id: $(el).val(),
                          sfId: $('#sfid').val()
                        }, function (data) {
                          var select2_data = [];
                          $.each(data, function (index, item) {
                            select2_data.push({
                              id: item.id,
                              text: item.nickname
                            });
                          });
                          $('.select-team-members[data-index="' + index + '"]').select2({
                            data: select2_data,
                            multiple: true
                          }).parent().find('.select2-container').css({"width": "100%"});
                        });

                      });
                    </script>
@endsection