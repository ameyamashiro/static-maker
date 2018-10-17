jQuery(function() {
  (function($) {
    var replaceVars = function(html, vars) {
      Object.keys(vars).forEach(function(key) {
        var reg = new RegExp(key, "g");
        html = html.replace(reg, vars[key]);
      });
      return html;
    };

    var count = 0;
    var rsyncVars = smData.rsync;

    rsyncVars.forEach(function(rsyncOpts) {
      var html = $("#rsync-template").html();
      html = html.replace(/{{COUNT}}/g, count);
      html = replaceVars(html, rsyncOpts);
      var $html = $(html);
      count++;

      var $source = $('[data-sm-source="rsync"]').last();
      if ($source.length) {
        $html.insertAfter($source);
      } else {
        $(".rsync-list").prepend($html);
      }
    });

    $(".add-target").on("click", function(e) {
      var target = $(this).data("sm-target");
      var $source = $('[data-sm-source="' + target + '"]').last();
      var html = $("#rsync-template").html();
      html = html.replace(/{{COUNT}}/g, count);
      html = replaceVars(
        html,
        Object.keys(rsyncVars[0]).reduce(function(a, c) {
          a[c] = "";
          return a;
        }, {})
      );
      var $html = $(html);
      count++;

      if ($source.length) {
        $html.insertAfter($source);
      } else {
        $(".rsync-list").prepend($html);
      }
    });

    $(".remove-target").on("click", function() {
      var target = $(this).data("sm-target");
      count--;
      $('[data-sm-source="' + target + '"]')
        .last()
        .remove();
    });
  })(jQuery);
});
