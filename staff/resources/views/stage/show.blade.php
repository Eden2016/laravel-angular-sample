@extends('layouts.default')

@section('content')


    <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>All Tournaments</h2>
                @include('_partials.breadcrumbs', ['items' => $breadcrumbs])
            </div>
            <div class="col-lg-2">

            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-sm-8">
                <div class="ibox">
                    <div class="ibox-title"><h3>{{ $stage->name }}</h3></div>
                    <div class="ibox-content">
                        <a href="{{ groute('stage.delete', 'current', ['tournamentId' => $stage->tournament_id, 'stageId' => $stage->id])  }}"
                           id="delete">
                            <button type="button" class="btn btn-danger dim">Delete</button>
                        </a>


                        <a href="{{ groute('stage.edit',  'current', ['tournamentId' => $stage->tournament_id, 'stageId' => $stage->id])}}">
                            <button type="button" class="btn btn-primary dim">Edit Stage</button>
                        </a>

                        <hr />
                        <div class="row">
                            <div class="col-md-6"><h4>Stage Formats</h4></div>
                            <div class="col-md-6 text-right"><a
                                        href="{{ groute('stage_format.create', 'current', ['tournamentId' => $stage->tournament_id, 'stageId' => $stage->id])  }}">
                                    <button type="button" class="btn btn-primary dim">Create Stage Format</button>
                                </a></div>
                        </div>



                        <hr />

                        @if (isset($stageFormats) && $stageFormats !== null)
                            <ul style="list-style: none;">
                            @foreach ($stageFormats as $format)
                                    <li>
                                        <a href="{{groute('stages.formats.view', 'current', ['tournamentId' => $stage->tournament_id, 'stageId' => $stage->id, 'sfId' => $format->id])}}">{{ $format->name }}</a>
                                    </li>
                            @endforeach
                            </ul>
                        @endif
                        </div>
                    </div>
                </div>
                @endsection

                @section('scripts')
                    @parent
                    <script type="text/javascript">
                      $(document).ready(function () {
                        $("#delete").on('click', function () {
                          var conf = confirm('Are you sure you want to delete this event?');

                          if (!conf) {
                            return false;
                          }
                        });
                      });
                    </script>
@endsection