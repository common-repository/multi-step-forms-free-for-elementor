console.log("ADMIN JS LOADED");

function onSuccess() {

}




function getFormData() {
  var form_data = new FormData();
  var form = jQuery('.multistep_form_auth');
  //var formData = {};


  var inputs = form.find(":input").not("[name='form_id']").not("[name='post_id']").not("[type='hidden']").not("[type='submit']").not("[type='button']");


  console.log("inputs", inputs);

  //var form_fields = {}
  inputs.each(function(input){
    console.log("input", inputs[input], inputs[input].name, inputs[input].value);
    //formData[inputs[input].name] = inputs[input].value;
    form_data.append(inputs[input].name, inputs[input].value);
  })

  form_data.append(jQuery('.multistep_form_auth_url')[0].name, jQuery('.multistep_form_auth_url')[0].value);

  //for (var data of form_data) {
  //  console.log(data);
  //}
  return form_data;
}

//console.log(">",getFormData());





url = HQ_URL+"/wp-json/multistep_forms_manager/v1/authenticate";
url2 = window.location.hostname + "/wp-json/Elementormultistep/v1/authenticate/";
console.log(">>",window.location.hostname + "/wp-json/Elementormultistep/v1/deauthenticate/")
jQuery('.activate_license_button').click(function(){
  console.log("CLICK");

  jQuery.ajax({
    url: url,
    type: 'POST',
    dataType: 'json',
    data: getFormData(),
    processData: false,
    contentType: false,
    success: onSuccess,

  }).error(function(xhr, desc) {
    //console.log("ERROR HAS HAPPENED! RAWR!!!!!!!!!!");
    var response = xhr['responseJSON'];
    console.log(">onError", response, desc)
    var error_message_display=jQuery('#error_message_display')[0];
    console.log('error_message_display',error_message_display)
    error_message_display.innerHTML = "Error: " + response['message'];

  }).success(function(response){
    console.log("onSuccess", response);
    jQuery('input[name="activation_stat"]')[0].value="HAI";
    var error_message_display=jQuery('#error_message_display')[0];
    error_message_display.innerHTML = '';
    jQuery('#multistep_ed')[0].value = response['ED'];
    /*jQuery.ajax({
      url: url2,
      type: 'POST',
      dataType: 'json',
      data: getFormData(),
      processData: false,
      contentType: false,
      success: onSuccess,

    }).error(function(xhr, desc) {
      //console.log("ERROR HAS HAPPENED! RAWR!!!!!!!!!!");
      console.log(">onError", xhr['responseJSON'], desc)
    }).success(function(response){
      console.log("onSuccess", response);
      jQuery('input[name="activation_stat"]')[0].value="HAI";
      jQuery("#submit").click();
    });*/


    //jQuery("#submit").click();
    jQuery("#submit").click();
  });

})


durl = HQ_URL+"/wp-json/multistep_forms_manager/v1/deauthenticate";
durl2 = "/wp-json/Elementormultistep/v1/deauthenticate/";
jQuery('.deactivate_license_button').click(function(){
  console.log("CLICK");
  if( confirm( 'Are you sure you want to deactivate Multistep Forms?' ) ) {
    jQuery.ajax({
      url: durl,
      type: 'POST',
      dataType: 'json',
      data: getFormData(),
      processData: false,
      contentType: false,
      success: onSuccess,

    }).error(function(xhr, desc) {
      //console.log("ERROR HAS HAPPENED! RAWR!!!!!!!!!!");
      console.log(">onError", xhr['responseJSON'], desc)
      var response = xhr['responseJSON'];
      console.log(">onError", response, desc)
      var error_message_display=jQuery('#error_message_display')[0];
      error_message_display.innerHTML = "Error: " + response['message'];
    }).success(function(response){
      console.log("onSuccess", response);
      jQuery('input[name="activation_stat"]')[0].value="NAI";
      var error_message_display=jQuery('#error_message_display')[0];
      error_message_display.innerHTML = '';

      /*
      jQuery.ajax({
        url: durl2,
        type: 'POST',
        dataType: 'json',
        data: getFormData(),
        processData: false,
        contentType: false,
        success: onSuccess,

      }).error(function(xhr, desc) {
        //console.log("ERROR HAS HAPPENED! RAWR!!!!!!!!!!");
        console.log(">onError", xhr['responseJSON'], desc)
      }).success(function(response){
        console.log("onSuccess", response);
        jQuery('input[name="activation_stat"]')[0].value="NAI";

      });*/

      jQuery("#submit").click();

    });
  } else {
    event.preventDefault();
  }


})

console.log("var type = jQuery( this ).closest( '.multistep-notice' ).data( 'notice' );",jQuery( this ).closest( '.multistep-notice' ).data( 'notice' ))
jQuery(document).ready(function(){
  console.log(jQuery('.multistep-notice')[0], jQuery('button.notice-dismiss'));
  jQuery('.multistep-notice').find('button.notice-dismiss').click(function () {
      // Read the "data-notice" information to track which notice
      // is being dismissed and send it via AJAX
      console.log("dismissing notice");
      var type = jQuery( this ).closest( '.multistep-notice' ).data( 'notice' );
      // Make an AJAX call
      // Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
      function getData(){
        data_1 = new FormData();
        data_1.append('action', 'dismissed_notice_handler');
        data_1.append("type", type);

        //data_1 = {action:'dismissed_notice_handler',type:type}

        return data_1;
      }
      jQuery.ajax( ajaxurl,
        {
          type: 'POST',
          dataType: 'json',
          data: getData(),
          processData: false,
          contentType: false,
        } ).success(function(response){
          console.log("success", response);
        }).error(function(xhr, desc){
          console.log("error", xhr, desc);
        });
    } );
})
