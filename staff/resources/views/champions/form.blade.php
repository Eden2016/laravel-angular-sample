@extends('layouts.default')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Champion</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ groute('/')  }}">Home</a>
            </li>
            <li>
                <a href="{{ groute('champions') }}">Champions</a>
            </li>
            <li class="active">
                <strong>Create/Edit champion</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row ">
        <div class="col-md-8 ibox">
            <div class="col-md-12 ibox-content">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif @if (Session::has('success'))
                    <div class="alert alert-success">{!! session('success') !!}</div>
            @endif

            <!-- Create Post Form -->
                <form action="{{ groute('maps.form', 'current',['id' => $champion->id]) }}" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{old('name', $champion->name)}}">
                    </div>
                    <div class="form-group">
                        <label for="titile">Title</label>
                        <input type="text" class="form-control" name="title" id="title" value="{{old('title', $champion->title)}}">
                    </div>
                    <div class="form-group">
                        <label for="info">Info</label>
                        <textarea class="form-control" rows="5" id="info" name="info">{{ old('info', $champion->info) }}</textarea>
                    </div>

                    @if($champion->image)
                        <div class="form-group">
                            <label>Current:</label>
                            <img src="{{url('uploads/'.$champion->image)}}" alt="{{$champion->name}}" class="img-responsive">
                        </div>
                        @if($champion->image)
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remove_image" value="true"> Remove image
                                </label>
                            </div>
                        @endif
                    @endif
                    <div class="form-group">
                        <label for="event_logo">Champion image</label>
                        <input type="file" name="file" id="map_image">
                    </div>
                    <button type="submit" class="btn btn-primary dim">Save</button>
                </form>
            </div>
        </div>
@endsection
