<?php

namespace AppBundle\Command;

use AppBundle\Exception\Command\InvalidOptionException;
use AppBundle\Entity\Category;
use AppBundle\Exception\Model\InvalidProductCodeException;
use AppBundle\Exception\Model\UnexistentCategoryException;
use AppBundle\Traits\CategoryRepositoryTrait;
use AppBundle\Traits\EntityManagerTrait;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ManageCategoryCommand extends ContainerAwareCommand
{
    use CategoryRepositoryTrait;
    use EntityManagerTrait;

    protected function configure()
    {
        $this
            ->setName('app:category')
            ->setDescription('Create and edit a category.')
            ->addOption(
                'create-with-name',
                null,
                InputOption::VALUE_REQUIRED,
                'Create a new category with the given name.'
            )
            ->addOption(
                'change-name-to',
                null,
                InputOption::VALUE_REQUIRED,
                'Change the category name to the given value.'
            )
            ->addOption(
                'category-id',
                null,
                InputOption::VALUE_REQUIRED,
                'Used select the category to edit.'
            )
            ->addOption(
                'enable',
                null,
                InputOption::VALUE_NONE,
                'Enable an category.'
            )
            ->addOption(
                'disable',
                null,
                InputOption::VALUE_NONE,
                'Disable category.'
            )
            ->addOption(
                'list',
                null,
                InputOption::VALUE_NONE,
                'List all categories.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // If no option is given, display the help
        if (!$input->getOption('create-with-name') &&
            !$input->getOption('change-name-to') &&
            !$input->getOption('enable') &&
            !$input->getOption('disable') &&
            !$input->getOption('list')) {
            $help = new HelpCommand();
            $help->setCommand($this);

            return $help->run($input, $output);
        }

        if ($this->listAction($input, $output)) {
            return;
        }

        if ($this->createAction($input, $output)) {
            return;
        }

        $this->disableAction($input, $output);
        $this->enableAction($input, $output);
        $this->changeNameAction($input, $output);
    }

    /**
     * Prints in the console all the existent categories.
     *
     * @param OutputInterface $output
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    private function listAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('list')) {
            return false;
        }
        $categories = $this->getCategoryRepository()->findBy([], ['name' => 'ASC']);
        $output->writeln(
            sprintf('<comment>%-10s|%-10s|%s</comment>', 'Id', 'Enabled', 'Name')
        );
        foreach ($categories as $category) {
            /** @var Category $category */
            $output->writeln(
                    sprintf(
                        '<info>%-10s|%-10s|%s</info>',
                        $category->getId(),
                        $category->getEnabled(),
                        $category->getName()
                    )
                );
        }

        return true;
    }

    /**
     * Create a new category.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws UnexistentCategoryException
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    private function createAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('create-with-name')) {
            return false;
        }
        $name = trim($input->getOption('create-with-name'));

        $enabled = true;
        if ($input->getOption('disable')) {
            $enabled = false;
        }

        $category = $this->getCategoryRepository()->findOneBy(['name' => $name]);
        if (!$category) {
            $category = new Category();
            $category
                ->setEnabled($enabled)
                ->setName($name);
            $this->getEntityManager()->persist($category);
            $this->getEntityManager()->flush();
            $output->writeln('<info>The category was sucessfully created.</info>');

            return true;
        }

        $output->writeln('<error>A category with the given name exists in the database.</error>');

        return true;
    }

    /**
     * Disable a category.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws UnexistentCategoryException
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    private function disableAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('disable')) {
            return false;
        }
        if (!$input->getOption('category-id')) {
            $output->writeln("<error>Can't disable a category without its id.</error>");

            return false;
        }

        $id = $input->getOption('category-id');
        /** @var Category $category */
        $category = $this->getCategoryRepository()->findOneBy(['id' => $id]);

        if (!$category) {
            throw new UnexistentCategoryException();
        }
        $category->setEnabled(false);
        $this->getEntityManager()->flush();
        $output->writeln('<info>The category was disabled.</info>');

        return false;
    }

    /**
     * Enable a category.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws UnexistentCategoryException
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    private function enableAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('enable')) {
            return false;
        }
        if (!$input->getOption('category-id')) {
            $output->writeln("<error>Can't enable a category without its id.</error>");

            return false;
        }

        $id = $input->getOption('category-id');
        /** @var Category $category */
        $category = $this->getCategoryRepository()->findOneBy(['id' => $id]);

        if (!$category) {
            throw new UnexistentCategoryException();
        }
        $category->setEnabled(true);
        $this->getEntityManager()->flush();
        $output->writeln('<info>The category was enabled.</info>');

        return false;
    }

    /**
     * Changes the name of a category.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    private function changeNameAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('change-name-to')) {
            return false;
        }
        $name = trim($input->getOption('change-name-to'));

        if (!$input->getOption('category-id')) {
            $output->writeln("<error>Can't change a category name without its id.</error>");

            return false;
        }

        $id = $input->getOption('category-id');
        /** @var Category $category */
        $category = $this->getCategoryRepository()->findOneBy(['id' => $id]);

        if (!$category) {
            throw new UnexistentCategoryException();
        }
        $category->setName($name);
        $this->getEntityManager()->flush();
        $output->writeln('<info>The category was enabled.</info>');

        return false;
    }
}
