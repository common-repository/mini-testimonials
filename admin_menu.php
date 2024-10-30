<?php

	
	//fetches an array of objects containing the information
	function etm_mt_admin_get_testimonials() {
		global $wpdb;
		$table_name = $wpdb->prefix . "etm_mt";		
		$etm_results = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY `id` ASC");
	    etm_mt_process_results($etm_results);
	}

	//print out the results
	function etm_mt_process_results($data) {		
		$i = 0;
		foreach($data as $obj) {
			//start the data output						
			echo "<div id='etm_mt_".$i."'><p>";
			echo "&quot;" . $obj->testimonial . "&quot;<br>";
			echo "&mdash;";
			if( trim( $obj->author_url ) != "") 
				echo "<a href='" . urldecode($obj->author_url) . "' target='_blank'>" . $obj->author_name .", " . $obj->author_company ."</a>";
			else 
				echo $obj->author_name .", " . $obj->author_company;
			echo "</p>";
			
			//print the form data to be saved
			echo "<input type='hidden' class='etm_toAdd' value='" . htmlspecialchars($obj->testimonial, ENT_QUOTES) . "|+etm+|" . htmlspecialchars($obj->author_name, ENT_QUOTES) . "|+etm+|" . htmlspecialchars($obj->author_company, ENT_QUOTES) . "|+etm+|" . urldecode($obj->author_url) . "' name='etm_testimonial_" . $i . "' id='etm_testimonial_" . $i . "'>";				
			 

			//finish output
			echo "<br><a href='#' onclick='etm_deleteElement(".$i.");'>Delete</a></p></div>";
			$i++;
		}

		//add the counter to the page so JS can fetch it
		echo "<input type='hidden' value='".$i."' id='etm_counter'>";
	}	
?>

<style>
.etm_button {
	display:inline-block;
	padding:0.3em 0.7em;
	background:#dcdcdc;
	border:1px solid #d9d9d9;
	border-radius:8px;
	cursor:pointer;
	color:#21759b;
	font-size:100%;
	font-weight:bold;
}

#etm_mt input {display:block; width:250px; margin-bottom:20px;}
.etm_testimonial {width:600px!important;}

#etm_form_output div {margin-bottom:2em;}

#etm_update {display:none;}

#etm_testimonial_output > div {background:#e9e9e9; border:#f3f3f3; padding:2px 20px; display:block; min-width:30%; margin:1em 0;}
</style>

<div class="wrap">
<div id='etm_update'></div>	
	
	<?php screen_icon(); ?>

	<h2>Mini Testimonials</h2>
	<h3>Plug In Created By <a href="http://ellytronic.com" target="_blank">Ellytronic Media</a></h3>
	<p>To use this plugin, place the following shortcode on any page you wish to have it displayed on:	<strong>[etm_mt]</strong>
	</p><hr>

	<form action="#" id="etm_mt">	
		<h3>Testimonials Editor</h3>
		
		<div id="etm_new_mt_editor">
			<label for="etm_new_mt">New Testimonial Text *</label>
			<input type="text" id="etm_new_mt" name="etm_new_mt" required maxlength="150" class="etm_testimonial">

			<label for="etm_new_mt_author_name">New Testimonial Author Name *</label>
			<input type="text" id="etm_new_mt_author_name" name="etm_new_mt_author_name" required maxlength="20">

			<label for="etm_new_mt_author_name">New Testimonial Authoring Company</label>
			<input type="text" id="etm_new_mt_author_company" name="etm_new_mt_author_company" maxlength="40">			

			<label for="etm_new_mt_author_url">New Testimonial Author URL (optional)</label><br>
			http://<input type="text" id="etm_new_mt_author_url" name="etm_new_mt_author_url" style="display:inline-block;">
			
			<p><span class="button button-primary" id="etm_addElement" onclick="etm_doAddFields();">Add Testimonial</span>&nbsp;&nbsp;
			<span class='button' id='etm_cancel' onclick='etm_doReset();'>Reset</span></p>
		</div>

		<br><hr>

		<div id="etm_testimonial_output">
			<h3>Your existing testimonials:</h3>
			<?php etm_mt_admin_get_testimonials();?>
		</div>

		<div id="etm_testimonial_data" style="display:none;">
		</div>
		
		<p class="submit">
			<span class="button button-primary" id="etm_submit">Save Changes</span>
		</p>
	</form>
</div>