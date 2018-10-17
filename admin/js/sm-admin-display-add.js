jQuery(function() {
  jQuery(".add-pages-by-post-type").on("submit", function(e) {
    e.preventDefault();
    var postType = jQuery("[name=post-type-select]").val();
    var data = smData.add_pages_by_post_type;
    var url = data.url;

    if (postType.length) {
      jQuery.post(
        url,
        {
          action: "static-maker-add_pages_by_post_type",
          post_type: postType
        },
        function(res, status) {
          var $postType = jQuery(".post-type-message");

          if (status === "success") {
            location.reload();
          } else {
            $postType.empty().html(data.messages.failed_to_register);

            var $error = jQuery(".error");
            $error.empty();
            $error.html(res);
          }
        }
      );
    }
  });

  jQuery(".add-page-by-url").on("submit", function(e) {
    e.preventDefault();
    var $msg = jQuery(".url-based-message");
    var data = smData.add_page_by_url;
    var actionUrl = data.url;
    var urlValue = jQuery("[name=url]").val();

    if (urlValue.length) {
      jQuery.ajax({
        type: "post",
        url: actionUrl,
        data: {
          action: "static-maker-add_page_by_url",
          url: urlValue
        },
        success: function(res) {
          location.reload();
        },
        error: function(jqXHR, textStatus, errorThrown) {
          $msg
            .empty()
            .html(
              data.messages.failed_to_register + "<br>" + jqXHR.responseText
            );
        }
      });
    }
  });
});
