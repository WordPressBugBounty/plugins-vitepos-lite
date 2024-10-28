(function ($) {
  $(document).ready(function () {
     console.info("dci sdk.js loaded");

    $(document).on(
      "click",
      ".apbd-dci-button-allow, .apbd-dci-button-skip, .apbd-dci-button-disallow",
      function () {
        let nonce = $(this)
            .closest(".apbd-dci-notice-data")
            .find("[name='nonce']")
            .val(),
          dci_name = $(this)
            .closest(".apbd-dci-notice-data")
            .find("[name='dci_name']")
            .val(),
          date_name = $(this)
            .closest(".apbd-dci-notice-data")
            .find("[name='dci_date_name']")
            .val(),
          allow_name = $(this)
            .closest(".apbd-dci-notice-data")
            .find("[name='dci_allow_name']")
            .val();

        $.ajax({
          url: ajaxurl,
          type: "POST",
          data: {
            action: "vtp_dci_sdk_insights",
            button_val: this.value,
            nonce: nonce,
            dci_name: dci_name,
            date_name: date_name,
            allow_name: allow_name,
          },
          success: function (response) {
            if (response.status == "success") {
              location.reload();
            } else {
              alert(response.message);
            }
          },
        });
      }
    );

    $(document).on("click", ".apbd-dci-global-notice .notice-dismiss", function () {
      let nonce = $(this)
          .closest(".apbd-dci-notice-data")
          .find("[name='nonce']")
          .val(),
        dci_name = $(this)
          .closest(".apbd-dci-notice-data")
          .find("[name='dci_name']")
          .val();

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
          action: "vtp_dci_sdk_dismiss_notice",
          nonce: nonce,
          dci_name: dci_name,
        },
      });
    });

    /**
     * If find .apbd-dci-feedback-wrapper then add class on the same id of the feedback
     */
    if ($(".apbd-dci-feedback-wrapper").length) {
      console.log("I am here");
      $(".apbd-dci-feedback-wrapper").each(function () {
        let feedback_id = $(this).attr("id");
        $("#deactivate-" + feedback_id).addClass(
          "apbd-dci-feedback-deactivate-plugin-btn"
        );
      });
    }

    $(document).on(
      "click",
      ".apbd-dci-feedback-deactivate-plugin-btn",
      function (e) {
        e.preventDefault();
        let id = $(this).attr("id");

        $("#" + id.replace("deactivate-", "")).show();
      }
    );

    $(document).on("click", ".apbd-dci-feedback-submit-btn", function () {
      let $noticeData = $(this).closest(".apbd-dci-notice-data");

      let nonce = $noticeData.find("[name='nonce']").val(),
        dci_name = $noticeData.find("[name='dci_name']").val(),
        product_id = $noticeData.find("[name='product_id']").val(),
        public_key = $noticeData.find("[name='public_key']").val(),
        version = $noticeData.find("[name='version']").val(),
        api_endpoint = $noticeData.find("[name='api_endpoint']").val(),
        deactivate_url = $(this).data("deactivate-url");

      let feedback_data = {};

      $noticeData.find("textarea, input[type='checkbox']").each(function () {
        const $input = $(this);
        const name = $input.attr("name");
        let value = $input.val();

        if ($input.attr("type") === "checkbox") {
          value = $input.is(":checked") ? "yes" : "no";
        }

        if (value !== "no" && value !== "") {
          feedback_data[name] = value;
        }
      });

      if (Object.keys(feedback_data).length === 0) {
        feedback_data["no_feedback"] = "yes";
      }

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
          action: "vtp_dci_sdk_insights_deactivate_feedback",
          nonce: nonce,
          product_id: product_id,
          public_key: public_key,
          version: version,
          api_endpoint: api_endpoint,
          feedback: JSON.stringify(feedback_data),
        },
        success: function (response) {
          window.location.href = deactivate_url;
        },
      });
    });
  });
})(jQuery);
