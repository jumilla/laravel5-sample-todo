<?php

namespace App\Database\Migrations;

use Jumilla\Versionia\Laravel\Support\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class App_1_0 extends Migration
{
    /**
     * Upgrade database.
     *
     * @return void
     */
    public function up()
    {
        $this->createTodosTable();
    }

    /**
     * Downgrade database.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('todos');
    }

    /**
     * Create 'todos' table.
     *
     * @return void
     */
    protected function createTodosTable()
    {
        Schema::create('todos', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('status');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
