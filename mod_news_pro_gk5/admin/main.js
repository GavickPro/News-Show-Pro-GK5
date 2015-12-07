/**
 * Admin script file
 * @package News Show Pro GK5
 * @Copyright (C) 2009-2012 Gavick.com
 * @ All rights reserved
 * @ Joomla! is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: GK5 1.0 $
 **/
jQuery(window).load(function() {	
	// initialize the configuration manager
	var configManager = new NSPGK5ConfigManager();
	
	if(jQuery('#jform_params_links_position').val() == 'bottom') jQuery('#jform_params_links_width').parent().parent().css('display','none');
	else jQuery('#jform_params_links_width').parent().parent().css('display','');	
	jQuery('#jform_params_links_position').change( function(){
		if(jQuery('#jform_params_links_position').val() == 'bottom') jQuery('#jform_params_links_width').parent().parent().css('display','none');
		else jQuery('#jform_params_links_width').parent().parent().css('display','');	
	});
	jQuery('#jform_params_links_position').blur( function(){
		if(jQuery('#jform_params_links_position').val() == 'bottom') jQuery('#jform_params_links_width').parent().parent().css('display','none');
		else jQuery('#jform_params_links_width').parent().parent().css('display','');
	});
	//
	// check Joomla! version and add suffix
	if(parseFloat((jQuery('#gk_about_us').data('jversion')).substr(0,3)) >= '3.2') {
		jQuery('#module-form').addClass('j32');
	}
	jQuery('a[href="#attrib-advanced"]').parent().addClass('separator');
	jQuery('a[href^="#description"]').parent().css('display', 'none');
	jQuery('a[href="#permissions"]').parent().after(jQuery('a[href="#attrib-advanced"]').parent());
	//
	jQuery('#moduleOptions a[href^="#collapse"]').each(function(i, el) {
		jQuery(el).attr('id', jQuery(el).html().replace(/ /g,'_').replace('!', ''));
	});
	
	jQuery('.gk_switch').each(function(i, el){
			el = jQuery(el);
			el.css('display','none');
			var style = (el.val() == 1) ? 'on' : 'off';
			var switcher = new jQuery('<div>',{'class' : 'switcher-'+style});
			el.before(switcher);
			switcher.click( function(){
				if(el.val() == 1){
					switcher.attr('class','switcher-off');
					el.val(0);
				} else {
					switcher.attr('class','switcher-on');
					el.val(1);
				}
			});
		});
	
	var link = new jQuery('<a>', { 'class' : 'gkHelpLink', 'href' : 'http://www.gavick.com/news-show-pro-gk5.html', 'target' : '_blank' });
	jQuery('div.accordion-group').eq(jQuery('div.accordion-group').length-2).find('.accordion-heading').append(link);
	link.click( function(e) { e.preventDefault(); e.stopPropagation(); });
	//
	new DataSources();
	new PortalModes();
	new ImageCrop();
	new ArticleLayout();
	// option to hide article format related fields
	var article_format = jQuery('#jform_params_use_own_article_format').val();
		
	if(article_format == 1) {
		jQuery('#jform_params_article_format').parent().parent().css('display', 'block');
		jQuery('.article-format-hide').each(function(i, el) {
			jQuery(el).parent().parent().css('display', 'none');
		});
	} else {
		jQuery('#jform_params_article_format').parent().parent().css('display', 'none');
		jQuery('.article-format-hide').each(function(i, el) {
			jQuery(el).parent().parent().css('display', 'block');
		});
	}
	
	jQuery('#jform_params_use_own_article_format').prev('div').click(function() {
		var article_format = jQuery('#jform_params_use_own_article_format').val();
		
		if(article_format == 1) {
			jQuery('#jform_params_article_format').parent().parent().css('display', 'block');
			jQuery('.article-format-hide').each(function(i,el) {
				jQuery(el).parent().parent().css('display', 'none');
			});
		} else {
			jQuery('#jform_params_article_format').parent().parent().css('display', 'none');
			jQuery('.article-format-hide').each(function(i,el) {
				jQuery(el).parent().parent().css('display', 'block');
			});
		}	
	});
	
	// option to hide js engine related fiels
	var used_js_engine = jQuery('#jform_params_engine_mode').val();
			
	jQuery('#jform_params_animation_function').parent().parent().css('display', (used_js_engine == 'mootools') ? 'block' : 'none');
	
	jQuery('#jform_params_engine_mode').change( function() {
			var used_js_engine = jQuery('#jform_params_engine_mode').val();
			jQuery('#jform_params_animation_function').parent().parent().css('display', (used_js_engine == 'mootools') ? 'block' : 'none');
	});
	
	jQuery('#jform_params_engine_mode').blur( function() {
			var used_js_engine = jQuery('#jform_params_engine_mode').val();
			jQuery('#jform_params_animation_function').parent().parent().css('display', (used_js_engine == 'mootools') ? 'block' : 'none');
	});
	jQuery('#jform_params_engine_mode').focus( function() {
			var used_js_engine = jQuery('#jform_params_engine_mode').val();
			jQuery('#jform_params_animation_function').parent().parent().css('display', (used_js_engine == 'mootools') ? 'block' : 'none');
	});
	
	jQuery('#config_manager_form').parent().css('margin-left', 0);
	jQuery('.gkFormLine.hasText').each(function (i, el) {
		jQuery(el).parent().css('margin-left', '20px');
	});
	
	jQuery('.input-pixels').each(function(i, el){jQuery(el).parent().html("<div class=\"input-prepend\">" + jQuery(el).parent().html() + "<span class=\"add-on\">px</span></div>")});
	jQuery('.input-minutes').each(function(i, el){jQuery(el).parent().html("<div class=\"input-prepend\">" + jQuery(el).parent().html() + "<span class=\"add-on\">minutes</span></div>")});
	jQuery('.input-percents').each(function(i, el){jQuery(el).parent().html("<div class=\"input-prepend\">" + jQuery(el).parent().html() + "<span class=\"add-on\">%</span></div>")});
	jQuery('.input-ms').each(function(i, el){jQuery(el).parent().html("<div class=\"input-prepend\">" + jQuery(el).parent().html() + "<span class=\"add-on\">ms</span></div>")});
	jQuery('.input-times').each(function(i, el){ jQuery(el).parent().find('#jform_params_img_width').after('<span class=\"add-on\">&times;</span>');});
	jQuery('.input-times').each(function(i, el){ jQuery(el).parent().find('#jform_params_links_img_width').after('<span class=\"add-on\">&times;</span>');});
	
	jQuery('#jform_params_img_height-lbl').parents().eq(1).css('display', 'none');
	jQuery('#jform_params_links_img_height-lbl').parents().eq(1).css('display', 'none');
	jQuery('.gk-group-layout').each(function(i, elm) {
		jQuery(elm).css('display', 'none');
	});
	
	jQuery('.hide-k2').each(function(i, el){
		el = jQuery(el);
		el.parent().find('.chzn-done').attr('style', 'display: none!important');
	});
	
	jQuery('#nsp-gk5-checkout').parent().css('margin-left', '10px');
	jQuery('#gk_about_us').parent().css('margin-left', '10px');
});
