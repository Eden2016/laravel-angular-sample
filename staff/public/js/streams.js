$(function(){
  var isoLangsS2 = [];
  $.ajax({
    url: 'http://static.esportsconstruct.com/langs.json',
    dataType: 'jsonp',
    jsonp: false,
    jsonpCallback: 'callback',
    context: this,
    complete: function(data) {
      for(key in data.responseJSON) {
        isoLangsS2.push({ id: key, text: data.responseJSON[key].name});
      }
    }
  });
  $('#stream-form-modal').on('show.bs.modal', function(e){
    var stream_id = $(e.relatedTarget).data('id');
    var modal = $(this);
    modal.find('#lang').select2({
      data: isoLangsS2,
      placeholder: 'Select language'
    });
    if(stream_id){
      $.get('/api/streams', {
        id: stream_id
      }, function(response){
        modal.find('[data-do="saveStream"]').data('id', stream_id);
        modal.find('#title').val(response.title);
        modal.find('#link').val(response.link);
        modal.find('#description').val(response.description);
        modal.find('#lang').select2('val', response.lang);
        modal.find('#game_id').val(response.game_id);
        modal.find('#embed_code').val(response.embed_code);
        modal.find('#platform').val(response.platform);
      });
    }else{
      modal.find('[data-do="saveStream"]').data('id', '');
      modal.find('#title').val('');
      modal.find('#link').val('');
      modal.find('#description').val('');
      modal.find('#lang').select2('val', '');
      modal.find('#game_id').val(null);
      modal.find('#embed_code').val('');
      modal.find('#platform').val(null);
    }
  });

  $('#delete-stream-modal').on('show.bs.modal', function(e){
    var stream_id = $(e.relatedTarget).data('id');
    var modal = $(this);
    modal.find('[data-do="deleteStream"]').data('id', stream_id);
  });

  $('body').on('click', '[data-do="saveStream"]', function(){
    var stream_id = $(this).data('id');
    var modal = $('#stream-form-modal');
    $.post('/api/streams/save', {
      "id": stream_id,
      "title": modal.find('#title').val(),
      "link": modal.find('#link').val(),
      "description": modal.find('#description').val(),
      "lang": modal.find('#lang').select2('val'),
      "game_id": modal.find('#game_id').val(),
      "embed_code": modal.find('#embed_code').val(),
      "platform": modal.find('#platform').val()
    }, function(){
      window.location.href = window.location.href;
    })
  });

  $('body').on('click', '[data-do="deleteStream"]', function(){
    var stream_id = $(this).data('id');
    $.get('/api/streams/delete', {
      id: stream_id
    }, function(){
      window.location.href = window.location.href;
    });
  });
});