jQuery(function() {
  jQuery(".trigger-add-individual").on("click", function(e) {
    e.preventDefault();
    var data = smData.enqueue_single_by_id;
    var url = data.url;
    var $target = jQuery(e.target);
    var postId = $target.data("post-id");

    var sendData = {
      action: "static-maker-enqueue_single_by_id"
    };

    if (postId) {
      sendData.post_id = postId;
    } else {
      sendData.id = $target.data("id");
    }

    jQuery
      .post(url, sendData, function(res, status) {
        if (status === "success") {
          alert(data.messages.process_completed);
        } else {
          alert(data.messages.failed_to_register);
        }
      })
      .fail(function() {
        alert(data.messages.failed_to_register);
      });
  });

  jQuery(".trigger-remove-individual").on("click", function(e) {
    e.preventDefault();
    var data = smData.enqueue_single_by_id;
    var url = data.url;
    var $target = jQuery(e.target);
    var postId = $target.data("post-id");

    var sendData = {
      action: "static-maker-enqueue_single_by_id"
    };

    if (postId) {
      sendData.post_id = postId;
    } else {
      sendData.id = $target.data("id");
    }

    sendData["action-type"] = "remove";

    jQuery
      .post(url, sendData, function(res, status) {
        if (status === "success") {
          alert(data.messages.process_completed);
        } else {
          alert(data.messages.failed_to_register);
        }
      })
      .fail(function() {
        alert(data.messages.failed_to_register);
      });
  });

  jQuery(".trigger-remove-from-list").on("click", function(e) {
    e.preventDefault();
    var data = smData.remove_page_from_list;
    var url = data.url;
    var $target = jQuery(e.target);

    var sendData = {
      action: "static-maker-remove_page_from_list"
    };

    sendData.id = $target.data("id");

    jQuery
      .post(url, sendData, function() {
        location.reload();
      })
      .fail(function() {
        alert(data.messages.failed_to_register);
      });
  });

  jQuery(".trigger-change-page-status").on("click", function(e) {
    e.preventDefault();
    var data = smData.change_page_status;
    var url = data.url;
    var $target = jQuery(e.target);

    var sendData = {
      action: "static-maker-change-page-status"
    };

    sendData["action-type"] = $target.data("action");
    sendData.id = $target.data("id");

    jQuery
      .post(url, sendData, function() {
        location.reload();
      })
      .fail(function() {
        alert(data.messages.failed_to_register);
      });
  });

  jQuery(".enqueue-all-pages").on("click", function(e) {
    e.preventDefault();

    var data = smData.enqueue_all_pages;
    var url = data.url;

    jQuery.ajax({
      type: "post",
      url: url,
      data: {
        action: "static-maker-enqueue_all_pages"
      },
      success: function(res, status) {
        if (status === "success") {
          alert(data.messages.process_completed);
        } else {
          alert(data.messages.failed_to_register);
        }
      },
      error: function() {
        alert(data.messages.failed_to_register);
      }
    });
  });
});
