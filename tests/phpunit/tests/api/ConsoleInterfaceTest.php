<?php

class ConsoleInterfaceTest extends PHPUnit_Framework_TestCase {

    /**
    * @group Interface
    */
	
	public function testInterfaceInstance() {
		// default
		AgaviContext::getInstance()->getModel('Console.ConsoleInterface',"Api");
		// specific host
		AgaviContext::getInstance()->getModel('Console.ConsoleInterface',"Api",array("host"=>"localhost")); 
	}
	
	/**
     * @expectedException ApiUnknownHostException
     */
    /**
    * @group Interface
    */

	public function testUnknownHostInstance() {
		$model = AgaviContext::getInstance()->getModel('Console.ConsoleInterface',"Api",array("host"=>"dgjksdd"));	
	}

    /**
    * @group Interface
    */

	public function testCommandSetup() {
		$console = AgaviContext::getInstance()->getModel('Console.ConsoleInterface',"Api");	
		$lsCmd = AgaviContext::getInstance()->getModel(
			'Console.ConsoleCommand',
			"Api",
			array(
				"command" => "ls",
				"connection" => $console, 
				"arguments" => array(
					'-la' => '', 
					'1' => '/usr/local/icinga-web/' 
				)
			)
		);
		$grepCmd = AgaviContext::getInstance()->getModel(
			'Console.ConsoleCommand',
			"Api",
			array(
				"command" => "grep",
				"connection" => $console, 
				"arguments" => array(
					'c.*'
				)
			)
		);
        $out=  "/tmp/IcingaApiTest";
		$grepCmd->stdoutFile($out);
		$lsCmd->pipeCmd($grepCmd);
		$console->exec($lsCmd);
	
		$this->assertEquals($lsCmd->getCommandString(),"/bin/ls -la  '/usr/local/icinga-web/' | /bin/grep 'c.*'  > $out"); 
	}
	

	/**
     * @expectedException ApiRestrictedCommandException
     * @group Interface
     */
	public function testInvalidCommand() {
		$console = AgaviContext::getInstance()->getModel('Console.ConsoleInterface',"Api");	
		$lsCmd = AgaviContext::getInstance()->getModel(
			'Console.ConsoleCommand',
			"Api",
			array(
				"command" => "ls ; sudo su",
				"connection" => $console, 
				"arguments" => array(
					'-c' => '', 
					'1' => '/usr/local/icinga-web/' 
				)
			)
		);
		
		$lsCmd->isValid(true,$msg);	
	}
	
	/**
	 * @group Interface
	 */
	public function testSshConnection() {
		$console = AgaviContext::getInstance()->getModel('Console.ConsoleInterface',"Api",array("host"=>"vm_host1"));	
		$lsCmd = AgaviContext::getInstance()->getModel(
			'Console.ConsoleCommand',
			"Api",
			array(
				"command" => "ls",
				"connection" => $console, 
				"arguments" => array(
					'-la' => '', 
					'1' => '/usr/local/icinga-web/' 
				)
			)
		);
		$console->exec($lsCmd);
	}

    /**
    * @group Interface
    */
	public function testSshKeyConnection() {
		$console = AgaviContext::getInstance()->getModel('Console.ConsoleInterface',"Api",array("host"=>"vm_host2"));	
		$lsCmd = AgaviContext::getInstance()->getModel(
			'Console.ConsoleCommand',
			"Api",
			array(
				"command" => "ls",
				"connection" => $console, 
				"arguments" => array(
					'-la' => '', 
					'1' => '/usr/local/icinga-web/' 
				)
			)
		);
		$console->exec($lsCmd);
	}

}
