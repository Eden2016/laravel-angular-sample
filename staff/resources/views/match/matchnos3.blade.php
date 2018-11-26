@extends('layouts.default')

@section('content')

<?php
$status = "Finished at ".date("d-m-Y H:i", strtotime($match->start_time) + $match->duration);
?>

<div class="match-wrapp">
    <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Api Matches</h2>
                <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('matches.list')}}">Matches</a>
                    </li>
                    <li class="active">
                        <strong>Matche</strong>
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
                    <div class="ibox-content">
                        <div class="col-md-6">
                	    
                	
                        <h2><?php echo $match->radiant_name; ?> VS. <?php echo $match->dire_name; ?> <?php if (isset($duration)) { ?>At minute <?php echo round($duration/60); ?><?php } ?></h2>
                    </div>
                    <div class="col-md-6 text-right">
                    
                        <?php if (!isset($duration)) { ?>
                            <a href="{{groute('home.dump', 'current', [$match->match_id])}}"><button type="button" class="btn btn-primary dim">Export to CSV</button></a>
                        <?php } else { ?>
                            <a href="{{groute('home.dump.duration', 'current', [$match->match_id, $duration])}}"><button type="button" class="btn btn-primary dim">Export to CSV</button></a>
                        <?php } ?>
                        <a href="{{groute('/')}}"><button type="button" class="btn btn-default dim">Go Home</button></a>
                    </div>
                    
                    <div class="col-md-6">
                        Match ID: {{ $match->match_id }}
                        | League ID: {{ $match->leagueid }}
                    </div>
                    <div class="col-md-6 text-right"></div>
                    
                    <div class="clearfix"></div>
                    <hr />

                    <div class="row">
                        <div class="col-md-4"><?php echo $match->radiant_name; ?> VS. <?php echo $match->dire_name; ?></div>
                        <div class="col-md-2"><?php echo $match->league_name; ?></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-4" style="text-align: right;">Status: <?php echo $status; ?></div>
                    </div>

                    <div>
                        <h3><?php echo $match->radiant_name; ?></h3>
                    </div>

                    <hr />
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Picks</h4>
                        </div>
                        <div class="col-md-6">
                            <h4>Bans</h4>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:10px;">
                    <?php if (isset($match->radiant_picks->pick) && count($match->radiant_picks->pick) > 0) { 
                            foreach ($match->radiant_picks->pick as $pick) {
                        ?>
                        <div class="col-md-1"><?php echo $heroMap[$pick->hero_id]; ?></div>
                    <?php 	}
                        }
                    ?>
                        <div class="col-md-1"></div>

                    <?php if (isset($match->radiant_bans->ban) && count($match->radiant_bans->ban) > 0) { 
                            foreach ($match->radiant_bans->ban as $ban) {
                        ?>
                        <div class="col-md-1"><?php echo $heroMap[$ban->hero_id]; ?></div>
                    <?php 	}
                        }
                    ?>
                    </div>
                    <hr />

                    <div class="ibox">
                       <div class="ibox-content">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Hero</th>
                                    <th>Level</th>
                                    <th>Kills</th>
                                    <th>Deaths</th>
                                    <th>Assists</th>
                                    <th>K/D/A</th>
                                    <th>Total Gold</th>
                                    <th>GPM</th>
                                    <th>Denies</th>
                                    <th>XP PM</th>
                                    <th>Item 1</th>
                                    <th>Item 2</th>
                                    <th>Item 3</th>
                                    <th>Item 4</th>
                                    <th>Item 5</th>
                                    <th>Item 6</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($slots) > 0) { ?>
                                <?php foreach ($slots as $player) { 
                                    $account = $player->player;

                                    if (!in_array($player->player_slot, \App\Slot::RADIANT_ARRAY))
                                        continue;

                                    if ($player->hero_id > 0) {
                                        if (isset($heroMap[$player->hero_id]))
                                            $hero = $heroMap[$player->hero_id];
                                        else
                                            $hero = $player->hero_id;
                                    } else {
                                        $hero = "Not chosen";
                                    }
                                    ?>
                                    <tr>
                                        <td><a href="{{ groute('players.view', 'current', [$player->account_id]) }}">{{ $account->personaname != "" ? $account->personaname : "Unnamed" }}</a></td>
                                        <td><?php echo $hero; ?></td>
                                        <td><?php echo $player->level; ?></td>
                                        <td><?php echo $player->kills; ?></td>
                                        <td><?php echo $player->deaths; ?></td>
                                        <td><?php echo $player->assists; ?></td>
                                        <td><?php echo $player->kills; ?>/<?php echo $player->deaths; ?>/<?php echo $player->assists; ?></td>
                                        <td><?php echo $player->gold; ?></td>
                                        <td><?php echo $player->gold_per_min; ?></td>
                                        <td><?php echo $player->denies; ?></td>
                                        <td><?php echo $player->xp_per_min; ?></td>
                                        <td><?php echo $itemMap[$player->item_0]; ?></td>
                                        <td><?php echo $itemMap[$player->item_1]; ?></td>
                                        <td><?php echo $itemMap[$player->item_2]; ?></td>
                                        <td><?php echo $itemMap[$player->item_3]; ?></td>
                                        <td><?php echo $itemMap[$player->item_4]; ?></td>
                                        <td><?php echo $itemMap[$player->item_5]; ?></td>
                                    </tr>
                                <?php 
                                    } 
                                }
                                ?>
                            </tbody>
                        </table>
                        </div>
                    </div>

                    <div style="margin-top:30px;">
                        <h3><?php echo $match->dire_name; ?></h3>
                    </div>

                    <hr />
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Picks</h4>
                        </div>
                        <div class="col-md-6">
                            <h4>Bans</h4>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:10px;">
                    <?php if (isset($match->dire_picks->pick) && count($match->dire_picks->pick) > 0) { 
                            foreach ($match->dire_picks->pick as $pick) {
                        ?>
                        <div class="col-md-1"><?php echo $heroMap[$pick->hero_id]; ?></div>
                    <?php 	}
                        }
                    ?>
                        <div class="col-md-1"></div>

                    <?php if (isset($match->dire_bans->ban) && count($match->dire_bans->ban) > 0) { 
                            foreach ($match->dire_bans->ban as $ban) {
                        ?>
                        <div class="col-md-1"><?php echo $heroMap[$ban->hero_id]; ?></div>
                    <?php 	}
                        }
                    ?>
                    </div>
                    <hr />

                    <div class="ibox">
                       <div class="ibox-content">                   
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Hero</th>
                                    <th>Level</th>
                                    <th>Kills</th>
                                    <th>Deaths</th>
                                    <th>Assists</th>
                                    <th>K/D/A</th>
                                    <th>Total Gold</th>
                                    <th>GPM</th>
                                    <th>Denies</th>
                                    <th>XP PM</th>
                                    <th>Item 1</th>
                                    <th>Item 2</th>
                                    <th>Item 3</th>
                                    <th>Item 4</th>
                                    <th>Item 5</th>
                                    <th>Item 6</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($slots) > 0) { ?>
                                <?php foreach ($slots as $player) { 
                                    $account = $player->player;

                                    if (!in_array($player->player_slot, \App\Slot::DIRE_ARRAY))
                                        continue;

                                    if ($player->hero_id > 0) {
                                        if (isset($heroMap[$player->hero_id]))
                                            $hero = $heroMap[$player->hero_id];
                                        else
                                            $hero = $player->hero_id;
                                    } else {
                                        $hero = "Not chosen";
                                    }
                                    ?>
                                    <tr>
                                        <td><a href="{{ groute('players.view', 'current', [$player->account_id]) }}">{{ $account->personaname != "" ? $account->personaname : "Unnamed" }}</a></td>
                                        <td>{{ $hero }}</td>
                                        <td>{{ $player->level }}</td>
                                        <td>{{ $player->kills }}</td>
                                        <td>{{ $player->deaths }}</td>
                                        <td>{{ $player->assists }}</td>
                                        <td>{{ $player->kills }}/<?php echo $player->deaths; ?>/<?php echo $player->assists; ?></td>
                                        <td>{{ $player->gold }}</td>
                                        <td>{{ $player->gold_per_min }}</td>
                                        <td>{{ $player->denies }}</td>
                                        <td>{{ $player->xp_per_min }}</td>
                                        <td>{{ $itemMap[$player->item_0] }}</td>
                                        <td>{{ $itemMap[$player->item_1] }}</td>
                                        <td>{{ $itemMap[$player->item_2] }}</td>
                                        <td>{{ $itemMap[$player->item_3] }}</td>
                                        <td>{{ $itemMap[$player->item_4] }}</td>
                                        <td>{{ $itemMap[$player->item_5] }}</td>
                                    </tr>
                                <?php 
                                    } 
                                }
                                ?>
                            </tbody>
                        </table>
                        </div>
                    </div>    
                    </div>
                </div>
                	

                
                </div>

@endsection


