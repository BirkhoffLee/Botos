<?php
$this->say('[System] Botos is restarting by ' . $this->SayName . '\'s permission.', 'Error', $config['channel']);
$this->say('[System] Botos is restarting.', 'Error', $config['admin']);
$this->send_data('QUIT Reboot', false);
echo "<script type='text/javascript'>parent.location.reload();</script>";
exit;