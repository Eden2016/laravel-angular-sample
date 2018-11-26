(function ( $ ) {
    $.fn.bracket = function( data, options ) {

        // Setting default options
        var settings = $.extend({
            teams: [],
            includeThird: false,
            includeFinals: true,
            includeLower: true,
            showRounds: true,
            showBracketName: true,
            highlightTeam: true,
            highlightColor: 'red',
            exludedTeams: [
                34,
                35
            ]
        }, options );

        var upperBracket = data[0],
            lowerBracket = data[1],
            finalBracket = data[2],
            deciderBracket = data[3],
            holder = this;

        //Generate Upper Bracket
        if (upperBracket.length > 0) {
            loadBracket(upperBracket, 'upper');
        }

        //Generate Lower Bracket
        if (lowerBracket.length && settings.includeLower) {
            loadBracket(lowerBracket, 'lower');
        }

        if (settings.highlightTeam) {
            holder.on('mouseenter', '.opponent-holder', function() {
                var teamId = $(this).data('team-id');
                console.log(settings.exludedTeams.indexOf(teamId));
                if (settings.exludedTeams.indexOf(teamId) === -1)
                    $('[data-team-id="' + teamId + '"]').css('background-color', settings.highlightColor);
            }).on('mouseleave', '.opponent-holder', function() {
                var teamId = $(this).data('team-id');
                if (settings.exludedTeams.indexOf(teamId) === -1)
                    $('[data-team-id="' + teamId + '"]').css('background-color', 'white');
            });
        }

        function loadBracket(bracketData, type) {
            var $bracketHolder = $('<div id="' + type + '-bracket"></div>').appendTo(holder);

            if (settings.showBracketName)
                $('<div class="bracket-title">' + capitalizeFirstLetter(type) + ' Bracket</div>').appendTo($bracketHolder);

            var $bracket = $('<div class="bracket-holder"></div>').appendTo($bracketHolder);

            for (var i = 0; i < bracketData.length; i++) {
                var $roundHolder = $('<div class="round-holder" id="round-' + type + '-' + (i+1) + '"></div>').appendTo($bracket);

                if (settings.showRounds)
                    $('<div class="round-name">Round ' + (i+1) + '</div>').appendTo($roundHolder);

                for (var l = 0; l < bracketData[i].length; l++) {
                    var $matchHolder = $('<div class="match-holder"></div>').appendTo($roundHolder).css("margin-top", i * ($('.match-holder').height() / 2)),
                    homeTeamScore = bracketData[i][l][0][2] != undefined ? bracketData[i][l][0][2] : '',
                    awayTeamScore = bracketData[i][l][1][2] != undefined ? bracketData[i][l][1][2] : '';

                    //Append Home Team Name and score to the match holder
                    var homeHtml = displayropDown(bracketData[i][l][0][1], bracketData[i][l][0][0], homeTeamScore, bracketData[i][l][0][3], 1);
                    $(homeHtml).appendTo($matchHolder);

                    //Append Away Team Name and score to the match holder
                    var awayHtml = displayropDown(bracketData[i][l][1][1], bracketData[i][l][1][0], awayTeamScore, bracketData[i][l][1][3], 2);
                    $(awayHtml).appendTo($matchHolder);
                }
            }
            $('<div class="clearfix"></div>').appendTo(holder);
        }

        function displayropDown(selectedTeamName, selectedTeamId, teamScore, matchId, side) {
            if (settings.teams.length > 0) {
                var opponentHtml = '<option value="34">Opponent 1</option>';
                if (side == 2)
                    opponentHtml = '<option value="35">Opponent 2</option>';

                var html = '<div class="opponent-holder" data-team-id="' + selectedTeamId + '"><select class="opponent-select" data-side="' + side + '" data-match-id=" ' + matchId + '">' + opponentHtml;
                for (var i = 0; i < settings.teams.length; i++) {
                    if (selectedTeamId != settings.teams[i][0]) {
                        html = html + '<option value="' + settings.teams[i][0] + '">' + settings.teams[i][1] + '</option>';
                    }
                    else {
                        html = html + '<option value="' + settings.teams[i][0] + '" selected>' + settings.teams[i][1] + '</option>';
                    }
                }
                html = html + '</select><span>' + teamScore + '</span></div>';

                return html;
            }
            else {
                return '<div class="opponent-holder" data-team-id="' + selectedTeamId + '">' + selectedTeamName + ' <span>' + teamScore + '</span></div>';
            }
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        this.on('change', '.opponent-select', function(e) {
            var $this = $(this);

            $.post('/dummymatch/changeOpponent', {
                "_token": $('#csrfToken').val(),
                match: $this.data('match-id'),
                opponent: $this.val(),
                side: $this.data('side')
            }, function(data) {

            });
        });

        return this;
    }
}( jQuery ));