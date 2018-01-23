<?php

namespace ZerockvNext\Notice;

class NoticeEntrance {
	protected $MailBox;
	protected $PostOffice;
	protected $Consts;

	public function MailBox() {
		$this->MailBox = $this->MailBox ?: new MailBox();
		return $this->MailBox;
	}

	public function PostOffice() {
		$this->PostOffice = $this->PostOffice ?: new PostOffice();
		return $this->PostOffice;
	}

	public function Consts() {
		$this->Consts = $this->Consts ?: new Consts();
		return $this->Consts;
	}
}