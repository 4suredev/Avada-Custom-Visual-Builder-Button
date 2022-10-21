jQuery(document).ready(function($){
    $("#button-shortcode-dialog").dialog({
        autoOpen: false,
        show: {
            effect: "fade",
            duration: 200
        },
        hide: {
            effect: "fade",
            duration: 200
        },
        close: function(event, ui){
            $("#page-mask").css({'opacity': 0, 'pointer-events': 'none'});
        },
        minWidth: 480
    });
    if(typeof acf != 'undefined'){
        acf.addAction('load', function(){
            $(document).on("click", ".generate-button-shortcode", function(e){
                e.preventDefault();
                $("#button-shortcode-dialog").dialog("open"); 
                $("#page-mask").css({"opacity":1, "pointer-events": "auto"});
            });
        });
    }else{
        $(document).on("click", ".generate-button-shortcode", function(e){
            e.preventDefault();
            $("#button-shortcode-dialog").dialog("open"); 
            $("#page-mask").css({"opacity":1, "pointer-events": "auto"});
        });
    }
   
    $('.generate-button-shortcode').click(function(e){
        e.preventDefault();
        $("#button-shortcode-dialog").dialog("open"); 
        $("#page-mask").css({"opacity":1, "pointer-events": "auto"});
    });
    $('.copy-code-btn').click(function(e){
        var id = $(this).attr('id');
        e.preventDefault();
        var shortcode = $('#'+id+'-shortcode').val();
        navigator.clipboard.writeText(shortcode);
        $(this).tooltip({ items: $(this), content: "Copied to clipboard"});
        $(this).tooltip("open");
    });
    $('.copy-code-btn').mouseout(function(){
        $(this).tooltip({ items: $(this), content: "Copied to clipboard"});
        $(this).tooltip("disable");
    });
    $('#page-mask').click(function(){
        $('#button-shortcode-dialog').dialog("close");
    });
})