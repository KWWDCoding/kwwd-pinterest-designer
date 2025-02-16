jQuery(document).ready(function($){
    // Instantiates the variable that holds the media library frame.
    var custom_pinterest_image_frame;
	const imagesuffix = '150x150';
	
    // Runs when the image upload button is clicked.
    $('#custom_pinterest_image_upload_button').click(function(e) {
        e.preventDefault();
        
        // If the frame already exists, reopen it.
        if (custom_pinterest_image_frame) {
            custom_pinterest_image_frame.open();
            return;
        }
        
        // Sets up the media library frame.
        custom_pinterest_image_frame = wp.media.frames.customHeader = wp.media({
            title: 'Choose Image',
            button: { text: 'Choose Image' },
            multiple: false
        });
        
        // Runs when an image is selected.
        custom_pinterest_image_frame.on('select', function() {
            var attachment = custom_pinterest_image_frame.state().get('selection').first().toJSON();
            $('#custom_pinterest_image_id').val(attachment.id);
			var image_url = attachment.url;
			
			//alert(image_url);
			
			//alert(imagesuffix);
			
	
	  if(imagesuffix !== '')
	  {
		const parts = image_url.split('.');
		let filename = "";

		for (let i = 0; i < parts.length - 1; i++) { // Stop before the last index
		  filename += parts[i] + "."; // Append each element (except the last) with a colon
		}
		//Check to see if we have a period at end of filename and get rid of it
		filename = filename.trim(); // Remove leading/trailing whitespace
		
		if (filename !== '') 
		{
		filename = filename.slice(0, -1);
		//filename = filename.substring(0, filename.length - 1);
		}


		const extension = parts[parts.length - 1].toLowerCase(); // Ensure extension is lowercase
		
		// Create the new filename with dimensions
		const newFilename = filename+'-'+imagesuffix+'.'+extension;
		$('#custom_pinterest_image').attr('src', newFilename);
		$('#ImageManagement').attr('style', '');
		$('#custom_pinterest_image_upload_button').attr('style', 'display: none');
	  }
	  else
	  {	  
	      $('#custom_pinterest_image').attr('src', image_url);
	  }
        });
        
        // Opens the media library frame.
        custom_pinterest_image_frame.open();
    });
	
	// Hide the Image Management Div when removing image
        $('#remove_custom_pinterest_image_button').click(function(){
            $('#custom_pinterest_image_id').val('');
            $('#custom_pinterest_image').attr('src', '/wp-content/plugins/kwwd-pinterest-image/img/holding.jpg');
			$('#ImageManagement').attr('style', 'display: none;');
			$('#custom_pinterest_image_upload_button').attr('style', '');
        });
});
