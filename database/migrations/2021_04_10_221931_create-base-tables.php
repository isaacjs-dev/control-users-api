<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('email', 128)->unique();
            $table->string('cpf', 16)->unique();
            $table->string('username', 64)->unique();
            $table->string('password', 128);
            $table->string('password_token', 128)->nullable();
            $table->string('photo', 128)->nullable();
            $table->string('phone', 16)->nullable();
            $table->integer('id_access_groups'); //Grupo de acesso
            $table->integer('id_user_states'); //Ativi | inativo
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
        });

        // Grupo de Acesso Tipo: (Exemplo)
        //  1 - Super administrador / Master
        //  2 - Administrador
        //  3 - Gerente IPTU
        //  4 - Gerente Divida Ativa
        //  5 - Gerente ISSS

        Schema::create('access_groups', function (Blueprint $table) {
            $table->id();
            $table->string('description', 128);
            $table->integer('order')->nullable();
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('component_access', function (Blueprint $table) {
            $table->id();
            $table->integer('id_access_group')->nullable();
            $table->integer('id_user')->nullable();
            $table->integer('id_component');
            $table->integer('permission')->default(1);
            $table->integer('menu')->default(-1);
            $table->integer('order')->nullable();
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->integer('type'); // 1-Routes | 2-Compoment | 3-Grupo de menus
            $table->string('name', 32);
            $table->string('description', 32);
            $table->string('route', 128)->nullable();
            $table->string('note')->nullable();

            /*
             *   O campo menu_item por padão é setado com o valor null ou 0, o
             * que segnifica que não ira aparecer no menu de opções. Esse campo
             * poderear assmumir o valor 1 se for um menu raiz, ou 2 se for um
             * submenu. Por definição compomentes não são mostrados no memu
            */
            $table->integer('menu_item')->nullable();

            /*
             *   O campo id_component_menu por padão é setado com o valor null
             * ou 0 sempre que o item for um componente (o queal não aparecerar
             * no menu), ou for um menu raiz, e receberar o id do item raiz
             * quando o mesmo for um submenu.
            */
            $table->integer('id_component_menu')->nullable();

            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted')->default(-1);
            $table->timestamp('deleted_at')->nullable();
        });

        /*
            Schema::create('', function (Blueprint $table) {
                $table->id();
            });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('access_groups');
        Schema::dropIfExists('component_access');
        Schema::dropIfExists('component');
    }
}
