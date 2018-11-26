@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Overwatch Hero</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ groute('/')  }}">Home</a>
                </li>
                <li>
                    <a href="{{ groute('owheroes') }}">Heroes</a>
                </li>
                <li class="active">
                    <strong>{{ isset($hero) ? 'Edit' : 'Create' }} Overwatch Hero</strong>
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
                    @endif
                    @if (Session::has('success'))
                        <div class="alert alert-success">{!! session('success') !!}</div>
                    @endif

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                        <input type="hidden" name="active" value="1"/>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{isset($hero) ? $hero->name : '' }}">
                        </div>
                        <div class="form-group">
                            <label for="info">Info</label>
                            <textarea rows="5" class="form-control" name="info" id="info">{{isset($hero) ? $hero->info : '' }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select class="form-control" name="role" id="role">
                                @foreach($roles as $key => $role)
                                    <option value="{{$key}}" {{ (isset($hero) && $hero->role == $key) ? 'selected' : '' }}>{{$role}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(isset($hero) and $hero->image)
                            <div class="form-group">
                                <label>Current:</label>
                                <img src="{{ $hero->portraitUrl()}}" alt="{{$hero->name}}" class="img-responsive">
                            </div>
                            <div class="form-group">
                                Replace image:
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="hero_image">Hero image (recommend 180x310 from <a href="https://playoverwatch.com/en-us/heroes/">https://playoverwatch.com/en-us/heroes/</a>)</label>
                            <input type="file" name="image" id="hero_image">
                        </div>
                        <button type="submit" class="btn btn-primary dim">Save</button>
                    </form>
                </div>
            </div>
@endsection
