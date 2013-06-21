/**
 * Admin script file
 * @package News Show Pro GK5
 * @Copyright (C) 2009-2012 Gavick.com
 * @ All rights reserved
 * @ Joomla! is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: GK5 1.0 $
 **/
window.addEvent("domready",function(){
	// initialize the configuration manager
	var configManager = new NSPGK5ConfigManager();
	// sliding options
	var modblock = $$('div[id^="module-sliders"]')[0];
	var baseW = modblock.getSize().x;
	var minW = 640;
	
	modblock.getParent().setStyle('position','relative');
	
	if(baseW < minW) {
		modblock.setStyles({
			"position": "absolute",
			"background": "white",
			"width": baseW + "px",
			"padding": "5px",
			"border-radius": "3px",
			"-webkit-box-shadow": "-8px 0 15px #aaa",
			"-moz-box-shadow": "-8px 0 15px #aaa",
			"box-shadow": "-8px 0 15px #aaa",
			"-webkit-box-sizing": "border-box",
			"-moz-box-sizing": "border-box",
			"-ms-box-sizing": "border-box",
			"box-sizing": "border-box"
		});
		
		var WidthFX = new Fx.Morph(modblock, {duration: 150});
		var mouseOver = false;
	
		modblock.addEvent('mouseenter', function() {
			mouseOver = true;

			WidthFX.start({
				'width': minW,
				'margin-left': (-1 * (minW - baseW))
			});
		});

		modblock.addEvent('mouseleave', function() {
			mouseOver = false;
			(function() {
				if(!mouseOver) {
					WidthFX.start({
						'width': baseW,
						'margin-left': 0
					});
				}
			}).delay(750);
		});
	}
	
	// fix the Joomla! behaviour
	$$('.panel h3.title').each(function(panel, i) {
		panel.addEvent('click', function(){
			if(panel.hasClass('pane-toggler')) {
				(function(){ 
					panel.getParent().getElement('.pane-slider').setStyle('height', 'auto'); 
				}).delay(750);

				(function() {
					var myFx = new Fx.Scroll(window, { duration: 150 }).toElement(panel);
				}).delay(250);
			}
		});
	});
	//
	//
	//
	if(document.id('jform_params_links_position').value == 'bottom') document.id('jform_params_links_width').getParent().setStyle('display','none');
	else document.id('jform_params_links_width').getParent().setStyle('display','');	
	document.id('jform_params_links_position').addEvent('change', function(){
		if(document.id('jform_params_links_position').value == 'bottom') document.id('jform_params_links_width').getParent().setStyle('display','none');
		else document.id('jform_params_links_width').getParent().setStyle('display','');	
	});
	document.id('jform_params_links_position').addEvent('blur', function(){
		if(document.id('jform_params_links_position').value == 'bottom') document.id('jform_params_links_width').getParent().setStyle('display','none');
		else document.id('jform_params_links_width').getParent().setStyle('display','');
	});
	
	$$('.input-pixels').each(function(el){el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit\">px</span>"});
	$$('.input-percents').each(function(el){el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit\">%</span>"});
	$$('.input-minutes').each(function(el){el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit\">minutes</span>"});
	$$('.input-ms').each(function(el){el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit\">ms</span>"});
	$$('.input-times').each(function(el){ el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit times\">&times;</span>"});
	
	$$('.gk-color').each(function(el, i) {
		var prnt = el.getParent(); 
		prnt.innerHTML = prnt.innerHTML + "<span class=\"gk-color-vis\"></span>";
		
		prnt.getElement('.gk-color').addEvent('focus', function() { 
			prnt.getElement('.gk-color-vis').setStyle('background-color', prnt.getElement('.gk-color').get('value')); 
		});
		
		prnt.getElement('.gk-color').addEvent('blur', function() { 
			prnt.getElement('.gk-color-vis').setStyle('background-color', prnt.getElement('.gk-color').get('value')); 
		});
		
		prnt.getElement('.gk-color').addEvent('keyup', function() { 
			prnt.getElement('.gk-color-vis').setStyle('background-color', prnt.getElement('.gk-color').get('value')); 
		});
		
		prnt.getElement('.gk-color').fireEvent('blur');
	});
	
	$$('.text-limit').each(function(el){
		var name = el.get('id') + '_type';
		var parent = el.getParent();
		el.inject(document.id(name),'before');		
        parent.dispose();
	});
	$$('.float').each(function(el){
		var destination = el.getParent().getPrevious().getElement('select');
		var parent = el.getParent();
        el.inject(destination, 'after');
		parent.dispose();	
	});
	$$('.enabler').each(function(el){
		var destination = el.getParent().getPrevious().getElement('select');
		var parent = el.getParent();
		el.inject(destination, 'after');
		parent.dispose();	
	});
	$$('.gk_switch').each(function(el){
		el.setStyle('display','none');
		var style = (el.value == 1) ? 'on' : 'off';
		var switcher = new Element('div',{'class' : 'switcher-'+style});
		switcher.inject(el, 'after');
		switcher.addEvent("click", function(){
			if(el.value == 1){
				switcher.setProperty('class','switcher-off');
				el.value = 0;
			} else {
				switcher.setProperty('class','switcher-on');
				el.value = 1;
			}
		});
	});
	
	var link = new Element('a', { 'class' : 'gkHelpLink', 'href' : 'http://www.gavick.com/news-show-pro-gk5.html', 'target' : '_blank' })
	link.inject($$('div.panel')[$$('div.panel').length-1].getElement('h3'), 'bottom');
	link.addEvent('click', function(e) { e.stopPropagation(); });
	//
	new DataSources();
	new PortalModes();
	new ImageCrop();
	new ArticleLayout();
	
	// option to hide article format related fields
	var article_format = document.id('jform_params_use_own_article_format').get('value');
		
	if(article_format == 1) {
		document.id('jform_params_article_format').getParent().setStyle('display', 'block');
		$$('.article-format-hide').each(function(el, i) {
			el.getParent().setStyle('display', 'none');
		});
	} else {
		document.id('jform_params_article_format').getParent().setStyle('display', 'none');
		$$('.article-format-hide').each(function(el, i) {
			el.getParent().setStyle('display', 'block');
		});
	}

	document.id('jform_params_use_own_article_format').getNext('div').addEvent('click', function() {
		var article_format = document.id('jform_params_use_own_article_format').get('value');
		
		if(article_format == 1) {
			document.id('jform_params_article_format').getParent().setStyle('display', 'block');
			$$('.article-format-hide').each(function(el, i) {
				el.getParent().setStyle('display', 'none');
			});
		} else {
			document.id('jform_params_article_format').getParent().setStyle('display', 'none');
			$$('.article-format-hide').each(function(el, i) {
				el.getParent().setStyle('display', 'block');
			});
		}	
	});
	
	// option to hide js engine related fiels
	var used_js_engine = document.id('jform_params_engine_mode').get('value');
			
	document.id('jform_params_animation_function').getParent().setStyle('display', (used_js_engine == 'mootools') ? 'block' : 'none');

	document.id('jform_params_engine_mode').addEvents({
		'change': function() {
			var used_js_engine = document.id('jform_params_engine_mode').get('value');
			document.id('jform_params_animation_function').getParent().setStyle('display', (used_js_engine == 'mootools') ? 'block' : 'none');
		},
		'blur': function() {
			var used_js_engine = document.id('jform_params_engine_mode').get('value');
			document.id('jform_params_animation_function').getParent().setStyle('display', (used_js_engine == 'mootools') ? 'block' : 'none');
		},
		'focus': function() {
			var used_js_engine = document.id('jform_params_engine_mode').get('value');
			document.id('jform_params_animation_function').getParent().setStyle('display', (used_js_engine == 'mootools') ? 'block' : 'none');
		}
	});
	
	// AMM fix
	document.getElements('#module-sliders .panel').each(function(el, i) {
		if(el.getParent().getProperty('id') == 'module-sliders' && el.getElement('h3').getProperty('id') != 'assignment-options' && el.getElement('h3').getProperty('id') != 'permissions') {
			el.addClass('nspgk5-panel');
		} else if(el.getParent().getProperty('id') == 'module-sliders'){
			el.addClass('non-nspgk5-panel');
		}
	});
});