<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNoticeTransfer extends Migration {
	public function up() {
		Schema::create('notice_transfer', function(Blueprint $table) {
			$table->increments('notice_transfer_id')
						->unsigned();

			$table->integer('notice_message_id')
						->unsigned()
						->default(0);

			$table->integer('sender_id')
						->unsigned()
						->default(0);

			$table->integer('receiver_id')
						->unsigned()
						->default(0);

			$table->string('type', 20)
						->default('');

			$table->dateTime('created_at');

			$table->dateTime('read_at')
						->nullable();

			$table->dateTime('receiver_deleted_at')
						->nullable();
		});
	}

	public function down() {
		Schema::dropIfExists('notice_transfer');
	}
}
