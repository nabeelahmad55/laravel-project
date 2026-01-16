// 2024_01_01_000004_create_tasks_table.php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->foreignId('project_id')->constrained()->cascadeOnDelete();
    $table->foreignId('assigned_to')->nullable()->constrained('users');
    $table->foreignId('created_by')->constrained('users');
    $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
    $table->enum('status', ['todo', 'in_progress', 'review', 'completed'])->default('todo');
    $table->integer('position')->default(0);
    $table->date('due_date')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
});