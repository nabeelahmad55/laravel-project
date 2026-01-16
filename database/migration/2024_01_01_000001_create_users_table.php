// 2024_01_01_000001_create_users_table.php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('avatar_url')->nullable();
    $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
    $table->rememberToken();
    $table->timestamps();
});