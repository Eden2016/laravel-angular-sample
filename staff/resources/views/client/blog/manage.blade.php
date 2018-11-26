@extends('client.layouts.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Manage</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('client.home')}}">Home</a>
                </li>
                <li class="active">
                    <strong>Manage</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">

            <div class="col-sm-12">
                <a href="{{route('client.blog.create')}}" class="btn btn-primary btn-lg pull-right">Add post</a>
            </div>

            <!---------- Unpablished posts ------------>

            <div class="col-sm-12">
                <h3>Posts</h3>
                <div class="col-sm-12">
                    <div class="row">
                        <ul class="nav nav-tabs" id="toggles">
                            <li class="active"><a data-toggle="tab" data-value="0" href="#un-all-posts">All</a></li>
                            <form action="#" id="filters-form" class="form-inline">
                                <div class="col-lg-8 pull-right">
                                    <div class="input-group col-xs-2">
                                       <input type="checkbox" name="withunpublished" id="withunpublished" checked>&nbsp;&nbsp;<label for="withunpublished"> With unpublished</label>
                                    </div>
                                    <div class="input-group col-xs-9">
                                        <input type="text" name="searchtext" class="form-control" placeholder="Search for...">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="submit">Search</button>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </ul>
                    </div>
                </div>
                <table class="footable table table-striped table-bordered dt-responsive nowrap ec-datatable footable-loaded" id="posts-table">
                    <thead>
                    <tr>
                        <th width="20">ID</th>
                        <th>Title</th>
                        <th width="100">Published on</th>
                        <th width="100">Created on</th>
                        <th width="80">Posting type</th>
                        <th width="80">Translations</th>
                        <th width="80">Highlight</th>
                        <th width="80">Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>

            @endsection

            @section('styles')
                @parent
                <link href="/bower_components/datatables/media/css/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
                <link href="/css/datatables.css" rel="stylesheet" type="text/css" />
                <style type="text/css">
                    #posts-table_length {
                        display: none;
                    }
                    #posts-table .unpublished {
                        background-color: #feffd9;
                    }
                    #posts-table .unpublished td{
                        background-color: #feffd9;
                    }
                    .game-icon {
                        display: inline-block;
                        width: 16px;
                        height: 16px;
                    }
                    .game-icon.dota2 {
                        background-image: url('data:image/gif;base64,R0lGODlhEAAQAMQAAJYjCa0pCrNaS9+3sPz6+blOOblxZZYsGN2uprY/J8x4abBmW8aYkOrGwKInCee/ucmFeuvRzu3c2diimsOOhfjx8Nufld6YjMFoWdiOgfLg3ppCM4gfCrQsC7gsB////yH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxMzggNzkuMTU5ODI0LCAyMDE2LzA5LzE0LTAxOjA5OjAxICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNyAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo1QTA3NTgwM0Y5NDExMUU2OUI0MkJEMERDRTNGRTJFQyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo1QTA3NTgwNEY5NDExMUU2OUI0MkJEMERDRTNGRTJFQyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjVBMDc1ODAxRjk0MTExRTY5QjQyQkQwRENFM0ZFMkVDIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjVBMDc1ODAyRjk0MTExRTY5QjQyQkQwRENFM0ZFMkVDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Af/+/fz7+vn49/b19PPy8fDv7u3s6+rp6Ofm5eTj4uHg397d3Nva2djX1tXU09LR0M/OzczLysnIx8bFxMPCwcC/vr28u7q5uLe2tbSzsrGwr66trKuqqainpqWko6KhoJ+enZybmpmYl5aVlJOSkZCPjo2Mi4qJiIeGhYSDgoGAf359fHt6eXh3dnV0c3JxcG9ubWxramloZ2ZlZGNiYWBfXl1cW1pZWFdWVVRTUlFQT05NTEtKSUhHRkVEQ0JBQD8+PTw7Ojk4NzY1NDMyMTAvLi0sKyopKCcmJSQjIiEgHx4dHBsaGRgXFhUUExIREA8ODQwLCgkIBwYFBAMCAQAAIfkEAAAAAAAsAAAAABAAEAAABZ5ggwxPpF2K0jzZZSFR4c10XSdFp+88j3erRCDQCSQgrGInV6hoMEPi5KMhdgTD5qchHGImWYXDERAQPhUxeeywQNgOQeXzeSTgGDXbTK8Y9gIAAA6DABhzdAgJAAUQjBEHggB8fQAHEwADH5CSBmcfEpYLHJqbBxwAHBB0FJYCHBwLBqewsBsLpwevtby9HAcIFAwMFBQCG7q3BgLDIQA7');
                    }
                    .game-icon.csgo {
                        background-image: url('data:image/gif;base64,R0lGODlhEAAQAKIAAM6PLmktD71zJJRIFz4cCKpdHYE2EiMOBCH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxMzggNzkuMTU5ODI0LCAyMDE2LzA5LzE0LTAxOjA5OjAxICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNyAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMjM3ODA4M0Y5RkMxMUU2OUFBMEFBQzdGRjVEQUM5MSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMjM3ODA4NEY5RkMxMUU2OUFBMEFBQzdGRjVEQUM5MSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEyMzc4MDgxRjlGQzExRTY5QUEwQUFDN0ZGNURBQzkxIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEyMzc4MDgyRjlGQzExRTY5QUEwQUFDN0ZGNURBQzkxIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Af/+/fz7+vn49/b19PPy8fDv7u3s6+rp6Ofm5eTj4uHg397d3Nva2djX1tXU09LR0M/OzczLysnIx8bFxMPCwcC/vr28u7q5uLe2tbSzsrGwr66trKuqqainpqWko6KhoJ+enZybmpmYl5aVlJOSkZCPjo2Mi4qJiIeGhYSDgoGAf359fHt6eXh3dnV0c3JxcG9ubWxramloZ2ZlZGNiYWBfXl1cW1pZWFdWVVRTUlFQT05NTEtKSUhHRkVEQ0JBQD8+PTw7Ojk4NzY1NDMyMTAvLi0sKyopKCcmJSQjIiEgHx4dHBsaGRgXFhUUExIREA8ODQwLCgkIBwYFBAMCAQAAIfkEAAAAAAAsAAAAABAAEAAAA1sIutxeJzglWDhkigoEORgRjEFRbNsAruLWocK1ggMsnBD72WZhzAQUrldQsYjEgXJmSBaVAxmI8IQqDdEpCAs1eL0Xwg/zLQcMnzP6UAaPyGeQmjT6iN53eiABADs=');
                    }
                    .game-icon.lol {
                        background-image: url('data:image/gif;base64,R0lGODlhEAAQALMAAAIgjI9hNJ90RCQlMgIRS7GETda0e29PMlgyHAEac8qradK1chkhTRkkXr+aXNGweCH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxMzggNzkuMTU5ODI0LCAyMDE2LzA5LzE0LTAxOjA5OjAxICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNyAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozQ0FDMEMyNkY5NDIxMUU2OUI0MkJEMERDRTNGRTJFQyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozQ0FDMEMyN0Y5NDIxMUU2OUI0MkJEMERDRTNGRTJFQyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjNDQUMwQzI0Rjk0MjExRTY5QjQyQkQwRENFM0ZFMkVDIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjNDQUMwQzI1Rjk0MjExRTY5QjQyQkQwRENFM0ZFMkVDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Af/+/fz7+vn49/b19PPy8fDv7u3s6+rp6Ofm5eTj4uHg397d3Nva2djX1tXU09LR0M/OzczLysnIx8bFxMPCwcC/vr28u7q5uLe2tbSzsrGwr66trKuqqainpqWko6KhoJ+enZybmpmYl5aVlJOSkZCPjo2Mi4qJiIeGhYSDgoGAf359fHt6eXh3dnV0c3JxcG9ubWxramloZ2ZlZGNiYWBfXl1cW1pZWFdWVVRTUlFQT05NTEtKSUhHRkVEQ0JBQD8+PTw7Ojk4NzY1NDMyMTAvLi0sKyopKCcmJSQjIiEgHx4dHBsaGRgXFhUUExIREA8ODQwLCgkIBwYFBAMCAQAAIfkEAAAAAAAsAAAAABAAEAAABHawmGmefXS+Iob/ICh0BBKcCEEkLMGMTFIGxcrer9DIiKAQABZg2ICxTL/gkDhqAEoCB3DJ1D170gQVUAwwrr7pMsEIWKGLqbaUc0J/KsIgcGjzaAjE4eQYFAVfUAInIwVSOQxydAd7JwEpOSGSHwISFBcXGQURADs=');
                    }
                    .game-icon.overwatch {
                        background-image: url('data:image/gif;base64,R0lGODlhEAAQALMAAFVWVX+Af8nJyZycm/mpIe/v7+rq6vz8/MLDwsbGxvT19P7//s/Pz8DAwERFRP///yH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxMzggNzkuMTU5ODI0LCAyMDE2LzA5LzE0LTAxOjA5OjAxICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNyAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozQ0FDMEMxRUY5NDIxMUU2OUI0MkJEMERDRTNGRTJFQyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozQ0FDMEMxRkY5NDIxMUU2OUI0MkJEMERDRTNGRTJFQyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjVBMDc1ODA5Rjk0MTExRTY5QjQyQkQwRENFM0ZFMkVDIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjVBMDc1ODBBRjk0MTExRTY5QjQyQkQwRENFM0ZFMkVDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Af/+/fz7+vn49/b19PPy8fDv7u3s6+rp6Ofm5eTj4uHg397d3Nva2djX1tXU09LR0M/OzczLysnIx8bFxMPCwcC/vr28u7q5uLe2tbSzsrGwr66trKuqqainpqWko6KhoJ+enZybmpmYl5aVlJOSkZCPjo2Mi4qJiIeGhYSDgoGAf359fHt6eXh3dnV0c3JxcG9ubWxramloZ2ZlZGNiYWBfXl1cW1pZWFdWVVRTUlFQT05NTEtKSUhHRkVEQ0JBQD8+PTw7Ojk4NzY1NDMyMTAvLi0sKyopKCcmJSQjIiEgHx4dHBsaGRgXFhUUExIREA8ODQwLCgkIBwYFBAMCAQAAIfkEAAAAAAAsAAAAABAAEAAABIbwyUnrWxgrRgw7GXaMD2GexDceylEMJ3cOLKsEzqDse4HrigLDATAUCoLEcQg4GgY5gzQQkBpwjakj+wwAEFLoQNtgNBxeQIIh9oihOSiggWWYiY6cHQfI2/d5A38MODkIDAICCH0AAQMDhQFJSQkJCIV5eQGVlQieDaCQjg2epaCnqKmgEQA7');
                    }
                    .game-icon.sc2 {
                        background-image: url('data:image/gif;base64,R0lGODlhEAAQALMAAGduc/Ly8WWKo83Nza+1tqXa5YOEhFN7mJybnEVRXoGlwh0iMODk5TM8Sf7+/v///yH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxMzggNzkuMTU5ODI0LCAyMDE2LzA5LzE0LTAxOjA5OjAxICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNyAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozQ0FDMEMyMkY5NDIxMUU2OUI0MkJEMERDRTNGRTJFQyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozQ0FDMEMyM0Y5NDIxMUU2OUI0MkJEMERDRTNGRTJFQyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjNDQUMwQzIwRjk0MjExRTY5QjQyQkQwRENFM0ZFMkVDIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjNDQUMwQzIxRjk0MjExRTY5QjQyQkQwRENFM0ZFMkVDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Af/+/fz7+vn49/b19PPy8fDv7u3s6+rp6Ofm5eTj4uHg397d3Nva2djX1tXU09LR0M/OzczLysnIx8bFxMPCwcC/vr28u7q5uLe2tbSzsrGwr66trKuqqainpqWko6KhoJ+enZybmpmYl5aVlJOSkZCPjo2Mi4qJiIeGhYSDgoGAf359fHt6eXh3dnV0c3JxcG9ubWxramloZ2ZlZGNiYWBfXl1cW1pZWFdWVVRTUlFQT05NTEtKSUhHRkVEQ0JBQD8+PTw7Ojk4NzY1NDMyMTAvLi0sKyopKCcmJSQjIiEgHx4dHBsaGRgXFhUUExIREA8ODQwLCgkIBwYFBAMCAQAAIfkEAAAAAAAsAAAAABAAEAAABJTwPWaqYdQANJx0gKIghEaQSuJJAXAYBeAyRcJ4DoIYTQL0i0RjQxoYTqeB8EBoni4JzWLRWAA0jYPMkdskpouDAmALrBwGZjWcajAkcJABUU0oDrY4htEQIKgKAgtvEgMyZA0lWQgbAwEPVwIGAngHDVkHeAQTA01BP2R4AB1wD2gYGgcxj6WFAhQABUetpUZepHARADs=');
                    }
                </style>
            @endsection

            @section('scripts')
                @parent
                <script src="/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
                <script src="/bower_components/datatables/media/js/dataTables.bootstrap.min.js"></script>

                <script type="text/javascript">
                  $(document).ready(function() {
                    var only_my = 0;
                    var oTable = $('#posts-table').DataTable({
                      processing: true,
                      serverSide: true,
                      stateSave: true,
                      searching: false,
                      iDisplayLength: 25,
                      fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                        if(aData['is_draft'] == 1) {
                          $(nRow).addClass('unpublished');
                        }
                        return nRow;
                      },
                      ajax: {
                        url: '{!! route('client.blog.posts.dataquery') !!}',
                        method: 'GET',
                        data: function (d) {
                          if($('input[name=searchtext]').val() != '') {
                            d.searchtext = $('input[name=searchtext]').val();
                          }
                          if($('input[name=withunpublished]')[0].checked) {
                            d.withunpublished = 1;
                          } else {
                            d.withunpublished = 0;
                          }
                        }
                      },
                      columns: [
                        { data: 'id', name: 'id' },
                        { data: 'title', name: 'title', render: function(data, aRows, allRows) {
                          return allRows.games + ' ' + data;
                        }},
                        { data: 'published_at', name: 'published_at' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'type', name: 'type' },
                        { data: 'translations', name: 'translations', sortable: false, searchable: false},
                        { data: 'is_highlight', name: 'is_highlight', render: function(data) {
                          if(data == 1) return "Yes";
                          return "No";
                        }},
                        { data: 'actions', name: 'actions', searchable: false, sortable: false}
                      ]
                    });

                    $('#filters-form').on('submit', function(e) {
                      oTable.draw();
                      e.preventDefault();
                    });

                  });
                </script>
@endsection