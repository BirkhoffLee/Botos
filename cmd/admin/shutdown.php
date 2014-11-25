<?php
$this->say('[System] Botos is shutting down by ' . $this->SayName . '\'s permission.', 'Error', $config['channel']);
$this->say('[System] Botos is shutting down.', 'Error', $config['admin']);
$this->Submitlog('[System] Botos shutted down by ' . $this->SayName);
$this->send_data('QUIT Shutdown', false);
exit;