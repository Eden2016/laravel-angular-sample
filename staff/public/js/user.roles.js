$(function(){
   $('#link-role-modal').on('show.bs.modal', function(e){
        var user_id = $(e.relatedTarget).data('user');
       $(this).find('[name="user_id"]').val(user_id);
   });

    $('#unlink-role-modal').on('show.bs.modal', function(e){
        $(this).find('span#user_names').text($(e.relatedTarget).data('names'));
        $.get('/api/user/roles', {
            "user": $(e.relatedTarget).data('user')
        }, function(response){
            $('#user_roles_table tbody tr').remove();
            $(response).each(function(idx, role){
                var tr = $(document.createElement('tr'));
                $(document.createElement('td')).text(role.id).appendTo(tr);
                $(document.createElement('td')).text(role.display_name).appendTo(tr);
                $(document.createElement('td'))
                    .append(
                        $(document.createElement('a')).attr({
                            "href": "javascript:;",
                            "data-role": role.pivot.role_id,
                            "data-user": role.pivot.user_id,
                            "data-do": "removeRole"
                        }).text('Unlink')
                    )
                    .appendTo(tr);
                tr.appendTo($('#user_roles_table tbody'));
            });
        });
    });

    $('[data-do="addRoleToUser"]').on('click', function(){
        var modal = $('#link-role-modal');
        $.post('/api/user/roles/add', {
           "user": modal.find('[name="user_id"]').val(),
            "role": modal.find('[name="role"]').val()
        });
    });

    $('body').on('click', '[data-do="removeRole"]',function(){
        var tr = $(this).parent().parent();
        $.post('/api/user/roles/remove', {
            "user": $(this).data('user'),
            "role": $(this).data('role')
        })
            .fail(function(){
                alert('Error deleting role');
            })
            .done(function(){
                tr.remove();
            });
    });

    $('#edit-user-modal').on('show.bs.modal', function(e){
        var modal = $('#edit-user-modal');
        $.get('/user/fetch', {
            "id": $(e.relatedTarget).data('id')
        }, function(response){
            modal.find('[name="user-name"]').val(response.name);
            modal.find('[name="user-mail"]').val(response.email);
            modal.find('[name="user-id"]').val(response.id);
        });
    });


    // Roles related functions
    $('#edit-role-modal').on('show.bs.modal', function(e){
        var modal = $('#edit-role-modal');
        $.get('/api/roles/single', {
            "role": $(e.relatedTarget).data('role')
        }, function(response){
            modal.find('[name="name"]').val(response.name);
            modal.find('[name="display_name"]').val(response.display_name);
            modal.find('[name="description"]').val(response.description);
            modal.find('[name="role_id"]').val(response.id);
        });
    });

    $('body').on('click', '[data-do="saveRole"]', function(){
        var modal = $('#edit-role-modal');

        var multiple = [];
        modal.find('[name="edit-permissions"] :selected').each(function(i, selected) {
          multiple[i] = $(selected).val();
        });

        $.post('/api/roles/single', {
            "id": modal.find('[name="role_id"]').val(),
            "name": modal.find('[name="name"]').val(),
            "display_name": modal.find('[name="display_name"]').val(),
            "description": modal.find('[name="description"]').val(),
            "permissions": multiple
        });
    });

    $('body').on('click', '[data-do="addRole"]', function(){
        var modal = $('#add-role-modal');

        var multiple = [];
        modal.find('[name="permissions"] :selected').each(function(i, selected) {
          multiple[i] = $(selected).val();
        });

        $.post('/api/roles/single', {
            "name": modal.find('[name="name"]').val(),
            "display_name": modal.find('[name="display_name"]').val(),
            "description": modal.find('[name="description"]').val(),
            "permissions": multiple
        }, function(){
            window.location.href = window.location.href;
        });
    });

    $('body').on('click', '[data-do="addUser"]', function(){
        var modal = $('#add-user-modal');
        $.post('/user/create', {
            "_token": $("#csrf-token").val(),
            "name": modal.find('[name="user-name"]').val(),
            "email": modal.find('[name="user-mail"]').val(),
            "password": modal.find('[name="user-password"]').val(),
            "timezone": modal.find('[name="timezone"]').val(),
            "role": modal.find('[name="user-role"]').val()
        }, function(){
            window.location.href = window.location.href;
        });
    });

    $('body').on('click', '[data-do="editUser"]', function(){
        var modal = $('#edit-user-modal');
        $.post('/user/edit', {
            "_token": $("#csrf-token").val(),
            "id": modal.find('[name="user-id"]').val(),
            "name": modal.find('[name="user-name"]').val(),
            "email": modal.find('[name="user-mail"]').val(),
            "password": modal.find('[name="user-password"]').val(),
            "timezone": modal.find('[name="timezone"]').val()
        }, function(data){
            window.location.href = window.location.href;
        });
    });

    $('body').on('click', '[data-do="deleteUser"]', function(){
        var $this = $(this);
        if (confirm('Are you sure you want to delete this user?')) {
            $.post('/user/delete', {
                "_token": $("#csrf-token").val(),
                "id": $this.data("id")
            }, function(){
                window.location.href = window.location.href;
            });
        }
    });


    $('#delete-role-modal').on('show.bs.modal', function(e){
        $(this).find('[data-do="deleteRole"]').data('id', $(e.relatedTarget).data('role'));
    });

    $('body').on('click', '[data-do="deleteRole"]', function(e){
        var role_id = $(this).data('id');
        $.post('/api/roles/delete', {
            "id": role_id
        }, function(response){
            if(response.success==true){
                $('[data-target="#delete-role-modal"][data-role="'+role_id+'"]').parent().parent().remove();
            }
        });
    });

});