@section('scripts')
    <script src="/js/jquery-2.1.1.js"></script>
    <!-- select2 -->
    <script src="/bower_components/select2/select2.min.js"></script>
    <script src="/js/main.js"></script>

    <script src="/js/bootstrap.min.js"></script>
<script src="/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<!-- FooTable -->
<script src="/js/plugins/footable/footable.all.min.js"></script>
<!-- peity -->
<script src="/js/plugins/peity/jquery.peity.min.js"></script>
<!-- peity -->
<script src="/js/demo/peity-demo.js"></script>
<!-- Custom and plugin javascript -->
<script src="/js/inspinia.js"></script>
<script src="/js/plugins/pace/pace.min.js"></script>
<!-- steps -->
<script src="/js/plugins/staps/jquery.steps.min.js"></script>
<!-- validate -->
<script src="/js/plugins/validate/jquery.validate.min.js"></script>
<!-- datepicker -->
<script src="/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<!-- dropzone -->
<script src="/js/plugins/dropzone/dropzone.js"></script>
<!-- charts -->
<script src="/js/plugins/typemce/tinymce.min.js"></script>
<!-- TypeMCE editor -->
<script src="/js/plugins/chartJs/Chart.min.js"></script>

<!-- bracket -->
<!-- <script src="/js/plugins/bracket/jquery.bracket.min.js"></script> -->
<script src="/js/bracket.js"></script>
<script type="text/javascript" src="/bower_components/moment/min/moment.min.js"></script>
<script type="text/javascript" src="/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script>
    $(document).ready(function () {
        $('#show-all-tour').click(function () {
            $('.player-page-recent-tour').toggleClass('show-all');
            if ($(this).hasClass('active')) {
                $(this).removeClass('active').text('Show All');
            } else {
                $(this).addClass('active').text('Hide');
            }
        });
        $("#form").steps({
            bodyTag: "fieldset",
            onStepChanging: function (event, currentIndex, newIndex) {
                // Always allow going backward even if the current step contains invalid fields!
                if (currentIndex > newIndex) {
                    return true;
                }

                // Forbid suppressing "Warning" step if the user is to young
                if (newIndex === 3 && Number($("#age").val()) < 18) {
                    return false;
                }

                var form = $(this);

                // Clean up if user went backward before
                if (currentIndex < newIndex) {
                    // To remove error styles
                    $(".body:eq(" + newIndex + ") label.error", form).remove();
                    $(".body:eq(" + newIndex + ") .error", form).removeClass("error");
                }

                // Disable validation on fields that are disabled or hidden.
                form.validate().settings.ignore = ":disabled,:hidden";

                // Start validation; Prevent going forward if false
                return form.valid();
            },
            onStepChanged: function (event, currentIndex, priorIndex) {
                // Suppress (skip) "Warning" step if the user is old enough.
                if (currentIndex === 2 && Number($("#age").val()) >= 18) {
                    $(this).steps("next");
                }

                // Suppress (skip) "Warning" step if the user is old enough and wants to the previous step.
                if (currentIndex === 2 && priorIndex === 3) {
                    $(this).steps("previous");
                }
            },
            onFinishing: function (event, currentIndex) {
                var form = $(this);

                // Disable validation on fields that are disabled.
                // At this point it's recommended to do an overall check (mean ignoring only disabled fields)
                form.validate().settings.ignore = ":disabled";

                // Start validation; Prevent form submission if false
                return form.valid();
            },
            onFinished: function (event, currentIndex) {
                var form = $(this);

                // Submit form input
                form.submit();
            }
        }).validate({
            errorPlacement: function (error, element) {
                element.before(error);
            },
            rules: {
                confirm: {
                    equalTo: "#password"
                }
            }
        });

        // function for starting calendar plugin
        function startCalendarPlugin(par) {
            par.forEach(function (elem) {
                $(elem).datetimepicker({
                    format: "YYYY-MM-DD HH:mm"
                });
            });
        }

        startCalendarPlugin(['#endHolder', '#startHolder', '#f_installment_holder', '#scheduleDatetHolder', '#matchDateHolder', '.match-date-holder']);

        $('#addTeamModal').on('shown.bs.modal', function () {
            startCalendarPlugin(['#starDatetHolder', '#endDatetHolder']);
        });

        Dropzone.options.myAwesomeDropzone = {

            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 100,
            maxFiles: 10,
            acceptedFiles: 'image/*',
            // Dropzone settings
            init: function () {
                var myDropzone = this;

                this.element.querySelector("button[type=submit]").addEventListener("click", function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    myDropzone.processQueue();
                });
                this.on("sendingmultiple", function () {
                });
                this.on("successmultiple", function (files, response) {
                });
                this.on("errormultiple", function (files, response) {
                });
            }

        }

        var lineData = {
            labels: ["March", "April", "May", "June", "July"],
            datasets: [

                {
                    label: "Example dataset",
                    fillColor: "rgba(26,179,148,0.5)",
                    strokeColor: "rgba(26,179,148,0.7)",
                    pointColor: "rgba(26,179,148,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(26,179,148,1)",
                    data: [56, 67, 25, 40, 19, 86]
                }
            ]
        };

        var lineOptions = {
            scaleShowGridLines: true,
            scaleGridLineColor: "rgba(0,0,0,.05)",
            scaleGridLineWidth: 1,
            bezierCurve: true,
            bezierCurveTension: 0.4,
            pointDot: true,
            pointDotRadius: 4,
            pointDotStrokeWidth: 1,
            pointHitDetectionRadius: 20,
            datasetStroke: true,
            datasetStrokeWidth: 2,
            datasetFill: true,
            responsive: true,
        };

        if ($('#lineChart2').lenght) {
            var ctx = document.getElementById("lineChart2").getContext("2d");
            var myNewChart = new Chart(ctx).Line(lineData, lineOptions);
        }

        //  Init TypeMCE

        tinymce.init({
            selector:'textarea.mce-editor'
        });

        //  Upload file name

        $('input[type="file"]').change(function(){
            var path = $(this).val().split('\\');
            var fileName = (path[path.length - 1]) ? path[path.length - 1] : 'Browse Files';
            $(this).parent().find('span').text(fileName);
        });

        $('.thumbnail').on('click', function () {
            $(this).parent().find('input[type=file]').trigger('click');
        });

        // Add new translation

        $('#add-translation').on('click', function () {
            var translation = prompt('Add new trabslation');

            if (translation)
                $('#translation').append('<option value="' + translation + '">' + translation + '</option>');
        });

    });

    $('.footable').footable();
    $('[data-toggle="tooltip"]').tooltip();

</script>


<script type="text/javascript">
    $(document).ready(function () {


        var leagueSuggestionsShown = false;


        $('.leagueSuggestions').hide();

        $('#league').on('keyup', function (e) {
            var leagueName = $('#league').val();

            if (leagueName.length > 3) {
                $.get(gameUrl + '/tournament/getLeagueByName/' + leagueName, function (data) {
                    if(typeof data !== 'object')
                        data = JSON.parse(data);
                    if (data.status == "success") {
                        $("#leagueSuggestions").html("");
                        ;

                        for (var i = 0; i < data.leagues.length; i++) {
                            $("#leagueSuggestions").append('<li onclick="select_league(\'' + data.leagues[i].name + '\', \'' + data.leagues[i].id + '\')">' + data.leagues[i].name + '</li>');
                        }

                        if (!leagueSuggestionsShown) {
                            $('.leagueSuggestions').slideDown();
                            leagueSuggestionsShown = true;
                        }
                    }
                });
            }
        });

    });

    function select_league(name, id) {
        $("#league").val(name);
        $("#leagueid").val(id);
        $(".leagueSuggestions").slideUp();

        leagueSuggestionsShown = false;
    }
    $('#addField').on('click', function () {
        var forms = $('input[name="prizeDist[]"]').length;

        $('#prizeDistHolder').append('<p class="btn btn-danger remove-prize-btn"><i class="fa fa-times"></i></p><input type="text" class="form-control" name="prizeDist[]" placeholder="' + (forms + 1) + ' place" />');
        removeInputPrize();
    });

    function removeInputPrize() {
        $('.remove-prize-btn').click(function () {
            $(this).next('input').remove();
            $(this).remove();
        });
    }

</script>

<script type="text/javascript">
    $(document).ready(function () {
        $("#delete").on('click', function () {
            var conf = confirm('Are you sure you want to delete this event?');

            if (!conf) {
                return false;
            }
        });

        $('.addMatch').on('click', function () {
            var id = $(this).data('id');

            $.post('/dummymatch/add', {
                '_token': '{{ csrf_token() }}',
                roundId: id
            }, function (data) {
              if(typeof data !== 'object')
                data = JSON.parse(data);

                if (data.status == "success")
                    location.reload();
                else
                    console.log(data);
            });
        });

        $('.markDone').on('click', function () {
            var id = $(this).data('id');

            $.post('/dummymatch/markDone', {
                '_token': '{{ csrf_token() }}',
                id: id
            }, function (data) {

                if (data.status == "success")
                    location.reload();
                else
                    console.log(data);
            });
        });

        $('.removeMatch').click(function () {
            var id = $(this).data('id'),
                    conf = confirm('Are you sure you want to delete this match?');

            if (conf) {
                $.get('/dummymatch/remove/' + id, function (data) {
                    location.reload();
                });
            }
        });

        $('.addMatchGame').click(function () {
            var id = $(this).data('id');

            $('#addMatchGameModal').modal('show');
            $('#matchid').val(id);
        });

        $('.editMatch').click(function () {
            var id = $(this).data('id');

            $.getJSON('/dummymatch/' + id, function (data) {
                if (data.status == "success") {
                    $('#editMatchModal').modal('show');
                    $('#matchid').val(data.match.id);

                    $('#opponent1name').val(data.match.opponent1name);
                    $('#opponent1').val(data.match.opponent1id);
                    $('#opponent2name').val(data.match.opponent2name);
                    $('#opponent2').val(data.match.opponent2id);
                    $('#winnername').val(data.match.winnername);
                    $('#winner').val(data.match.winner);
                    $('#start').val(data.match.start);
                    $('#map_id').val(data.match.map_id);
                    $('#match_position').val(data.match.position);

                    if (data.match.toutouMatch != null)
                        $('#toutou_match').val(data.match.toutouMatch);

                    if (data.match.is_tie == 1)
                        $('#tie').prop('checked', true);

                    if (data.match.is_forfeited == 1)
                        $('#forfeited').prop('checked', true);
                }
            });
        });

        $('#saveMatch').click(function () {
            var isTie = 0,
                    isForfeited = 0;

            if ($('#tie').is(':checked'))
                isTie = 1;

            if ($('#forfeited').is(':checked'))
                isForfeited = 1;

            $.post('/match/storeMatch', {
                '_token': '{{ csrf_token() }}',
                id: $('#matchid').val(),
                opponent1: $('#opponent1').val(),
                opponent2: $('#opponent2').val(),
                is_tie: isTie,
                is_forfeited: isForfeited,
                winner: $('#winner').val(),
                start: $('#start').val(),
                map_id: $('#map_id').val(),
                position: $('#match_position').val(),
                ttMatch: $('#toutou_match').val()
            }, function (data) {
              if(typeof data !== 'object')
                data = JSON.parse(data);

                if (data.status == "success")
                    location.reload();
            });
        });

        $('#editMatchModal').on('hide.bs.modal', function () {
            $('#matchid').val('');

            $('#opponent1name').val('');
            $('#opponent1').val('');
            $('#opponent2name').val('');
            $('#opponent2').val('');
            $('#winnername').val('');
            $('#winner').val('');

            $('#toutou_match').val('');

            $('#tie').prop('checked', false);
            $('#forfeited').prop('checked', false);
        });

        $('#addMatchGameModal').on('hide.bs.modal', function () {
            $('#matchid').val('');
            $("#steamid").val("");
            $("#opponent1_score").val("");
            $("#opponent2_score").val("");
            $("#game_number").val("");
            $("#start_date").val("");

            //location.reload();
        });

        $("#saveMatchGame").on('click', function () {
            $("#saveMatchGame").attr('disabled', true);

            var matchid = $("#matchid").val(),
                    steamid = $("#steamid").val(),
                    opponent1 = $("#opponent1_score").val(),
                    opponent2 = $("#opponent2_score").val(),
                    game = $("#game_number").val(),
                    start = $("#start_date").val();

            $.post("/match_game/store", {
                '_token': '{{ csrf_token() }}',
                match: matchid,
                steam: steamid,
                opp1score: opponent1,
                opp2score: opponent2,
                opponent1_members: $('#addMatchGameModal [name="opponent1_members[]"]').val(),
                opponent2_members: $('#addMatchGameModal [name="opponent2_members[]"]').val(),
                map_id: $('#addMatchGameModal #map_id').val(),
                gameNum: game,
                startDate: start
            }, function (data) {
              if(typeof data !== 'object')
                data = JSON.parse(data);
                if (data.success) {
                    $("#addMatchGameModal").modal('hide');
                    $("#saveMatchGame").attr('disabled', false);
                }
            });
        });

        $("#saveMatchGame_edit").on('click', function () {
            $("#saveMatchGame_edit").attr('disabled', true);

            var matchid = $("#matchid_edit").val(),
                    mgId = $("#matchgameid").val(),
                    steamid = $("#steamid_edit").val(),
                    opponent1 = $("#opponent1_score_edit").val(),
                    opponent2 = $("#opponent2_score_edit").val(),
                    opponent1_members = $('#editMatchGameModal [name="opponent1_members[]"]').val(),
                    opponent2_members = $('#editMatchGameModal [name="opponent2_members[]"]').val(),
                    map_id = $('#editMatchGameModal #map_id').val(),
                    game = $("#game_number_edit").val(),
                    start = $("#start_date_edit").val();

            $.post("/match_game/store", {
                '_token': '{{ csrf_token() }}',
                id: mgId,
                match: matchid,
                steam: steamid,
                opp1score: opponent1,
                opp2score: opponent2,
                opponent1_members: opponent1_members,
                opponent2_members: opponent2_members,
                gameNum: game,
                startDate: start,
                map_id: map_id
            }, function (data) {
              if(typeof data !== 'object')
                data = JSON.parse(data);
                if (data.success) {
                    $("#editMatchGameModal").modal('hide');
                    $("#saveMatchGame_edit").attr('disabled', false);
                }
            });
        });

        $("#deleteMatchGame").on('click', function () {
            var conf = confirm('Are you sure you want to delete this match game?'),
                    mgId = $('#matchgameid').val();

            if (conf) {
                $.get('/match_game/delete/' + mgId, function (data) {
                    if (data.status == "success")
                        location.reload();
                    else
                        console.log(data);
                });
            }
        });

        $(".editMg").on('click', function () {
            var mgId = $(this).data('mgid');
            $.get('/match_game/' + mgId, function (data) {
              if(typeof data !== 'object')
                data = JSON.parse(data);

                if (data.success) {
                    $("#matchgameid").val(data.match_game.id);
                    $("#steamid_edit").val(data.match_game.match_id);
                    $("#opponent1_score_edit").val(data.match_game.opponent1_score);
                    $("#opponent2_score_edit").val(data.match_game.opponent2_score);
                    $("#game_number_edit").val(data.match_game.number);
                    $("#start_date_edit").val(data.start);
                    $('#editMatchGameModal [name="opponent1_members[]"]').val(data.match_game.opponent1_members).trigger('change');
                    $('#editMatchGameModal [name="opponent2_members[]"]').val(data.match_game.opponent2_members).trigger('change');
                    $('#editMatchGameModal #map_id').val(data.match_game.map_id).trigger('change');

                    $('#editMatchGameModal').modal('show');
                }
            });
        });

    });

</script>

<script type="text/javascript">
    var leagueSuggestionsShown1 = false,
            leagueSuggestionsShown2 = false;

    $(document).ready(function () {
        $('#suggestion1').hide();
        $('#suggestion2').hide();
        $('#suggestion3').hide();

        $('#opponent1name').on('keyup', function (e) {
            var teamName = $('#opponent1name').val();

            if (teamName.length > 3) {
                $.get(gameUrl + '/team/getTeamByNameNew/' + teamName, function (data) {
                  if(typeof data !== 'object')
                    data = JSON.parse(data);
                    if (data.status == "success") {
                        $("#leagueSuggestions1").html("");
                        ;

                        for (var i = 0; i < data.teams.length; i++) {
                            var teamName = data.teams[i].name.replace(/\'/g, '\\\'');
                            $("#leagueSuggestions1").append('<li onclick="select_opponent(\'' + teamName + '\', \'' + data.teams[i].id + '\', \'1\')">' + teamName + '</li>');
                        }

                        if (!leagueSuggestionsShown1) {
                            $('#suggestion1').slideDown();
                            leagueSuggestionsShown1 = true;
                        }
                    }
                });
            } else {
                ('#suggestion1').hide();
                leagueSuggestionsShown1 = false;
            }
        });

        $('#opponent2name').on('keyup', function (e) {
            var teamName = $('#opponent2name').val();

            if (teamName.length > 3) {
                $.get(gameUrl + '/team/getTeamByNameNew/' + teamName, function (data) {
                  if(typeof data !== 'object')
                    data = JSON.parse(data);
                    if (data.status == "success") {
                        $("#leagueSuggestions2").html("");

                        for (var i = 0; i < data.teams.length; i++) {
                            var teamName = data.teams[i].name.replace(/\'/g, '\\\'');
                            $("#leagueSuggestions2").append('<li onclick="select_opponent(\'' + teamName + '\', \'' + data.teams[i].id + '\', \'2\')">' + teamName + '</li>');
                        }

                        if (!leagueSuggestionsShown2) {
                            $('#suggestion2').slideDown();
                            leagueSuggestionsShown2 = true;
                        }
                    }
                });
            } else {
                $('#suggestion2').hide();
                leagueSuggestionsShown2 = false;
            }
        });

        $('#winnername').on('keyup', function (e) {
            var teamName = $('#winnername').val();

            if (teamName.length > 3) {
                $.get(gameUrl + '/team/getTeamByNameNew/' + teamName, function (data) {
                  if(typeof data !== 'object')
                    data = JSON.parse(data);
                    if (data.status == "success") {
                        $("#leagueSuggestions3").html("");
                        ;

                        for (var i = 0; i < data.teams.length; i++) {
                            var teamName = data.teams[i].name.replace(/\'/g, '\\\'');
                            $("#leagueSuggestions3").append('<li onclick="select_opponent(\'' + teamName + '\', \'' + data.teams[i].id + '\', \'3\', \'true\')">' + data.teams[i].name + '</li>');
                        }

                        if (!leagueSuggestionsShown3) {
                            $('#suggestion3').slideDown();
                            leagueSuggestionsShown3 = true;
                        }
                    }
                });
            } else {
                $('#suggestion3').hide();
                leagueSuggestionsShown3 = false;
            }
        });

    });

    function select_opponent(name, id, number, winner = false) {
        if (!winner) {
            $("#opponent" + number + "name").val(name);
            $("#opponent" + number).val(id);
        } else {
            $("#winnername").val(name);
            $("#winner").val(id);
        }

        $("#suggestion" + number).slideUp();

        leagueSuggestionsShown1 = false;
        leagueSuggestionsShown2 = false;
        leagueSuggestionsShown3 = false;
    }

</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.addTeam').click(function () {
            var id = $(this).data('id');

            $('#addTeamModal').modal('show');
            $('#playerid').val(id);
        });

        $('#saveTeamHistory').on('click', function () {
            var isCoach = 0;

            if ($('#coach').is(':checked'))
                isCoach = 1;


            $.post('/player/addRoster', {
                "_token": "{{ csrf_token() }}",
                playerId: $('#playerid').val(),
                teamId: $('#team').val(),
                start: $('#start_date').val(),
                end: $('#end_date').val(),
                coach: isCoach,
                is_sub: $('#is_sub').is(':checked') ? 1 : 0,
                is_standin: $('#is_standin').is(':checked') ? 1 : 0,
                is_manager: $('#is_manager').is(':checked') ? 1 : 0

            }, function (data) {

                if (data.status == "success")
                    location.reload();
                else
                    console.log(data);
            });
        });

        $('#addTeamModal').on('hide.bs.modal', function () {
            $('#playerid').val('');

            $('#teamname').val('');
            $('#team').val('');
            $('#start_date').val('');
            $('#end_date').val('');
        });

        $('.editTeam').click(function () {
            var id = $(this).data('id');

            $.get(gameUrl + '/player/roster/' + id, function (data) {

                if (data.status == "success") {
                    console.log('edit roster loaded');
                    console.log(data);
                    $('#editTeamModal').modal('show');
                    $('#rosterid').val(data.roster.id);

                    $('#teamnameEdit').val(data.roster.team.name);
                    $('#teamEdit').val(data.roster.team_id);
                    $('#start_date_edit').val(data.roster.start_date);
                    $('#end_date_edit').val(data.roster.end_date);

                    if (data.roster.is_coach == 1) {
                        $('#coachEdit').attr('checked', true);
                    }

                    $('#is_sub_edit').attr('checked', data.roster.is_sub == 1);
                    $('#is_standin_edit').attr('checked', data.roster.is_standin == 1);
                    $('#is_manager_edit').attr('checked', data.roster.is_manager == 1);
                }
            });
        });

        $('#editTeamHistory').on('click', function () {
            var isCoach = 0;

            if ($('#coachEdit').is(':checked'))
                isCoach = 1;


            $.post('{{route('player.roster.edit')}}', {
                "_token": "{{ csrf_token() }}",
                rosterId: $('#rosterid').val(),
                teamId: $('#teamEdit').val(),
                start: $('#start_date_edit').val(),
                end: $('#end_date_edit').val(),
                coach: isCoach,
                is_sub: $('#is_sub_edit').is(':checked') ? 1 : 0,
                is_standin: $('#is_standin_edit').is(':checked') ? 1 : 0,
                is_manager: $('#is_manager_edit').is(':checked') ? 1 : 0
            }, function (data) {

                if (data.status == "success")
                    location.reload();
                else
                    console.log(data);
            });
        });

        $('#editTeamModal').on('hide.bs.modal', function () {
            $('#rosterid').val('');

            $('#teamnameEdit').val('');
            $('#teamEdit').val('');
            $('#start_date_edit').val('');
            $('#end_date_edit').val('');
        });

        $('.removeTeam').click(function () {
            var id = $(this).data('id');
            var conf = confirm('Are you sure you want to delete this roster history?');

            if (conf) {
                $.post('/player/removeRoster', {
                    '_token': '{{ csrf_token() }}',
                    rosterId: id
                }, function (data) {

                    if (data.status == "success")
                        location.reload();
                    else
                        console.log(data)
                });
            }
        });

        $('#team').on('keyup', function (e) {
            var teamName = $('#team').val();

            if (teamName.length > 3) {
                $.get(gameUrl + '/team/getTeamByName/' + teamName, function (data) {
                  if(typeof data !== 'object')
                    data = JSON.parse(data);
                    if (data.status == "success") {
                        $("#leagueSuggestions").html("");

                        for (var i = 0; i < data.teams.length; i++) {
                            var teamName = data.teams[i].name.replace(/\'/g, '\\\'');
                            $("#leagueSuggestions").append('<li onclick="select_league(\'' + teamName + '\', \'' + data.teams[i].id + '\')">' + teamName + '</li>');
                        }

                        if (!leagueSuggestionsShown) {
                            $('.leagueSuggestions').slideDown();
                            leagueSuggestionsShown = true;
                        }
                    }
                });
            } else {
                $('.leagueSuggestions').hide();
                leagueSuggestionsShown = false;
            }
        });

    });

</script>

<script type="text/javascript">
    var teamSuggestionShown = false,
            teamEditSuggestionShown = false;

    $(document).ready(function () {
        $('#suggestion').hide();
        $('#suggestionEdit').hide();

        $('#teamname').on('keyup', function (e) {
            var teamName = $('#teamname').val();

            if (teamName.length > 3) {
                $.get(gameUrl + '/team/getTeamByNameNew/' + teamName, function (data) {
                  if(typeof data !== 'object')
                    data = JSON.parse(data);
                    if (data.status == "success") {
                        $("#teamSuggestions").html("");
                        ;

                        for (var i = 0; i < data.teams.length; i++) {
                            var teamName = data.teams[i].name.replace(/\'/g, '\\\'');
                            $("#teamSuggestions").append('<li onclick="select_team(\'' + teamName + '\', \'' + data.teams[i].id + '\', false)">' + teamName + '</li>');
                        }

                        if (!teamSuggestionsShown) {
                            $('#suggestion').slideDown();
                            teamSuggestionsShown = true;
                        }
                    }
                });
            } else {
                $('#suggestion').hide();
                teamSuggestionsShown = false;
            }
        });

        $('#teamnameEdit').on('keyup', function (e) {
            var teamName = $('#teamnameEdit').val();

            if (teamName.length > 3) {
                $.get(gameUrl + '/team/getTeamByNameNew/' + teamName, function (data) {
                  if(typeof data !== 'object')
                    data = JSON.parse(data);
                    if (data.status == "success") {
                        $("#teamEditSuggestions").html("");
                        ;

                        for (var i = 0; i < data.teams.length; i++) {
                            var teamName = data.teams[i].name.replace(/\'/g, '\\\'');
                            $("#teamEditSuggestions").append('<li onclick="select_team(\'' + teamName + '\', \'' + data.teams[i].id + '\', \'true\')">' + teamName + '</li>');
                        }

                        if (!teamEditSuggestionShown) {
                            $('#suggestionEdit').slideDown();
                            teamEditSuggestionShown = true;
                        }
                    }
                });
            } else {
                $('#suggestionEdit').hide();
                teamEditSuggestionShown = false;
            }
        });

    });

    function select_team(name, id, edit = false) {
        if (!edit) {
            $("#teamname").val(name);
            $("#team").val(id);

            $("#suggestion").slideUp();

            teamSuggestionShown = false;
        } else {
            $("#teamnameEdit").val(name);
            $("#teamEdit").val(id);

            $("#suggestionEdit").slideUp();

            teamEditSuggestionShown = false;
        }
    }
    // from edit team page    
    function select_league(name, id) {

        $("#team").val(name);
        $("#teamid").val(id);
        $(".leagueSuggestions").slideUp();

        leagueSuggestionsShown = false;
    }

    $(document).ready(function () {
        $('#deleteTeam').on('click', function (e) {
            var conf = confirm('Are you sure you want to delete this team?'),
                    teamId = $(this).data('id');

            if (conf) {
                $.get('/team/remove/' + teamId, function (data) {
                    if (data.status == "success") {
                        window.location = '/teams/list';
                    } else {
                        console.log(data);
                    }
                });
            }
        });
    });

</script>

<!-- StageFormat Opponent Prefilled Change -->
<script type="text/javascript">
    $(document).ready(function () {
        $('.opponent1-change').on('change', function (e) {
            var matchId = $(this).data('id'),
                    opponentId = $(this).val();

            $.post('/dummymatch/changeOpponent', {
                '_token': '{{ csrf_token() }}',
                match: matchId,
                opponent: opponentId,
                side: 1
            }, function (data) {

            });
        });

        $('.opponent2-change').on('change', function (e) {
            var matchId = $(this).data('id'),
                    opponentId = $(this).val();

            $.post('/dummymatch/changeOpponent', {
                '_token': '{{ csrf_token() }}',
                match: matchId,
                opponent: opponentId,
                side: 2
            }, function (data) {

            });
        });
    });

</script>

<!-- Initilize select2 for all .select2 -->
<script type="text/javascript">
    $(document).ready(function () {
        if ($('.select2').legth > 0)
            $('.select2').select2();
    });
</script>
<script src="/js/demo/chartjs-demo.js"></script>
@show