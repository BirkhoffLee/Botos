<?php
$this->say('[System] botOS is shutting down by ' . $this->SayName . '\'s permission.', 'Error', $config['channel']);
$this->say('[System] botOS is shutting down.', 'Error', $config['admin']);
$this->Submitlog('[System] botOS shutted down by ' . $this->SayName);
$this->send_data('QUIT', 'Shutdown');
exit;