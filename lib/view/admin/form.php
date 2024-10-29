<?php
/**
 * @var string $callback_url
 * @var string $client_id
 * @var string $client_secret
 * @var string $status
 * @var string $message
 */
?>
<div class="wrap">
	<h2>freee 設定</h2>

	<?php if (!empty($message)) { ?>
		<?php if ($status == 'ok') { ?>
			<div class="updated">
				<p><?php echo esc_html($message) ?></p>
			</div>
		<?php } ?>
		<?php if ($status == 'ng') { ?>
			<div class="error">
				<p><?php echo esc_html($message) ?></p>
			</div>
		<?php } ?>
	<?php } ?>

	<p>freeeでアプリケーションを作成しAPP ID, SECRETの値を入力してください。<br>
		freeeでのアプリケーション作成は<a href="https://secure.freee.co.jp/oauth/applications" target="_blank">こちら</a>から。<br>
		コールバックURIには "<?php echo esc_html($callback_url) ?>"を入力してください。
	</p>
	<form action="<?php echo $callback_url ?>" method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="bofa_setting_nonce" value="<?php echo $nonce ?>">
		<p><label for="client_id">APP_ID</label>
			<input id="client_id" type="text" name="client_id" value="<?php echo esc_html( $client_id ) ?>">
		</p>
		<p><label for="client_secret">APP Secret</label>
			<input id="client_secret" type="text" name="client_secret" value="<?php echo esc_html( $client_secret ) ?>">
		</p>
		<p class="submit">
			<input type="submit" value="保存" class="button button-primary">
		</p>
	</form>
</div>
