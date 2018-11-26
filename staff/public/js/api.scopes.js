$(function(){
    $('#api-scope-modal').on('show.bs.modal', function(e){
        var scope_id = $(e.relatedTarget).data('id');
        var modal = $('#api-scope-modal');
        if(scope_id){
            $.get('/api/api_scopes', {
                "id": scope_id
            }, function(response){
                modal.find('[data-do="saveApiScope"]').data('id', scope_id);
                modal.find('#scope_id').val(response.id);
                modal.find('#description').val(response.description);
            });
        }else{
            modal.find('[data-do="saveApiScope"]').data('id', '');
            modal.find('#scope_id').val('');
            modal.find('#description').val('');
        }
    });

    $('#delete-api-scope-modal').on('show.bs.modal', function(e){
        var scope_id = $(e.relatedTarget).data('id');
        $(this).find('[data-do="deleteApiScope"]').data('id', scope_id);
    });

    $('body').on('click', '[data-do="deleteApiScope"]',function(){
        var scope_id = $(this).data('id');
        $.get('/api/api_scopes/delete', {
            "id": scope_id
        }, function(){
            window.location.href  = window.location.href;
        });
    });

    $('body').on('click', '[data-do="saveApiScope"]', function(){

        var modal = $('#api-scope-modal');
        $.post('/api/api_scopes', {
            "id": modal.find('#scope_id').val(),
            "description": modal.find('#description').val()
        }, function(){
            window.location.href  = window.location.href;
        });
    });
});