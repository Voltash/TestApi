<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:generate-user',
    description: 'Generate user',
)]
class GenerateUserCommand extends Command
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addOption('admin',null,  InputOption::VALUE_OPTIONAL, 'Is admin', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = new User();
        $user->setEmail($input->getArgument('email'));
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $input->getArgument('password'))
        );
        if ($input->getOption('admin') !== false) {
            $user->setRoles(['ROLE_ADMIN']);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('User created');

        return Command::SUCCESS;
    }
}
