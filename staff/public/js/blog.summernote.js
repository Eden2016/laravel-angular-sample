$(document).ready(function() {
  var GameWidgets = function (context) {
    var ui = $.summernote.ui;
    // create button
    var button = ui.button({
      contents: '<i class="fa fa-gamepad"/> Insert game widget',
      tooltip: 'Insert esports widget',
      click: function () {
        $('#widget_options').val('0');
        $('#add-widget').modal('show');
        $('#insert-widget-button').unbind( 'click' );
        $('#insert-widget-button').click(function(event) {
          var tagname = $('#widget_type').val();
          var $widget = $('<'+tagname+' />', { 'data-value':  $('#widget_options').val()});
          var $container = $('<div></div>').append($widget[0]);
          context.invoke('editor.insertNode', $container[0]);
        });
      }
    });
    return button.render();   // return button as jquery object
  };

  $('#post, #t_post, #te_post').summernote({
    height: 250,
    toolbar: [
      ['style', ['style']],
      ['font', ['bold', 'italic', 'underline', 'clear']],
      ['color', ['color']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['height', ['height']],
      ['table', ['table']],
      ['insert', ['link', 'picture', 'hr']],
      ['view', ['fullscreen', 'codeview']],
      ['help', ['help']],
      ['mybutton', ['gameWidget']]
    ],
    buttons: {
      gameWidget: GameWidgets
    }
  });
});
