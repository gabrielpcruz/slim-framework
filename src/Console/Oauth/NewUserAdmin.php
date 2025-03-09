<?php

namespace SlimFramework\Console\Oauth;

use SlimFramework\Enum\EnumProfile;
use SlimFramework\Migration\ConsoleMigration;
use DateTime;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class NewUserAdmin extends ConsoleMigration
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('slim:new-user-admin');
        $this->setDescription('Create a new user admin account.');
        $this->configureConnection();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->info("Creating a new user admin...");

        $helper = $this->getHelper('question');

        $uuid = uniqid();

        $questionUserName = new Question(
            "Please type the user e-mail:" . PHP_EOL,
            "email_{$uuid}@gmail.com"
        );

        $userEmail = $helper->ask($input, $output, $questionUserName);

        $questionUserName = new Question(
            "Please type the user name:" . PHP_EOL,
            "username{$uuid}"
        );

        $username = $helper->ask($input, $output, $questionUserName);


        $questionPassword = new Question(
            "Please type the user password:" . PHP_EOL,
            '123456'
        );

        $password = $helper->ask($input, $output, $questionPassword);

        $date = new DateTime();

        $oauth2_client_id = $this->connection->table('oauth2_client')->insert([
            'identifier' => str_rand(40),
            'secret' => str_rand(60),
            'created_at' => $date,
            'updated_at' => $date,
        ]);


        $this->connection->table('user')->insert([
            'profile_id' => EnumProfile::ADMINISTRATOR_ID,
            'oauth2_client_id' => $oauth2_client_id,
            'username' => $username,
            'email' => $userEmail,
            'password' => password_hash($password, PASSWORD_DEFAULT, ['cost' => 14]),
            'status' => 1,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        return Command::SUCCESS;
    }
}
