$(function () {
  $(".gallery img").on("click", function () {
    $("#modal-img").attr("src", $(this).attr("src"));
    $("#modal").fadeIn();
  });

  $("#modal").on("click", function () {
    $(this).fadeOut();
  });
});
