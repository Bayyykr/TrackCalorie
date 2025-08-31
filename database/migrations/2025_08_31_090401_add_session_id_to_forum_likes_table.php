<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionIdToForumLikesTable extends Migration
{
    public function up()
    {
        Schema::table('forum_likes', function (Blueprint $table) {
            $table->string('session_id')->nullable()->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('forum_likes', function (Blueprint $table) {
            $table->dropColumn('session_id');
        });
    }
}
