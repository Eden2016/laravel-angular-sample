$(function(){
    $('#api-access-modal').on('show.bs.modal', function(e){
        var access_id = $(e.relatedTarget).data('id');
        var modal = $('#api-access-modal');
        if(access_id){
            $.get('/api/api_access', {
                "id": access_id
            }, function(response){
                modal.find('.modal-title').text('Edit API access');
                modal.find('[data-do="saveApiAccess"]').data('id', access_id);
                modal.find('#client_id').text(response.id);
                modal.find('#client_secret').text(response.secret);
                modal.find('#name').val(response.name);
                modal.find('#scopes').val(response.scopes).trigger('change');
            });
        }else{
            modal.find('.modal-title').text('Add API access');
            modal.find('[data-do="saveApiAccess"]').data('id', '');
            modal.find('#client_id').text('');
            modal.find('#client_secret').text('');
            modal.find('#name').val('');
            modal.find('#scopes').val(null).trigger('change');
        }
    });

    $('#delete-api-access-modal').on('show.bs.modal', function(e){
        var access_id = $(e.relatedTarget).data('id');
        $(this).find('[data-do="deleteApiAccess"]').data('id', access_id);
    });

    $('body').on('click', '[data-do="deleteApiAccess"]',function(){
        var access_id = $(this).data('id');
        $.get('/api/api_access/delete', {
            "id": access_id
        }, function(){
            window.location.href  = window.location.href;
        });
    });

    $('body').on('click', '[data-do="saveApiAccess"]', function(){
        var access_id = $(this).data('id');
        var modal = $('#api-access-modal');
        $.post('/api/api_access', {
            "id": access_id,
            "name": modal.find('#name').val(),
            "scopes": modal.find('#scopes').val()
        }, function(){
           window.location.href  = window.location.href;
        });
    });

});