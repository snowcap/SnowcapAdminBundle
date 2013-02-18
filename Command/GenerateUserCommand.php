<?php

namespace Snowcap\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Snowcap\AdminBundle\Entity\User as AdminUser;

class GenerateUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('snowcap:admin:generate-user')
            ->setDescription('Create a new user in the database')
            ->setDefinition(array(
                    new InputArgument('username', InputArgument::OPTIONAL),
                    new InputArgument('email', InputArgument::OPTIONAL),
                    new InputArgument('password', InputArgument::OPTIONAL),
                    new InputOption('roles', 'r', InputOption::VALUE_OPTIONAL)
                ))
            ->setHelp(<<<EOT
The <info>snowcap:admin:generate-user</info> command creates a user:

  <info>php app/console snowcap:admin:generate-user matthieu</info>

This interactive shell will ask you for an email and then a password.

You can alternatively specify the email and password as the second and third arguments:

  <info>php app/console fos:user:create matthieu matthieu@example.com mypassword</info>

You can create a super admin via the roles flag:

  <info>php app/console fos:user:create admin --roles=ADMIN,SUPER_ADMIN</info>
EOT
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input->setInteractive(true);
        $username = $this->getOptionalInteractiveArgument($input, $output, 'username', 'Please choose a username');
        $email = $this->getOptionalInteractiveArgument($input, $output, 'email', 'Please choose an email address');
        $password = $this->getOptionalInteractiveArgument($input, $output, 'password', 'Please choose a password', true);
        $roleString = $this->getOptionalInteractiveOption($input, $output, 'roles', 'Please specify a comma-separated list of roles');
        $roles = explode(',', $roleString);

        $user = new AdminUser();

        $factory = $this->getContainer()->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($password, $user->getSalt());

        $user
            ->setUsername($username)
            ->setEmail($email)
            ->setPassword($encodedPassword)
            ->setRoles($roles);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($user);
        $em->flush($user);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string $argument
     * @param string $question
     * @param bool $hidden
     * @return mixed
     * @throws \Exception
     */
    protected function getOptionalInteractiveArgument(InputInterface $input, OutputInterface $output, $argument, $question, $hidden = false)
    {
        $dialog = $this->getDialogHelper();
        if(null !== $input->getArgument($argument)) {
            $value = $input->getArgument($argument);
        }
        elseif ($input->isInteractive()) {
            $formattedQuestion = sprintf('%s: ', $question);
            if($hidden) {
                $value = $dialog->askHiddenResponse($output, $formattedQuestion, true);
            }
            else {
                $value = $dialog->ask($output, $formattedQuestion);
            }
        }
        else {
            throw new \Exception(sprintf('No argument named "%s"', $argument));
        }

        return $value;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string $option
     * @param string $question
     * @param bool $hidden
     * @return mixed
     * @throws \Exception
     */
    protected function getOptionalInteractiveOption(InputInterface $input, OutputInterface $output, $option, $question, $hidden = false)
    {
        $dialog = $this->getDialogHelper();
        if(null !== $input->getOption($option)) {
            $value = $input->getOption($option);
        }
        elseif ($input->isInteractive()) {
            $formattedQuestion = sprintf('%s: ', $question);
            if($hidden) {
                $value = $dialog->askHiddenResponse($output, $formattedQuestion, true);
            }
            else {
                $value = $dialog->ask($output, $formattedQuestion);
            }
        }
        else {
            throw new \Exception(sprintf('No option named "%s"', $option));
        }

        return $value;
    }

    /**
     * @return DialogHelper
     */
    protected function getDialogHelper()
    {
        return new DialogHelper();
    }


}