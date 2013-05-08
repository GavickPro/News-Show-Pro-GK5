<?php
/**
* Default template
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK5 1.0 $
**/

// no direct access
defined('_JEXEC') or die('Restricted access');
$news_amount = count($this->content);
if($this->config['links_position'] != 'bottom' && $this->config['news_short_pages'] > 0 && count($news_list_tab) > 0 && $this->config['news_full_pages'] > 0){
	$links_width = $this->config['links_width'];
	$arts_width = 100 - $this->config['links_width'];
} else {
	$links_width = 100;
	$arts_width = 100;
}
?>
<?php if($news_amount > 0) : ?>
	<div class="nspMain<?php if($this->config['autoanim'] == TRUE) echo ' autoanim'; ?><?php if($this->config['hover_anim'] == TRUE) echo ' hover'; ?><?php echo ' ' . $this->config['moduleclass_sfx']; ?>" id="nsp-<?php echo $this->config['module_id']; ?>" data-config="<?php echo $news_config_json; ?>">
		<?php if(($this->config['news_column'] * $this->config['news_rows']) > 0) : ?>
			<div class="nspArts<?php echo ' '.$this->config['links_position']; ?>" style="width:<?php echo $arts_width; ?>%;">
				<?php if(
						count($news_html_tab) > ($this->config['news_column'] * $this->config['news_rows']) && 
						$this->config['news_full_pages'] > 1 &&
						$this->config['top_interface_style'] != 'none'
						) : ?>
				<div class="nspTopInterface">
					<?php if(
								$this->config['top_interface_style'] == 'pagination' || 
								$this->config['top_interface_style'] == 'arrows_with_pagination'
							) : ?>
					<ul class="nspPagination">
						<?php for($i = 0; $i < ceil(count($news_html_tab) / ($this->config['news_column'] * $this->config['news_rows'])); $i++) : ?>
						<li><?php echo $i+1; ?></li>
						<?php endfor; ?>
					</ul>
					<?php endif; ?>
					
					<?php if(
								$this->config['top_interface_style'] == 'arrows' || 
								$this->config['top_interface_style'] == 'arrows_with_pagination'
							) : ?>
					<span class="nspPrev"><?php echo JText::_('MOD_NEWS_PRO_GK5_NSP_PREV'); ?></span>
					<span class="nspNext"><?php echo JText::_('MOD_NEWS_PRO_GK5_NSP_NEXT'); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<div class="nspArtScroll1">
					<div class="nspArtScroll2 nspPages<?php echo $this->config['news_full_pages']; ?>">
					<?php for($i = 0; $i < count($news_html_tab); $i++) : ?>
						<?php if($i == 0) : ?>
						<div class="nspArtPage active nspCol<?php echo $this->config['news_full_pages']; ?>">
						<?php endif; ?>
							<?php 
								$style = 'padding:'. $this->config['art_padding'] .';';
								if(($i+1) % ($this->config['news_column']) == 1) $style .= 'clear:both;';
							?>
							<div class="nspArt nspCol<?php echo $this->config['news_column']; ?><?php echo (isset($news_featured_tab[$i]) && $news_featured_tab[$i] == '1') ? ' nspFeatured' : ''; ?>" style="<?php echo $style; ?>">
								<?php echo $news_html_tab[$i];?>
							</div>
						<?php if(($i > 0 && (($i+1) % ($this->config['news_column'] * $this->config['news_rows']) == 0) && $i != count($news_html_tab) - 1) || ($this->config['news_column'] * $this->config['news_rows'] == 1 && $i != count($news_html_tab) - 1)) : ?>
						</div>
						<div class="nspArtPage nspCol<?php echo $this->config['news_full_pages']; ?>">
						<?php elseif($i == count($news_html_tab) - 1) : ?>
						</div>
						<?php endif; ?>
					<?php endfor; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php if($this->config['news_short_pages'] > 0 && count($news_list_tab) > 0 ) : ?>
		<div class="nspLinksWrap<?php echo ' '.$this->config['links_position']; ?>" style="width:<?php echo $links_width-0.1; ?>%;">
			<div class="nspLinks" style="margin:<?php echo $this->config["links_margin"]; ?>;">
				<?php if(count($news_list_tab) > 0) : ?>
				<div class="nspLinkScroll1">
					<div class="nspLinkScroll2 nspPages<?php echo $this->config['news_short_pages']; ?>">
						<?php for($j = 0; $j < count($news_list_tab); $j++) : ?>
							<?php if($j == 0) : ?>
							<ul class="nspList active nspCol<?php echo $this->config['news_short_pages'] * $this->config['links_columns_amount']; ?>">
							<?php endif; ?>
							
							<?php echo $news_list_tab[$j]; ?>
							
							<?php if(($j > 0 && (($j+1) % ($this->config['links_amount']) == 0) && $j != count($news_list_tab) - 1) || ($this->config['links_amount'] == 1 && $j != count($news_list_tab) - 1)) : ?>
							</ul>
							<ul class="nspList nspCol<?php echo $this->config['news_short_pages'] * $this->config['links_columns_amount']; ?><?php if($j <= $this->config['links_columns_amount']) : ?> active<?php endif; ?>">
							<?php elseif($j == count($news_list_tab) - 1) : ?>
							</ul>
							<?php endif; ?>
						<?php endfor; ?>		
					</div>
				</div>	
				<?php endif; ?>	
				
				<?php if(
						count(($news_list_tab) > $this->config['links_amount']) && 
						$this->config['news_short_pages'] > 1 &&
					 	ceil(floor(count($news_list_tab) / $this->config['links_amount']) / $this->config['links_columns_amount']) >= 1 &&
						$this->config['bottom_interface_style'] != 'none'
						) : ?>
				<div class="nspBotInterface">
					<?php if(
								$this->config['bottom_interface_style'] == 'pagination' || 
								$this->config['bottom_interface_style'] == 'arrows_with_pagination'
							) : ?>
					<ul class="nspPagination">
						<?php for($i = 0; $i < ceil(ceil(count($news_list_tab) / $this->config['links_amount']) / $this->config['links_columns_amount']); $i++) : ?>
						<li><?php echo $i+1; ?></li>
						<?php endfor; ?>
					</ul>
					<?php endif; ?>
					
					<?php if(
								$this->config['bottom_interface_style'] == 'arrows' || 
								$this->config['bottom_interface_style'] == 'arrows_with_pagination'
							) : ?>
					<span class="nspPrev"><?php echo JText::_('MOD_NEWS_PRO_GK5_NSP_PREV'); ?></span>
					<span class="nspNext"><?php echo JText::_('MOD_NEWS_PRO_GK5_NSP_NEXT'); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>	
			</div>
		</div>
		<?php endif; ?>
	</div>
<?php else : ?>
	<p><?php echo JText::_('MOD_NEWS_PRO_GK5_NSP_ERROR'); ?></p>
<?php endif; ?>