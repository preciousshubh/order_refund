jQuery(document).ready(function ($) {
  console.log(" Loaded main.js");
  $("tbody").on("click", ".add_more_button", function () {
    var $currentRow = $(this).closest(".row");
    var newRow = $("tbody").find(".row").last().clone();
    newRow.find("input").val("");
    $currentRow.after(newRow);
    $(".remove_button").show();
    $("tbody").find(".row").first().find(".remove_button").hide();

    updateRowIndex();
  });

  $("tbody").on("click", ".remove_button", function () {
    $(this).closest(".row").remove();
    updateRowIndex();
  });

  function row_hide() {
    if ($(".row").length === 1) {
      $(".remove_button").hide();
    }
  }

  row_hide();

  updateRowIndex();

  function updateRowIndex() {
    $("tbody")
      .find(".row")
      .each(function (index) {
        $(this)
          .find(".id")
          .text(index + 1);
      });
  }
});
