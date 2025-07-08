<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Requisiciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("articulos_unidades_medida", function (Blueprint $table)
        {
            $table->increments("id");
            $table->string("nombre")->unique();
            $table->integer("empleado_registra_id");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("requisiciones_tipos", function (Blueprint $table)
        {
            $table->increments("id");
            $table->string("nombre")->unique();
            $table->string("ruta");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("requisiciones2", function (Blueprint $table)
        {
            $table->increments("id");
            $table->string("folio", 50);
            $table->string("lugar_entrega", 100);
            $table->string("revision", 2);
            $table->date("fecha_emision");
            $table->date("fecha_entrega");
            $table->unsignedInteger("proyecto_id");
            $table->unsignedInteger("tipo_id");
            $table->unsignedInteger("area_id")->comment("departamento");
            $table->unsignedInteger("empleado_solicita_id");
            $table->unsignedInteger("empleado_aprueba_id");
            $table->text("notas")->nullable();
            $table->string("motivo_eliminacion")->nullable();
            $table->integer("condicion")->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("tipo_id")->references("id")->on("requisiciones_tipos")
                ->onDelete("cascade");
        });

        Schema::create("requis_personal_aprueba", function (Blueprint $table)
        {
            $table->increments("id");
            $table->integer("empleado_id")->unique();
            $table->softDeletes();
        });

        Schema::create("requisicion_materiales_partidas", function (Blueprint $table)
        {
            $table->increments("id");
            $table->unsignedInteger("requi_id");
            $table->string("comentarios")->nullable();
            $table->string("concepto", 500);
            $table->string("marca", 50);
            $table->integer("tipo");
            $table->double("cantidad");
            $table->text("documentos_requeridos");
            $table->unsignedInteger("um_id");
            $table->integer("empleado_registra_id");
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("um_id")
                ->references("id")->on("articulos_unidades_medida");

            $table->foreign("requi_id")
                ->references("id")->on("requisiciones2");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("requisiciones");
        Schema::dropIfExists("requisiciones_tipos");
        Schema::dropIfExists("articulos_unidades_medida");
    }
}
