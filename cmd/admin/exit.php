<?php
$this->say('[System] BOT is shutting down by ' . $this->SayName . '\'s permission.', 'Information', $config['channel']);
$this->say('[System] BOT is shutting down.', 'Information', $config['admin']);
$this->Submitlog('[System] BOT shutted down by ' . $this->SayName);
$this->send_data('QUIT', 'Shutdown');
exit;