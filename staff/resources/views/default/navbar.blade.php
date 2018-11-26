<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element text-center">
                            <span>
                            <img alt="image" class="img-circle" src="/img/profile_small.jpg">
                             </span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                                    <span class="block m-t-xs"> <strong class="font-bold">{{ Auth::user()->name }}</strong></span>
                                    <span class="text-xs block">{{ Auth::user()->timezone }}</span>
                                    <span class="text-xs block">{{ Auth::user()->roles[0]->display_name }}</span>
                                </span>
                    </a>
                    <ul class="dropdown-menu animated bounceIn m-t-xs">
                        <li><a href="/logout">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    Esports
                </div>
            </li>
            <li{{ isset($homeActiveMenu) ? ' class=active' : '' }}>
                <a href="{{ groute('/') }}"><i class="fa fa-home"></i> <span
                            class="nav-label">Dashboard</span></a>
            </li>
            <li{{ isset($eventListActiveMenu) ? ' class=active' : '' }}>
                <a href="{{ groute('events') }}"><i class="fa fa-clock-o"></i> <span
                            class="nav-label">Events</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ groute('event.create') }}">Add</a></li>
                    <li><a href="{{ groute('events') }}">Manage</a></li>
                </ul>
            </li>
            <li{{ isset($tournamentsActiveMenu) ? ' class=active' : '' }}>
                <a href="{{ groute('tournaments.list') }}"><i class="fa fa-code-fork"></i> <span
                            class="nav-label">Tournaments</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ groute('tournaments.list') }}">Manage</a></li>
                    <li><a href="{{ groute('tournaments.api_list') }}">API Tournaments</a></li>
                </ul>
            </li>
            <li{{ isset($matchesActiveMenu) ? ' class=active' : '' }}>
                <a href="{{ groute('matches.list') }}"><i class="fa fa-gamepad"></i> <span
                            class="nav-label">Matches</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li>
                        <a href="{{ groute('matches.general') }}">General</a>
                    </li>
                    <li><a href="{{ groute('matches.list') }}">API Matches</a></li>
                </ul>
            </li>
            <li{{ isset($teamsActiveMenu) ? ' class=active' : '' }}>
                <a href="{{ groute('teams.list') }}"><i class="fa fa-group"></i> <span
                            class="nav-label">Teams</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ groute('team.create') }}">Add</a></li>
                    <li><a href="{{ groute('teams.list') }}">Manage</a></li>
                    @if (request()->currentGameSlug == "dota2")
                        <li><a href="{{ groute('teams.api_list') }}">API Teams List</a></li>
                    @endif
                </ul>
            </li>
            <li{{ isset($playersActiveMenu) ? ' class=active' : '' }}>
                <a href="{{ groute('players.list') }}"><i class="fa fa-male"></i> <span
                            class="nav-label">Players</span><span
                            class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ groute('player.create') }}">Add</a></li>
                    <li><a href="{{ groute('players.list') }}">Manage</a></li>
                    <li><a href="{{ groute('players.api') }}">API Players</a></li>
                </ul>
            </li>
            @permission('add_roles')
            <li>
                <a href="{{ groute('players.list') }}"><i class="fa fa-male"></i> <span
                            class="nav-label">User Roles</span><span
                            class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ groute('user.roles') }}">Manage</a></li>
                    <li><a href="{{ groute('clients.list') }}">Client list</a></li>
                </ul>
            </li>
            @endpermission @permission('manage_perms')
            <li>
                <a href="{{ groute('players.list') }}"><i class="fa fa-male"></i> <span
                            class="nav-label">Permissions</span><span
                            class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ groute('user.permissions') }}">Manage</a></li>
                </ul>
            </li>
            @endpermission @permission('manage_perms')
            <li>
                <a href="{{ groute('accounts.api') }}"><i class="fa fa-male"></i> <span
                            class="nav-label">API Access</span><span
                            class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ groute('accounts.api') }}">Manage</a></li>
                    <li><a href="{{ groute('accounts.api.scopes') }}">Scopes</a></li>
                </ul>
            </li>
            @endpermission
            <li>
                <a href="{{ groute('streams')  }}"><i class="fa fa-youtube-play"></i> <span class="nav-label">Streams</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ groute('streams')  }}">Manage</a></li>
                </ul>
            </li>
            @permission('add_roles')
            <li>
                <a href="#no-top"><i class="fa fa-male"></i> <span class="nav-label">Cache Administration</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ groute('cache.player_stats') }}">Regenarate Player
                            Stats</a></li>
                </ul>
            </li>
            @endpermission
            @if(request()->currentGameSlug == 'csgo')
                <li{{ isset($playersActiveMenu) ? ' class=active' : '' }}>
                    <a href="#"><i class="fa fa-map-marker"></i> <span class="nav-label">Maps</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li><a href="{{ groute('maps.form') }}">Add</a></li>
                        <li><a href="{{ groute('maps') }}">Manage</a></li>
                    </ul>
                </li>
            @endif
            @if(request()->currentGameSlug == 'lol')
                @permission('lol_champions')
                <li{{ isset($playersActiveMenu) ? ' class=active' : '' }}>
                    <a href="#"><i class="fa fa-street-view"></i> <span class="nav-label">Champions</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li><a href="{{ groute('champion.form') }}">Add</a></li>
                        <li><a href="{{ groute('champions') }}">Manage</a></li>
                        <li><a href="{{ groute('champion.automatic') }}">Automatic refresh</a></li>
                    </ul>
                </li>
                @endpermission
            @endif
            @if(request()->currentGameSlug == 'overwatch')
                @permission('ow_heroes')
                <li{{ request()->is('overwatch/ow-heroes*') ? ' class=active' : '' }}>
                    <a href="#"><i class="fa fa-street-view"></i> <span class="nav-label">OW:Heroes</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li><a href="{{ groute('owheroes.create') }}">Add</a></li>
                        <li><a href="{{ groute('owheroes') }}">Manage</a></li>
                    </ul>
                </li>
                @endpermission
                <li{{ request()->is('overwatch/maps*') ? ' class=active' : '' }}>
                    <a href="#"><i class="fa fa-street-view"></i> <span class="nav-label">Maps</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li><a href="{{ groute('maps.form') }}">Add</a></li>
                        <li><a href="{{ groute('maps') }}">Manage</a></li>
                    </ul>
                </li>
            @endif

            {{-----------------Added menu items------------------}}

            <li>
                <a href="#"><i class="fa fa-pencil"></i> <span class="nav-label">Editorial</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ groute('blog.create') }}">Add post</a></li>
                    <li><a href="{{ groute('blog.manage') }}">Manage posts</a></li>
                </ul>
            </li>

            <li>
                <a href="#"><i class="fa fa-pencil"></i> <span class="nav-label">Data Science</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ groute('prediction') }}">Prediction</a></li>
                </ul>
            </li>

            {{---------------------------------------------------}}

            @role('owner')
            <li>
                <a href="{{groute('logs')}}"><i class="fa fa-male"></i> <span class="nav-label">Logs</span></a>
            </li>
            @endrole
            @permission('manage_toutou')
            <li{{ isset($playersActiveMenu) ? ' class=active' : '' }}>
                <a href="{{ groute('toutou.matches') }}"><i class="fa fa-gamepad"></i> <span class="nav-label">Toutou Matches</span><span class="fa arrow"></span></a>
            </li>
            @endpermission
        </ul>
    </div>
</nav>