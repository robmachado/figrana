// NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
// IT'S ALL JUST JUNK FOR OUR DOCS!
// ++++++++++++++++++++++++++++++++++++++++++

!function ($) {

  $(function(){
    $('[data-toggle="confirmation"]').confirmation(
        {
            onConfirm: function() {
                var id = $(this)[0].getAttribute('data-id');
                encerra(id);
                
            },
            onCancel: function() {
                
            }
        }
    );
    $('[data-toggle="confirmation-singleton"]').confirmation({singleton:true});
    $('[data-toggle="confirmation-popout"]').confirmation({popout: true});

  })



}(window.jQuery)

function encerra(id) {
    $.post( "encerraop.php", { id: id })
        .done(function( data ) {
        //alert( "Return: " + data );
        location.reload();
    });
}