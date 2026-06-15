<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\EnvFile;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Throwable;

class GeneratePostmanApiTokenCommand extends Command
{
    protected $signature = 'api:token:generate';

    protected $description = 'Generate POSTMAN_API_TOKEN and save to .env';

    public function handle(): int
    {
        $user = User::firstOrCreate(
            ['email' => 'api@internal'],
            [
                'name' => 'postman',
                'password' => bcrypt(Str::random(64)),
            ],
        );

        $user->tokens()->where('name', 'postman')->delete();
        [, $secret] = explode('|', $user->createToken('postman')->plainTextToken, 2);

        try {
            EnvFile::set('POSTMAN_API_TOKEN', $secret);
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info("POSTMAN_API_TOKEN: {$secret} saved to .env");
        return self::SUCCESS;
    }
}
