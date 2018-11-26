@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Dashboard</h2>

        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">MATCHES</div>
                            <div class="panel-body">
                                <div class="input-group">
                                    <input type="text" placeholder="Search matches" class="input form-control" id="search-match" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn btn-primary" disabled> <i class="fa fa-search"></i> Search</button>
                                    </span>
                                </div>
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#tab-matches-1">Live Matches <span
                                                    class="badge badge-danger"> {{count($dummies['live'])}} </span></a>
                                    </li>
                                    <li><a data-toggle="tab" href="#tab-matches-2"> Upcoming Matches <span
                                                    class="badge badge-warning"> {{count($dummies['upcoming'])}} </span></a>
                                    </li>
                                    <li><a data-toggle="tab" href="#tab-matches-3"> Recent Matches <span
                                                    class="badge badge-warning"> {{count($dummies['completed'])}} </span></a>
                                    </li>
                                    <li><a data-toggle="tab" href="#tab-matches-4"> TBA Matches <span
                                                    class="badge badge-warning"> {{count($dummies['tba'])}} </span></a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div id="tab-matches-1" class="tab-pane active">
                                        <div class="table-responsive">
                                            <table class="footable table table-stripped" data-page-size="10" data-limit-navigation="5" data-filter="#search-match" data-filter-minimum="3">
                                                <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Team 1</th>
                                                    <th>Team 2</th>
                                                    <th>Game</th>
                                                    <th>Time</th>
                                                    <th>Date</th>
                                                    <th>Stage Link</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($dummies['live'] as $live)
                                                    <tr>
                                                        <td>
                                                            <?php try{ ?>
                                                            <a href="{{groute('match.view', 'current', [
                                                            'tournamentId' => $live->tournament->id,
                                                            'stageId' => $live->stageRound->stageFormat->stage->id,
                                                            'sfId' => $live->stageRound->stageFormat->id,
                                                            'matchId' => $live->id
                                                        ])}}" target="_blank">
                                                                {{$live->id}}</a>
                                                            <?php }catch(\Exception $e){ ?>
                                                            {{$live->id}}
                                                            <?php } ?>
                                                        </td>
                                                        <td>{{$live->opponent1_details->name}}</td>
                                                        <td>{{$live->opponent2_details->name}}</td>
                                                        <td>
                                                            @unless(!$live->game)
                                                                {{$live->game->name}}
                                                            @endunless
                                                        </td>
                                                        <td data-value="{{ strtotime($live->start) }}">{{$live->start ? date_convert($live->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'H:i') : '-'}}</td>
                                                        <td data-value="{{ strtotime($live->start) }}">{{$live->start ? date_convert($live->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd-m-Y') : '-'}}</td>
                                                        <td>
                                                            <a href="{{groute('stage_format', 'current', [
                                                            'tournamentId' => $live->tournament->id,
                                                            'stageId' => $live->stageRound->stageFormat->stage->id,
                                                            'sfId' => $live->stageRound->stageFormat->id
                                                            ])}}">Stage {{ $live->stageRound->stageFormat->id }}</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
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
                                    <div id="tab-matches-2" class="tab-pane">

                                        <div class="table-responsive">
                                            <table class="footable table table-stripped" data-page-size="10"
                                                   data-limit-navigation="5" data-filter="#search-match"
                                                   data-filter-minimum="3">
                                                <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Team 1</th>
                                                    <th>Team 2</th>
                                                    <th>Game</th>
                                                    <th>Time</th>
                                                    <th>Date</th>
                                                    <th>Stage Link</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($dummies['upcoming'] as $upcoming)
                                                    <tr>
                                                        <td><?php try{ ?>
                                                            <a href="{{groute('match.view', 'current', [
                                                            'tournamentId' => $upcoming->tournament->id,
                                                            'stageId' => $upcoming->stageRound->stageFormat->stage->id,
                                                            'sfId' => $upcoming->stageRound->stageFormat->id,
                                                            'matchId' => $upcoming->id
                                                        ])}}" target="_blank">
                                                                {{$upcoming->id}}</a>
                                                            <?php }catch(\Exception $e){ ?>
                                                            {{$upcoming->id}}
                                                            <?php } ?>
                                                        </td>
                                                        <td>{{$upcoming->opponent1_details->name}}</td>
                                                        <td>{{$upcoming->opponent2_details->name}}</td>
                                                        <td>@unless(!$upcoming->game)
                                                                {{$upcoming->game->name}}
                                                            @endunless</td>
                                                        <td data-value="{{ strtotime($upcoming->start) }}">{{$upcoming->start ? date_convert($upcoming->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'H:i') : '-'}}</td>
                                                        <td data-value="{{ strtotime($upcoming->start) }}">{{$upcoming->start ? date_convert($upcoming->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd-m-Y') : '-'}}</td>
                                                        <td>
                                                            <a href="{{groute('stage_format', 'current', [
                                                            'tournamentId' => $upcoming->tournament->id,
                                                            'stageId' => $upcoming->stageRound->stageFormat->stage->id,
                                                            'sfId' => $upcoming->stageRound->stageFormat->id
                                                            ])}}">Stage {{ $upcoming->stageRound->stageFormat->id }}</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
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
                                    <div id="tab-matches-3" class="tab-pane">

                                        <div class="table-responsive">
                                            <table class="footable table table-stripped" data-page-size="10" data-limit-navigation="5" data-filter="#search-match" data-filter-minimum="3">
                                                <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Team 1</th>
                                                    <th>Team 2</th>
                                                    <th>Game</th>
                                                    <th>Time</th>
                                                    <th>Date</th>
                                                    <th>Stage Link</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($dummies['completed'] as $recent)
                                                    <tr>
                                                        <td><?php try{ ?>
                                                            <a href="{{groute('match.view', 'current', [
                                                            'tournamentId' => $recent->tournament->id,
                                                            'stageId' => $recent->stageRound->stageFormat->stage->id,
                                                            'sfId' => $recent->stageRound->stageFormat->id,
                                                            'matchId' => $recent->id
                                                        ])}}" target="_blank">
                                                                {{$recent->id}}</a>
                                                            <?php }catch(\Exception $e){ ?>
                                                            {{$recent->id}}
                                                            <?php } ?>
                                                        </td>
                                                        <td>{{$recent->opponent1_details->name}}</td>
                                                        <td>{{$recent->opponent2_details->name}}</td>
                                                        <td>@unless(!$recent->game)
                                                                {{$recent->game->name}}
                                                            @endunless</td>
                                                        <td data-value="{{ strtotime($recent->start) }}">{{$recent->start ? date_convert($recent->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'H:i') : '-'}}</td>
                                                        <td data-value="{{ strtotime($recent->start) }}">{{$recent->start ? date_convert($recent->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd-m-Y') : '-'}}</td>
                                                        <td>
                                                            <a href="{{groute('stage_format', 'current', [
                                                            'tournamentId' => $recent->tournament->id,
                                                            'stageId' => $recent->stageRound->stageFormat->stage->id,
                                                            'sfId' => $recent->stageRound->stageFormat->id
                                                            ])}}">Stage {{ $recent->stageRound->stageFormat->id }}</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
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
                                    <div id="tab-matches-4" class="tab-pane">

                                        <div class="table-responsive">
                                            <table class="footable table table-stripped" data-page-size="10" data-limit-navigation="5" data-filter="#search-match" data-filter-minimum="3">
                                                <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Team 1</th>
                                                    <th>Team 2</th>
                                                    <th>Game</th>
                                                    <th>Time</th>
                                                    <th>Date</th>
                                                    <th>Stage Link</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($dummies['tba'] as $tba)
                                                    <tr>
                                                        <td><?php try{ ?>
                                                            <a href="{{groute('match.view', 'current' ,[
                                                            'tournamentId' => $tba->tournament->id,
                                                            'stageId' => $tba->stageRound->stageFormat->stage->id,
                                                            'sfId' => $tba->stageRound->stageFormat->id,
                                                            'matchId' => $tba->id
                                                        ])}}" target="_blank">
                                                                {{$tba->id}}</a>
                                                            <?php }catch(\Exception $e){ ?>
                                                            {{$tba->id}}
                                                            <?php } ?>
                                                        </td>
                                                        <td>{{$tba->opponent1_details->name}}</td>
                                                        <td>{{$tba->opponent2_details->name}}</td>
                                                        <td>@unless(!$tba->game)
                                                                {{$tba->game->name}}
                                                            @endunless</td>
                                                        <td data-value="{{ strtotime($tba->start) }}">{{$tba->start ? date_convert($tba->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'H:i') : '-'}}</td>
                                                        <td data-value="{{ strtotime($tba->start) }}">{{$tba->start ? date_convert($tba->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd-m-Y') : '-'}}</td>
                                                        <td>
                                                            <a href="{{groute('stage_format', 'current', [
                                                            'tournamentId' => $tba->tournament->id,
                                                            'stageId' => $tba->stageRound->stageFormat->stage->id,
                                                            'sfId' => $tba->stageRound->stageFormat->id
                                                            ])}}">Stage {{ $tba->stageRound->stageFormat->id }}</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
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
                            <!-- panel body -->
                        </div>
                        <!-- panel -->
                    </div>
                    <!-- col lg 12 -->
                </div>
                <!-- row -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">API MATCHES</div>
                            <div class="panel-body">
                                <div class="input-group">
                                    <input type="text" placeholder="Search matches" class="input form-control" id="search-api-match">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn btn-primary" disabled=""> <i class="fa fa-search"></i> Search</button>
                                    </span>
                                </div>
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#tab-3">Live Matches <span
                                                    class="badge badge-danger"> {{isset($matches) ? count($matches) : 0}} </span></a>
                                    </li>
                                    <li><a data-toggle="tab" href="#tab-4"> Recent Matches <span class="badge badge-warning"> {{count($recentMatches)}} </span></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="tab-3" class="tab-pane active">
                                        <?php
                                        if (isset($matches) && count($matches) > 0) {
                                        $groupedLeagues = array();
                                        foreach ($matches as $data) {
                                            if (!isset($groupedLeagues[isset($data->league_id) ? $data->league_id : 0])) {
                                                $groupedLeagues[isset($data->league_id) ? $data->league_id : 0] = isset($data->league_name) ? $data->league_name : "";
                                            }
                                        }
                                        foreach ($groupedLeagues as $leagueId=>$league) {
                                        ?>
                                        <div style="margin-top:15px;margin-bottom:15px;font-weight:bold;font-size: 15px;color:red;">
                                            <?php echo $league; ?>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered footable" data-filter="#search-api-match" data-filter-minimum="3">
                                                <thead>
                                                <tr>
                                                    <th>Radiant Team</th>
                                                    <th>Dire Team</th>
                                                    <th>Game</th>
                                                    <th>Stage</th>
                                                    <th>Started</th>
                                                </tr>
                                                </thead>
                                                <tbody class="table-striped">
                                                <?php
                                                foreach ($matches as $match_id => $match) {
                                                $matchLeague = isset($match->league_id) ? $match->league_id : 0;
                                                if ($matchLeague != $leagueId)
                                                    continue;

                                                if (isset($match->series_type)) {
                                                    switch ($match->series_type) {
                                                        case 0:
                                                            $gameType = "1";
                                                            break;
                                                        case 1:
                                                            $gameType = "3";
                                                            break;
                                                        case 2:
                                                            $gameType = "5";
                                                            break;
                                                        case 3:
                                                            $gameType = "7";
                                                            break;
                                                        default:
                                                            $gameType = "1";
                                                    }
                                                } else {
                                                    $gameType = "1";
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <a href="{{groute('match', 'current', [$match_id])}}">
                                                            <?php echo isset($match->radiant) ? $match->radiant : "Radiant"; ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <?php echo isset($match->dire) ? $match->dire : "Dire"; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo isset($match->game_number) ? $match->game_number : 1; ?> of
                                                        <?php echo $gameType; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo isset($match->stage) ? $match->stage : ""; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo date("d-m-Y H:i", isset($match->started_at) ? $match->started_at : 0); ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                unset($matches->$match_id);
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php
                                        }
                                        } else {
                                        ?>
                                        <div style="margin:15px 0;">There are no live matches at the moment</div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                    <div id="tab-4" class="tab-pane">

                                        <?php
                                        if (isset($recentMatches) && count($recentMatches) > 0) {
                                        $groupedLeagues = array();
                                        foreach ($recentMatches as $data) {
                                            if (!isset($groupedLeagues[isset($data->league_id) ? $data->league_id : 0])) {
                                                $groupedLeagues[isset($data->league_id) ? $data->league_id : 0] = isset($data->league_name) ? $data->league_name : "";
                                            }
                                        }

                                        foreach ($groupedLeagues as $leagueId=>$league) {
                                        ?>
                                        <div style="margin-top:15px;margin-bottom:15px;font-weight:bold;font-size: 15px;color:red;">
                                            <?php echo $league; ?>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered footable" data-filter="#search-api-match" data-filter-minimum="3">
                                                <thead>
                                                <tr>
                                                    <th>Radiant Team</th>
                                                    <th>Dire Team</th>
                                                    <th>Game</th>
                                                    <th>Stage</th>
                                                    <th>Finished at</th>
                                                </tr>
                                                </thead>
                                                <tbody class="table-striped">
                                                <?php
                                                foreach ($recentMatches as $k => $match) {
                                                $matchLeague = isset($match->league_id) ? $match->league_id : 0;
                                                if ($matchLeague != $leagueId)
                                                    continue;

                                                if (isset($match->series_type)) {
                                                    switch ($match->series_type) {
                                                        case 0:
                                                            $gameType = "1";
                                                            break;
                                                        case 1:
                                                            $gameType = "3";
                                                            break;
                                                        case 2:
                                                            $gameType = "5";
                                                            break;
                                                        case 3:
                                                            $gameType = "7";
                                                            break;
                                                        default:
                                                            $gameType = "1";
                                                    }
                                                } else {
                                                    $gameType = "1";
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <a href="{{groute('match', 'current', [$match->match_id])}}">
                                                            <?php echo isset($match->radiant) ? $match->radiant : "Radiant"; ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <?php echo isset($match->dire) ? $match->dire : "Dire"; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo (isset($match->game_number) && $match->game_number) ? $match->game_number : 1; ?> of
                                                        <?php echo $gameType; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo is_object($match->stage) ? "" : $match->stage; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo date("d-m-Y H:i", isset($match->finished_at) ? $match->finished_at : 0); ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php
                                        }
                                        }
                                        ?>

                                    </div>
                                </div>
                            </div>
                            <!-- panel body -->
                        </div>
                        <!-- panel -->
                    </div>
                    <!-- col lg 12 -->
                </div>
                <!-- row -->
            </div>
            <!-- col md 10 -->
            <div class="col-lg-2">
                <div class="ibox">
                    <div class="ibox-content">
                        <a href="{{groute('event.create')}}"><button class="btn btn-primary dim">ADD EVENT</button></a>
                        <a href="{{groute('team.create')}}"><button class="btn btn-primary dim">ADD TEAM</button></a>
                        <a href="{{groute('player.create')}}"><button class="btn btn-primary dim">ADD PLAYER</button></a>
                        <a href="{{groute('maps.form')}}"><button class="btn btn-primary dim">ADD MAP</button></a>
                    </div>
                </div>
            </div>
            <!-- /.modal -->
@endsection