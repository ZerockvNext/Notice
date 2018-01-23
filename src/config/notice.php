<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Table
	|--------------------------------------------------------------------------
	|
	| This is the tables used by Notice.
	| if you changed migration, you should modify these configurations at the
	| same time.
	|
	*/
	'table' => [
		'notice_message'  => 'notice_message',
		'notice_transfer' => 'notice_transfer',
	],

	/*
	|--------------------------------------------------------------------------
	| Field
	|--------------------------------------------------------------------------
	|
	| This is the field used by Notice.
	| if you changed migration, you should modify these configurations at the
	| same time.
	| if you changed notice_message_id to a new key, you must also modify
	| notice_message_id in the notice_transfer table too.
	|
	*/

	'field' => [
		'notice_message_id'  => 'notice_message_id',
		'notice_transfer_id' => 'notice_transfer_id',
		'user_id'            => 'user_id',
	],

	/*
	|--------------------------------------------------------------------------
	| ORM
	|--------------------------------------------------------------------------
	|
	| This is the class for your user Eloquent ORM in your App.
	|
	*/

	'orm' => [
		'user' => \App\User::class,
	],
];