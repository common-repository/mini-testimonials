	jQuery(document).ready(function(){

		//Save Changes pressed
		jQuery("#etm_submit").on("click", function(){
			etm_submitForm();
		});
	});


	/**
	* Cleans the fields (removes special characters)
	*
	* @since 0.9.3
	*/
	function etm_cleanFields(the_field,is_uri) {	
		//make is_uri optional	
		is_uri = (typeof is_uri === "undefined") ? false : is_uri;
		
		//remove extra spaces
		the_field = jQuery.trim(the_field);				

		//different characters to clean if it's a URI
		if(!is_uri) {
			var entityMap = {
			    "&": "&amp;",
			    "<": "&lt;",
			    ">": "&gt;",
			    '"': '&quot;',
			    "'": '&#39;',
			    "`": '&#39;',
			    "/": '&#x2F;'
			};  

			the_field = String(the_field).replace(/[&<>"`'\/]/g, function (s) {
				return entityMap[s];
			});		    

	    } else {
			var entityMap = {			    
			    "<": "&lt;",
			    ">": "&gt;",
			    '"': '&quot;',
			    "'": '&#39;',
			    "`": '&#39;',		    
			    " ": '%20'	
			};  
	    	the_field = String(the_field).replace(/[<>"'` ]/g, function (s) {
	    		return entityMap[s];
	    	});				    	
	    }
	    
		return the_field;
	}	


	//adds the fields
	function etm_doAddFields() {		
		//prep the variables
		var etm_counter = jQuery("#etm_counter").val();
		var mt_text = etm_cleanFields( jQuery("#etm_new_mt").val() );			
		var mt_author_name = etm_cleanFields( jQuery("#etm_new_mt_author_name").val() );
		var mt_author_company = etm_cleanFields( jQuery("#etm_new_mt_author_company").val() );
		var mt_author_url = etm_cleanFields( jQuery("#etm_new_mt_author_url").val(), true ); //we don't clean this completely, because it might be dirty for a good reason..		
			
		//are any of our required fields empty?
		if(mt_text == "" || mt_text == null || mt_author_name == "" || mt_author_name == null) {
			jQuery("#etm_update").hide(0);
			jQuery("#etm_update").html("<p>You must fill out all the required fields first!</p>");
			jQuery("#etm_update").addClass("error");
			jQuery("html, body").animate({ scrollTop: 0 }, 300);
			jQuery("#etm_update").fadeIn(500);		
			return false;	
		} else {
			//just in case anything is visible, hide it
			jQuery("#etm_update").removeClass("error");
			jQuery("#etm_update").hide(0);
		}


		//prep the output
		var etm_newElement = '<div id="etm_mt_' + etm_counter + '">';
		etm_newElement =  etm_newElement + '<p>&quot;' + mt_text + '&quot;<br>&mdash;';
		if(mt_author_url != "")  {
			etm_newElement =  etm_newElement + "<a href='#' taget='_blank' onclick='return false;'>" + mt_author_name + ", " + mt_author_company + "</a>";
		} else {
			etm_newElement =  etm_newElement + mt_author_name + ", " + mt_author_company;
		}
		etm_newElement =  etm_newElement + "</p>";		

					
		//finish the element
		var etm_newElement = etm_finishElement(etm_newElement, etm_counter);

        //print the new element
        jQuery("#etm_testimonial_output").append(etm_newElement);		

		//add the element to the form data
		//these need to be cleaned again
		var etm_formElement = "<input type='hidden' class='etm_toAdd' value='" + etm_cleanFields(mt_text) + "|+etm+|" + etm_cleanFields(mt_author_name) + "|+etm+|" + etm_cleanFields(mt_author_company) + "|+etm+|" + etm_cleanFields(mt_author_url, true) + "' name='etm_testimonial_" + etm_counter + "' id='etm_testimonial_" + etm_counter + "'>";
		jQuery("#etm_testimonial_data").append(etm_formElement);

        //increase our counter
        etm_counter++;
        jQuery("#etm_counter").val(etm_counter);

		//reset workarea
		etm_doReset();

	}	

	//adds the final touches to a new element
	function etm_finishElement(etm_newElement, etm_counter) {
		etm_newElement = etm_newElement + '<a href="#" onclick="etm_deleteElement(' + etm_counter +'); return false;">Delete</a><br><br>';
		etm_newElement = etm_newElement + '</div><!-- // #etm_mt_' + etm_counter +' -->';
		return etm_newElement;
	}



	//cancels and returns to previous state
	function etm_doReset() {
		//clear the work area
		jQuery("#etm_new_mt").val("");
		jQuery("#etm_new_mt_author_name").val("");
		jQuery("#etm_new_mt_author_company").val("");
		jQuery("#etm_new_mt_author_url").val("");
	}

	//removes an element
	function etm_deleteElement(etm_ID) {
		if(confirm("Are you sure you wish to delete this element?")) {			
			//remove it from the work area
			jQuery("#etm_mt_" + etm_ID).remove();
			//remove it from the form data
			jQuery("#etm_testimonial_" + etm_ID).remove();
		}
	}

	//submits the form
	function etm_submitForm() {
		//prepare the JSON object "data"		
		var data = {};
		var formData = [];
		var field_counter = -1;
		jQuery('.etm_toAdd').each(function( index ) {						
			formData[index] = jQuery(this).val();
			field_counter = index;
		});	

		//lets ensure there's data to submit			
		if(field_counter < 0 || field_counter == null) {
			//nope
			jQuery("#etm_update").html("<p>You must have at least one testimonial before you can save!</p>");
			jQuery("#etm_update").addClass("error");
			jQuery("#etm_update").fadeIn(500);				
			setTimeout(5000, function(){
				jQuery("#etm_update").fadeOut(500, function(){
					jQuery("#etm_update").removeClass("error");
				});
			});
			return false;		
		}

		data.action = "etm_mt_update_form";
		data.data = formData;	

		console.log(data);
		
		//submit the form
		jQuery.post(ajaxurl, data, function(response) {
			if(response == "true" || response == true) {
				jQuery("#etm_update").html("<p>Testimonials Updated</p>");
				jQuery("#etm_update").addClass("updated");
				jQuery("html, body").animate({ scrollTop: 0 }, 300);
				jQuery("#etm_update").fadeIn(1000, function(){
					
					setTimeout(function(){
						jQuery("#etm_update").fadeOut(1000, function(){
							jQuery("#etm_update").removeClass("updated");
						});
					}, 5000);
				});
				console.log(response);

			} else {
				jQuery("#etm_update").html("<p>Database Error: Please notify webmaster</p>");
				jQuery("#etm_update").addClass("error");
				jQuery("html, body").animate({ scrollTop: 0 }, 300);
				jQuery("#etm_update").fadeIn(500, function(){
					
					setTimeout(5000, function(){
						jQuery("#etm_update").fadeOut(500, function(){
							jQuery("#etm_update").removeClass("error");
						});
					});
				});				
				console.log(response);
			}			
		});
	}