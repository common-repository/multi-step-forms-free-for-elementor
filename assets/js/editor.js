
function check_page_breaks() {
  //field_type_selects = jQuery("div.elementor-repeater-fields select[data-setting='field_type'] option[value='page_break']:selected").parent().parent().parent().parent().parent().parent().parent();
  var field_type_selects = jQuery("div.elementor-repeater-fields:has(select[data-setting='field_type'])");
  console.log("page break selects", field_type_selects);

  console.log("<<<<<<<<<<check_page_breaks!!!!!!>>>>>>>>>>>>");
  for(var x = 0; x < field_type_selects.length; x++) {
    var select_box_wrapper = jQuery(field_type_selects[x]);
    var selected = jQuery(select_box_wrapper).find("select").find(":selected").val();
    console.log("select", select_box_wrapper, selected, select_box_wrapper.hasClass("page_break_item"));


    if (select_box_wrapper.hasClass("page_break_item") && selected !== "page_break") {
      select_box_wrapper.removeClass("page_break_item");
      //original_title = select_box_wrapper.find('.elementor-repeater-row-item-title .original_title_multistep')[0].innerHTML;
      //select_box_wrapper.find('.elementor-repeater-row-item-title')[0].innerHTML = original_title;
    }
    if (selected == "page_break") {
      //page_break_counter++;
      if (!select_box_wrapper.hasClass("page_break_item")){
        select_box_wrapper.addClass("page_break_item");
        //original_title = select_box_wrapper.find('.elementor-repeater-row-item-title')[0].innerHTML;
        //select_box_wrapper.find('.elementor-repeater-row-item-title')[0].innerHTML = "Page Break #"+page_break_counter+'<span class="original_title_multistep">'+original_title+'</span>';
      }
    }







    //console.log("select box >", select_box, select_box.value);
  }

  var page_breaks = jQuery("div.elementor-repeater-fields.page_break_item");

  var page_break_counter = 0;
  for(var x = 0; x < page_breaks.length; x++) {
    var select_box_wrapper = jQuery(page_breaks[x]);

    console.log("page_breaks select", select_box_wrapper);

    page_break_counter++;
    if (select_box_wrapper.find('.original_title_multistep').length > 0) { // Has element and is fine
      //original_title = select_box_wrapper.find('.elementor-repeater-row-item-title .original_title_multistep')[0].innerHTML;
      //select_box_wrapper.find('.elementor-repeater-row-item-title')[0].innerHTML = original_title;
    }

    if (select_box_wrapper.find('.original_title_multistep').length == 0) { //Doesn't have


      original_title = select_box_wrapper.find('.elementor-repeater-row-item-title')[0].innerHTML;
      select_box_wrapper.find('.elementor-repeater-row-item-title')[0].innerHTML = "Page Break #"+page_break_counter+'<span class="original_title_multistep">'+original_title+'</span>';

    }

  }
}
/*jQuery("#elementor-panel-content-wrapper").load(function(){
  check_page_breaks();
});*/
/*
jQuery(window).on("elementor/frontend/init", function() {
  elementor.channels.editor.on('namespace:editor:submit', function() {
    console.log('Try this!');
    check_page_breaks();
  });
});*/


/*window.elementorFrontend.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
  console.log("I'M OPEN!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
} );*/


/*
window.on("elementor/frontend/init", function() {
  elementor.hooks.addAction( 'panel/open_editor/multistep.default', function( scope ) {
  	alert("Hello World", scope);
  } );
});*/

jQuery ( window ).on (
        'elementor:init',
        function () {
            console.log("ELEMENTOR,", window.elementor, typeof window.elementor)
            /*
            elementorFrontend.Module.extend( {
            	onEditSettingsChange: function( propertyName ) {
            		if ( 'activeItemIndex' === propertyName ) {
            			// do your thing;
            		}
            	},
            } );*/

            window.elementor.hooks.addAction(
                'panel/open_editor/widget/multistep-free',
                function( panel, model, view ) {
                    //alert("Hello World!!");
                    check_page_breaks();

                    const $this = jQuery( panel.$el );

                    console.log("$this",$this, $this.find(".elementor-repeater-fields-wrapper")[0]);

                    /*
                    $this.find('div.elementor-repeater-tool-duplicate').on('click:duplicate',function () {
                      alert('clicked');
                      check_page_breaks();
                    });

                    $this.on('click:duplicate','*',function () {
                      alert('clicked!!');
                      check_page_breaks();
                    });

                    $this.on('duplicate','*',function () {
                      alert('clicked!!!');
                      check_page_breaks();
                    });


                    $this.find('div.elementor-repeater-tool-duplicate').on('mousedown @ui.duplicateButton', function() {
                      //alert('clicked!!!!');
                      check_page_breaks();
                    });*/

                    var $id = $this.find(".elementor-control-custom_id input[data-setting=\"custom_id\"]");



                    console.log("SET FUNCTION CODE: ",$id);



                    function setShortCode(ele) {
                      console.log("ele",ele)
                      if (!ele.value) {
                        console.log("NOT RIGHT")
                        return;
                      }

                      console.log("CODE", ele)
                      console.log("CODE", jQuery(ele))
                      console.log("CODE", jQuery(ele).parents(".elementor-repeater-row-controls"))
                      console.log("CODE", jQuery(ele).parents(".elementor-repeater-row-controls").find(".elementor-multistep-field-shortcode"));
                      var short = jQuery(ele).parents(".elementor-repeater-row-controls").find(".elementor-multistep-field-shortcode");

                      console.log("CODE", jQuery(ele).parents(".elementor-repeater-row-controls").find(".elementor-multistep-field-shortcode")[0].value)
                      jQuery(ele).parents(".elementor-repeater-row-controls").find(".elementor-multistep-field-shortcode")[0].value = "[field id=\""+ele.value+"\"]";
                    }



                    $id.on('change', function(event){
                      setShortCode(event.srcElement);
                    });

                    for(var x = 0; x < $id.length; x++) {
                      setShortCode($id[x]);
                    }

                    jQuery("input[data-setting='email_from'")
                    console.log("$this",$this, $this.find("input[data-setting='email_from']"), $this.find("input[data-setting='email_from']")[0])
                    //var $id = $this.find(".elementor-control-custom_id input[data-setting=\"custom_id\"]");

                    /*jQuery("input[data-setting='email_from']").on("input", function() {
                      console.log("Hi change");
                    })*/

                    var observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {

                            var nodes = Array.from(mutation.removedNodes);
                            //var directMatch = nodes.indexOf(target) > -1
                            //var parentMatch = nodes.some(parent => nodes.contains(mutation));
                            if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                                // element added to DOM
                                console.log(mutation)

                                jQuery("input[data-setting='email_from']").on("input", function() {
                                  console.log("SET SELECT")
                                  jQuery("select[data-setting='email_reply_to']")[0].innerHTML = "<option value='"+jQuery("input[data-setting='email_from']")[0].value+"'>"+jQuery("input[data-setting='email_from']")[0].value+"</option><option value='email'>Email Field</option>";
                                })

                                if (jQuery("input[data-setting='email_from']").length > 0) {
                                  jQuery("select[data-setting='email_reply_to']")[0].innerHTML = "<option value='"+jQuery("input[data-setting='email_from']")[0].value+"'>"+jQuery("input[data-setting='email_from']")[0].value+"</option><option value='email'>Email Field</option>";
                                }

                            }
                        });
                    });

                    var config = {
                        attributes: true,
                        childList: true,
                        characterData: true
                    };

                    observer.observe($this.find("#elementor-controls")[0], config);




                    ///////////////////////// runs before insert.
                    /*$this.find('div.elementor-repeater-tool-duplicate').on('click',function () {
                      alert('clicked!! working');
                      check_page_breaks();
                    });*/
                    /////////////////////////
                    /*
                    elementor.channels.editor.on('click','div.elementor-repeater-tool-duplicate',function () {
                      alert('clicked!!');
                      check_page_breaks();
                    });

                    elementor.channels.editor.on('click',function () {
                      alert('clicked??');
                      check_page_breaks();
                    });

                    elementor.channels.editor.on( 'click',() => alert( 'This is where you do your stuff??????') );
                    elementor.channels.editor.on( 'duplicate',() => alert( 'This is where you do your stuff!!!') );
                    elementor.channels.editor.on( 'click:duplicate',() => alert( 'This is where you do your stuff!!!!!!') );
                    elementor.channels.editor.on( 'document/repeater/duplicate',() => alert( 'This is where you do your stuff???') );

                    */

                }
            );



            /*
            window.elementor.hooks.addAction(
                'click',
                function(  ) {
                    alert("Hello World!!");

                }
            );

            window.elementor.hooks.addAction(
                'document/repeater/duplicate',
                function(  ) {
                    alert("Hello World!!");

                }
            );


            window.elementor.hooks.addAction(
                'duplicate',
                function(  ) {
                    alert("Testing");

                }
            );


            window.elementor.hooks.addAction(
                'document/repeater/remove',
                function(  ) {
                    alert("Hello World??");

                }
            );


            window.elementor.hooks.addAction(
                'panel/open_editor/widget/multistep/duplicate',
                function(  ) {
                    alert("Testing");

                }
            );



            elementor.channels.editor.on( 'click',() => alert( 'This is where you do your stuff') );

            elementor.channels.editor.on( 'duplicate',() => alert( 'This is where you do your stuff!!!') );
            */

            //elementor.channels.editor.on( 'test_event_multi',() => alert( 'This is where you do your stuff') );


            /*elementor.channels.editor.on('click',function( view ) {
                var changed = view.elementSettingsModel.clicked;
                console.log( "click!!!", view, changed );
            });*/
        }
);


jQuery( document ).ready(function() {
  console.log("EDITOR.JS LOADED.");


  /*
  .bind('DOMNodeInserted', function() {
      alert('node inserted');
  });*/


  /*jQuery("iframe").load(function(){
      jQuery(this).contents().on("mousedown, mouseup, click", function(){
          //alert("Click detected inside iframe.");
          check_page_breaks();
      });
  });*/


  /*
  jQuery(document).on("DOMNodeInserted", "#elementor-panel-page-editor", function(){
    console.log("UPDATE ALERT!!");
    field_type_selects = jQuery("div.elementor-repeater-fields select[data-setting='field_type'] option[value='page_break']:selected").parent().parent().parent().parent().parent().parent().parent().not(".page_break_item");
    console.log("EDITOR.JS LOADED.", field_type_selects);
    console.log("field_type_selects!",field_type_selects);
    for(var x = 0; x < field_type_selects.length; x++) {
      select_box = jQuery(field_type_selects[x]);
      select_box.addClass("page_break_item");
      //console.log("select box >", select_box, select_box.value);
    }



  });
  */
  /*
  jQuery(document).on("mousedown, mouseup, click", "div.elementor-repeater-row-tool.elementor-repeater-tool-duplicate", function(){
    console.log("CLICK elementor-repeater-tool-duplicate");
    //jQuery("div.elementor-repeater-fields select[data-setting='field_type']").parent().parent().parent().parent().parent().parent();
    this.cancelable = false;
    alert("CLICKED!");
    check_page_breaks();

    //check_page_breaks();
  });*/
  /*jQuery(document).on("change", "select[data-setting='field_type']", function(){
    console.log("UPDATE ALERT!!");
    //jQuery("div.elementor-repeater-fields select[data-setting='field_type']").parent().parent().parent().parent().parent().parent();

    check_page_breaks();
  });*/
  /*
  jQuery(document).on("change", "select[data-setting='field_type']", function(){
    console.log("UPDATE ALERT!!");
    //jQuery("div.elementor-repeater-fields select[data-setting='field_type']").parent().parent().parent().parent().parent().parent();
    check_page_breaks();
  });*/





  /*
  jQuery(document).on("mousedown, mouseup, click", "div.elementor-repeater-row-tool.elementor-repeater-tool-remove", function(){
    console.log("CLICK elementor-repeater-tool-remove");
    //jQuery("div.elementor-repeater-fields select[data-setting='field_type']").parent().parent().parent().parent().parent().parent();
    this.cancelable = false;
    check_page_breaks();

    //check_page_breaks();
  });*/




  /*
  jQuery(".elementor-repeater-row-tool.elementor-repeater-tool-duplicate").load(function(){
      jQuery(this).on("mousedown, mouseup, click", function(){
          //alert("Click detected inside iframe.");
          console.log("CLICK elementor-repeater-tool-duplicate");
          check_page_breaks();
      });
  });

  jQuery(".elementor-repeater-row-tool.elementor-repeater-tool-remove").load(function(){
      jQuery(this).on("mousedown, mouseup, click", function(){
          //alert("Click detected inside iframe.");
          console.log("CLICK elementor-repeater-tool-remove");
          check_page_breaks();
      });
  });*/





  /*
  jQuery(".elementor-repeater-row-tool.elementor-repeater-tool-duplicate").click(function(){

    check_page_breaks();
  });
  jQuery('.elementor-repeater-row-tool.elementor-repeater-tool-remove').click(function(){

    check_page_breaks();
  });*/


  /*
  field_type_selects = jQuery("div.elementor-repeater-fields select[data-setting='field_type'] option[value!='page_break']:selected").parent().parent().parent().parent().parent().parent().parent().hasClass(".page_break_item");
  for(var x = 0; x < field_type_selects.length; x++) {
    select_box = jQuery(field_type_selects[x]);
    select_box.removeClass("page_break_item");
    //console.log("select box >", select_box, select_box.value);
  }
  */
});
