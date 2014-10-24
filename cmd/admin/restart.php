<?php
$this->say('[System] BOT is restarting by ' . $this->SayName . '\'s permission.', 'Information', $config['channel']);
$this->say('[System] BOT is restarting.', 'Information', $config['admin']);
$this->send_data('QUIT', 'Restart');
echo "<meta http-equiv=\"refresh\" content=\"0.1\">";
exit;