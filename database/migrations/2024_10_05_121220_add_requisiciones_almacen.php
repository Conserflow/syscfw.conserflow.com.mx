<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequisicionesAlmacen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("requisiciones2", function (Blueprint $table)
        {
            $table->text("motivo_rechazo")->after("condicion")->nullable();
            $table->integer("aprueba_almacen_id")->after("empleado_aprueba_id")->nullable();

            $table->foreign("aprueba_almacen_id")->references("id")->on("empleados");
        });

        Schema::table("requisicion_materiales_partidas", function (Blueprint $table)
        {
            $table->double("cantidad_almacen")->after("cantidad")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("requisiciones2", function (Blueprint $table)
        {
            $table->dropColumn("motivo_rechazo");
        });

        Schema::table("requisicion_materiales_partidas", function (Blueprint $table)
        {
            $table->dropColumn("cantidad_almacen");
        });
    }
}
