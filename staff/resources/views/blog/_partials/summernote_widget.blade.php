<div class="modal" tabindex="-1" role="dialog" id="add-widget">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add esportsconstruct widget</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="title">Widget:</label>
                    <select class="form-control" id="widget_type" name="widget_type">
                        <option value="dota2draft">Full dota2 draft embed</option>
                        <option value="dota2matchgamedraft">Dota2 draft embed for single match</option>
                        <option value="dota2scoreboard">Dota2 single game scoreboard</option>
                        <option value="matchdetails">Upcoming match details</option>
                        <option value="player">Player details</option>
                        <option value="team">Team details</option>
                        <option value="tournament">Tournament details</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="title">Widget value id:</label>
                    <input type="text" name="widget_options" id="widget_options" class="form-control" required/>
                </div>
            </div>
            <div class="modal-footer">
                <button id="insert-widget-button" data-dismiss="modal" type="button" class="btn btn-primary dim">Insert widget</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@section('styles')
    @parent
    <link href="/bower_components/summernote/dist/summernote.css" rel="stylesheet">
    <style>
        @media (min-width: 768px) {
            .modal-dialog {
                width: 900px;
                margin: 30px auto;
            }
        }
        .list-group-item.link-like {
            cursor: pointer;
        }

        .list-group-item.link-like:hover {
            background-color: #f0f0f0;
        }

        .note-editor dota2draft[data-value] {
            display: inline-block;
            border: solid 1px #c7c7c7;
            background: #f0f0f0;
            height: 20px;
            margin-bottom: 10px;
        }
        .note-editor dota2draft[data-value]:after {
            content: "Dota 2 draft widget: " attr(data-value) " ";
            font-weight: bold;
            font-size: 12px;
            padding: 5px 6px 3px;
        }
        .note-editor dota2matchgamedraft[data-value] {
            display: inline-block;
            border: solid 1px #c7c7c7;
            background: #f0f0f0;
            height: 20px;
            margin-bottom: 10px;
        }
        .note-editor dota2matchgamedraft[data-value]:after {
            content: "Dota2 draft embed for single match: " attr(data-value) " ";
            font-weight: bold;
            font-size: 12px;
            padding: 5px 6px 3px;
        }
        .note-editor dota2scoreboard[data-value] {
            display: inline-block;
            border: solid 1px #c7c7c7;
            background: #f0f0f0;
            height: 20px;
            margin-bottom: 10px;
        }
        .note-editor dota2scoreboard[data-value]:after {
            content: "Dota2 single game scoreboard: " attr(data-value) " ";
            font-weight: bold;
            font-size: 12px;
            padding: 5px 6px 3px;
        }
        .note-editor matchdetails[data-value] {
            display: inline-block;
            border: solid 1px #c7c7c7;
            background: #f0f0f0;
            height: 20px;
            margin-bottom: 10px;
        }
        .note-editor matchdetails[data-value]:after {
            content: "Upcoming match details: " attr(data-value) " ";
            font-weight: bold;
            font-size: 12px;
            padding: 5px 6px 3px;
        }
        .note-editor player[data-value] {
            display: inline-block;
            border: solid 1px #c7c7c7;
            background: #f0f0f0;
            height: 20px;
            margin-bottom: 10px;
        }
        .note-editor player[data-value]:after {
            content: "Player details: " attr(data-value) " ";
            font-weight: bold;
            font-size: 12px;
            padding: 5px 6px 3px;
        }
        .note-editor team[data-value] {
            display: inline-block;
            border: solid 1px #c7c7c7;
            background: #f0f0f0;
            height: 20px;
            margin-bottom: 10px;
        }
        .note-editor team[data-value]:after {
            content: "Team details: " attr(data-value) " ";
            font-weight: bold;
            font-size: 12px;
            padding: 5px 6px 3px;
        }
        .note-editor tournament[data-value] {
            display: inline-block;
            border: solid 1px #c7c7c7;
            background: #f0f0f0;
            height: 20px;
            margin-bottom: 10px;
        }
        .note-editor tournament[data-value]:after {
            content: "Tournament details: " attr(data-value) " ";
            font-weight: bold;
            font-size: 12px;
            padding: 5px 6px 3px;
        }
    </style>
@endsection

@section('scripts')
    @parent
        <script src="/bower_components/summernote/dist/summernote.js"></script>
        <script src="/js/blog.summernote.js"></script>
    @endsection