<style>
	.mapHolder {
		position: absolute;
		width: 512px;
		height: 512px;
	}

	.mapHolder > img {
		position: absolute;
		z-index: 1;
		width: 512px;
		height: 512px;
	}

	.racks_radiant {
		position: absolute;
		z-index: 2;
		width: 32px;
		height: 32px;
		background: url('/images/map/racks_radiant.png') no-repeat;
	}
	.racks_dire {
		position: absolute;
		z-index: 2;
		width: 32px;
		height: 32px;
		background: url('/images/map/racks_dire.png') no-repeat;
	}

	.tower_radiant {
		position: absolute;
		z-index: 2;
		width: 32px;
		height: 32px;
		background: url('/images/map/tower_radiant.png') no-repeat;
	}
	.tower_dire {
		position: absolute;
		z-index: 2;
		width: 32px;
		height: 32px;
		background: url('/images/map/tower_dire.png') no-repeat;
	}

	.player_icon {
		position: absolute;
		z-index: 2;
		border-radius: 30px;
		height: 32px;
		padding: 3px;
		position: absolute;
		width: 32px;
	}

	.player_icon.focused {
	    box-shadow: 0 5px 5px #000;
	    border-color: #fff!important;
	    transform: scale(1.1,1.1);
	    z-index: 10;
	}

	.player_icon.radiant {
		background: rgba(0,255,0,.2);
		border: 2px solid #0f0;
	}

	.player_icon.dire {
	    background: rgba(255,0,0,.2);
	    border: 2px solid red;
	}

	.player_icon:hover {
		border-color: #fff;
		z-index: 10;
	}

	.player_icon > img {
		cursor: crosshair;
	}
</style>

<div class="mapHolder">
	<img src="/images/map/dota_map.jpg" alt="" />

	<!-- Start dire barracks rendering -->
	@for ($i = 0; $i < count($dire_barracks_positions); $i++)
        @if ($barracks_status_dire[$i])
            <div class="racks_dire" style="top: {{ $dire_barracks_positions[$i][0] }}%;left: {{ $dire_barracks_positions[$i][1] }}%;"></div>
        @endif
    @endfor
    <!-- End dire barracks rendering -->

    <!-- Start dire tower rendering -->
    @for ($i = 0; $i < count($dire_tower_positions); $i++)
    	@if ($tower_status_dire[$i])
    		<div class="tower_dire" style="top: {{ $dire_tower_positions[$i][0] }}%;left: {{ $dire_tower_positions[$i][1] }}%;"></div>
    	@endif
    @endfor
    <!-- End dire tower rendering -->

    <!-- Start radiant barracks rendering -->
    @for ($i = 0; $i < count($radiant_barracks_positions); $i++)
        @if ($barracks_status_radiant[$i])
            <div class="racks_radiant" style="top: {{ $radiant_barracks_positions[$i][0] }}%;left: {{ $radiant_barracks_positions[$i][1] }}%;"></div>
        @endif
    @endfor
    <!-- End radiant barracks rendering -->

    <!-- Start radiant tower rendering -->
    @for ($i = 0; $i < count($radiant_tower_positions); $i++)
    	@if ($tower_status_radiant[$i])
    		<div class="tower_radiant" style="top: {{ $radiant_tower_positions[$i][0] }}%;left: {{ $radiant_tower_positions[$i][1] }}%;"></div>
    	@endif
    @endfor
    <!-- End radiant tower rendering -->

    <!-- Start player rendering -->
    @if (!$froms3)
		@if (isset($players) && count($players) > 0)
		@foreach ($players as $player)
			@if (in_array($player->slot, \App\Slot::RADIANT_ARRAY))
				<?php $side = "radiant"; ?>
			@else
				<?php $side = "dire"; ?>
			@endif
			<div class="player_icon {{ $side }}" style="top: {{ 100 - round(100 * ((8100 + round($player->pos_y)) / 16200)) }}%;left: {{ round(100 * ((7500 + round($player->pos_x)) / 15000)) }}%;">
				<img src="/images/map/heroes/{{ $player->hero_id }}.png" title="{{ $player->kills }} | {{ $heroMap[$player->hero_id] }} | {{ $player->name }}" alt="" />
			</div>
		@endforeach
		@endif
	@else
		@if (isset($dire_players) && count($dire_players) > 0)
		@foreach ($dire_players as $player)
			<div class="player_icon dire" style="top: calc({{ 100 - round(100 * ((8100 + (int)$player->position_y) / 16200)) }}% - 16px);left: calc({{ round(100 * ((7500 + (int)$player->position_x) / 15000)) }}% - 16px);">
				<img src="/images/map/heroes/{{ $player->hero_id }}.png" title="{{ $player->kills }} | {{ $heroMap[$player->hero_id] }} | {{ $player->name }}" alt="" />
			</div>
		@endforeach
		@endif

		@if (isset($radiant_players) && count($radiant_players) > 0)
		@foreach ($radiant_players as $player)
			<div class="player_icon radiant" style="top: calc({{ 100 - round(100 * ((8100 + (int)$player->position_y) / 16200)) }}% - 16px);left: calc({{ round(100 * ((7500 + (int)$player->position_x) / 15000)) }}% - 16px);">
				<img src="/images/map/heroes/{{ $player->hero_id }}.png" title="{{ $player->kills }} | {{ $heroMap[$player->hero_id] }} | {{ $player->name }}" alt="" />
			</div>
		@endforeach
		@endif
	@endif
	<!-- End player rendering -->
</div>