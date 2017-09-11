<?php
$css = file_get_contents(public_path().'/css/email.css');
?>

		<style>
			{!! $css !!}
		</style>
		{!! $page->content !!}

