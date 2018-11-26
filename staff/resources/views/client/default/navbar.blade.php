<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="logo-element">
                    Esports
                </div>
            </li>
            <li>
                <a href="#"><i class="fa fa-pencil"></i> <span class="nav-label">Editorial</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ route('client.blog.create') }}">Add post</a></li>
                    <li><a href="{{ route('client.blog.manage') }}">Manage posts</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>