<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    private string $table = 'notices';

    public function up(): void
    {
        if (!Schema::hasTable($this->table)) {
            //创建表
            Schema::create($this->table, function (Blueprint $table) {
                $table->id();
                $table->string('title')->comment('标题');
                $table->text('content')->comment('内容');
                $table->string('type')->default('system')->comment('类别'); // system, private, group
                $table->json('target_users')->nullable()->comment('目标用户'); // 目标用户ID数组
                $table->boolean('is_global')->default(0)->comment('是否全局通知'); // 是否全局通知
                $table->boolean('is_read')->default(0)->comment('是否已读');
                $table->timestamp('read_at')->nullable()->comment('查看时间');
                $table->unsignedBigInteger('sender_id')->nullable()->comment('发送人');
                $table->unsignedBigInteger('receiver_id')->nullable()->comment('接收人');
                $table->integer('weight')->default(0)->comment('权重，数值越小权重越大');
                $table->timestamp('expires_at')->nullable()->comment('过期时间');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['receiver_id', 'is_read']);
                $table->index(['is_global', 'created_at']);
                $table->index('expires_at');
                $table->comment('通知公告表');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable($this->table)) {
            //检查是否存在数据
            $exists = DB::table($this->table)->exists();
            //不存在数据时，删除表
            if (!$exists) {
                //删除 reverse
                Schema::dropIfExists($this->table);
            }
        }
    }
};
