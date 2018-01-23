<?php

namespace ZerockvNext\Notice;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use ZerockvNext\Notice\Model\Logic\NoticeMessageLogicModel;
use ZerockvNext\Notice\Model\Logic\NoticeTransferLogicModel;

class PostOffice {
	const TYPE_MESSAGE = 'message';
	const TYPE_SYSTEM  = 'system';
	const TYPE_NOTICE  = 'notice';

	protected $_Sender    = null;
	protected $_Type      = self::TYPE_MESSAGE;
	protected $_Title     = '';
	protected $_Message   = '';
	protected $_Receivers = [];

	protected $_Error    = '';
	protected $TypeAllow = [self::TYPE_MESSAGE, self::TYPE_SYSTEM, self::TYPE_NOTICE];

	/* get Params */
	public function getSender() { return $this->_Sender; }

	public function getType() { return $this->_Type; }

	public function getReceivers() { return $this->_Receivers; }

	public function getTitle() { return $this->_Title; }

	public function getMessage() { return $this->_Message; }

	public function getError() { return $this->_Error; }

	public function self() { return $this; }

	/* set Params */
	public function sender($Sender) {
		if(!$this->checkSender($Sender)) {
			throw new \Exception("Param 'Sender' can not be empty!");
		}
		$this->_Sender = (int)$Sender;
		return $this;
	}

	public function type($Type) {
		if(!$this->checkType($Type)) {
			throw new \Exception("Type '{$Type}' is not allow!");
		}
		$this->_Type = strtolower($Type);
		return $this;
	}

	public function title($Title) {
		$this->_Title = (string)$Title;
		return $this;
	}

	public function message($Message) {
		$this->_Message = (string)$Message;
		return $this;
	}

	public function addReceiver($Receiver) {
		if(!$this->checkReceiver($Receiver)) {
			throw new \Exception("Param 'Receiver' can not be empty!");
		}

		$Receiver = (int)$Receiver;
		if(!in_array($Receiver, $this->_Receivers)) {
			$this->_Receivers[] = $Receiver;
		}
		return $this;
	}

	public function addReceivers($Receivers) {
		foreach($Receivers as $Receiver) {
			$this->addReceiver($Receiver);
		}
		return $this;
	}

	public function removeReceiver($Receiver) {
		if(!$this->checkReceiver($Receiver)) {
			throw new \Exception("Param 'Receiver' can not be empty!");
		}

		$Receiver = (int)$Receiver;
		if(($Index = array_search($Receiver, $this->_Receivers)) !== false) {
			array_splice($this->_Receivers, $Index, 1);
		}
		return $this;
	}

	public function reset() {
		$this->_Sender    = null;
		$this->_Type      = self::TYPE_MESSAGE;
		$this->_Title     = '';
		$this->_Message   = '';
		$this->_Receivers = [];
		$this->_Error     = '';
		return $this;
	}

	/* helps */
	protected function setError($Error = null) {
		$this->_Error = $Error !== null ?: $this->_Error;
		return false;
	}

	protected function rollBackAndFailed($Error = null) {
		DB::rollBack();
		return $this->setError($Error);
	}

	/* send */
	public function send() {
		$this->checkAllParams();
		DB::beginTransaction();

		if(!$this->beforeSend()) {
			return $this->rollBackAndFailed(null);
		}

		if(!$MessageModel = $this->createMessage()) {
			return $this->rollBackAndFailed('create message failed!');
		}

		if(!$this->transportMessage($MessageModel->notice_message_id)) {
			return $this->rollBackAndFailed('transport message failed!');
		}

		if(!$this->afterSend()) {
			return $this->rollBackAndFailed(null);
		}

		DB::commit();
		return true;
	}

	protected function beforeSend() { return true; }

	protected function afterSend() { return true; }

	/* handle Message */
	protected function createMessage() {
		return (new NoticeMessageLogicModel())->create($this->buildMessage());
	}

	protected function buildMessage() {
		return [
			'title'      => $this->_Title,
			'type'       => $this->_Type,
			'contents'   => $this->_Message,
			'created_at' => Carbon::now(),
			'created_by' => $this->_Sender,
		];
	}

	/* handle Transport */
	protected function transportMessage($MessageID) {
		return (new NoticeTransferLogicModel())->insert($this->buildTransfers($MessageID));
	}

	protected function buildTransfers($MessageID) {
		$Transfers = [];
		foreach($this->_Receivers as $Receiver) {
			$Transfers[] = [
				'notice_message_id' => $MessageID,
				'sender_id'         => $this->_Sender,
				'receiver_id'       => $Receiver,
				'type'              => $this->_Type,
				'created_at'        => Carbon::now(),
			];
		}
		return $Transfers;
	}

	/* check Params */
	protected function checkAllParams() {
		if(!$this->checkSender($this->_Sender)) {
			throw new \Exception("Param 'Sender' not set!");
		}

		if(!$this->checkReceivers($this->_Receivers)) {
			throw new \Exception("Param 'Receivers' not set!");
		}
	}

	protected function checkSender($Sender) { return isset($Sender); }

	protected function checkType($Type) { return in_array(strtolower($Type), $this->TypeAllow); }

	protected function checkReceiver($Receiver) { return isset($Receiver); }

	protected function checkReceivers($Receivers) { return (count($Receivers) > 0); }
}