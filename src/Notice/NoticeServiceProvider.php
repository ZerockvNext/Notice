<?php

namespace ZerockvNext\Notice;

use Illuminate\Support\ServiceProvider;

class NoticeServiceProvider extends ServiceProvider {
	protected $Migrations = [
		'CreateTableNoticeMessage'  => 'create_table_notice_message',
		'CreateTableNoticeTransfer' => 'create_table_notice_transfer',
	];

	public function register() {
		$this->app->bind('notice', function() {
			return new NoticeEntrance();
		});
	}

	public function boot() {
		$this->publishConfig();
		$this->publishMigration();
	}

	protected function publishConfig() {
		$this->publishes([__DIR__ . '/../config/notice.php' => config_path('notice.php')]);
		$this->mergeConfigFrom(__DIR__ . '/../config/notice.php', 'notice');
	}

	protected function publishMigration() {
		foreach($this->Migrations as $Class => $File) {
			if(class_exists($Class)) {
				continue;
			}
			$this->copyMigrationFile($File);
		}
	}

	protected function copyMigrationFile($FileName) {
		$Ext      = '.php';
		$FileName = $FileName . $Ext;
		$Source   = $this->getMigrationSourcePath($FileName);
		$Target   = $this->getMigrationTargetPath($FileName);
		$this->publishes([$Source => $Target], 'migrations');
	}

	protected function getMigrationSourcePath($FileName) {
		$SourceDir = __DIR__ . '/../migrations/';
		return $SourceDir . $FileName;
	}

	protected function getMigrationTargetPath($FileName) {
		$TargetDir = base_path('/database/migrations/');
		$TimeNow   = date('Y_m_d_His', time());
		return $TargetDir . $TimeNow . '_' . $FileName;
	}
}