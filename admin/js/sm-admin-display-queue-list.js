jQuery(function() {
  jQuery(".process-all").on("click", function(e) {
    e.preventDefault();
    var data = smData.process_queue_all;
    var url = data.url;

    jQuery.ajax({
      type: "post",
      url: url,
      data: {
        action: "static-maker-process_queue_all"
      },
      success: function(res) {
        alert(data.messages.process_completed);
      }
    });
  });
});
