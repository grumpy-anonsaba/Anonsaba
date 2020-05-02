<?php
//Anonsaba 3.0 Main page
	require './config/config.php';
	require './modules/core.php';
	//Is Anonsaba even installed?
	if (!file_exists(svrpath.'.installed')) {
		Core::Error('It appears you haven\'t installed Anonsaba 3.0 please click <a href="/INSTALL/install.php">here</a> to install!');
	}