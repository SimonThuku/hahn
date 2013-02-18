  (function($) {
       $().ready(function() {
        
        var HAHNAIR = {
          
          


        hideGallery : function() {
         
          $('.GAMES').bind('click', function() {
           
               
                $('.games-list').css({'opacity':'1'});;
              

              

           });
        },
        init : function() {

          HAHNAIR.hideGallery();
          
        }
      };

      $(function() {
        HAHNAIR.init();
      });

 });
})(jQuery);

      
