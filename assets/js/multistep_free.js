console.log(">>>>> MULTISTEP LOGIC LOADED", php_vars.is_admin, ">", php_vars.is_admin != 1);
String.prototype.repdel = function(replace_phrase) {
	return this.replace(replace_phrase, "");
}


class MultistepForm_Js {
	/**
	 * Purpose: Initialization of properties.
	 * @param {form div object} form
	 */
	constructor(form) {
		this.form = form;
		this.form_id = form[0].getAttribute("sid");
		this.settings = {};
		this.elements = {};
		this.elements.$form = form;
		this.elements.$submitButton = form.find('button[type="submit"]');
		this.current_tab = -1;

		this.selector = "form[sid='"+this.form_id + "'].elementor-multistep";
		console.log("this.form_id",this.selector);

	}
	/**
	 * Purpose: Retrieve Ajaxurl for server-side processing.
	 */
	getSettings() {
		return php_vars.ajaxurl;
	}

	/**
	 * Purpose: Bind events to buttons and inputs for validation. Bind event to form to post results to the Ajaxurl.
	 */
	bindEvents() {
		console.log(">bindEvents")
		this.form.on('submit', this.submission_steps);
		var inputs = jQuery(this.selector).find(":input").not("[name='form_id']").not("[name='post_id']").not("[type='hidden']").not("[type='submit']")
		inputs.on("change", jQuery.proxy(function(){this.validate},this) );

		jQuery(inputs).keydown(function(event){
	    if(event.keyCode == 13) {
	      event.preventDefault();
	      return false;
	    }
	  });

		jQuery(this.selector+' .multistep-button-controls-backwards').on("click", jQuery.proxy(function(){
			 this.showTab(-1)
			if (this.validate() == true) {
				//this.showTab(-1)
			} else {
				// Handle fails
			}
		}, this) );
		jQuery(this.selector+' .multistep-button-controls-forward').on("click", jQuery.proxy(function(){
			if (this.validate() == true) {
				this.showTab(1)
			} else {
			 // Handle fails
			}
	 }, this) );



	 var $fileInput = this.form.find('input[type=file]');
	 if ($fileInput.length) {
		 $fileInput.on('change', this.validateFileSize);
	 }
 	}
		/**
		 * Purpose: Validate size of file upload to make sure it is not too large to handle.
		 * @param {event object} event
		 */
	 validateFileSize(event) {
		 console.log(">validate")
		 var _this = this;

		 var $field = jQuery(event.currentTarget),
				 files = $field[0].files;

		 if (!files.length) {
			 return;
		 }

		 var maxSize = parseInt($field.attr('data-maxsize')) * 1024 * 1024,
				 maxSizeMessage = $field.attr('data-maxsize-message');

		 var filesArray = Array.prototype.slice.call(files);
		 filesArray.forEach(function (file) {
			 if (maxSize < file.size) {
				 $field.parent().addClass('elementor-error').append('<span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">' + maxSizeMessage + '</span>').find(':input').attr('aria-invalid', 'true');

				 _this.form.trigger('error');
			 }
		 });
	 }
	 /**
 	 * Purpose: Starts loading circle to simulate loading.
 	 */
	 beforeSend() {
		 console.log(">beforeSend")
		 var $form = this.form;

		 $form.animate({
			 opacity: '0.45'
		 }, 500).addClass('elementor-form-waiting');

		 $form.find('.elementor-message').remove();

		 $form.find('.elementor-error').removeClass('elementor-error');

		 $form.find('div.elementor-field-group').removeClass('error').find('span.elementor-form-help-inline').remove().end().find(':input').attr('aria-invalid', 'false');

		 this.elements.$submitButton.attr('disabled', 'disabled').find('> span').prepend('<span class="elementor-button-text elementor-form-spinner"><i class="fa fa-spinner fa-spin"></i>&nbsp;</span>');
	 }

	 /**
 	 * Purpose: Retrieves form data and preps for delivery.
 	 * @param {string} action
	 * @param {boolean or string} collect_all
 	 */
	 getFormData(action, collect_all) {
		 action = action || "elementor_pro_forms_send_form";
		 collect_all = collect_all || true;

		 console.log(">getFormData", collect_all);

		 var form_data = new FormData();
		 var form = jQuery(this.form);
		 var formData = {"post_id":form.find("input[name='post_id']")[0].value,
										 "form_id":form.find("input[name='form_id']")[0].value};

		 if (collect_all !== true) {
			 console.log("parseInt(collect_all)",parseInt(collect_all, 10));
			 console.log("jQuery(this.selector + ' div.page_break_class')", jQuery(this.selector+ ' div.page_break_class'));
			 var page = jQuery(jQuery(this.selector+ ' div.page_break_class')[parseInt(collect_all, 10)]);
			 console.log("page", page);
			 var inputs = page.find(":input").not("[name='form_id']").not("[name='post_id']").not("[type='hidden']").not("[type='submit']");
		 } else {
			 var inputs = form.find(":input").not("[name='form_id']").not("[name='post_id']").not("[type='hidden']").not("[type='submit']");
		 }

		 console.log("inputs", inputs);

		 var form_fields = {}
		 inputs.each(function(input){
			 console.log("input", inputs[input], inputs[input].name, inputs[input].value);
			 formData[inputs[input].name] = inputs[input].value;
			 form_data.append(inputs[input].name, inputs[input].value);
		 })

		 formData['action'] = action;
		 form_data.append("action", formData['action']);
		 formData['referrer'] = location.toString();
		 form_data.append("referrer", formData['referrer']);
		 form_data.append("form_id", formData['form_id']);
		 form_data.append("post_id", formData['post_id']);

		 console.log(">Data", formData);
		 console.log(">>>>>form_data",form_data);

		 return form_data;
	 }

	 /**
 	 * Purpose: Handles the success of an ajax delivery.
 	 * @param {ajax response object} response
 	 */
	 onSuccess(response) {
		 console.log(">onSuccess", response);
		 var $form = this.form;

		 this.elements.$submitButton.removeAttr('disabled').find('.elementor-form-spinner').remove();

		 $form.animate({
			 opacity: '1'
		 }, 100).removeClass('elementor-form-waiting');

		 if (!response.success) {
			 if (response.data.errors) {
				 jQuery.each(response.data.errors, function (key, title) {
					 $form.find('#form-field-' + key).parent().addClass('elementor-error').append('<span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">' + title + '</span>').find(':input').attr('aria-invalid', 'true');
				 });

				 $form.trigger('error');
			 }
			 $form.append('<div class="elementor-message elementor-message-danger" role="alert">' + response.data.message + '</div>');
		 } else {
			 $form.trigger('submit_success', response.data);

			 // For actions like redirect page
			 $form.trigger('form_destruct', response.data);

			 $form.trigger('reset');

			 if ('undefined' !== typeof response.data.message && '' !== response.data.message) {
				 $form.append('<div class="elementor-message elementor-message-success page_break_class_style" role="alert">' + response.data.message + '</div>');
			 }
		 }
	 }
	 /**
 	 * Purpose: Handles the error of an ajax delivery
 	 * @param {object} xhr
	 * @param {string} desc
 	 */
	 onError(xhr, desc) {
		 console.log(">onError", xhr, desc)
		 var $form = this.form;

		 $form.append('<div class="elementor-message elementor-message-danger" role="alert">' + desc + '</div>');

		 this.elements.$submitButton.html(this.elements.$submitButton.text()).removeAttr('disabled');

		 $form.animate({
			 opacity: '1'
		 }, 100).removeClass('elementor-form-waiting');

		 $form.trigger('error');
	 }
	 /**
 	 * Purpose: Retrieves parent form id
 	 */
	 getFormId() {
		 return this.form_id;
	 }
	 /**
 	 * Purpose: Validates that the current page of the form is correct, and if not, sends the appropriate response message regarding why.
 	 */
	 validate() {
		 var steps = jQuery(this.selector + " div.step");
		 var number = null;
		 steps.each(function(step) {
			 console.log("step", step, steps[step], this);
			 if (jQuery(this).hasClass("active")) {
				 console.log("number", number);
				 number = step;
				 console.log("number", number);
			 }
		 })

		 var form = jQuery(this.selector);
		 var current_tab_num = this.current_tab;
		 console.log("current_tab",current_tab_num);
		 var page = jQuery(jQuery(this.selector+ ' div.page_break_class')[current_tab_num]);
		 var form_id = this.form_id;
		 console.log("page", page);
		 var inputs = page.find(":input").not("[name='form_id']").not("[name='post_id']").not("[type='hidden']").not("[type='submit']")
		 var fails = {}

		 for(var input_index = 0; input_index < inputs.length; input_index++) {
			 var input = inputs[input_index];
			 console.log(input, input.type, php_vars.is_admin != 1);
			 if (php_vars.is_admin != 1) {
				 switch(input.type) {
					 case 'email':

						 if (input.value.length == 0 && input.hasAttribute("required")) {
							 fails[input.name+"_"+form_id]={"element":input, "reason":"This field appears to be empty"};
						 };
						 if (input.value.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/i) == null) {
							 fails[input.name+"_"+form_id]={"element":input, "reason":"This field doesn't follow email format"};
						 }

						case 'tel':
  						 if (input.value.length == 0 && input.hasAttribute("required")) {
  							 fails[input.name+"_"+form_id]={"element":input, "reason":"This field appears to be empty"};
  						 };
							 if (input.value.length > 15) {
  							 fails[input.name+"_"+form_id]={"element":input, "reason":"Max length is 15 digits."};
  						 };
  						 if (input.value.match(/^[0-9()#&+*-=.]+$/i) == null) {
  							 fails[input.name+"_"+form_id]={"element":input, "reason":"This field doesn't follow phone format"};
  						 }
					 default:
						 console.log("input.value.length > 0", input.value.length > 0, input.value.length);
						 if (input.value.length == 0 && input.hasAttribute("required")) {
							 fails[input.name+"_"+form_id]={"element":input, "reason":"This field is required."};
						 };
				 }
			 }
		 }

		 var all_labels = jQuery(this.form).find("span.multistep-warning");

		 for(var label_index = 0; label_index < all_labels.length; label_index++) {
			 var label = all_labels[label_index];
			 if (jQuery(label).hasClass("fade-in") == true) {
				 jQuery(label).removeClass('fade-in');
				 label.innerHTML = "";
			 }
		 }

		 console.log("fails.length", Object.keys(fails).length); //Fix fails
		 for(var fail_index = 0; fail_index < Object.keys(fails).length; fail_index++) {
			 console.log("fail_index",fail_index);
			 var fail = fails[Object.keys(fails)[fail_index]];
			 console.log("fail ele", fail);
			 var label_for = Object.keys(fails)[fail_index].replace("[", "-").repdel("]").replace("form_fields", "form_field").replace("_", "-").repdel("_"+form_id);
			 console.log("label_for",label_for);
			 var label = jQuery(this.form).find("label[for='"+label_for+"']");
			 console.log("fail label", label);
			 var warning_span = jQuery(this.form).find("span#warning-"+label_for.repdel("form-field-"))[0];
			 console.log("warning_span",warning_span);

			 if (jQuery(warning_span).hasClass("fade-in") == false) {
				 jQuery(warning_span).addClass("fade-in");
			 }


			 warning_span.innerHTML = fail['reason'];
		 }
		 console.log("fails",fails);
		 console.log("fails.length",Object.keys(fails).length)
		 if (Object.keys(fails).length == 0) { // All good
			 return true;
		 } else if (Object.keys(fails).length > 0) { // Something doesn't pass
			 return false;
		 } else { // Major error
			 console.log("ERROR!!!")
			 return 'error';
		 }
	 }
	 /**
 	 * Purpose: Validation check before form is preped for delivery.
 	 */
	 submission_steps(eventy) {
		 if (self.validate() == true && php_vars.is_admin != 1) {
			 self.handleSubmit(eventy);
		 } else {
			 // Handle fails
		 }
	 }

	 /**
 	 * Purpose: Preps the ajax package for delivery, and mails it.
 	 */
	 handleSubmit(event) {
		 console.log(">handleSubmit");
		 var $form = this.form;
		 console.log("$form",$form);
		 event.preventDefault();

		 if ($form.hasClass('elementor-form-waiting')) {
			 return false;
		 }
		 this.beforeSend();

		 jQuery.ajax({
			 url: self.getSettings('ajaxUrl'),
			 type: 'POST',
			 dataType: 'json',
			 data: self.getFormData(),
			 processData: false,
			 contentType: false,
			 success: self.onSuccess,
			 error: self.onError
		 }).error(function() {
			 console.log("ERROR HAS HAPPENED! RAWR!!!!!!!!!!");
		 });
	 }
	 /**
	 * Purpose: Pages through the different pages of the Multistep Form Widget (For ElementorPro)
	 */
	 showTab(tab_num) {
		 var tabs = jQuery(this.selector+' .page_break_class');
		 if (tab_num !== 1 && tab_num !== -1){

		 } else {


			 console.log("? tabs.length " + tabs.length + " ? current_tab + tab_num " + (this.current_tab + tab_num));
			 if (this.current_tab + tab_num > -1 && this.current_tab + tab_num < (tabs.length)) {
				 jQuery(tabs[this.current_tab]).addClass("invisible");
				 this.current_tab += tab_num;
				 jQuery(tabs[this.current_tab]).removeClass("invisible");

				 console.log("(current_tab + tab_num)+1 == (tabs.length)", (this.current_tab)+1 == (tabs.length), (this.current_tab)+1, (tabs.length));
				 if ((this.current_tab)+1 == (tabs.length)) { // Last Page
					 jQuery(this.selector+" .multistep-button-controls-forward").addClass("invisible");
					 jQuery(this.selector+" .multistep-button-controls button[type=submit]").removeClass("invisible");
				 } else {
					 jQuery(this.selector+" .multistep-button-controls-forward").removeClass("invisible");
					 jQuery(this.selector+" .multistep-button-controls button[type=submit]").addClass("invisible");
				 }

				 console.log("(current_tab + tab_num) == 0", (this.current_tab) == 0, (this.current_tab), 0)
				 if (this.current_tab == 0) { // First Page
					 jQuery(this.selector+" .multistep-button-controls-backwards").addClass("invisible");
					 jQuery(this.selector+" .button-control-placeholder").removeClass("invisible");

				 } else {
					 jQuery(this.selector+" .multistep-button-controls-backwards").removeClass("invisible");
					 jQuery(this.selector+" .button-control-placeholder").addClass("invisible");
				 }

				 var steps = jQuery(this.selector+' .step');
				 for (var i = 0; i < steps.length; i++) {
					 var step_to_check = jQuery(steps[i])
					 console.log(i, steps.length-1, this.current_tab, " INCLUDING 0")
					 if (step_to_check.hasClass("active")) {
						 step_to_check.removeClass('active');
					 }

					 if (i > this.current_tab) { // Past Current tab
						 if (step_to_check.hasClass("finish")) { //Check if class is on step
							 step_to_check.removeClass("finish"); // Remove class
						 }
					 }
					 else {
						 if (i == this.current_tab) { // Past Current tab
							 if (!step_to_check.hasClass("active")) { //Check if class is on step
								 step_to_check.addClass("active"); // Remove class
							 }
						 }
						 if (!step_to_check.hasClass("finish")) { //Check if class is missing from step
							 step_to_check.addClass("finish"); // Add class
						 }
					 }
				 }
			 } else {
			 }
		 }
	 }
	 /**
	 * Purpose: Creates the steps for the step tracker at the bottom of the widget.
	 */
	 create_step() {
		 jQuery(this.selector + " .current-page-tracker").append('<div class="circle step"></div>');
	 }
	 /**
	 * Purpose: Final steps of setup.
	 */
 	setup() {
		console.log('this.selector',this.selector, this.form.id, this.form);
		console.log("getSettings('ajaxUrl')",this.getSettings('ajaxUrl'));

	 	 var tabs = jQuery(this.selector+' .page_break_class');

	 	jQuery(this.selector+" .multistep-button-controls button[type=submit]").addClass("invisible")
		jQuery(this.selector + " .current-page-tracker").html("");
		for (var i = 0; i < tabs.length; i++) {
			this.create_step();
		}

		this.bindEvents();
		this.validate();
		console.log("jQuery Form", jQuery(this.selector));

		this.showTab(1);
	}

}

function start_up(first) {
	first = first || {};

	var form_objs = first;

	console.log("first",form_objs);

	var forms = jQuery('form.elementor-multistep');

	for(var form_index = 0; form_index < forms.length; form_index++) {
		var form = forms[form_index];
		if (!(form_objs.hasOwnProperty(form.getAttribute('sid')))) {
			form_objs[form.getAttribute('sid')] = {"object": form, 'class':new MultistepForm_Js(jQuery("form[sid='"+form.getAttribute('sid')+"'].elementor-multistep"))}; //form[sid='a2db0b6'].elementor-multistep
			form_objs[form.getAttribute('sid')]['class'].setup();
		}
	}
	return form_objs;
}

/*if (php_vars.is_admin == 1) {

}*/


//jQuery("div.elementor-repeater-fields select")
