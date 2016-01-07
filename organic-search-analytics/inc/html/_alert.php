<?php if( isset( $alert ) ) { ?>
	<div class="alert <?php echo $alert['type'] ?>"><?php echo $alert['message'] ?></div>
<?php } ?>

<?php if( isset( $_SESSION['alert_success'] ) || isset( $_SESSION['alert_error'] ) ) { ?>
	<?php
		if( isset( $_SESSION['alert_success'] ) ) {
			$msg = $_SESSION['alert_success'];
			$type = "success";
			unset( $_SESSION['alert_success'] );
		} elseif ( isset( $_SESSION['alert_error'] ) ) {
			$msg = $_SESSION['alert_error'];
			$type = "error";
			unset( $_SESSION['alert_error'] );
		}
	?>
	<?php if( isset( $type ) && isset ( $msg ) ) {?>
		<div class="alert <?php echo $type ?>"><?php echo $msg ?></div>
	<?php } ?>
<?php } ?>