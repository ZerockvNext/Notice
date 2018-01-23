<?php

namespace ZerockvNext\Notice\Model\Logic;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

use ZerockvNext\Notice\Model\Struct\NoticeTransferStructModel;

class NoticeTransferLogicModel extends NoticeTransferStructModel {

	protected $_filterRemoved = false;

	public function getTransferByID($TransferID, $Receiver = null) {
		$Query = static::query()->with(['sender', 'message']);
		$this->queryTransferIDs($Query, [$TransferID]);
		$this->queryReceiver($Query, $Receiver);
		return $Query->first();
	}

	public function getTransferPaged($Page, $Limit = 15, $Filter = [], $OrderBy = 'created_at', $Direction = 'DESC') {
		$Page    = (int)$Page < 1 ? 1 : (int)$Page;
		$Limit   = (int)$Limit <= 100 ? (int)$Limit : 100;
		$Offset  = ($Page - 1) * $Limit;
		$Columns = isset($Filter['columns']) ? [$Filter['columns']] : ['*'];

		return $this->buildFilter($Filter)
								->with(['sender', 'message'])
								->orderBy($OrderBy, $Direction)
								->offset($Offset)
								->limit($Limit)
								->get($Columns);
	}

	public function totalTransfers($ReceiverID, $Type = null) {
		$Filter = ['receiver' => $ReceiverID, 'type' => $Type];
		return $this->buildFilter($Filter)->count();
	}

	public function totalUnread($ReceiverID, $Type = null) {
		$Query = static::query();
		$this->queryReceiver($Query, $ReceiverID);
		$this->queryType($Query, $Type);
		$this->queryUnread($Query);
		$this->queryRemoved($Query);

		return $Query->count('type');
	}

	public function countUnread($ReceiverID, $Type = null) {
		$Query = static::query();
		$this->queryReceiver($Query, $ReceiverID);
		$this->queryType($Query, $Type);
		$this->queryUnread($Query);
		$this->queryRemoved($Query);
		return $Query->groupBy('type')->get(['type', \DB::raw('count(type) as `count`')]);
	}

	public function read($TransferIDs, $Receiver = null) {
		$Query = static::query();
		$this->queryTransferIDs($Query, $TransferIDs);
		$this->queryReceiver($Query, $Receiver);
		return $Query->update(['read_at' => Carbon::now()]);
	}

	public function readAll($Receiver) {
		return $this->queryReceiver(static::query(), $Receiver)
								->update(['read_at' => Carbon::now()]);
	}

	public function unread($TransferIDs, $Receiver = null) {
		$Query = static::query();
		$this->queryTransferIDs($Query, $TransferIDs);
		$this->queryReceiver($Query, $Receiver);
		return $Query->update(['read_at' => null]);
	}

	public function remove($TransferIDs, $Receiver = null) {
		$Query = static::query();
		$this->queryTransferIDs($Query, $TransferIDs);
		$this->queryReceiver($Query, $Receiver);
		return $Query->update(['receiver_deleted_at' => Carbon::now()]);
	}

	/**
	 * @param bool|null $Option
	 *   null: include removed;
	 *   true: only removed;
	 *   false: with out removed;
	 *
	 * @return self
	 */
	public function filterRemoved($Option = false) {
		$this->_filterRemoved = ($Option === null) ? null : (bool)$Option;
		return $this;
	}

	protected function buildFilter($Filter) {
		$Builder = static::query();

		if(isset($Filter['receiver'])) {
			$this->queryReceiver($Builder, $Filter['receiver']);
		}

		if(!empty($Filter['type'])) {
			$this->queryType($Builder, $Filter['type']);
		}

		return $this->queryRemoved($Builder);
	}

	protected function queryRemoved(Builder $Query) {
		if($this->_filterRemoved === null) {
			return $Query;
		}
		return ($this->_filterRemoved ?
			$Query->whereNotNull('receiver_deleted_at') :
			$Query->whereNull('receiver_deleted_at'));
	}

	protected function queryUnread(Builder $Query) {
		return $Query->whereNull('read_at');
	}

	protected function queryType(Builder $Query, $Type) {
		return ($Type === null) ? $Query : $Query->where('type', (string)$Type);
	}

	protected function queryTransferIDs(Builder $Query, $TransferIDs) {
		$TransferIDs = is_array($TransferIDs) ? $TransferIDs : [$TransferIDs];
		return ($TransferIDs === []) ? $Query : $Query->whereIn($this->primaryKey, $TransferIDs);
	}

	protected function queryReceiver(Builder $Query, $Receiver) {
		return ($Receiver === null) ? $Query : $Query->where('receiver_id', (int)$Receiver);
	}
}
