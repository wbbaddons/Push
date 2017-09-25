<?php
namespace wcf\system\push;
use wcf\util\StringUtil;

/**
 * Push Handler.
 *
 * @author	Tim Düsterhus
 * @copyright	2012-2013 Tim Düsterhus
 * @license	BSD 3-Clause License <http://opensource.org/licenses/BSD-3-Clause>
 * @package	be.bastelstu.wcf.push
 * @subpackage	system.push
 */
class PushHandler extends \wcf\system\SingletonFactory {
	/**
	 * Array of messages to send at shutdown.
	 * 
	 * @var	array<string>
	 */
	private $deferred = array();
	
	/**
	 * Channels to join for this page view.
	 *
	 * @var	string[]
	 */
	private $channels = [ ];
	
	/**
	 * Returns whether a push service is enabled.
	 * 
	 * @return	boolean
	 */
	public function isEnabled() {
		if (!defined('PUSH_BACKEND')) return false;
		
		$backend = PUSH_BACKEND;
		return (boolean) $backend::getInstance()->isEnabled();
	}
	
	/**
	 * Returns whether the push service appears to be available.
	 * 
	 * @return	boolean
	 */
	public function isRunning() {
		if (!$this->isEnabled()) return false;
		
		$backend = PUSH_BACKEND;
		return (boolean) $backend::getInstance()->isRunning();
	}
	
	/**
	 * @see	\wcf\system\push\PushHandler::getFeatureFlags()
	 */
	public function getFeatureFlags() {
		$backend = PUSH_BACKEND;
		if (method_exists($backend, 'getFeatureFlags')) {
			return $backend::getInstance()->getFeatureFlags();
		}
		
		return [ ];
	}
	
	/**
	 * Sends a message to the connected clients. Returns `true` on success and `false`
	 * otherwise.
	 * 
	 * $message must be an array containing the following:
	 * message: string - The message to send.
	 * payload: ?array - Additional data to send.
	 * target: ?array - Targets to send to. The message is send to the Union of Targets.
	 * 
	 * @param	array		$message
	 * @return	boolean
	 */
	public function sendMessage($message, array $userIDs = array(), array $payload = array()) {
		if (!$this->isEnabled()) return false;
		
		$backend = PUSH_BACKEND;
		return (boolean) $backend::getInstance()->sendMessage($message, $userIDs, $payload);
	}
	
	/**
	 * @deprecated
	 */
	public function sendDeferredMessage($message, array $userIDs = array(), array $payload = array()) {
		if (!$this->isEnabled()) return false;
		
		$this->deferred[] = array(
			'message' => $message,
			'userIDs' => $userIDs,
			'payload' => $payload
		);
		
		return true;
	}
	
	/**
	 * Sends out the deferred messages.
	 */
	public function __destruct() {
		foreach ($this->deferred as $data) {
			$this->sendMessage($data['message'], $data['userIDs'], $data['payload']);
		}
	}
	
	/**
	 * Joins the given channel for this request.
	 *
	 * @param	string	$name
	 */
	public function joinChannel($name) {
		$this->channels[$name] = $name;
	}
	
	/**
	 * Returns the list of joined channels.
	 *
	 * @return	string[]
	 */
	public function getChannels() {
		return array_values($this->channels);
	}
}
