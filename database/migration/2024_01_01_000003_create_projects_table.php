// 2024_01_01_000003_create_projects_table.php
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->foreignId('team_id')->constrained()->cascadeOnDelete();
    $table->foreignId('created_by')->constrained('users');
    $table->enum('status', ['planning', 'active', 'completed', 'archived'])->default('planning');
    $table->date('due_date')->nullable();
    $table->timestamps();
});