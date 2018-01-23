<?php

namespace ZerockvNext\Notice\Model\Struct;

use Illuminate\Database\Eloquent\Model;
use App\Model\Account\UserModel;

class NoticeMessageStructModel extends Model {
	public $timestamps = false;

	protected $table      = 'notice_message';
	protected $primaryKey = 'notice_message_id';
	protected $fillable   = [
		'title',
		'type',
		'contents',
		'created_at',
		'created_by',
		'sender_deleted_at',
	];

	public function __construct(array $attributes = []) {
		parent::__construct($attributes);
		$this->table      = config('notice.table.notice_message', 'notice_message');
		$this->primaryKey = config('notice.field.notice_message_id', 'notice_message_id');
	}

	public function sender() {
		return $this->hasOne(
			config('notice.orm.user', 'App\User'),
			config('notice.field.user_id', 'id'),
			'created_by'
		);
	}

	public function receivers() {
		return $this->hasManyThrough(
			config('notice.orm.user', 'App\User'),
			NoticeTransferStructModel::class,
			'receiver_id',
			config('notice.orm.user', 'id'),
			$this->primaryKey
		);
	}
}
