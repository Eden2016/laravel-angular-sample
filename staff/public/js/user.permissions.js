$(function(){
    $('#edit-permission-modal').on('show.bs.modal', function(e){
        var permission_id = $(e.relatedTarget).data('id');
        var modal = $('#edit-permission-modal');
        $.get('/api/permissions/single',{
            "id": permission_id
        }, function(response){
            modal.find('[name="name"]').val(response.name);
            modal.find('[name="display_name"]').val(response.display_name);
            modal.find('[name="description"]').val(response.description);
            modal.find('[data-do="savePermission"]').data('id', permission_id);
        });
    });

    $('body').on('click', '[data-do="savePermission"]', function(){
        var modal = $('#edit-permission-modal');
        var permission_id = $(this).data('id');
        $.post('/api/permissions/single', {
            "id": permission_id,
            "name": modal.find('[name="name"]').val(),
            "display_name": modal.find('[name="display_name"]').val(),
            "description": modal.find('[name="description"]').val()
        }, function(){
            window.location.href = window.location.href;
        });
    });

    $('#delete-permission-modal').on('show.bs.modal', function(e){
        $(this).find('[data-do="deletePermission"]').data('id', $(e.relatedTarget).data('id'));
    });

    $('body').on('click', '[data-do="deletePermission"]', function(){
       var permission_id = $(this).data('id');
        $.post('/api/permissions/delete', {
            "id": permission_id
        }, function(response){
           if(response.success==true){
               $('[data-target="#delete-permission-modal"][data-id="'+permission_id+'"]').parent().parent().remove();
           }
        });
    });

    $('body').on('click', '[data-do="addPermission"]', function(){
       var modal = $('#add-permission-modal');
        $.post('/api/permissions/single', {
           "name": modal.find('[name="name"]').val(),
            "display_name": modal.find('[name="display_name"]').val(),
            "description": modal.find('[name="description"]').val()
        }, function(){
            window.location.href = window.location.href;
        });
    });
});