<?php

namespace App\Database\Migrations;

use Jumilla\Versionia\Laravel\Support\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Framework_1_0 extends Migration
{
    /**
     * Upgrade database.
     *
     * @return void
     */
    public function up()
    {
        $this->createSessionsTable();
    }

    /**
     * Downgrade database.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
    }

    /**
     * Create 'sessions' table.
     *
     * @return void
     */
    protected function createSessionsTable()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->text('payload');
            $table->integer('last_activity');
        });
    }
}
