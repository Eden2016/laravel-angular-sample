<div id="page-wrapper" class="gray-bg">
    <div class="row border-bottom">
        <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>

            </div>
            <ul class="nav navbar-top-links navbar-left">
                <li class="dropdown{{ request()->currentGameSlug == '' ? ' active' : '' }}">
                    <a aria-expanded="false" role="button" href="{{groute(request()->route()->getName(),'all', request()->route()->parameters())}}">All</a>
                </li>
                @foreach(request()->allGames as $game)
                    <li class="dropdown{{ $game->slug == request()->currentGameSlug ? ' active' : '' }}">
                        <a aria-expanded="false" role="button" href="{{groute(request()->route()->getName(), $game->slug, request()->route()->parameters())}}">
                            {{$game->hashtag}}
                        </a>
                    </li>
                @endforeach
            </ul>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <a href="/logout">
                        <i class="fa fa-sign-out"></i> Log out
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    @yield('content')
</div>
</div>

<div class="footer">
    <div>
        <strong>Copyright</strong> {{ \App\Services\VersionServices::getCurrentVersion() }} &copy; 2014-2016
    </div>
</div>
</div>