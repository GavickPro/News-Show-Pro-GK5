/**
* Main script file
* @package News Show Pro GK5
* @Copyright (C) 2009-2012 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK5 1.0 $
**/

var NSP5 = new Class({
	// class fields
	animation: true,
	anim_arts: false,
	anim_lists: false,
	arts: null,
	arts_block_width: 0,
	arts_current: 0,
	arts_pages: null,
	arts_per_page: null,
	arts_scroller: null,
	config: null,
	hover_anim: null,
	links: null,
	links_block_width: 0,
	links_pages: null,
	links_pages_amount: null,
	links_scroller: null,
	links_current: 0,
	modInterface: null,
	module: null,
	// touch events properties
	swipe_max_time: 500, // in ms
	swipe_min_move: 30, // in px
	//
	initialize: function(module) {
		// init class fields
		this.init_fields(module);
		// init the interface
		this.init_interface();
	},
	init_fields: function(module) {
		// the most important class field ;)
		this.module = module;
		this.module.addClass('activated');
		// rest of the fields
		this.config = JSON.decode(this.module.get('data-config'));
		this.config['animation_function'] = eval(this.config['animation_function']);
		this.arts = this.module.getElements('.nspArt');
		this.arts_pages = this.module.getElements('.nspArtPage');
		this.arts_per_page = this.config['news_column'] * this.config['news_rows'];
		this.hover_anim = this.module.hasClass('hover');
		this.links = (this.module.getElement('.nspLinkScroll1')) ? this.module.getElement('.nspLinkScroll1').getElements('li') : [];
		this.links_pages = this.module.getElements('.nspList');
		this.links_pages_amount = Math.ceil(Math.ceil(this.links.length / this.config['links_amount']) / this.config['links_columns_amount']);
		this.modInterface = { 
			top: this.module.getElement('.nspTopInterface'), 
			bottom: this.module.getElement('.nspBotInterface')
		};
		this.pages_amount = Math.ceil(this.arts.length / this.arts_per_page);
	},
	init_interface: function() {
		var $this = this;
		// arts
		if(this.arts.length > 0){
			this.arts_block_width = 100;
			
			this.arts_scroller = new Fx.Tween($this.module.getElement('.nspArtScroll2'), {
				duration: $this.config['animation_speed'], 
				wait:false, 
				property: 'margin-left', 
				unit: '%',
				transition: $this.config['animation_function']
			});
		}
		// events
		this.module.addEvents({
			'mouseenter': function() {
				if(!$this.module.hasClass('onhover')) $this.module.addClass('onhover');
			},
			'mouseleave': function() {
				if($this.module.hasClass('onhover')) $this.module.removeClass('onhover');
			}
		});
		// links
		if(this.links.length > 0){
			this.links_block_width = 100;
			
			this.links_scroller = new Fx.Tween($this.module.getElement('.nspLinkScroll2'), {
				duration:$this.config['animation_speed'], 
				wait:false, 
				property: 'margin-left',
				unit: '%',
				transition: $this.config['animation_function']
			});
		}
		// top interface
		this.nsp_art_list(0, 'top');
		this.nsp_art_list(0, 'bottom');
		//
		if(this.modInterface.top && this.modInterface.top.getElement('.nspPagination')){
			this.modInterface.top.getElement('.nspPagination').getElements('li').each(function(item,i){
				item.addEvent($this.hover_anim ? 'mouseenter' : 'click', function(){
					$this.arts_anim(i);
				});	
			});
		}
		//
		if(this.modInterface.top && this.modInterface.top.getElement('.nspPrev')){
			this.modInterface.top.getElement('.nspPrev').addEvent("click", function(){
				$this.arts_anim('prev');
			});
			
			this.modInterface.top.getElement('.nspNext').addEvent("click", function(){
				$this.arts_anim('next');
			});
		}
		// bottom interface
		if(this.modInterface.bottom && this.modInterface.bottom.getElement('.nspPagination')){
			this.modInterface.bottom.getElement('.nspPagination').getElements('li').each(function(item,i){
				item.addEvent($this.hover_anim ? 'mouseenter' : 'click', function(){	
					$this.lists_anim(i);
				});	
			});
		}
		//
		if(this.modInterface.bottom && this.modInterface.bottom.getElement('.nspPrev')){
			this.modInterface.bottom.getElement('.nspPrev').addEvent("click", function(){
				$this.lists_anim('prev');
			});
			
			this.modInterface.bottom.getElement('.nspNext').addEvent("click", function(){
				$this.lists_anim('next');
			});
		}
		//
		if(this.module.hasClass('autoanim')){
			(function(){
				$this.nsp_gk5_autoanim();
			}).delay($this.config['animation_interval']);
		}
		// Touch events for the articles
		var arts_wrap = this.module.getElement('.nspArts');
		if(arts_wrap) {		
			var arts_pos_start_x = 0;
			var arts_pos_start_y = 0;
			var arts_time_start = 0;
			var arts_swipe = false;
			
			arts_wrap.addEvent('touchstart', function(e) {
				arts_swipe = true;
				
				if(e.changedTouches.length > 0) {
					arts_pos_start_x = e.changedTouches[0].pageX;
					arts_pos_start_y = e.changedTouches[0].pageY;
					arts_time_start = new Date().getTime();
				}
			});
			
			arts_wrap.addEvent('touchmove', function(e) {
				if(e.changedTouches.length > 0 && arts_swipe) {
					if(
						Math.abs(e.changedTouches[0].pageX - arts_pos_start_x) > Math.abs(e.changedTouches[0].pageY - arts_pos_start_y)
					) {
						e.preventDefault();
					} else {
						arts_swipe = false;
					}
				}
			});
			
			arts_wrap.addEvent('touchend', function(e) {
				if(e.changedTouches.length > 0 && arts_swipe) {					
					if(
						Math.abs(e.changedTouches[0].pageX - arts_pos_start_x) >= $this.swipe_min_move && 
						new Date().getTime() - arts_time_start <= $this.swipe_max_time
					) {
						if(e.changedTouches[0].pageX - arts_pos_start_x > 0) {
							$this.arts_anim('prev');
						} else {
							$this.arts_anim('next');
						}
					}
				}
			});
		}
		// Touch events for the links
		var links_wrap = this.module.getElement('.nspLinksWrap');
		if(links_wrap) {
			var links_pos_start_x = 0;
			var links_pos_start_y = 0;
			var links_time_start = 0;
			var links_swipe = false;
			
			links_wrap.addEvent('touchstart', function(e) {
				links_swipe = true;
				
				if(e.changedTouches.length > 0) {
					links_pos_start_x = e.changedTouches[0].pageX;
					links_pos_start_y = e.changedTouches[0].pageY;
					links_time_start = new Date().getTime();
				}
			});
			
			links_wrap.addEvent('touchmove', function(e) {
				if(e.changedTouches.length > 0 && links_swipe) {
					if(
						Math.abs(e.changedTouches[0].pageX - links_pos_start_x) > Math.abs(e.changedTouches[0].pageY - links_pos_start_y)
					) {
						e.preventDefault();
					} else {
						links_swipe = false;
					}
				}
			});
			
			links_wrap.addEvent('touchend', function(e) {
				if(e.changedTouches.length > 0 && links_swipe) {					
					if(
						Math.abs(e.changedTouches[0].pageX - links_pos_start_x) >= $this.swipe_min_move && 
						new Date().getTime() - links_time_start <= $this.swipe_max_time
					) {
						if(e.changedTouches[0].pageX - links_pos_start_x > 0) {
							$this.lists_anim('prev');
						} else {
							$this.lists_anim('next');
						}
					}
				}
			});
		}
	},
	//
	nsp_art_list: function(i, pos){
		var num  = (i !== null) ? i : (pos == 'top') ? this.arts_current : this.links_current;
		
		if(this.modInterface[pos] && this.modInterface[pos].getElement('.nspPagination')){
			var pagination = this.modInterface[pos].getElement('.nspPagination');
			pagination.getElements('li').setProperty('class', '');
			pagination.getElements('li')[num].setProperty('class', 'active');
		}
	},
	//
	arts_anim: function(dir) {
		if(!this.anim_arts) {
			var $this = this;
			this.anim_arts = true;
			this.arts_pages[this.arts_current].removeClass('active');
			
			if(dir == 'next') {
				this.arts_current = (this.arts_current == this.pages_amount - 1) ? 0 : this.arts_current + 1;
			} else if(dir == 'prev') {
				this.arts_current = (this.arts_current == 0) ? this.pages_amount - 1 : this.arts_current - 1;
			} else {
				this.arts_current = dir;
			}
			
			this.arts_scroller.start(-1 * this.arts_current * this.arts_block_width);
			
			(function() {
				$this.arts_pages[$this.arts_current].addClass('active');
			}).delay(this.config['animation_speed'] * 0.5);
			
			(function() {
				$this.anim_arts = false;
			}).delay(this.config['animation_speed']);
			
			this.nsp_art_list(this.arts_current, 'top');
			this.animation = false;
			(function(){
				$this.animation = true;
			}).delay(this.config['animation_interval'] * 0.8);
		}
	},
	//
	lists_anim: function(dir) {
		if(!this.anim_lists) {
			var $this = this;
			this.anim_lists = true;
			
			for(var x = 0; x < this.config['links_columns_amount']; x++) {
				var item = this.links_pages[this.links_current * this.config['links_columns_amount'] + x];
				if(item) item.removeClass('active');
			}
			
			if(dir == 'next') {
				this.links_current = (this.links_current == this.links_pages_amount - 1) ? 0 : this.links_current + 1;
			} else if(dir == 'prev') {
				this.links_current = (this.links_current == 0) ? this.links_pages_amount - 1 : this.links_current - 1;
			} else {
				$this.links_current = dir;
			}
			
			(function() {
				$this.anim_lists = false;
			}).delay(this.config['animation_speed']);
			
			(function() {
				for(var x = 0; x < $this.config['links_columns_amount']; x++) {
					var item = $this.links_pages[$this.links_current * $this.config['links_columns_amount'] + x]; 
					if(item) item.addClass('active');
				}
			}).delay(this.config['animation_speed'] * 0.5);
			
			this.links_scroller.start(-1 * this.links_current * this.links_block_width);
			this.nsp_art_list(null, 'bottom');
		}
	},
	//
	nsp_gk5_autoanim: function() {
		var $this = this;
		//
		if(!this.module.hasClass('onhover')) {
			this.arts_anim('next');
		}
		//
		(function() {
			$this.nsp_gk5_autoanim();
		}).delay($this.config['animation_interval']);
	}
});
//
window.addEvent("load", function(){	
	$$('.nspMain').each(function(module){	
		if(!module.hasClass('activated')) {	
			new NSP5(module);
		}
	});
});