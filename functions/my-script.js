/*jQuery(document).ready(function() {
	jQuery('#upload_image_btn').click(function() {
		formfield = jQuery('#upload_image').attr('name');
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	});

	window.send_to_editor = function(html) {
		imgurl = jQuery('img',html).attr('src');
		jQuery('#upload_image').val(imgurl);
		tb_remove();
	}
});*/
jQuery(document).ready(function() {
	var targetfield = null;
    jQuery('.st_upload_button').click(function() {
         targetfield = jQuery(this).prev('.upload-url');
         tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
         return false;
    });
    window.send_to_editor = function(html) {
         imgurl = jQuery('img',html).attr('src');
         jQuery(targetfield).val(imgurl);
         tb_remove();
    }
});