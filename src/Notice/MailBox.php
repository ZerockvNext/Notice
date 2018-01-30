<?php

namespace ZerockvNext\Notice;

use ZerockvNext\Notice\Model\Logic\NoticeTransferLogicModel;

class MailBox {
	const MODE_RECYCLED         = true;
	const MODE_All              = null;
	const MODE_WITHOUT_RECYCLED = false;

	protected $_Receiver = null;
	protected $_Type     = null;
	protected $_Mode     = self::MODE_WITHOUT_RECYCLED;

	protected $_Error        = '';
	protected $TransferModel = null;
	protected $ModeAllow     = [self::MODE_RECYCLED, self::MODE_All, self::MODE_WITHOUT_RECYCLED];

	/* get Params */
	public function getReceiver() { return $this->_Receiver; }

	public function getError() { return $this->_Error; }

	/* set Params */
	public function receiver($Receiver) {
		if(!$this->checkReceiver($Receiver)) {
			throw new \Exception("Param 'Receiver' can not be empty!");
		}
		$this->_Receiver = (int)$Receiver;
		return $this;
	}

	public function mode($Option) {
		if(!$this->checkMode($Option)) {
			throw new \Exception("Mode '{$Option}' is not allow!");
		}
		$this->_Mode = $Option;
		$this->getTransferModel()->filterRemoved($this->_Mode);
		return $this;
	}

	/* helps */
	protected function setError($Error = null) {
		$this->_Error = $Error !== null ?: $this->_Error;
		return false;
	}

	protected function getTransferModel() {
		$this->TransferModel = $this->TransferModel ?: new NoticeTransferLogicModel();
		return $this->TransferModel->filterRemoved($this->_Mode);
	}

	protected function formatMessage($Message) {
		if(!$Message) {
			return $Message;
		}
		$Result                = $Message['message'];
		$Result['read_at']     = $Message['read_at'];
		$Result['deleted_at']  = $Message['receiver_deleted_at'];
		$Result['transfer_id'] = $Message['notice_transfer_id'];
		$Result['sender_id']   = $Message['sender_id'];
		$Result['sender_name'] = $Message['sender']['name'];
		$Result['message_id']  = $Result['notice_message_id'];
		unset($Result['notice_message_id'], $Result['sender_deleted_at']);
		return $Result;
	}

	/* main Functions */
	public function hasUnread($Type = null) { return $this->totalUnread($Type) > 0; }

	public function totalUnread($Type = null) {
		return $this->checkThrowReceiver()->getTransferModel()
								->totalUnread($this->_Receiver, $Type);
	}

	public function totalMessages($Type = null) {
		return $this->checkThrowReceiver()->getTransferModel()
								->totalTransfers($this->_Receiver, $Type);
	}

	public function countUnread($Type = null) {
		$Result = $this->checkThrowReceiver()->getTransferModel()
									 ->countUnread($this->_Receiver, $Type)->toArray();
		return array_column($Result, 'count', 'type');
	}

	public function getPagedMessages($Page, $Limit, $Type = null) {
		$Filter   = ['receiver' => $this->_Receiver, 'type' => $Type];
		$Messages = $this->checkThrowReceiver()->getTransferModel()
										 ->getTransferPaged($Page, $Limit, $Filter)->toArray();

		$PagedMessages = [];
		foreach($Messages as $Message) {
			$PagedMessages[] = $this->formatMessage($Message);
		}
		return $PagedMessages;
	}

	public function getTransfer($TransferID) {
		$Result = $this->checkThrowReceiver()->getTransferModel()
									 ->getTransferByID($TransferID, $this->_Receiver);
		return $Result ? $this->formatMessage($Result->toArray()) : $Result;
	}

	public function read($TransferIDs) {
		$TransferIDs = is_array($TransferIDs) ? $TransferIDs : [(int)$TransferIDs];
		return $this->checkThrowReceiver()->getTransferModel()
								->read($TransferIDs, $this->_Receiver);
	}

	public function readAll($Type = null) {
		return $this->checkThrowReceiver()->getTransferModel()
								->readAll($this->_Receiver, $Type);
	}

	public function unread($TransferIDs) {
		$TransferIDs = is_array($TransferIDs) ? $TransferIDs : [(int)$TransferIDs];
		return $this->checkThrowReceiver()->getTransferModel()
								->unread($TransferIDs, $this->_Receiver);
	}

	public function remove($TransferIDs) {
		$TransferIDs = is_array($TransferIDs) ? $TransferIDs : [(int)$TransferIDs];
		return $this->checkThrowReceiver()->getTransferModel()
								->remove($TransferIDs, $this->_Receiver);
	}

	/* check Params */
	protected function checkReceiver($Receiver) { return isset($Receiver); }

	protected function checkMode($Option) { return in_array($Option, $this->ModeAllow); }

	protected function checkThrowReceiver() {
		if(!$this->checkReceiver($this->_Receiver)) {
			throw new \Exception("Param 'Receiver' not set!");
		}
		return $this;
	}
}