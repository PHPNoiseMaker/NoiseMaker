<div class="alert alert-error">
	
	<span style="font-size:24px;margin-bottom:10px;"><?php echo h($error['code']); ?>: <?php echo h($error['message']); ?></span>
	<br /><i class="icon-exclamation-sign"></i> I'm sorry, but the page you requested ( <i><?php echo h($url); ?></i> ) could not be loaded.
	<br />File: <a href="#"><?php echo h($error['file']); ?></a>
	<i>Line: <?php echo h($error['line']); ?></i>
	
</div>