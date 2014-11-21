<ul id="pagination">
	<li><span>Page</span></li>
	<?php if ($first_page !== FALSE): ?>
	<!--li>
		<a href="<?php echo HTML::chars($page->url($first_page)) ?>" rel="first">
			<?php //echo __('First') ?>
		</a>
	</li-->
	<?php else: ?>
		<?php //echo __('First') ?>
	<?php endif ?>

	<?php if ($previous_page !== FALSE): ?>
	<!--li>
		<a href="<?php echo HTML::chars($page->url($previous_page)) ?>" title="Previous" rel="prev">
			<?php //echo __('Previous') ?></a>
	</li-->
	<?php else: ?>
		<?php //echo __('Previous') ?>
	<?php endif ?>

	<?php for ($i = 1; $i <= $total_pages; $i++): ?>

		<?php if ($i == $current_page): ?>
			<li><strong><a href class="parent" href="javascript: void(0)" title="Current Page <?php echo $i ?>"><?php echo $i ?></a></strong></li>
		<?php else: ?>
			<li><a href="<?php echo HTML::chars($page->url($i)) ?>" title="Page <?php echo $i ?>"><?php echo $i ?></a></li>
		<?php endif ?>

	<?php endfor ?>

	<?php if ($next_page !== FALSE): /* ?>
	<li>
		<a href="<?php echo HTML::chars($page->url($next_page)) ?>" rel="next" title="Next Page">
			<?php echo __('Next') ?></a>
	</li>	
	<?php else: ?>
		<?php echo __('Next') ?>
	<?php */ endif; ?>

	<?php if ($last_page !== FALSE): 
	/*
	?>
	<li>
		<a href="<?php echo HTML::chars($page->url($last_page)) ?>" rel="last" title="Last">
			<?php //echo __('Last') ?></a>
	</li>	
	<?php 
	 * 
	 */
	else:
		/*
	?>
		<?php //echo __('Last') ?>
	<?php
	*/
	 endif; 
	?>
		 

</ul><!-- .pagination -->