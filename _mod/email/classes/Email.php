<?php defined('SYSPATH') OR die('No direct access allowed.');

class Email {

	// Instances pool
	protected static $default = 'default';
	protected static $instances = array();

	/**
	 * @param $cname  string Config key from config/email.php
	 * @param $config array  Config
	 */
	protected function __construct($cname, $config)
	{
		if (!class_exists('Swift_Mailer', FALSE))
		{
			// Load SwiftMailer
			require Kohana::find_file('vendor', 'swift/swift_required');
		}

		switch ($config['driver'])
		{
			case 'smtp':
				// Set port
				$port = isset($config['port']) ? $config['port'] : 25;

				// Create SMTP Transport
				$transport = Swift_SmtpTransport::newInstance($config['hostname'], $port);

				// Set encryption
				if (isset($config['encryption'])) $transport->setEncryption($config['encryption']);

				// Do authentication, if part of the DSN
				if (isset($config['username'])) $transport->setUsername($config['username']);
				if (isset($config['password'])) $transport->setPassword($config['password']);

				// Set the timeout
				if (isset($config['timeout'])) $transport->setTimeout($config['timeout']);
				break;

			case 'sendmail':
				// Create sendmail Transport
				$transport = Swift_SendmailTransport::newInstance(isset($config['sendmail']) ? $config['sendmail'] : '/usr/sbin/sendmail -bs');
				break;

			default:
				// Create native Transport
				$transport = Swift_MailTransport::newInstance();
				break;
		}

		// Store connection into instance pool
		self::$instances[$cname] = Swift_Mailer::newInstance($transport);
	}

	/**
	 * @param $cname string Config key from config/email.php
	 *
	 * @return Swift_Mailer instance
	 */
	public static function instance($cname = NULL)
	{
		if ($cname === NULL) $cname = self::$default;

		if (!isset(Email::$instances[$cname]))
		{
			$config = Kohana::$config->load('email')->get(Kohana::$environment);
			new self($cname, $config[$cname]);
		}

		return self::$instances[$cname];
	}

	/**
	 * @param $cname   string       Config key from config/email.php
	 * @param $subject string       Message subject
	 * @param $body    string       Message body
	 * @param $from    string|array From single address (for example 'user@domain.tld' or array('user@domain.tld' => 'John Doe'))
	 * @param $to      string|array To single address (for example 'user@domain.tld' or array('user@domain.tld' => 'John Doe'))
	 *
	 * @return int Result code
	 */
	public static function send($cname, $subject, $body, $from, $to, $html = FALSE)
	{
		$mailer = self::instance($cname);

		//$message = new Swift_Message($subject, $body, 'text/plain', 'utf-8');
		//$message->setFrom($from)->setTo($to);
		
		/** modification from 3.2 **/
		//return $mailer->send($message);
		
		// Determine the message type
		$html = ($html === TRUE) ? 'text/html' : 'text/plain';

		// Create the message
		$message = new Swift_Message($subject, $body, $html, 'utf-8');

		if (is_string($to))
		{
		  // Single recipient
		  $message->setTo($to);
		}
		elseif (is_array($to))
		{
		  if (isset($to[0]) AND isset($to[1]))
		  {
			// Create To: address set
			$to = array('to' => $to);
		  }

		  foreach ($to as $method => $set)
		  {
			if ( ! in_array($method, array('to', 'cc', 'bcc'), TRUE))
			{
			  // Use To: by default
			  $method = 'to';
			}

			// Create method name
			$method = 'add'.ucfirst($method);

			if (is_array($set))
			{
			  // Add a recipient with name
			  $message->$method($set[0], $set[1]);
			}
			else
			{
			  // Add a recipient without name
			  $message->$method($set);
			}
		  }
		}

		if (is_string($from))
		{
		  // From without a name
		  $message->setFrom($from);
		}
		elseif (is_array($from))
		{
		  // From with a name
		  $message->setFrom($from[0], $from[1]);
		}

		try
		{
		  return $mailer->send($message);
		}
		catch (Swift_SwiftException $e)
		{
		  // Throw Kohana Http Exception
		  throw new Http_Exception_408('Connecting to mailserver timed out: :message', array(
			':message' => $e->getMessage()
		  ));
		}
	}
}