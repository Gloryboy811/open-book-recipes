jQuery(function($){
	/*
	 * Select/Upload image(s) event
	 */
	$('body').on('click', '.recipe_upload_image_button', function(e){
		e.preventDefault();
 
    		var button = $(this),
    		    custom_uploader = wp.media({
			title: 'Insert image',
			library : {
				// uncomment the next line if you want to attach image to the current post
				// uploadedTo : wp.media.view.settings.post.id, 
				type : 'image'
			},
			button: {
				text: 'Use this image' // button label text
			},
			multiple: true // for multiple image selection set to true
		}).on('select', function() { // it also has "open" and "close" events 
			// var attachment = custom_uploader.state().get('selection').first().toJSON();
			// $(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:95%;display:block;" />').next().val(attachment.id).next().show();
			// if you sen multiple to true, here is some code for getting the image IDs
			var attachments = custom_uploader.state().get('selection'),
			    attachment_ids = new Array(),
				i = 0;
				$('#recipe-images').html('');
			attachments.each(function(first, i) {
				var attachment = first.toJSON()
				//  attachment_ids[i] = attachment['id'];
				//  
				// console.log( attachment );
				$('#recipe-images').append(
					`<li id="rb-gallery-${attachment.id}">
						<img src="${attachment.sizes.thumbnail.url}" alt="" style="max-width:100%;"/>
						<input type="hidden" name="rb_gallery[${i}]" value="${attachment.id}" />
						<a href="#" data-rbgid="${attachment.id}" class="recipe_remove_image_button">Remove</a>
					</li>`
				);

				
				// i++;
			});	
		})
		.open();
	});
 

	$('body').on('click', '.recipe_clear_gallery_button', function(e){
		e.preventDefault();
		$('#recipe-images').html('');
	});

	/*
	 * Remove image event
	 */
	$('body').on('click', '.recipe_remove_image_button', function(){
		const gid = $(this).attr('data-rbgid');
		$('#rb-gallery-'+gid).remove();
		
		return false;
	});
 
});