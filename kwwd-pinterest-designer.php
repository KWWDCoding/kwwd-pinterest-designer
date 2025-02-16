<?php
/**
 * Plugin Name: KWWD Pinterest Designer
 * Description: Create a Pinterest image dynamically and upload it to the Media Library
 * Plugin URI: https://github.com/KWWDCoding/kwwd-pinterest-designer
 * Version: 1.0.1
 * Author: KWWD
 * Author URI: https://www.kwwd.co.uk
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;
define('KWWD_Plugin_Directory', plugin_dir_url(__FILE__));

// Register Custom Meta Boxes for Ratings
function kwwd_pinterest_design_box() {
    add_meta_box(
        'kww_pinterest_design',
        'Pinterest Design',
        'kwwd_pinterest_designer_callback',
        'post',
        'advanced',
        'high'
    );
}
add_action('add_meta_boxes', 'kwwd_pinterest_design_box');

// Display Pin Design Content
function kwwd_pinterest_designer_callback($post)
{

// TABS
?>
	<!-- Tab Navigation -->
		<div class="tab-container">
			<ul class="tab-list">
				<li class="tab" id="design-pinimage-tab" onclick="switchTab('design-pinimage-tab')">Design Pin</li>
				<li class="tab" id="upload-custompin-tab" onclick="switchTab('upload-custompin-tab')">Upload Pin</li>
			</ul>
		</div>
<div class="tab-content">		
<?php	


	$PinterestHeadline = get_post_meta($post->ID, 'kwwd_pinterest_headline', true);
    $PinterestTemplate = get_post_meta($post->ID, 'kwwd_pinterest_template', true);
    $PinterestFooter = get_post_meta($post->ID, 'kwwd_pinterest_footer', true);
    wp_nonce_field('kwwd_save_pin_design_meta_box', 'kwwd_pinterest_designer_meta_box_nonce');

		/************************************
		 ** Set up our variables
		 ***********************************/
		  
		 $PinScript = 'kwwd-pinterest-designer.php';
		 $PinScriptFolder = 'kwwd-pinterest-designer';
		 //$PluginDirectory = KWWD_Plugin_Directory;
		 $PluginDirectory =  plugin_dir_path(__FILE__);
		 $PluginDirectoryURL =  plugin_dir_url(__FILE__);
		 $DefaultPinImageFolder = 'DefaultPins';
		 $UserPinImageFolder = 'UserPins';
		 $UserOverlays = 'UserOverlay';
		 $PinFontsFolder = 'UserFonts';
		 
		 $PinWidth = get_option('kwwd_pinterest_designer_pin_width', '800');
		 $PinHeight = get_option('kwwd_pinterest_designer_pin_height', '1200');
		 //$PinImageWidth = 769;
		 $PinImageWidth = get_option('kwwd_pinterest_designer_pin_feat_width', '769');
		 $PinImageHeight = get_option('kwwd_pinterest_designer_pin_feat_height', '515');
		 
		 $PinThumbWidth = $PinImageWidth; 
		$PinThumbHeight = $PinImageHeight; 
		$PinImageMargin = get_option('kwwd_pinterest_designer_pin_feat_margin', '27px 0 0 16px');

		$font_color = get_option('kwwd_pinterest_designer_font_color', '#FFFFFF');
		$default_font = get_option('kwwd_pinterest_designer_default_font', 'Abel-Regular');
		$default_category_font = get_option('kwwd_pinterest_designer_category_font', 'Abel-Regular');
		$category_bg_color = get_option('kwwd_pinterest_designer_category_bg', '#FFFFFF');
		$category_font_color = get_option('kwwd_pinterest_designer_category_font_color', '#000000');
		$category_border_color = get_option('kwwd_pinterest_designer_category_border', '#000000');
		
		$PinBGColor = '#000000';
		$PinImgBorderColor = '#C0C0C0';
		 
		 /** FONTS**/
		 	$availableFonts = [$PluginDirectoryURL.$PinFontsFolder.'/Abel-Regular', $PluginDirectoryURL.$PinFontsFolder.'/BebasNeue-Regular', $PluginDirectoryURL.$PinFontsFolder.'/Roboto-Condensed', $PluginDirectoryURL.$PinFontsFolder.'/Roboto-Slab', $PluginDirectoryURL.$PinFontsFolder.'/ShadowsIntoLight-Regular', $PluginDirectoryURL.$PinFontsFolder.'/Satisfy-Regular', 'Impact', 'Arial', 'Times New Roman'];	
		 
		 /************************************
		  ** Styles
		  ***********************************/
		 ?>
		 <style>
		 <?php
			foreach ($availableFonts as $font) 
			{
				if (strpos($font, $PluginDirectoryURL) !== false)	
				{
					// We need to add reference to font
					
					$fontname = $font;
					$fontname = str_replace($PluginDirectoryURL, '', $fontname);
					$fontname = str_replace($PinFontsFolder.'/', '', $fontname);
				?>
					
							 @font-face {
			font-family: '<?php echo $fontname;?>';
			src: url('<?php echo $font;?>.woff2') format('woff2'),
				 url('<?php echo $font;?>.woff') format('woff'),
				 url('<?php echo $font;?>.ttf') format('truetype'); 
		}
		<?php	
				}// END IF
			}// END for each
		 ?>

		 #PinControls {float: left; background-color: #C0C0C0; 
						width: 75%; margin: 0 20px 10px 0;}
		 #PinControls label {width: 200px}				
		#PinControls button {color: #ffffff; background-color: #009f6b; border:1px solid #009f6b; border-radius: 5px; float: right; padding: 8px; margin: 5px 0 10px 0}	
		#TemplateControls {float:left; display: block;}
		#TemplateControls label {width: 90px; display: inline-block; font-weight: bold; margin: 5px 0 5px 0}
		#TemplateControls input[type="color"]{margin: 0 0 10px 0}
		#TemplateControls select {margin: 0 0 10px 0}
		#PinHeadline {float: left}
		#SelectOptions {float: left; margin: 0 0 0 5px;}
		#SelectOptions label {width: 120px; display: inline-block; font-weight: bold; margin: 5px 0 5px 0}
		#SelectOptions select {width: 150px; margin: 5px 0 5px 0}		
						
		 #PinThumbcontainer {width:<?php echo $PinWidth;?>px; 
							height:<?php echo $PinHeight;?>px; 
							z-index: 0;
							position: relative;
							display: flex;
							flex-direction: column;	
							background-color: <?php echo $PinBGColor;?>;
							}
		 #PinThumbImage {width:<?php echo $PinThumbWidth;?>px; 
						height:<?php echo $PinThumbHeight;?>px;
						z-index: 1
						}
						
						
		#PinFeaturedImageBorder {}				
		
		#PinFeaturedImage {z-index: 2;
					width:<?php echo $PinImageWidth;?>px; 
					height:<?php echo $PinImageHeight;?>px;
					background-repeat: no-repeat;
					margin: <?php echo $PinImageMargin?>;
					box-sizing: border-box;
		}

		#PinHeadlineContainer{
						font-family: '<?php echo $default_font;?>', Arial, sans-serif;;
						z-index: 2;
						width:<?php echo $PinThumbWidth-52;?>px;
						margin: 100px 0 0 26px;
						color: <?php echo $font_color;?>;
						font-size: 80px;
						text-align: center;
						text-shadow: 2px 2px 10px #36454f;
						line-height: 75px;	
						cursor: grab; /* Indicates it's draggable */
						position: absolute; /* Allows free movement within the container */
						top: <?php echo $PinImageHeight + 50;?>px; /* Initial position */
						
		} 
		
		#PinCategory {
			z-index: 5; 
			position: absolute; /* Ensures it floats over other elements */
			top: 10px; /* Adjust position as needed */
			left: 0px; /* Adjust as needed */
			background-color: <?php echo $category_bg_color;?>; 
			color: <?php echo $category_font_color;?>;
			border:3px solid <?php $category_border_color;?>;
			font-family: '<?php echo $default_category_font;?>', Arial, sans-serif;
			display: none;
		}
		
       .CategoryTextHolder {
							font-size: 30px; 
							padding: 5px; 
							display: block; 							
							width: <?php echo $PinThumbWidth*0.4;?>px;
							cursor: grab; /* Indicates it's draggable */
							position: absolute; /* Allows free movement within the container */
							top: 10px; /* Initial position */
							}

		#PinFooter {
					cursor: grab; /* Indicates it's draggable */
					position: absolute; /* Allows free movement within the container */
					bottom: 0;
					height: 100px;
					display: inline-block;
					z-index:5;
					width:<?php echo $PinThumbWidth;?>px; 
					background-repeat: no-repeat;
					}	
						
		.ClearFix {clear:both}	


/***********************
 TAB NAVIGATION
 **********************/
	.tab-container {
		margin-bottom: 5px;
	}

	.tab-list {
		display: flex;
		padding: 0;
		margin: 0;
		list-style: none;
	}

	.tab {
		padding: 10px 20px;
		background-color: #f0f0f0;
		cursor: pointer;
		border: 1px solid #36454f;
		border-radius: 5px 5px 0 0;
		margin-right: 5px;
	}
	
	.tab a {text-decoration: none; color: #000000;}
	.tab a:hover {text-decoration: none; color: #ffffff;}

	.tab:hover {
		background-color: #5c0894;
		color: #ffffff;
	}

	.tab.active {
		background-color: #5c0894;
		color: #ffffff;
		border-bottom: none;
	}

	.tab-content {
		padding: 20px;
		border: 1px solid #5c0894;
		border-radius: 0 0 5px 5px;
	}

	.tab-pane {
		display: none;
	}

	.tab-pane.active {
		display: block;
		background-color: #ffffff;
	} 
	
/******** END TAB NAV ***********/
		
		 </style>
		 <div id="design-pinimage-tab-content" class="tab-pane active">
		 <?php
		 $featured_image = get_the_post_thumbnail_url($post->ID, 'full');
		 echo '<input type="hidden" value="'.$featured_image.'" name="FeaturedImage" id="FeaturedImage">';

		 /******************************************
		  ** Loop through user pins - if there
		  ** are none then user default
		  ******************************************/
		  echo '<div id="PinControls">'."\n";
		  echo '<div id="TemplateControls"><textarea name="PinHeadline" id="PinHeadline" cols="30" rows="5"></textarea>'."\n".'<br>';
		  		  
		  echo '<label for="PinTemplate">Template</label> <select name="PinTemplate" onChange="GrabThumb()" id="PinThumbName">'."\n";
		  echo '<option value="">-- Select Template --</option>'."\n";
		  $BlnHasUserPins = false;
		  $DefaultPinImage = '';
		  $UserIterator = new DirectoryIterator($PluginDirectory.$UserPinImageFolder);
						foreach($UserIterator as $UserFileInfo) 
						{
							if($UserFileInfo->isDot()) continue;
							if(!$UserFileInfo->isDir()) {
								$BlnHasUserPins = true;
								$PinImage = $UserFileInfo->getFilename();
								$PinDescription = str_replace('.png', '', $PinImage);
								$PinDescription = str_replace('.jpg', '', $PinDescription);
								$PinDescription = str_replace('.jpeg', '', $PinDescription);
								$PinDescription = str_replace('.gif', '', $PinDescription);
								$PinDescription = str_replace('-', ' ', $PinDescription);
								$PinDescription = str_replace('_', ' ', $PinDescription);
								$PinDescription = ucwords($PinDescription);
								echo '<option value="'.KWWD_Plugin_Directory.$UserPinImageFolder.'/'.$PinImage.'"';
								if($DefaultPinImage=='')
								{
									$DefaultPinImage = KWWD_Plugin_Directory.$UserPinImageFolder.'/'.$PinImage;
									echo ' selected';
								}
								echo '>'.$PinDescription.'</option>'."\n";
							}// End Is Not Directory Check
						}//End For
						
		if(!$BlnHasUserPins)
		{
			// Grab Default Pins
			  $DefaultIterator = new DirectoryIterator($PluginDirectory.$DefaultPinImageFolder);
						foreach($DefaultIterator as $DefaultFileInfo) 
						{
							if($DefaultFileInfo->isDot()) continue;
							if(!$DefaultFileInfo->isDir()) {
								$BlnHasUserPins = true;
								$PinImage = $DefaultFileInfo->getFilename();
								$PinDescription = str_replace('.png', '', $PinImage);
								$PinDescription = str_replace('.jpg', '', $PinDescription);
								$PinDescription = str_replace('.jpeg', '', $PinDescription);
								$PinDescription = str_replace('.gif', '', $PinDescription);
								$PinDescription = str_replace('-', ' ', $PinDescription);
								$PinDescription = str_replace('_', ' ', $PinDescription);
								$PinDescription = ucwords($PinDescription);
								echo '<option value="'.KWWD_Plugin_Directory.$DefaultPinImageFolder.'/'.$PinImage.'"';
								if($DefaultPinImage=='')
								{
									$DefaultPinImage = KWWD_Plugin_Directory.$DefaultPinImageFolder.'/'.$PinImage;
									echo ' selected';
								}
								echo '>'.$PinDescription.'</option>'."\n";
							}// End Is Not Directory Check
						}//End For
		}	
		  
		  echo '</select>'."\n".'<br>';
		  
		  /**** Or Use Colour BG ****/
		  
		echo '<label for="PinBGColor">BG/Border</label><input type="color" id="PinBGColor" value="'.$PinBGColor.'">'."\n";
		echo '<input type="color" id="PinImgBorderColor" value="'.$PinImgBorderColor.'"><br>';
		echo '<label for="ShowImageBorder">Show Border</label><select name="ShowImageBorder" id="ShowImageBorder" onChange="ShowHideBorder()"><option value="0">No</option><option value="1">Yes</option></select><br>';
		 
		 
		 /*****************************************
		  ** USER FOOTER OVERLAY
		  ****************************************/
		 $DefaultFooterImage = '';
		 echo '<label for="UserFooterOverlay">Footer</label> <select name="UserFooterOverlay" id="UserFooterOverlay" onChange="GrabFooter()">'; 
		  echo '<option value="">-- Select Footer --</option>'."\n";
			// Grab Default Pins
			  $FooterIterator = new DirectoryIterator($PluginDirectory.$UserOverlays);
						foreach($FooterIterator as $FooterFileInfo) 
						{
							if($FooterFileInfo->isDot()) continue;
							if(!$FooterFileInfo->isDir()) {
								$FooterImage = $FooterFileInfo->getFilename();
								$FooterDescription = str_replace('.png', '', $FooterImage);
								$FooterDescription = str_replace('.jpg', '', $FooterDescription);
								$FooterDescription = str_replace('.jpeg', '', $FooterDescription);
								$FooterDescription = str_replace('.gif', '', $FooterDescription);
								$FooterDescription = str_replace('-', ' ', $FooterDescription);
								$FooterDescription = str_replace('_', ' ', $FooterDescription);
								$FooterDescription = ucwords($FooterDescription);
								echo '<option value="'.KWWD_Plugin_Directory.$UserOverlays.'/'.$FooterImage.'"';
								if($DefaultFooterImage=='')
								{
									$DefaultFooterImage = KWWD_Plugin_Directory.$UserOverlays.'/'.$FooterImage;
									echo ' selected';
								}
								echo '>'.$FooterDescription.'</option>'."\n";
							}// End Is Not Directory Check
						}//End For
		  
		  echo '</select>'."\n".'<br>';
		  
		  echo '<label for="PinFont">Pin Font</label><select name="PinFont" id="PinFont" onChange="SwapFont(\'PinHeadlineContainer\', \'PinFont\')">'."\n";
		  echo '<option value="">-- Select Font --</option>'."\n";
		  $SelectedText = '';
			foreach ($availableFonts as $font) 
			{
					$Pinfontname = $font;
					$Pinfontname = str_replace($PluginDirectoryURL, '', $Pinfontname);
					$Pinfontname = str_replace($PinFontsFolder.'/', '', $Pinfontname);
					$SelectedText = ($Pinfontname === $default_font) ? 'selected' : '';
					$fontDisplayname = $font;
					$fontDisplayname = str_replace($PluginDirectoryURL, '', $fontDisplayname);
					$fontDisplayname = str_replace($PinFontsFolder.'/', '', $fontDisplayname);
					$fontDisplayname = str_replace('-', ' ', $fontDisplayname);
					//$fontDisplayname = $font;
					echo '<option value="'.$Pinfontname.'" '.$SelectedText.'>'.$fontDisplayname.'</option>'."\n";
			}
		  echo '</select><br>'."\n";
		  echo '<label for="PinColor">Font</label><input type="color" id="PinColor" value="'.$font_color.'"><br>'."\n";
		  echo '</div><!--END Template Controls-->';
		  echo '<div id="SelectOptions"><label>Category</label><input type="text" name="PinCategoryText" id="PinCategoryText">'."\n".'<br>';
		  
		  echo '<label for="CategoryBorder">Border/BG/Font</label><input type="color" id="CategoryBorder" value="'.$category_border_color.'"><input type="color" id="CategoryBG" value="'.$category_bg_color.'"><input type="color" id="CategoryFC" value="'.$category_font_color.'"><br>'."\n";
		  
		  echo '<label for="CategoryAlign">Align</label><select name="CategoryAlign" id="CategoryAlign" onChange="AlignCategory()"><option value="left" selected>Left</option><option value="center">Center</option><option value="right">Right</option></select><br>'."\n";
		  
		  echo '<label for="CategoryWidth">Width</label><select name="CategoryWidth" id="CategoryWidth" onChange="ChangeCategoryWidth()"><option value="50">50</option><option value="100">100</option><option value="150">150</option><option value="200" selected>200</option><option value="250">250</option><option value="300">300</option><option value="350">350</option><option value="400">400</option><option value="450">450</option><option value="500">500</option><option value="550">550</option><option value="600">600</option><option value="6500">650</option><option value="700">700</option><option value="750">750</option><option value="800">800</option></select><br>'."\n";
		  
		  
		  echo '<label for="CategoryFont">Category Font</label><select name="CategoryFont" id="CategoryFont" onChange="SwapFont(\'PinCategory\', \'CategoryFont\')">'."\n";
		  echo '<option value="">-- Select Font --</option>'."\n";
		  $SelectedText = '';
			foreach ($availableFonts as $font) 
			{
					$fontname = $font;
					$fontname = str_replace($PluginDirectoryURL, '', $fontname);
					$fontname = str_replace($PinFontsFolder.'/', '', $fontname);
					$SelectedText = ($fontname === $default_category_font) ? 'selected' : '';
					$fontDisplayname = str_replace('-', ' ', $fontname);
					echo '<option value="'.$fontname.'" '.$SelectedText.'>'.$fontDisplayname.'</option>'."\n";
			}
		  echo '</select><br>'."\n";
		  
?>
		 <button onclick="generateImage()" type="button">SAVE</button>
		 </div><!--END SelectOptions-->
		 <div class="ClearFix"></div>
		 </div><!--END Controls-->
		 <div class="ClearFix"></div>
		 <div id="PinThumbcontainer">
			<div id="PinCategory" class="TempCatClass"></div>		 
			<div id="PinFeaturedImageBorder"><div id="PinFeaturedImage"></div></div>
			<div id="PinHeadlineContainer"></div>
			<div id="PinFooter"></div>
		 </div>
		 <div class="ClearFix"></div>
</div><!--END design-pinimage-tab-content-->
<div id="upload-custompin-tab-content" class="tab-pane">
<?php
/************************************************
 ** UPLOAD CUSTOM PIN STUFF
 ***********************************************/
 // Add nonce for security and authentication.
    wp_nonce_field('custom_pinterest_image_metabox_nonce', 'custom_pinterest_image_metabox_nonce');

    // Get the existing meta value (if any).
    $image_id = get_post_meta($post->ID, 'custom_pinterest_image_id', true);
    $thumbnail_url = plugins_url( 'img/holding.jpg', __FILE__ );
	$blnHasPinImage = false;
	
    if ($image_id) {
        $thumbnail = wp_get_attachment_image_src($image_id, 'thumbnail');
        if ($thumbnail) {
            $thumbnail_url = $thumbnail[0];
			$blnHasPinImage = true;
        }
    }
    ?>
		<div style="float:left; width: 200px">		
			<img id="custom_pinterest_image" src="<?php echo esc_attr($thumbnail_url); ?>" style="max-width:100%;" />
		</div>
    <div style="float:left; margin: 0 0 0 30px; ">
		<input type="button" id="custom_pinterest_image_upload_button" class="button" value="<?php _e('Upload Image'); ?>" style="display:visible"/><br><br>
		<div id="ImageManagement" style="display:none">
        <?php //if (!empty($thumbnail_url)) : ?>
            <button type="button" id="edit_custom_pinterest_image_button" class="button"><?php _e('Edit Image'); ?></button><br><br>
           <button type="button" id="remove_custom_pinterest_image_button" class="button"><?php _e('Remove Image'); ?></button>
        <?php// endif; ?>
		</div>
		    <input type="hidden" id="custom_pinterest_image_id" name="custom_pinterest_image_id" value="<?php echo esc_attr($image_id); ?>" />
    </div>
	<div style="clear:both"></div>
</div><!-- END upload-custompin-tab-content-->	
</div><!-- END tab-content-->	
<?php
 
 /***********************************************
  ** JAVASCRIPT
  **********************************************/
?>		 
		 
		 
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
		<script src="<?php echo KWWD_Plugin_Directory;?>html2canvas.min.js"></script>
		 <script>
		// Load Festured Image on Page Load
		document.addEventListener("DOMContentLoaded", function() {
			var FeaturedImage = document.getElementById("FeaturedImage").value;
			var FeaturedContainer = document.getElementById("PinFeaturedImage");
			FeaturedContainer.style.backgroundImage = 'url(\''+FeaturedImage+'\')';
			// Set a default Pin Background
			var ThumbContainer = document.getElementById("PinThumbcontainer");
			ThumbContainer.style.backgroundImage = 'url(\'<?php echo $DefaultPinImage;?>\')';
			// Set default footer overlay
			var FooterContainer = document.getElementById("PinFooter");
			FooterContainer.style.backgroundImage = 'url(\'<?php echo $DefaultFooterImage;?>\')';
			// Set Default Pin Headline
			var PinHeadlineText = document.getElementById('title').value;
			var PinHeadlineContainer = document.getElementById('PinHeadlineContainer');
			PinHeadlineText = PinHeadlineText.replace(/\n/g, '<br>');	
			PinHeadlineContainer.innerHTML = PinHeadlineText;
			PinHeadlineContainer.style.fontFamily = 'Abel-Regular';
			PinHeadlineContainer.style.lineHeight  = '75px';
			var UserHeadline = document.getElementById('PinHeadline');
			UserHeadline.value = PinHeadlineText;
		});
		
		// Tab Code
		function switchTab(tabId) 
		{
			// Hide all tab panes
			const tabs = document.querySelectorAll('.tab-pane');
			tabs.forEach(tab => tab.classList.remove('active'));

			// Remove active class from all tabs
			const tabLinks = document.querySelectorAll('.tab');
			tabLinks.forEach(tab => tab.classList.remove('active'));

			// Show the selected tab content
			document.getElementById(tabId + '-content').classList.add('active');

			// Add the active class to the clicked tab
			document.getElementById(tabId).classList.add('active');
		}
		 
		 // Update Pin Headline Text
		 document.getElementById('PinHeadline').onkeyup = function()
		{
			var PinHeadlineText = document.getElementById('PinHeadline').value;
			var PinHeadlineContainer = document.getElementById('PinHeadlineContainer');
			PinHeadlineText = PinHeadlineText.replace(/\n/g, '<br>');	
			PinHeadlineContainer.innerHTML = PinHeadlineText;
		}
		 // Update Pin Category Text
		 document.getElementById('PinCategoryText').onkeyup = function()
		{	
			var PinCategoryText = document.getElementById('PinCategoryText').value;
			var PinCategoryContainer = document.getElementById('PinCategory');
			PinCategoryText = PinCategoryText.replace(/\n/g, '<br>');	
			PinCategoryContainer.innerHTML = PinCategoryText;
			if(PinCategoryText!='')
			{
				document.getElementById('PinCategory').style.display = 'block';
				PinCategoryContainer.classList.add("CategoryTextHolder")  = 'CategoryTextHolder'; 
			}
			else
			{
				document.getElementById('PinCategory').style.display = 'none';
				PinCategoryContainer.classList.remove("CategoryTextHolder")  = 'CategoryTextHolder';
				
			}
		}
		
				// Font Colour
        document.getElementById('PinColor').addEventListener('input', (event) => {
			var PinColor = document.getElementById('PinColor').value;
			document.getElementById("PinHeadlineContainer").style.color  = PinColor;
        });
		// BG Color
		document.getElementById('PinBGColor').addEventListener('input', (event) => {
			var PinBGColor = document.getElementById('PinBGColor').value;
			document.getElementById("PinThumbcontainer").style.backgroundColor  = PinBGColor;
        });	
		// Feat Border
		document.getElementById('PinImgBorderColor').addEventListener('input', (event) => {
			var PinImgBorderColor = document.getElementById('PinImgBorderColor').value;
			//document.getElementById("PinFeaturedImage").style.borderColor  = PinImgBorderColor;
			document.getElementById("PinFeaturedImage").style.border  = '5px solid '+PinImgBorderColor;
        });	
		
		function ShowHideBorder()
		{
			var BorderState = document.getElementById('ShowImageBorder').value;
			if(BorderState==1)
			{
				var PinImgBorderColor = document.getElementById('PinImgBorderColor').value;
				document.getElementById("PinFeaturedImage").style.border  = '5px solid '+PinImgBorderColor;m
				document.getElementById("PinFeaturedImage").style.marginLeft = '-15px !important';
			}
			else
			{
				document.getElementById("PinFeaturedImage").style.borderStyle   = 'none';
				 
			}	
		}

		// Category Stuff
		// Border Colour
        document.getElementById('CategoryBorder').addEventListener('input', (event) => {
			var BorderColour = document.getElementById('CategoryBorder').value;
			document.getElementById("PinCategory").style.borderColor  = BorderColour;
        });	
		// Background Clour
        document.getElementById('CategoryBG').addEventListener('input', (event) => {
			var BGColour = document.getElementById('CategoryBG').value;
			document.getElementById("PinCategory").style.backgroundColor  = BGColour;			
        });			
		// Font Colour
        document.getElementById('CategoryFC').addEventListener('input', (event) => {
			var FontColour = document.getElementById('CategoryFC').value;
			document.getElementById("PinCategory").style.color  = FontColour;			
        });
		// Category Text Alignment
		function AlignCategory()
		{
			var FontAlign = document.getElementById('CategoryAlign').value;	
			document.getElementById("PinCategory").style.textAlign  = FontAlign;	
		}
		
		// Category Width
		function ChangeCategoryWidth()
		{
			var CategoryWidth = document.getElementById('CategoryWidth').value;	
			document.getElementById("PinCategory").style.width  = CategoryWidth+'px';
		}
		
		 // Update Pin Thumbnail
		 function GrabThumb()
		 {
			var SelectedPin = document.getElementById("PinThumbName").value;
			var ThumbContainer = document.getElementById("PinThumbcontainer");
			ThumbContainer.style.backgroundImage = 'url(\''+SelectedPin+'\')';
		 }

		 // Update Pin Footer
		 function GrabFooter()
		 {
			var SelectedFooter = document.getElementById("UserFooterOverlay").value;
			var FooterContainer = document.getElementById("PinFooter");
			FooterContainer.style.backgroundImage = 'url(\''+SelectedFooter+'\')';
		 }


		 // Update Featured Image
		 function SetFeaturedImage(NewImage)
		 {
			var FeaturedImageContainer = document.getElementById("PinFeaturedImage");
			FeaturedImageContainer.style.backgroundImage = 'url(\''+NewImage+'\')';
		 }
		 // Remove Featured Image
		 function RemoveFeaturedImage()
		 {
			var FeaturedImageContainer = document.getElementById("PinFeaturedImage");
			FeaturedImageContainer.style.backgroundImage = 'none';
		 }
		 
			// Make Headline Draggable
			$(document).ready(function() {
				$("#PinHeadlineContainer").draggable({
					axis: "y", // Restrict movement to vertical only
					containment: "#PinThumbcontainer" // Keep it within the main container
				});
			});

			// Make Category Draggable
			$(document).ready(function() {
				$("#PinCategory").draggable({
					//axis: "y", // Restrict movement to vertical only
					containment: "#PinThumbcontainer" // Keep it within the main container
				});
			});
			
			// Make Footer Overlay Draggable
			$(document).ready(function() {
				$("#PinFooter").draggable({
					//axis: "y", // Restrict movement to vertical only
					containment: "#PinThumbcontainer" // Keep it within the main container
				});
			});

	// Swap Font
	
	function SwapFont(TextToChange, FontField)
		{
			var TextContainer = document.getElementById(TextToChange);
			var TheFont = document.getElementById(FontField).value;
			if(TextToChange=='PinHeadlineContainer')
			{
				if(TheFont=='Satisfy-Regular')
				{
					TextContainer.style.lineHeight = "100px";				
				}
				else if(TheFont == 'ShadowsIntoLight-Regular')
				{
					TextContainer.style.lineHeight = "90px";	
				}
				else
				{
					TextContainer.style.lineHeight = "75px";
				}
			}
			TextContainer.style.fontFamily = TheFont;
		}

		// HTML CANVAS - Save Image
		function generateImage() {
			
			console.log(html2canvas);
			var imageContainer = document.getElementById('PinThumbcontainer');
			
			var TempFileName = document.getElementById("PinHeadline").value;
			TempFileName = TempFileName.replace(/\s+/g, '-');
			
			let TempSaveName = prompt('Name Your Image', 'Pin-'+TempFileName);
			
			ImageSaveName = TempSaveName.replace(/\s+/g, '-');

			//html2canvas(imageContainer).then(function (canvas) {
			html2canvas(imageContainer, { scale: 1 }).then(function (canvas) {
				// Convert the canvas to a PNG data URL
				var dataUrl = canvas.toDataURL("image/png");

				// Create a temporary link element
				var link = document.createElement('a');
				link.href = dataUrl;

				// Set the download attribute with the desired file name
				link.download = ImageSaveName;

				// Trigger the download by simulating a click event
				link.click();
			}).catch(function(error) {
				console.error('Error generating the image', error);
			});
		}
	
	
	// Set Featured Image On Pin
jQuery(document).ready(function ($) {
    function fetchFeaturedImage() {
        var imageURL = $('#set-post-thumbnail img').attr('src'); // Get the new image URL
        if (imageURL) {
           // $('#PinFeaturedImage').attr('src', imageURL);
		   SetFeaturedImage(imageURL);
        } else {
            //$('#PinFeaturedImage').attr('src', '');
			RemoveFeaturedImage();
        }
    }

    // Use MutationObserver to watch for changes in the postimagediv
    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.type === "childList" || mutation.type === "attributes") {
                fetchFeaturedImage();
            }
        });
    });

    // Observe changes in the featured image container
    var targetNode = document.getElementById("postimagediv");
    if (targetNode) {
        observer.observe(targetNode, { childList: true, subtree: true, attributes: true });
    }
});

// Upload Custom Pin Scripts
// $blnHasPinImage VALUE: <?php echo $blnHasPinImage;?>

<?php
	if($blnHasPinImage)
		//Show the edit controls if we have an image assigned and not the holding image
	// And swap tthe tab to the upload tab
	{
	?>
	jQuery(document).ready(function($){
	$('#ImageManagement').attr('style', '');
	$('#custom_pinterest_image_upload_button').attr('style', 'display:none');
	
	// Swap tab
	switchTab('upload-custompin-tab');
});
<?php
	}
	?>
    jQuery(document).ready(function($){
        // Edit Image button functionality
        $('#edit_custom_pinterest_image_button').click(function(){
            var custom_pinterest_image_frame;
            if (custom_pinterest_image_frame) {
                custom_pinterest_image_frame.open();
                return;
            }

            custom_pinterest_image_frame = wp.media.frames.customHeader = wp.media({
                title: 'Edit Image',
                button: { text: 'Edit Image' },
                multiple: false
            });

            custom_pinterest_image_frame.on('select', function() {
                var attachment = custom_pinterest_image_frame.state().get('selection').first().toJSON();
                $('#custom_pinterest_image_id').val(attachment.id);
                var thumbnailUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                $('#custom_pinterest_image').attr('src', thumbnailUrl);
            });

            custom_pinterest_image_frame.open();
        });
        // Remove Image button functionality
    });

</script>
 <?php 

} // END kwwd_pinterest_designer_callback



/*********************************************
 ** END UPLOAD IMAGE STUFF
 ********************************************/

/********************************************************
 ** Upload Custom Pinterest Image
 *******************************************************/
// SAVE
function save_custom_pinterest_image_metabox($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['custom_pinterest_image_metabox_nonce'])) {
        return;
    }

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['custom_pinterest_image_metabox_nonce'], 'custom_pinterest_image_metabox_nonce')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions.
    if ('page' === $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // Make sure that it is set.
    if (!isset($_POST['custom_pinterest_image_id'])) {
        return;
    }

    // Sanitize user input.
    $image_id = sanitize_text_field($_POST['custom_pinterest_image_id']);

    // Update the meta field.
    update_post_meta($post_id, 'custom_pinterest_image_id', $image_id);

    // Generate thumbnails.
    if ($image_id) {
        $metadata = wp_generate_attachment_metadata($image_id, get_attached_file($image_id));
        wp_update_attachment_metadata($image_id, $metadata);
    }
}
add_action('save_post', 'save_custom_pinterest_image_metabox');


/********************************************
 ** Add Pinterest Image before Post Content
 ** ON Post Page
 *******************************************/
 
// Define function to retrieve and display image
function display_pinterest_image($content) {
 // Retrieve post ID
  $post_id = get_the_ID();
  $modified_content = '';

  // Retrieve image ID from meta field (replace with your logic)
  $custom_pinterest_image_id = get_post_meta($post_id, 'custom_pinterest_image_id', true);

  // Retrieve image URL if ID exists
  $custom_pinterest_image_url = wp_get_attachment_url($custom_pinterest_image_id);

  if ($custom_pinterest_image_url) {
    // Retrieve alt text
    $custom_pinterest_image_alt_text = get_post_meta($custom_pinterest_image_id, '_wp_attachment_image_alt', true);

    // Build the modified content with image and original content
	$modified_content .= ''.PHP_EOL;
	$modified_content .= '<!-- Custom Hidden Image For Pinterest -->'.PHP_EOL;
    $modified_content .= '<img src="' . esc_url($custom_pinterest_image_url) . '" alt="' . esc_url($custom_pinterest_image_url) . '" class="my-pinterest-image" style="display:none">' . PHP_EOL;
	// Save Pin Button
	$modified_content .= '<!-- END Custom Hidden Image For Pinterest -->'.PHP_EOL;
	$modified_content .= $content.PHP_EOL;
	
  } else {
    // Handle situations where no image is found (optional)
    $modified_content = $content; // Keep original content if no image
  }

  // Return the modified content
  return $modified_content;
}

// Hook the function to the_content filter
add_filter('the_content', 'display_pinterest_image');



/********************************************
/** ENQUEUE ADMIN JS 
 *******************************************/

function custom_pinterest_image_upload_enqueue_script() {
	$QS = '150x150';
	$dimensions_query = http_build_query(array('thumbnailDimensions' => $QS));
	wp_enqueue_script( 'custom-image-upload', plugins_url( 'js/adminscripts.js', __FILE__ ), array(), null, true );
	wp_localize_script('my-plugin-featured-image', 'myPluginAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'custom_pinterest_image_upload_enqueue_script');

/********************************************
 ** ADMIN PAGE THINGS
 *******************************************/
 
function kwwd_pinterest_designer_admin_menu() {
    add_menu_page(
        'Pin Designer Customisation', // Page title
        'Pin Designer',          // Menu title
        'manage_options',     // Capability
        'kwwd-pinterest-designer-settings',  // Menu slug
        'kwwd_pinterest_designer_settings_page' // Callback function to render the page
    );
}
add_action('admin_menu', 'kwwd_pinterest_designer_admin_menu');
 
 
function kwwd_pinterest_designer_settings_page() {
    // Get current saved values
    $font_color = get_option('kwwd_pinterest_designer_font_color', '#FFFFFF');
    $default_font = get_option('kwwd_pinterest_designer_default_font', 'Abel-Regular');
	$default_category_font = get_option('kwwd_pinterest_designer_category_font', 'Abel-Regular');
	$category_bg_color = get_option('kwwd_pinterest_designer_category_bg', '#FFFFFF');
	$category_font_color = get_option('kwwd_pinterest_designer_category_font_color', '#FFFFFF');
	$category_border_color = get_option('kwwd_pinterest_designer_category_border', '#000000');
	$PinWidth = get_option('kwwd_pinterest_designer_pin_width', '800');
	$PinHeight = get_option('kwwd_pinterest_designer_pin_height', '1200');
	$PinImageWidth = get_option('kwwd_pinterest_designer_pin_feat_width', '769');
	$PinImageHeight = get_option('kwwd_pinterest_designer_pin_feat_height', '515');
	$PinImageMargin = get_option('kwwd_pinterest_designer_pin_feat_margin', '27px 0 0 16px');

    // Available fonts
    $availableFonts = ['Abel-Regular', 'BebasNeue-Regular', 'Roboto-Condensed', 'Roboto-Slab', 'ShadowsIntoLight-Regular', 'Satisfy-Regular', 'Impact', 'Arial', 'Times New Roman'];
    
    // Get uploaded images from 'UserPins' directory
    $upload_dir = plugin_dir_path(__FILE__) . 'UserPins/';
    $image_urls = glob($upload_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    $image_urls = array_map('basename', $image_urls);
	
	
	$overlay_upload_dir = plugin_dir_path(__FILE__) . 'UserOverlay/';
    $overlay_image_urls = glob($overlay_upload_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    $overlay_image_urls = array_map('basename', $overlay_image_urls);

    ?>
    <div class="wrap">
        <h2>Pinterest Designer Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('kwwd_pinterest_designer_settings_group'); ?>
            <?php do_settings_sections('kwwd-pinterest-designer-settings'); ?>
            
            <!-- Font Color Picker -->
            <label for="kwwd_pinterest_designer_font_color"><strong>Font Color:</strong></label>
            <input type="text" id="kwwd_pinterest_designer_font_color" name="kwwd_pinterest_designer_font_color" value="<?php echo esc_attr($font_color); ?>" class="my-color-field" />
			<input type="color" value="<?php echo esc_attr($font_color); ?>" name="DefaultPinFontColor" id="DefaultPinFontColor">
            <br><br>

            <!-- Default Font Dropdown -->
            <label for="kwwd_pinterest_designer_default_font"><strong>Default Font:</strong></label>
            <select name="kwwd_pinterest_designer_default_font" id="kwwd_pinterest_designer_default_font">
                <?php foreach ($availableFonts as $font): ?>
                    <option value="<?php echo esc_attr($font); ?>" <?php selected($default_font, $font); ?>>
                        <?php echo esc_html(str_replace('-', ' ', $font)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>
            <!-- Category Font Color Picker -->
            <label for="kwwd_pinterest_designer_category_font_color"><strong>Category Font Color:</strong></label>
            <input type="text" id="kwwd_pinterest_designer_category_font_color" name="kwwd_pinterest_designer_category_font_color" value="<?php echo esc_attr($category_font_color); ?>" class="my-color-field" />
			<input type="color" value="<?php echo esc_attr($category_font_color); ?>" name="DefaultCatFontColor" id="DefaultCatFontColor">
            <br><br>	
            <!-- Default Category Font Dropdown -->
            <label for="kwwd_pinterest_designer_category_font"><strong>Default Caategory Font:</strong></label>
            <select name="kwwd_pinterest_designer_category_font" id="kwwd_pinterest_designer_category_font">
                <?php foreach ($availableFonts as $font): ?>
                    <option value="<?php echo esc_attr($font); ?>" <?php selected($default_category_font, $font); ?>>
                        <?php echo esc_html(str_replace('-', ' ', $font)); ?>
                    </option>
                <?php endforeach; ?>
            </select>			
			<br><br>
			<!-- Category BG Color Picker -->
            <label for="kwwd_pinterest_designer_category_bg"><strong>Category BG Color:</strong></label>
            <input type="text" id="kwwd_pinterest_designer_category_bg" name="kwwd_pinterest_designer_category_bg" value="<?php echo esc_attr($category_bg_color); ?>" class="my-color-field" />
			<input type="color" value="<?php echo esc_attr($category_bg_color); ?>" name="DefaultCatBGColor" id="DefaultCatBGColor">
            <br><br>
			<!-- Category Border Color Picker -->
            <label for="kwwd_pinterest_designer_category_border"><strong>Category Border Color:</strong>:</label>
            <input type="text" id="kwwd_pinterest_designer_category_border" name="kwwd_pinterest_designer_category_border" value="<?php echo esc_attr($category_border_color); ?>" class="my-color-field" />
			<input type="color" value="<?php echo esc_attr($category_border_color); ?>" name="DefaultCatBorderColor" id="DefaultCatBorderColor">
            <br><br>
			<label for="kwwd_pinterest_designer_pin_width"><strong>Pin Width</strong>:</label>
			<input type="text" value="<?php echo $PinWidth;?>" name="kwwd_pinterest_designer_pin_width" id="kwwd_pinterest_designer_pin_width"><br><br>
			<label for="kwwd_pinterest_designer_pin_height"><strong>Pin Height</strong>:</label>
			<input type="text" value="<?php echo $PinHeight;?>" name="kwwd_pinterest_designer_pin_height" id="kwwd_pinterest_designer_pin_height"><br><br>
			<label for="kwwd_pinterest_designer_pin_feat_width"><strong>Main Image Width</strong>:</label>
			<input type="text" value="<?php echo $PinImageWidth;?>" name="kwwd_pinterest_designer_pin_feat_width" id="kwwd_pinterest_designer_pin_feat_width"><br><br>
			<label for="kwwd_pinterest_designer_pin_feat_height"><strong>Main Image Height</strong>:</label>
			<input type="text" value="<?php echo $PinImageHeight;?>" name="kwwd_pinterest_designer_pin_feat_height" id="kwwd_pinterest_designer_pin_feat_height"><br><br>
			<label for="kwwd_pinterest_designer_pin_feat_margin"><strong>Main Image Margin</strong>:</label>
			<input type="text" value="<?php echo $PinImageMargin;?>" name="kwwd_pinterest_designer_pin_feat_margin" id="kwwd_pinterest_designer_pin_feat_margin"><br><br>
           
			<!-- Save Button -->
            <input type="submit" value="Save Changes" class="button-primary">
        </form>
<script>
// Update Colour

		// Main font color
        document.getElementById('DefaultPinFontColor').addEventListener('input', (event) => {
			var TXTColour = document.getElementById('DefaultPinFontColor').value;
			document.getElementById("kwwd_pinterest_designer_font_color").value  = TXTColour;			
        });	
		// Category Font Color
		        document.getElementById('DefaultCatFontColor').addEventListener('input', (event) => {
			var CATColour = document.getElementById('DefaultCatFontColor').value;
			document.getElementById("kwwd_pinterest_designer_category_font_color").value  = CATColour;			
        });
		// Category BG Color
		        document.getElementById('DefaultCatBGColor').addEventListener('input', (event) => {
			var BGColour = document.getElementById('DefaultCatBGColor').value;
			document.getElementById("kwwd_pinterest_designer_category_bg").value  = BGColour;			
        });			
		// Category Border Color
		        document.getElementById('DefaultCatBorderColor').addEventListener('input', (event) => {
			var BorderColour = document.getElementById('DefaultCatBorderColor').value;
			document.getElementById("kwwd_pinterest_designer_category_border").value  = BorderColour;			
        });	
</script>
        <hr>

        <!-- Image Upload Form -->
        <h3>Upload an Image</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="kwwd_pinterest_designer_image" accept="image/png, image/jpeg, image/gif">
            <input type="submit" name="upload_image" value="Upload" class="button">
        </form>

        <?php
        // Handle image upload
        if (isset($_POST['upload_image']) && !empty($_FILES['kwwd_pinterest_designer_image']['name'])) {
            kwwd_pinterest_designer_handle_image_upload();
        }

        // Display Uploaded Images
        if (!empty($image_urls)) {
            echo '<h3>Uploaded Images</h3><div style="display: flex; gap: 10px; flex-wrap: wrap;">';
            foreach ($image_urls as $image) {
                echo '<div><img src="'.plugins_url('UserPins/'.$image, __FILE__).'" width="100"><br>' . esc_html($image) . '</div>';
            }
            echo '</div>';
        }
        ?>
		
		        <!-- Footer Upload Form -->
        <h3>Upload a Footer Overlay</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="kwwd_pinterest_designer_image_overlay" accept="image/png, image/jpeg, image/gif">
            <input type="submit" name="upload_image_overlay" value="Upload" class="button">
        </form>

        <?php
        // Handle image upload
        if (isset($_POST['upload_image_overlay']) && !empty($_FILES['kwwd_pinterest_designer_image_overlay']['name'])) {
            kwwd_pinterest_designer_handle_overlay_upload();
        }

        // Display Uploaded Overlays
        if (!empty($overlay_image_urls)) {
            echo '<h3>Uploaded Overlays</h3><div style="display: flex; gap: 10px; flex-wrap: wrap;">';
            foreach ($overlay_image_urls as $overlay_image) {
                echo '<div><img src="'.plugins_url('UserOverlay/'.$overlay_image, __FILE__).'" width="100"><br>' . esc_html($overlay_image) . '</div>';
            }
            echo '</div>';
        }
    echo '</div>';
}
 
function kwwd_pinterest_designer_register_settings() {
    register_setting('kwwd_pinterest_designer_settings_group', 'kwwd_pinterest_designer_font_color');
    register_setting('kwwd_pinterest_designer_settings_group', 'kwwd_pinterest_designer_default_font');
	register_setting('kwwd_pinterest_designer_settings_group', 'kwwd_pinterest_designer_category_font_color');
	register_setting('kwwd_pinterest_designer_settings_group', 'kwwd_pinterest_designer_category_font');
	register_setting('kwwd_pinterest_designer_settings_group', 'kwwd_pinterest_designer_category_bg');
	register_setting('kwwd_pinterest_designer_settings_group', 'kwwd_pinterest_designer_category_border');
	register_setting('kwwd_pinterest_designer_settings_group', 'kwwd_pinterest_designer_pin_width');
	register_setting('kwwd_pinterest_designer_settings_group', 'kwwd_pinterest_designer_pin_height');
	register_setting('kwwd_pinterest_designer_settings_group', 'kwwd_pinterest_designer_pin_feat_width');
	register_setting('kwwd_pinterest_designer_settings_group', 'kwwd_pinterest_designer_pin_feat_height');
	register_setting('kwwd_pinterest_designer_settings_group', 'kwwd_pinterest_designer_pin_feat_margin');
}
add_action('admin_init', 'kwwd_pinterest_designer_register_settings');


function kwwd_pinterest_designer_handle_image_upload() {
    if (!isset($_FILES['kwwd_pinterest_designer_image'])) return;

    $file = $_FILES['kwwd_pinterest_designer_image'];
    $allowed_types = ['image/png', 'image/jpeg', 'image/gif'];

    // Check file type
    if (!in_array($file['type'], $allowed_types)) {
        echo '<div class="error"><p>Invalid file type. Only PNG, JPG, and GIF are allowed.</p></div>';
        return;
    }

    // Create directory if it doesn't exist
    $upload_dir = plugin_dir_path(__FILE__) . 'UserPins/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Move uploaded file
    $file_path = $upload_dir . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        echo '<div class="updated"><p>Image uploaded successfully.</p></div>';
    } else {
        echo '<div class="error"><p>Image upload failed.</p></div>';
    }
}


function kwwd_pinterest_designer_handle_overlay_upload() {
    if (!isset($_FILES['kwwd_pinterest_designer_image_overlay'])) return;

    $file = $_FILES['kwwd_pinterest_designer_image_overlay'];
    $allowed_types = ['image/png', 'image/jpeg', 'image/gif'];

    // Check file type
    if (!in_array($file['type'], $allowed_types)) {
        echo '<div class="error"><p>Invalid file type. Only PNG, JPG, and GIF are allowed.</p><p>You uploaded '.$file['type'].'</div>';
        return;
    }

    // Create directory if it doesn't exist
    $upload_dir = plugin_dir_path(__FILE__) . 'UserOverlay/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Move uploaded file
    $file_path = $upload_dir . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        echo '<div class="updated"><p>Image uploaded successfully.</p></div>';
    } else {
        echo '<div class="error"><p>Image upload failed.</p></div>';
    }
}

/************************************************
 ** Github Update Code
 ***********************************************/
 
if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'github-updater.php';
    new GitHub_Plugin_Updater(__FILE__, 'KWWDCoding', 'kwwd-pinterest-designer');
} 

?>