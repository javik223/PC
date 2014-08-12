<h2>Setup video services</h2>

<?
foreach($services as $service)
{
	?>
	<h3><?=$service->service_name;?></h3>
	
	<form method="post">
		<?
		foreach($service->api_options as $k => $v)
		{
			?>
			<p>
				<label><?=$k?></label><br />
				<input type="text" name="<?=$k?>" value="" />
			</p>
			<?
		}
		?>
		
		<input type="submit" class="button-primary" value="Save Settings" />
	</form>
	<?
}
?>
