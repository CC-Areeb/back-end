## Custom user table reset command

`php artisan app:reset` 
- This command will first truncate the users table then add a super admin user 
- Make sure there is no foreign key constraints
