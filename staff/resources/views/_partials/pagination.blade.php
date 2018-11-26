@if($pages>1)
    <div >
        <ul class="pagination">
            @for($i=1; $i<=$pages; $i++)
                <li @if($i==request()->get('page', 1)) class="active" @endif>
                    <a href="{{request()->url().'?'.http_build_query(
                            array_add(request()->except('page'), 'page', $i)
                        )}}" >{{$i}}</a>
                </li>
            @endfor
        </ul>
    </div>
@endif