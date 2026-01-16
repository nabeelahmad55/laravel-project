// 2024_01_01_000002_create_teams_table.php
Schema::create('teams', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->foreignId('owner_id')->constrained('users');
    $table->boolean('is_public')->default(false);
    $table->timestamps();
});