$(document).ready(function(){

  // Table Striping
  $("table").each(function(){
    $("tbody tr:even", this).addClass("odd");
    $("tbody tr:odd", this).addClass("even");
  });

  // Navigation
  var cUrlPath = jQuery.url.attr("path");
  var cCurrentfile = jQuery.url.attr("file");

  $("nav ul li a").each(function (){
    var cNav = $(this).attr("href");
    if(cNav.search(cUrlPath) > -1) {
      $(this).addClass("on");
    }
    if($("nav ul li a.on").size() > 1){
      $("nav ul li a").removeClass("on");
    }
  });

  $(".openMenu").click(function(){
    $(this).parent('.mobile').children('ul').toggleClass("show");
    return false;
  });

});