$("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
  });

  $('#companies').children().click(function(){ $("#CompaniesLink").html($(this).text())  });


$( "#CommentUser" ).on( "click", function() {
	$('#CommentUser').modal('show')
  console.log(555 );
});