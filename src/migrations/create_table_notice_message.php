<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNoticeMessage extends Migration {

	public function up() {
		Schema::create('notice_message', function(Blueprint $table) {
			$table->increments('notice_message_id')
						->unsigned();

			$table->string('title', 200)
						->default('');

			$table->string('type', 20)
						->default('');

			$table->string('contents', 5000)
						->default('');

			$table->dateTime('created_at');

			$table->integer('created_by')
						->unsigned()
						->default(0);

			$table->dateTime('sender_deleted_at')
						->nullable();
		});
	}

	public function down() {
		Schema::dropIfExists('notice_message');
	}
}
