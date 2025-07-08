<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CambioMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("elementos_menus", function (Blueprint $table)
        {
            $table->boolean("mostrar")->default(1);
        });
    }

    /**
     * Revere the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("elementos_menus", function (Blueprint $table)
        {
            $table->dropColumn("mostrar");
        });
    }
}
