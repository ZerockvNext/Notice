<?php

namespace ZerockvNext\Notice;

use Illuminate\Support\Facades\Facade;

class NoticeFacade extends Facade {
	protected static function getFacadeAccessor() {
		return 'notice';
	}
}