@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Create client</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ groute('/')  }}">Home</a>
                </li>
                <li>
                    <a href="{{ groute('clients.list') }}">Clients</a>
                </li>
                <li class="active">
                    <strong>Creation of client</strong>
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
                            <input type="text" class="form-control" name="name" id="name" value="">
                        </div>
                        <div class="form-group">
                            <label for="info">Email</label>
                            <input type="text" class="form-control" name="email" id="email" value="">
                        </div>
                        <div class="form-group">
                            <label for="info">Password</label>
                            <input type="password" class="form-control" name="password" id="password" value="">
                        </div>
                        <div class="form-group">
                            <label for="info">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" value="">
                        </div>
                        <h3>Required client options</h3>
                        <div class="panel" style="border: solid 1px #ddd">
                            <div class="panel-body table-bordered">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <p>Headline image minimal size and aspect ratio</p>
                                        <div class="form-group">
                                            <label for="name">Width</label>
                                            <input type="text" class="form-control" name="headline_width" id="headline_width" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="info">Height</label>
                                            <input type="text" class="form-control" name="headline_height" id="headline_height" value="">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <p>Thumb image minimal size and aspect ratio</p>
                                        <div class="form-group">
                                            <label for="name">Width</label>
                                            <input type="text" class="form-control" name="thumb_width" id="thumb_width" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="info">Height</label>
                                            <input type="text" class="form-control" name="thumb_height" id="thumb_height" value="">
                                        </div>
                                    </div>
                                </div>
                                <p class="text-danger">Aspect ration will be calculated automatically</p>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary dim">Create client account</button>
                    </form>
                </div>
            </div>
@endsection
