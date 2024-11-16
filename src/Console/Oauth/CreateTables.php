<?php

namespace SlimFramework\Console\Oauth;

use Illuminate\Database\Schema\Blueprint;
use SlimFramework\Enum\EnumProfile;
use SlimFramework\Migration\ConsoleMigration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateTables extends ConsoleMigration
{
    /**
     * @return string
     */
    protected function getConnectionName(): string
    {
        return 'default';
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('slim:create-tables');
        $this->setDescription('Create the tables from oauth.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Mannaging oauth2</info>');

        $output->writeln('<comment>Droping existing tables...</comment>');
        $this->dropTables();

        $output->writeln('<comment>Creating tables...</comment>');
        $this->createTables();

        $output->writeln('<comment>Iserting data...</comment>');
        $this->insertData();

        return Command::SUCCESS;
    }


    /**
     *  Delete tables.
     */
    private function dropTables(): void
    {
        $this->schemaBuilder()->dropIfExists('oauth2_scope');
        $this->schemaBuilder()->dropIfExists('oauth2_refresh_token');
        $this->schemaBuilder()->dropIfExists('oauth2_access_token');
        $this->schemaBuilder()->dropIfExists('user');
        $this->schemaBuilder()->dropIfExists('profile');
        $this->schemaBuilder()->dropIfExists('oauth2_auth_code');
        $this->schemaBuilder()->dropIfExists('oauth2_session');
        $this->schemaBuilder()->dropIfExists('oauth2_client');
    }

    /**
     * Crate tables.
     */
    private function createTables(): void
    {
        if (!$this->schemaBuilder()->hasTable('oauth2_scope')) {
            $this->schemaBuilder()->create('oauth2_scope', function (Blueprint $table) {
                $table->id();

                $table->string('description', 255);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->softDeletes('deleted_at', 0);
            });
        }

        if (!$this->schemaBuilder()->hasTable('oauth2_client')) {
            //oauth2_client
            $this->schemaBuilder()->create('oauth2_client', function (Blueprint $table) {
                $table->id();

                $table->string('identifier', 255);
                $table->string('secret', 255);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->softDeletes('deleted_at', 0);
            });
        }

        if (!$this->schemaBuilder()->hasTable('oauth2_session')) {
            //oauth2_session
            $this->schemaBuilder()->create('oauth2_session', function (Blueprint $table) {
                $table->id();

                $table->foreignId('oauth2_client_id')->constrained('oauth2_client');
                $table->string('owner_type', 255);
                $table->string('owner_id', 255);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->softDeletes('deleted_at', 0);
            });
        }

        if (!$this->schemaBuilder()->hasTable('oauth2_auth_code')) {
            //oauth2_auth_code
            $this->schemaBuilder()->create('oauth2_auth_code', function (Blueprint $table) {
                $table->id();

                $table->foreignId('oauth2_session_id')->constrained('oauth2_session');
                $table->integer('expire_time')->nullable();
                $table->string('client_redirect_id', 255);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->softDeletes('deleted_at', 0);
            });
        }

        if (!$this->schemaBuilder()->hasTable('profile')) {
            //user
            $this->schemaBuilder()->create('profile', function (Blueprint $table) {
                $table->id();

                $table->string('name', 45);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->softDeletes('deleted_at', 0);
            });
        }

//        if (!$this->schemaBuilder()->hasTable('client')) {
//            //client
//            $this->schemaBuilder()->create('client', function ($table) {
//                $table->id();

//
//                //FK
//                $table->integer('oauth2_client_id')->unsigned();
//                $table->foreign('oauth2_client_id')->references('id')->on('oauth2_client');
//                $table->string('name', 45);
//                $table->tinyInteger('status');
//                $table->dateTime('created_at');
//                $table->dateTime('updated_at');
//                $table->softDeletes('deleted_at', 0);
//            });
//        }

        if (!$this->schemaBuilder()->hasTable('user')) {
            //user
            $this->schemaBuilder()->create('user', function (Blueprint $table) {
                $table->id();

                //FK
                $table->foreignId('profile_id')->constrained('profile');
                $table->foreignId('oauth2_client_id')->constrained('oauth2_client');

                $table->string('username', 45);
                $table->string('password', 255);
                $table->string('email', 255)->nullable();
                $table->string('name', 255)->nullable();
                $table->tinyInteger('status');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->softDeletes('deleted_at', 0);
            });
        }

        if (!$this->schemaBuilder()->hasTable('oauth2_access_token')) {
            //oauth2_access_token
            $this->schemaBuilder()->create('oauth2_access_token', function (Blueprint $table) {
                $table->id();

                $table->foreignId('oauth2_client_id')->constrained('oauth2_client');

                //FK
                $table->foreignId('user_id')->constrained('user');

                $table->string('access_token', 255)->nullable();
                $table->dateTime('expiry_date_time')->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->softDeletes('deleted_at', 0);
            });
        }

        if (!$this->schemaBuilder()->hasTable('oauth2_refresh_token')) {
            //oauth2_access_token
            $this->schemaBuilder()->create('oauth2_refresh_token', function (Blueprint $table) {
                $table->id();


                //FK
                $table->foreignId('oauth2_access_token_id')->constrained('oauth2_access_token');

                $table->dateTime('expire_time')->nullable();
                $table->string('refresh_token', 255);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->softDeletes('deleted_at', 0);
            });
        }
    }

    /**
     * Enter standard data.
     */
    private function insertData(): void
    {
        $date = new \DateTime();

        $this->connection->table('profile')->insert([
            'name' => EnumProfile::ADMINISTRATOR,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        $this->connection->table('profile')->insert([
            'name' => EnumProfile::USER,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}
