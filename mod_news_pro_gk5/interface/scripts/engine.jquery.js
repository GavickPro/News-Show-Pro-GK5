/**
* Main script file
* @package News Show Pro GK5
* @Copyright (C) 2009-2012 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK5 1.0 $
**/

jQuery.noConflict();

jQuery(window).load(function(){
	jQuery(document).find('.nspMain').each(function(i, module) {	
		if(!jQuery(module).hasClass('activated')) {	
			new NSP5(module);
		}
	});
});

var NSP5 = function(module) {
	// init class fields
	this.init_fields(module);
	// init the interface
	this.init_interface();
};

NSP5.prototype = {
	// class fields
	animation: true,
	anim_arts: false,
	anim_lists: false,
	arts: null,
	arts_block_width: 0,
	arts_current: 0,
	arts_pages: null,
	arts_per_page: null,
	config: null,
	hover_anim: null,
	links: null,
	links_block_width: 0,
	links_pages: null,
	links_pages_amount: null,
	links_current: 0,
	modInterface: null,
	module: null,
	// touch events properties
	swipe_max_time: 500, // in ms
	swipe_min_move: 30, // in px
	//
	init_fields: function(module) {
		// the most important class field ;)
		this.module = jQuery(module);
		this.module.addClass('activated');
		// rest of the fields
		this.config = jQuery.parseJSON(this.module.attr('data-config').replace(/'/g,"\""));
		this.arts = this.module.find('.nspArt');
		this.arts_pages = this.module.find('.nspArtPage');
		this.arts_per_page = this.config['news_column'] * this.config['news_rows'];
		this.hover_anim = this.module.hasClass('hover');
		this.links = (this.module.find('.nspLinkScroll1')) ? this.module.find('.nspLinkScroll1 li') : [];
		this.links_pages = this.module.find('.nspList');
		this.links_pages_amount = Math.ceil(Math.ceil(this.links.length / this.config['links_amount']) / this.config['links_columns_amount']);
		this.modInterface = { 
			top: this.module.find('.nspTopInterface'), 
			bottom: this.module.find('.nspBotInterface')
		};
		this.pages_amount = Math.ceil(this.arts.length / this.arts_per_page);
	},
	init_interface: function() {
		var $this = this;
		// arts
		if(this.arts.length > 0){
			this.arts_block_width = 100;
		}
		// events
		this.module.mouseenter(function() {
			if(!$this.module.hasClass('onhover')) {
				$this.module.addClass('onhover');
			}
		});
		//
		this.module.mouseleave(function() {
			if($this.module.hasClass('onhover')) {
				$this.module.removeClass('onhover');
			}
		});
		// links
		if(this.links.length > 0){
			this.links_block_width = 100;
		}
		// top interface
		this.nsp_art_list(0, 'top');
		this.nsp_art_list(0, 'bottom');
		//
		if(this.modInterface.top && this.modInterface.top.find('.nspPagination')){
			this.modInterface.top.find('.nspPagination li').each(function(i, item){
				if($this.hover_anim == 'mouseenter') {
					jQuery(item).mouseenter(function(){
						$this.arts_anim(i);
					});	
				}else {
					jQuery(item).click(function(){
						$this.arts_anim(i);
					});	
				}
			});
		}
		//
		if(this.modInterface.top && this.modInterface.top.find('.nspPrev')){
			this.modInterface.top.find('.nspPrev').click(function(){
				$this.arts_anim('prev');
			});
			
			this.modInterface.top.find('.nspNext').click(function(){
				$this.arts_anim('next');
			});
		}
		// bottom interface
		if(this.modInterface.bottom && this.modInterface.bottom.find('.nspPagination')){
			this.modInterface.bottom.find('.nspPagination li').each(function(i, item){
				if($this.hover_anim == 'mouseenter') {
					jQuery(item).mouseenter(function(){
						$this.lists_anim(i);
					});	
				}else {
					jQuery(item).click(function(){
						$this.lists_anim(i);
					});	
				}
			});
		}
		//
		if(this.modInterface.bottom && this.modInterface.bottom.find('.nspPrev')){
			this.modInterface.bottom.find('.nspPrev').click(function(){
				$this.lists_anim('prev');
			});
			
			this.modInterface.bottom.find('.nspNext').click(function(){
				$this.lists_anim('next');
			});
		}
		//
		if(this.module.hasClass('autoanim')){
			setTimeout(function(){
				$this.nsp_gk5_autoanim();
			},$this.config['animation_interval']);
		}
		// Touch events for the articles
		var arts_wrap = this.module.find('.nspArts');
		if(arts_wrap) {		
			var arts_pos_start_x = 0;
			var arts_pos_start_y = 0;
			var arts_time_start = 0;
			var arts_swipe = false;
			
			arts_wrap.bind('touchstart', function(e) {
				arts_swipe = true;
				var touches = e.originalEvent.changedTouches || e.originalEvent.touches;
	
				if(touches.length > 0) {
					arts_pos_start_x = touches[0].pageX;
					arts_pos_start_y = touches[0].pageY;
					arts_time_start = new Date().getTime();
				}
			});
			
			arts_wrap.bind('touchmove', function(e) {
				var touches = e.originalEvent.changedTouches || e.originalEvent.touches;
				
				if(touches.length > 0 && arts_swipe) {
					if(
						Math.abs(touches[0].pageX - arts_pos_start_x) > Math.abs(touches[0].pageY - arts_pos_start_y)
					) {
						e.preventDefault();
					} else {
						arts_swipe = false;
					}
				}
			});
						
			arts_wrap.bind('touchend', function(e) {
				var touches = e.originalEvent.changedTouches || e.originalEvent.touches;
				
				if(touches.length > 0 && arts_swipe) {									
					if(
						Math.abs(touches[0].pageX - arts_pos_start_x) >= $this.swipe_min_move && 
						new Date().getTime() - arts_time_start <= $this.swipe_max_time
					) {					
						if(touches[0].pageX - arts_pos_start_x > 0) {
							$this.arts_anim('prev');
						} else {
							$this.arts_anim('next');
						}
					}
				}
			});
		}
		// Touch events for the links
		var links_wrap = this.module.find('.nspLinksWrap');
		if(links_wrap) {	
			var links_pos_start_x = 0;
			var links_pos_start_y = 0;
			var links_time_start = 0;
			var links_swipe = false;
			
			links_wrap.bind('touchstart', function(e) {
				links_swipe = true;
				var touches = e.originalEvent.changedTouches || e.originalEvent.touches;
	
				if(touches.length > 0) {
					links_pos_start_x = touches[0].pageX;
					links_pos_start_y = touches[0].pageY;
					links_time_start = new Date().getTime();
				}
			});
			
			links_wrap.bind('touchmove', function(e) {
				var touches = e.originalEvent.changedTouches || e.originalEvent.touches;
				
				if(touches.length > 0 && links_swipe) {
					if(
						Math.abs(touches[0].pageX - links_pos_start_x) > Math.abs(touches[0].pageY - links_pos_start_y)
					) {
						e.preventDefault();
					} else {
						links_swipe = false;
					}
				}
			});
						
			links_wrap.bind('touchend', function(e) {
				var touches = e.originalEvent.changedTouches || e.originalEvent.touches;
				
				if(touches.length > 0 && links_swipe) {									
					if(
						Math.abs(touches[0].pageX - links_pos_start_x) >= $this.swipe_min_move && 
						new Date().getTime() - links_time_start <= $this.swipe_max_time
					) {					
						if(touches[0].pageX - links_pos_start_x > 0) {
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
		
		if(this.modInterface[pos] && this.modInterface[pos].find('.nspPagination')){
			var pagination = this.modInterface[pos].find('.nspPagination');
			pagination.find('li').attr('class', '');
			jQuery(pagination.find('li')[num]).attr('class', 'active');
		}
	},
	//
	arts_anim: function(dir) {
		if(!this.anim_arts) {
			var $this = this;
			this.anim_arts = true;
			jQuery(this.arts_pages[this.arts_current]).removeClass('active');
			
			if(dir == 'next') {
				this.arts_current = (this.arts_current == this.pages_amount - 1) ? 0 : this.arts_current + 1;
			} else if(dir == 'prev') {
				this.arts_current = (this.arts_current == 0) ? this.pages_amount - 1 : this.arts_current - 1;
			} else {
				this.arts_current = dir;
			}
			//		
			jQuery($this.module.find('.nspArtScroll2')).animate({
				'margin-left': (-1 * this.arts_current * this.arts_block_width) + "%"
			}, $this.config['animation_speed']);
			
			setTimeout(function() {
				jQuery($this.arts_pages[$this.arts_current]).addClass('active');
			}, this.config['animation_speed'] * 0.5);
			
			setTimeout(function() {
				$this.anim_arts = false;
			}, this.config['animation_speed']);
			
			this.nsp_art_list(this.arts_current, 'top');
			this.animation = false;
			setTimeout(function(){
				$this.animation = true;
			}, this.config['animation_interval'] * 0.8);
		}
	},
	//
	lists_anim: function(dir) {
		if(!this.anim_lists) {
			var $this = this;
			this.anim_lists = true;
			
			for(var x = 0; x < this.config['links_columns_amount']; x++) {
				var item = this.links_pages[this.links_current * this.config['links_columns_amount'] + x];
				if(item) {
					jQuery(item).removeClass('active');
				}
			}
			
			if(dir == 'next') {
				this.links_current = (this.links_current == this.links_pages_amount - 1) ? 0 : this.links_current + 1;
			} else if(dir == 'prev') {
				this.links_current = (this.links_current == 0) ? this.links_pages_amount - 1 : this.links_current - 1;
			} else {
				$this.links_current = dir;
			}
			
			setTimeout(function() {
				for(var x = 0; x < $this.config['links_columns_amount']; x++) {
					var item = $this.links_pages[$this.links_current * $this.config['links_columns_amount'] + x]; 
					if(item) {
						jQuery(item.addClass('active'));
					}
				}
			}, this.config['animation_speed'] * 0.5);
			//
			setTimeout(function() {
				$this.anim_lists = false;
			}, this.config['animation_speed']);
			//
			jQuery($this.module.find('.nspLinkScroll2')).animate({
				'margin-left': (-1 * this.links_current * this.links_block_width) + "%"
			}, $this.config['animation_speed']);
			
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
		setTimeout(function() {
			$this.nsp_gk5_autoanim();
		}, $this.config['animation_interval']);
	}
};