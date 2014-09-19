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
	 * Sends a message to the connected clients. Returns `true` on success and `false`
	 * otherwise.
	 * 
	 * If `$userIDs` is an empty array the message will be sent to every connected client. 
	 * Otherwise the message will only be sent to clients with the given userID.
	 * 
	 * ATTENTION: Do NOT (!) send any security related information via sendMessage.
	 * Not every push service can validate whether the userID given was forged by a malicious client!
	 * 
	 * @param	string			$message
	 * @param	array<integer>	$userIDs
	 * @return	boolean
	 */
	public function sendMessage($message, $userIDs = array()) {
		if (!$this->isEnabled()) return false;
		if (!\wcf\data\package\Package::isValidPackageName($message)) return false;
		$userIDs = array_unique(\wcf\util\ArrayUtil::toIntegerArray($userIDs));
		
		$backend = PUSH_BACKEND;
		return (boolean) $backend::getInstance()->sendMessage($message, $userIDs);
	}
	
	/**
	 * Registers a deferred message. Returns `true` on any well-formed message and `false`
	 * otherwise.
	 * Deferred messages will be sent on shutdown. This can be useful if your handler depends
	 * on data that may not be written to database yet or to achieve a better performance as the
	 * page is delivered first.
	 * 
	 * ATTENTION: Use this method only if your messages are not critical as you cannot check
	 * whether your message was delivered successfully.
	 * ATTENTION: Do NOT (!) send any security related information via sendDeferredMessage.
	 * Not every push service can validate whether the userID given was forged by a malicious client!
	 * 
	 * @see	\wcf\system\push\PushHandler::sendMessage()
	 */
	public function sendDeferredMessage($message, $userIDs = array()) {
		if (!$this->isEnabled()) return false;
		if (!\wcf\data\package\Package::isValidPackageName($message)) return false;
		$userIDs = array_unique(\wcf\util\ArrayUtil::toIntegerArray($userIDs));
		
		$this->deferred[] = array(
			'message' => $message,
			'userIDs' => $userIDs
		);
		
		return true;
	}
	
	/**
	 * Sends out the deferred messages.
	 */
	public function __destruct() {
		foreach ($this->deferred as $data) {
			$this->sendMessage($data['message'], $data['userIDs']);
		}
	}
}
