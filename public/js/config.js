$(':root').css("--Height", $( window ).height() );
$( window ).resize(function() {
    $(':root').css("--Height", $( window ).height() );
    $('.modal-dialog').css('top',$( window ).height() + 'px');
});