<?php
/**
 * @var array $companies
 */
?>
<div class="widget bofa_widget">
	<?php foreach ($companies as $compan) { ?>
		<?php /** @var object $company */ ?>
		<h3><?php echo esc_html($company->display_name); ?></h3>
		<table>
			<tr>
				<th>口座</th>
				<th>残高</th>
			</tr>
			<?php foreach ($company->wallets as $wallet) { ?>
				<tr>
					<td><?php echo esc_html($wallet->name) ?></td>
					<?php if ($wallet->walletable_balance < 0) { ?>
						<td class="money minus"><?php echo esc_html(number_format($wallet->walletable_balance)) ?></td>
					<?php } else { ?>
						<td class="money"><?php echo esc_html(number_format($wallet->walletable_balance)) ?></td>
					<?php } ?>
				</tr>
			<?php } ?>
		</table>
	<?php } ?>
</div>
