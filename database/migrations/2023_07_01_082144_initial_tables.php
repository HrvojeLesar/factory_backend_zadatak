<?php

use App\Tables\Tables;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    const LOCALE_CHAR_LEN = 2;

    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create(Tables::$CATEGORIES, function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create(Tables::$MEALS, function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->timestamps();
            $table->foreignId("category_id")->nullable()->references("id")->on(Tables::$CATEGORIES);
        });

        Schema::create(Tables::$INGREDIENTS, function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create(Tables::$TAGS, function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create(Tables::$MEAL_INGREDIENTS, function (Blueprint $table) {
            $table->softDeletes();
            $table->timestamps();
            $table->foreignId("meal_id")->nullable(false)->references("id")->on(Tables::$MEALS);
            $table->foreignId("ingredient_id")->nullable(false)->references("id")->on(Tables::$INGREDIENTS);
            $table->primary(["meal_id", "ingredient_id"]);
        });

        Schema::create(Tables::$MEAL_TAGS, function (Blueprint $table) {
            $table->softDeletes();
            $table->timestamps();
            $table->foreignId("meal_id")->nullable(false)->references("id")->on(Tables::$MEALS);
            $table->foreignId("tag_id")->nullable(false)->references("id")->on(Tables::$TAGS);
            $table->primary(["meal_id", "tag_id"]);
        });

        Schema::create(Tables::$LANGUAGES, function (Blueprint $table) {
            $table->string("locale", self::LOCALE_CHAR_LEN);
            $table->string("language");
            $table->softDeletes();
            $table->timestamps();
            $table->primary("locale");
        });

        Schema::create(Tables::$CATEGORY_TRANSLATIONS, function (Blueprint $table) {
            $table->id();
            $table->string("locale", self::LOCALE_CHAR_LEN)->index();
            $table->foreignId("category_id")->references("id")->on(Tables::$CATEGORIES);
            $table->string("title");
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create(Tables::$MEAL_TRANSLATIONS, function (Blueprint $table) {
            $table->id();
            $table->string("locale", self::LOCALE_CHAR_LEN);
            $table->foreignId("meal_id")->references("id")->on(Tables::$MEALS)->onDelete("cascade");
            $table->string("title");
            $table->string("description");
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create(Tables::$INGREDIENT_TRANSLATIONS, function (Blueprint $table) {
            $table->id();
            $table->string("locale", self::LOCALE_CHAR_LEN)->index();
            $table->foreignId("ingredient_id")->references("id")->on(Tables::$INGREDIENTS)->onDelete("cascade");
            $table->string("title");
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create(Tables::$TAG_TRANSLATIONS, function (Blueprint $table) {
            $table->id();
            $table->string("locale", self::LOCALE_CHAR_LEN)->index();
            $table->foreignId("tag_id")->references("id")->on(Tables::$TAGS)->onDelete("cascade");
            $table->string("title");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Tables::$MEAL_TAGS);
        Schema::dropIfExists(Tables::$MEAL_INGREDIENTS);
        Schema::dropIfExists(Tables::$TAGS);
        Schema::dropIfExists(Tables::$INGREDIENTS);
        Schema::dropIfExists(Tables::$MEALS);
        Schema::dropIfExists(Tables::$CATEGORIES);

        Schema::dropIfExists(Tables::$TAG_TRANSLATIONS);
        Schema::dropIfExists(Tables::$INGREDIENT_TRANSLATIONS);
        Schema::dropIfExists(Tables::$MEAL_TRANSLATIONS);
        Schema::dropIfExists(Tables::$CATEGORY_TRANSLATIONS);
        Schema::dropIfExists(Tables::$LANGUAGES);
    }
};
