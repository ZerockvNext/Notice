<?php

namespace ZerockvNext\Notice\Model\Struct;

use Illuminate\Database\Eloquent\Model;
use App\Model\Account\UserModel;

class NoticeTransferStructModel extends Model {
	public $timestamps = false;

	protected $table           = 'notice_transfer';
	protected $primaryKey      = 'notice_transfer_id';
	protected $noticeMessageId = 'notice_message_id';
	protected $fillable        = [
		'sender_id',
		'receiver_id',
		'type',
		'created_at',
		'read_at',
		'receiver_deleted_at',
	];

	public function __construct(array $attributes = []) {
		parent::__construct($attributes);
		$this->table           = config('notice.table.notice_transfer', 'notice_transfer');
		$this->primaryKey      = config('notice.field.notice_transfer_id', 'notice_transfer_id');
		$this->noticeMessageId = config('notice.field.notice_message_id', 'notice_message_id');
		$this->fillable[]      = $this->noticeMessageId;
	}

	public function message() {
		return $this->belongsTo(
			NoticeMessageStructModel::class,
			$this->noticeMessageId,
			$this->noticeMessageId
		);
	}

	public function sender() {
		return $this->hasOne(
			config('notice.orm.user', 'App\User'),
			config('notice.field.user_id', 'id'),
			'sender_id'
		);
	}

	public function receiver() {
		return $this->hasOne(
			config('notice.orm.user', 'App\User'),
			config('notice.field.user_id', 'id'),
			'receiver_id'
		);
	}
}
