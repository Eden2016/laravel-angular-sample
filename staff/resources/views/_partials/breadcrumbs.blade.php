@if($items)
    <ol class="breadcrumb">
        @foreach($items as $b)
            @if($b['active'])
                <li class="active">
                    <strong>{{ $b['name'] }}</strong>
                </li>
            @else
            <li>
                <a href="{{$b['url']}}">{{$b['name']}}</a>
            </li>
            @endif
        @endforeach

    </ol>
@endif