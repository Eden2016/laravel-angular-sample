(function ( $ ) {
  bracket = function( data, options ) {
    // Setting default options

    var settings = $.extend({
      renderTo: false,
      includeThird: false,
      includeFinals: true,
      includeLower: true,
      includeQualifiers: false,
      showRounds: true,
      showBracketName: true,
      upperBracketOnly: false,
      highlightTeam: true,
      uniqueStageName:false,
      singleEliminationThirdPlaceMatch: false,
      highlightColor: '#FF9200',
      exludedTeams: [
        34,
        35
      ]
    }, options );

    // creating eqal number of rounds so that lower and upper brackets match

    if(!settings.upperBracketOnly){
    	var equal = data[0].length - data[1].length

        if (equal > 0) {
            for (var i = 0; i < equal; i++) {
            	data[1].unshift([]);
            }
        }
        else if (equal < 0) {
            for (var i = 0; i < -equal; i++) {
            	data[0].unshift([]);
            }
        }
    }

    var upperBracket = data[0],
      lowerBracket = data[1],
      finalBracket = data[2],
      deciderBracket = data[3],
      upperBracketOnly = settings.upperBracketOnly,
      uniqueStageName = settings.uniqueStageName,
      singleElThirdPlace = settings.singleEliminationThirdPlaceMatch,
      holder = settings.renderTo ? settings.renderTo : this,
      qualifiers = [];

      //Push qualifiers in brackets if needed
    if (settings.includeQualifiers) {
      pushQualifiers(upperBracket);
      pushQualifiers(lowerBracket);
    }

    //Generate Upper Bracket
    if (upperBracket.length > 0) {
      loadBracket(upperBracket, 'upper', singleElThirdPlace);
    }

    //Generate Lower Bracket
    if (lowerBracket.length && settings.includeLower) {
      loadBracket(lowerBracket, 'lower', false);
    }

    // add final match
    if (finalBracket.length > 0) {
    	loadFinalMatch(finalBracket);
    }

    if (settings.highlightTeam) {
      holder.on('mouseenter', '.opponent-holder', function() {
        var teamId = $(this).data('team-id');
        if (settings.exludedTeams.indexOf(teamId) === -1)
          $('[data-team-id="' + teamId + '"]').css({'box-shadow': '0px 0px 0px 2px' + settings.highlightColor, 'zIndex': '1'});
      }).on('mouseleave', '.opponent-holder', function() {
        var teamId = $(this).data('team-id');
        if (settings.exludedTeams.indexOf(teamId) === -1)
          $('[data-team-id="' + teamId + '"]').css({'box-shadow': '0px 0px 0px 0px white', 'zIndex': '0'});
      });
    }

    function loadFinalMatch(bracketData) {

    	if(bracketData[0].length){
    	// setting start points for drawing lines
    	var pathsArray = [];
        var start = '#' + $('.bracket-cont-'+ uniqueStageName + ' #upper-bracket .bracket-holder .round-holder:last-child .match-holder:last-child').attr('id');
        var start2 = '#' + $('.bracket-cont-'+ uniqueStageName + ' #lower-bracket .bracket-holder .round-holder:last-child .match-holder:last-child').attr('id');

    	var $bracketHolder = $(holder).find('#upper-bracket .bracket-holder');
    	var $roundHolder = $('<div class="round-holder round-upper-final"></div>').appendTo($bracketHolder);
    	$('<div class="round-name super-final-round">Finals</div>').appendTo($roundHolder);

    	// adding empty round at the end of the lower bracket so it has room for final match round
    	var $bracketHolderLower = $(holder).find('#lower-bracket .bracket-holder');
    	var $roundHolderLower = $('<div class="round-holder" id="round-upper-final"></div>').appendTo($bracketHolderLower);
    	// $('<div class="round-name super-final-round"></div>').appendTo($roundHolderLower);

    	var $matchesHolder = $('<div class="matches-holder"></div>').appendTo($roundHolder);
    	var $matchHolder = $('<div id="final-match-holder-'+ uniqueStageName +'" class="match-holder"></div>').appendTo($matchesHolder),
        homeTeamScore = bracketData[0][0][0][2] != undefined ? bracketData[0][0][0][2] : '',
        awayTeamScore = bracketData[0][0][1][2] != undefined ? bracketData[0][0][1][2] : '',
        homeTeamWin = (homeTeamScore < awayTeamScore) ? 'active':'',
        awayTeamWin = (homeTeamScore > awayTeamScore) ? 'active':'';
        homeCountry = bracketData[0][0][0][4].toLowerCase().replace(/ /g, '-');
        awayCountry = bracketData[0][0][1][4].toLowerCase().replace(/ /g, '-');

        if (homeTeamScore == awayTeamScore) { homeTeamWin = ''; awayTeamWin = ''; }

        if(bracketData[0][0][0][0] !== 35 && bracketData[0][0][0][0] !== 34) {
        	  //Append Home Team Name and score to the match holder
            $('<div class="opponent-holder '+ homeTeamWin +'" data-team-id="' + bracketData[0][0][0][0] + '"><span><img src="assets/images/flags/' + homeCountry  + '.png" alt="logo"></span><span>' + bracketData[0][0][0][1] + '</span><span>' + homeTeamScore + '</span></div>').appendTo($matchHolder);
        } else {
        	$('<div class="opponent-holder '+ homeTeamWin +'" data-team-id="' + bracketData[0][0][0][0] + '"></div>').appendTo($matchHolder);
        }
        if(bracketData[0][0][1][0] !== 35 && bracketData[0][0][1][0] !== 34) {
        	//Append Away Team Name and score to the match holder
        	$('<div class="opponent-holder '+ awayTeamWin +'" data-team-id="' + bracketData[0][0][1][0] + '"><span><img src="assets/images/flags/' + awayCountry  + '.png" alt="logo"></span><span>' + bracketData[0][0][1][1] + '</span><span>' + awayTeamScore + '</span></div>').appendTo($matchHolder);
        }else {
        	$('<div class="opponent-holder '+ awayTeamWin +'" data-team-id="' + bracketData[0][0][1][0] + '"></div>').appendTo($matchHolder);
        }

        var end = $('#final-match-holder-'+ uniqueStageName);

        pathsArray.push({ start: start, end: end, strokeWidth: 1,stroke: "white", offset: 20});
        pathsArray.push({ start: start2, end: end, strokeWidth: 1,stroke: "white", offset: 20});


        // on the end we call pluigin and we pass array with start end end paths
	      $('<div class="svg-connectors-container" id="' + uniqueStageName + 'upper"></div>').prependTo(holder.parent());
	      $("#" + uniqueStageName + 'upper').HTMLSVGconnect({
	          stroke: "#000",
	          strokeWidth: 1,
	          orientation: "vertical",
	          offset: 100,
	          paths: pathsArray
	      });
	      // trigger window resize so bracket connectors fall in place
	      pathsArray = [];
	      window.dispatchEvent(new Event('resize'));
	      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	    	  window.dispatchEvent(new Event('resize'));
	        });

    	}
    }

    function pushQualifiers(bracketData) {
      if(bracketData.length) {
        var qualifyRound = bracketData[bracketData.length-1];
        var qualifiers = [];
        for (var i = 0; i < qualifyRound.length; i++) {
          var winningTeam = qualifyRound[i].sort(function (a, b) {
            return b[2] - a[2];
          });

          qualifiers.push(winningTeam[0]);
        }

        bracketData.push(qualifiers);
      }
    }

    function loadBracket(bracketData, type, matchForThirdPlace) {


      if(upperBracketOnly){ // if it's only displayed upper bracket add class so we can style it
    	  holder.addClass('upper-bracket-only');
      }
      var $bracketHolder = $('<div id="'+ type +'-bracket"></div>').appendTo(holder);


      if (settings.showBracketName)
        $('<div class="bracket-title">' + capitalizeFirstLetter(type) + ' Bracket</div>').appendTo($bracketHolder);

      var $bracket = $('<div class="bracket-holder"></div>').appendTo($bracketHolder);
      var invitationalBracket = false;
      var $roundCount = 1;

      for (var i = 0; i < bracketData.length; i++) {
        if(i == bracketData.length - 1){
        	if(invitationalBracket){
        		var $roundHolder = $('<div class="round-holder final-round final-invitational" id="round-' + type + '-' + (i+1) + '"></div>').appendTo($bracket);
        	} else {
        		var $roundHolder = $('<div class="round-holder final-round" id="round-' + type + '-' + (i+1) + '"></div>').appendTo($bracket);
        	}

        } else {

          var invitationalClass = '';
          if (type == 'upper' && bracketData[i].length == bracketData[i+1].length) { // if current and next round have same number of matches it's invitational
        	invitationalClass = 'invitational-round';
        	invitationalBracket = true;
          }
          var $roundHolder = $('<div class="round-holder '+ invitationalClass +'" id="round-'+ type +'-' + (i+1) + '"></div>').appendTo($bracket);
        }
        // if round is not empty add round number and increase it
        if (!settings.includeQualifiers && settings.showRounds && bracketData[i].length != 0){
        	$('<div class="round-name">Round ' + $roundCount + '</div>').appendTo($roundHolder);
        	$roundCount++;
        }
        else if (settings.includeQualifiers && settings.showRounds && bracketData.length == i) {
          //Show this as a Qualified round
          $('<div class="round-name">Qualified</div>').appendTo($roundHolder);
        }

        var $matchesHolder = $('<div class="matches-holder '+ invitationalClass +'"></div>').appendTo($roundHolder);
        for (var l = 0; l < bracketData[i].length; l++) {

          // Check if this is a qualified team cell
          if (!(bracketData[i][l][0] instanceof Array)) {
            var qualifiedTeam = bracketData[i][l];
            console.log("This is a qualified team, take care of it in another way");
            // qualifiedTeam[0] - team ID
            // qualifiedTeam[1] - team name
            // qualifiedTeam[2] - nothing importnat
            // qualifiedTeam[3] - nothing important
            // qualifiedTeam[4] - team country of origin
            continue;
          }

        	var $matchHolder = $('<div id="' + uniqueStageName + type + i + l +'" class="match-holder '+ invitationalClass +'"></div>').appendTo($matchesHolder),
            homeTeamScore = bracketData[i][l][0][2] != undefined ? bracketData[i][l][0][2] : '',
            awayTeamScore = bracketData[i][l][1][2] != undefined ? bracketData[i][l][1][2] : '',
            homeTeamWin = (homeTeamScore < awayTeamScore) ? 'active':'',
            awayTeamWin = (homeTeamScore > awayTeamScore) ? 'active':'';
            homeCountry = bracketData[i][l][0][4].toLowerCase().replace(/ /g, '-');
            awayCountry = bracketData[i][l][1][4].toLowerCase().replace(/ /g, '-');



            if (homeTeamScore == awayTeamScore) { homeTeamWin = ''; awayTeamWin = ''; }

            if(bracketData[i][l][0][0] !== 35 && bracketData[i][l][0][0] !== 34) {
            	//Append Home Team Name and score to the match holder
                $('<div class="opponent-holder '+ homeTeamWin +'" data-team-id="' + bracketData[i][l][0][0] + '"><span><img src="assets/images/flags/' + homeCountry  + '.png" alt="logo"></span><span>' + bracketData[i][l][0][1] + '</span><span>' + homeTeamScore + '</span></div>').appendTo($matchHolder);
            } else {
            	$('<div class="opponent-holder '+ homeTeamWin +'" data-team-id="' + bracketData[i][l][0][0] + '"></div>').appendTo($matchHolder);
            }

            if(bracketData[i][l][1][0] !== 35 && bracketData[i][l][1][0] !== 34) {
            	//Append Away Team Name and score to the match holder
                $('<div class="opponent-holder '+ awayTeamWin +'" data-team-id="' + bracketData[i][l][1][0] + '"><span><img src="assets/images/flags/' + awayCountry  + '.png" alt="logo"></span><span>' + bracketData[i][l][1][1] + '</span><span>' + awayTeamScore + '</span></div>').appendTo($matchHolder);
            } else {
            	$('<div class="opponent-holder '+ awayTeamWin +'" data-team-id="' + bracketData[i][l][1][0] + '"></div>').appendTo($matchHolder);
            }


          // if it's last round and it's format with match for third place
          if( matchForThirdPlace && i == bracketData.length - 1){
        	  	 var $thirdPlaceMatchHolder = $('<div class="match-holder third-place-decider"></div>').appendTo($matchesHolder);
        	  	 // Append Home Team Name and score to the match holder
        	  	 $('<div class="opponent-holder '+ homeTeamWin +'" data-team-id="' + data[3][0][0][0][0] + '"><span><img src="assets/images/flags/' + data[3][0][0][0][4].toLowerCase().replace(/ /g, '-')  + '.png" alt="logo"></span><span>' + data[3][0][0][0][1] + '</span><span>' + homeTeamScore + '</span></div>').appendTo($thirdPlaceMatchHolder);

                 // Append Away Team Name and score to the match holder
                 $('<div class="opponent-holder '+ awayTeamWin +'" data-team-id="' + data[3][0][0][1][0] + '"><span><img src="assets/images/flags/' + data[3][0][0][1][4].toLowerCase().replace(/ /g, '-')  + '.png" alt="logo"></span><span>' + data[3][0][0][1][1] + '</span><span>' + awayTeamScore + '</span></div>').appendTo($thirdPlaceMatchHolder);

                 if(data[3][0][0][0][0] !== 35 && data[3][0][0][0][0] !== 34) {
               	  //Append Home Team Name and score to the match holder
                	 $('<div class="opponent-holder '+ homeTeamWin +'" data-team-id="' + data[3][0][0][0][0] + '"><span><img src="assets/images/flags/' + data[3][0][0][0][4].toLowerCase().replace(/ /g, '-')  + '.png" alt="logo"></span><span>' + data[3][0][0][0][1] + '</span><span>' + homeTeamScore + '</span></div>').appendTo($thirdPlaceMatchHolder);
	               } else {
	            	     $('<div class="opponent-holder '+ homeTeamWin +'" data-team-id="' + data[3][0][0][0][0] + '"></div>').appendTo($thirdPlaceMatchHolder);
	               }
                 if(data[3][0][0][1][0] !== 35 && data[3][0][0][1][0] !== 34) {
                  	  //Append Home Team Name and score to the match holder
                   	 $('<div class="opponent-holder '+ homeTeamWin +'" data-team-id="' + data[3][0][0][1][0] + '"><span><img src="assets/images/flags/' + data[3][0][0][1][4].toLowerCase().replace(/ /g, '-')  + '.png" alt="logo"></span><span>' + data[3][0][0][1][1] + '</span><span>' + homeTeamScore + '</span></div>').appendTo($thirdPlaceMatchHolder);
   	               } else {
   	            	     $('<div class="opponent-holder '+ homeTeamWin +'" data-team-id="' + data[3][0][0][1][0] + '"></div>').appendTo($thirdPlaceMatchHolder);
   	               }

          }
        }
      }

      $('<div class="clearfix"></div>').appendTo(holder);


    	  var pathsArray = [];
    	// loop true match containers
    		for(var z = 1; z < $('.bracket-cont-'+ uniqueStageName + ' #' + type + '-bracket ' + '.bracket-holder').find('.round-holder').length; z++) {
    			 // then loop true matches itself taking two matches in each round
    			for(var y = 1; y <= $('.bracket-cont-'+ uniqueStageName + ' #' + type + '-bracket ' +' .bracket-holder').find('.round-holder:nth-child('+ z +')').find('.match-holder').length; y = y+2){
    				// taking id's of two first matches of the looping container
    				 var start = '#' + $('.bracket-cont-'+ uniqueStageName + ' #' + type + '-bracket ' +' .bracket-holder').find('.round-holder:nth-child('+ z +')').find('.match-holder:nth-child('+ y +')').attr('id');
    	             var start2 = '#' + $('.bracket-cont-'+ uniqueStageName + ' #' + type + '-bracket ' +' .bracket-holder').find('.round-holder:nth-child('+ z +')').find('.match-holder:nth-child('+ (y+1) +')').attr('id');
    	             // if rounds have same number of matches lines should be straight and each mach should connect to parallel match
    	             if ($('.bracket-cont-'+ uniqueStageName + ' #' + type + '-bracket ' +' .bracket-holder').find('.round-holder:nth-child('+ z +')').find('.match-holder').length == $('.bracket-cont-'+ uniqueStageName + ' #' + type + '-bracket' +' .bracket-holder').find('.round-holder:nth-child('+ (z+1) +')').find('.match-holder').length && !$('.bracket-cont-'+ uniqueStageName + ' #' + type + '-bracket' + ' .bracket-holder').find('.round-holder:nth-child('+ (z+1) +')').hasClass('final-round')) {
    	            	// taking end id's of two parallel matches in next round
    	            	 var end = '#' + $('.bracket-cont-'+ uniqueStageName + ' #' + type + '-bracket ' +' .bracket-holder').find('.round-holder:nth-child('+ (z+1) +')').find('.match-holder:nth-child('+ y +')').attr('id');
    	            	 var end2 = '#' + $('.bracket-cont-'+ uniqueStageName + ' #' + type + '-bracket ' +' .bracket-holder').find('.round-holder:nth-child('+ (z+1) +')').find('.match-holder:nth-child('+ (y+1) +')').attr('id');
    	            	// push paths objects to array which we will pass to plugin
    	                 pathsArray.push({ start: start, end: end, strokeWidth: 1,stroke: "white", offset: 20});
    	                 pathsArray.push({ start: start2, end: end2, strokeWidth: 1,stroke: "white", offset: 20});
    	             } else {
    	            	// taking end id of match in next round wich is parallel to two matches from prevoius round
    	            	 var end = '#' + $('.bracket-cont-'+ uniqueStageName + ' #' + type + '-bracket' +' .bracket-holder').find('.round-holder:nth-child('+ (z+1) +')').find('.match-holder:nth-child('+ ((y + 1) / 2 ) +')').attr('id');
    	                 // push paths objects to array which we will pass to plugin
    	                 pathsArray.push({ start: start, end: end, strokeWidth: 1,stroke: "white", offset: 20});
    	                 pathsArray.push({ start: start2, end: end, strokeWidth: 1,stroke: "white", offset: 20});
    	             }
    			}
      	  	}

    	      // on the end we call pluigin and we pass array with start end end paths
    	      $('<div class="svg-connectors-container" id="' + uniqueStageName + type + '"></div>').prependTo(holder.parent());
    	      $("#" + uniqueStageName + type).HTMLSVGconnect({
    	          stroke: "#000",
    	          strokeWidth: 1,
    	          orientation: "vertical",
    	          offset: 100,
    	          paths: pathsArray
    	      });
    	      // trigger window resize so bracket connectors fall in place
    	      pathsArray = [];
    	      window.dispatchEvent(new Event('resize'));
    	      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    	    	  window.dispatchEvent(new Event('resize'));
    	        });
    }

    function capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    }

    return this;
  }
}( jQuery ));
