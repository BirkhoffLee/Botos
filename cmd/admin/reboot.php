<?php
$this->say('[System] botOS is restarting by ' . $this->SayName . '\'s permission.', 'Error', $config['channel']);
$this->say('[System] botOS is restarting.', 'Error', $config['admin']);
$this->send_data('QUIT', 'Restart');
echo "<meta http-equiv=\"refresh\" content=\"0.1\">";
exit;