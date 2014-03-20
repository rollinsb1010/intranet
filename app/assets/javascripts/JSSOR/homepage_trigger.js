
        jQuery(document).ready(function ($) {
           var options = {

                $AutoPlay: 1,                                        //[Optional] 1, True (Must be set to true to enable slideshow)   
                $PauseOnHover: 3,                                    //[Optional] 3, Pause for Desktop & Mobile devices 
                
               $ThumbnailNavigatorOptions: {
                     $Class: $JssorThumbnailNavigator$,              //[Required] Class to create thumbnail navigator instance
                     $ChanceToShow: 2,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
                     $ActionMode: 3,                                 //[Optional] 0 None, 1 act by click, 2 act by mouse hover, 3 both, default value is 1
                     $AutoCenter: 0,                                 //[Optional] Auto center thumbnail items in the thumbnail navigator container, 0 None, 1 Horizontal, 2 Vertical, 3 Both, default value is 3
                     $Lanes: 0,                                      //[Optional] Specify lanes to arrange thumbnails, default value is 1
                     $SpacingX: 0,                                   //[Optional] Horizontal space between each thumbnail in pixel, default value is 0
                     $SpacingY: 0,                                   //[Optional] Vertical space between each thumbnail in pixel, default value is 0
                     $DisplayPieces: 5,                              //[Optional] Number of pieces to display, default value is 1
                     $ParkingPosition: 0,                            //[Optional] The offset position to park thumbnail
                     $Orientation: 2,                                //[Optional] Orientation to arrange thumbnails, 1 horizental, 2 vertical, default value is 1
                     $DisableDrag: false                             //[Optional] Disable drag or not, default value is false
                }      
            };          
            var jssor_slider1 = new $JssorSlider$("slider1_container", options);
        });

