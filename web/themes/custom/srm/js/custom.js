
jQuery(document).ready(function($){

  $(".commerce-order-item-add-to-cart-form").insertAfter(".test1");
        // $(.nav-div .nav-list-item a).addClass('nav-list-item-link');
    
    $("li.nav-list-item a.fas fa-search").append("<a href='/srmsteel/web/search' class='fas fa-search' data-drupal-link-system-path='search'></a>"); 

//  $(".my-test-class").prepend("<div class='bottom-line'></div>");
  $(".my-test-class").prepend("<div class='row text-center'>"); 
   $(".my-test-class").prepend("<div class='container'>");
  $(".my-test-class").prepend("<section id='testimonials' class='py-5'>"); 

$(".owl-item").prepend("</section></div></div>");

$('ul.links').each(function(){
var list=$(this),
    select=$(document.createElement('select')).insertBefore($(this).hide()).change(function(){
  window.open($(this).val(),'_self')
});
$('>li a', this).each(function(){
  var option=$(document.createElement('option'))
   .appendTo(select)
   .val(this.href)
   .html($(this).html());
  if($(this).attr('class') === 'selected'){
    option.attr('selected','selected');
  }
});
list.remove();
});
});
           
