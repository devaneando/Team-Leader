<?php

namespace AppBundle\Command;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Exception\InvalidOptionException;
use AppBundle\Exception\InvalidProductCodeException;
use AppBundle\Exception\InvalidProductPriceException;
use AppBundle\Exception\UnexistentCategoryException;
use AppBundle\Exception\UnexistentProductException;
use AppBundle\Traits\CategoryRepositoryTrait;
use AppBundle\Traits\EntityManagerTrait;
use AppBundle\Traits\ProductRepositoryTrait;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ManageProductCommand extends ContainerAwareCommand
{
    const REGEX_VALID_CODE = '/^[A-Z0-9]+$/';

    use CategoryRepositoryTrait;
    use ProductRepositoryTrait;
    use EntityManagerTrait;

    protected function configure()
    {
        $this
            ->setName('app:product')
            ->setDescription('Create, enable and disable a product.')
            ->addOption(
                'create-with-name',
                null,
                InputOption::VALUE_REQUIRED,
                'Create a new product with the given name.'
            )
            ->addOption(
                'enable',
                null,
                InputOption::VALUE_NONE,
                'Enable a product.'
            )
            ->addOption(
                'disable',
                null,
                InputOption::VALUE_NONE,
                'Disable a product.'
            )
            ->addOption(
                'list-products',
                null,
                InputOption::VALUE_NONE,
                'List all products. Will prevail over any other option.'
            )
            ->addOption(
                'list-categories',
                null,
                InputOption::VALUE_NONE,
                'List all categories.'
            )
            ->addOption(
                'product-id',
                null,
                InputOption::VALUE_REQUIRED,
                'The id of the product to be enabled, disabled or renamed.'
            )
            ->addOption(
                'category-id',
                null,
                InputOption::VALUE_REQUIRED,
                'The id of the product to be enabled, disabled or renamed.'
            )
            ->addOption(
                'code',
                null,
                InputOption::VALUE_REQUIRED,
                'The code of the product.'
            )
            ->addOption(
                'price',
                null,
                InputOption::VALUE_REQUIRED,
                'The price of the product.'
            )
            ->addOption(
                'change-name-to',
                null,
                InputOption::VALUE_REQUIRED,
                'Change the name of a product.'
            )
            ->addOption(
                'change-price-to',
                null,
                InputOption::VALUE_REQUIRED,
                'Change the price of a product.'
            )
            ->addOption(
                'add-category-to',
                null,
                InputOption::VALUE_REQUIRED,
                'Add a category (use the id) to a product.'
            )
            ->addOption(
                'remove-category-from',
                null,
                InputOption::VALUE_REQUIRED,
                'Remove a category (use the id) from a product.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // If no option is given, display the help
        if (!$input->getOption('create-with-name') &&
            !$input->getOption('enable') &&
            !$input->getOption('disable') &&
            !$input->getOption('list-products') &&
            !$input->getOption('change-name-to') &&
            !$input->getOption('change-price-to') &&
            !$input->getOption('add-category-to') &&
            !$input->getOption('remove-category-from') &&
            !$input->getOption('list-categories')) {
            $help = new HelpCommand();
            $help->setCommand($this);

            return $help->run($input, $output);
        }
        $this->addColors($output);
        if ($this->listProductAction($input, $output)) {
            return;
        }
        if ($this->listCategoryAction($input, $output)) {
            return;
        }
        if ($this->createAction($input, $output)) {
            return;
        }
        $this->disableAction($input, $output);
        $this->enableAction($input, $output);
        $this->changeNameAction($input, $output);
        $this->changePriceAction($input, $output);
        $this->removeCategoryAction($input, $output);
        $this->addCategoryAction($input, $output);
    }

    /**
     * Add colors formats to the output.
     *
     * @param OutputInterface $output
     */
    private function addColors(OutputInterface &$output)
    {
        $styles = [
            ['header', new OutputFormatterStyle('blue', 'default', ['bold'])],
            ['code', new OutputFormatterStyle('cyan', 'default', ['bold'])],
            ['name', new OutputFormatterStyle('yellow', 'default')],
            ['enabled', new OutputFormatterStyle('green', 'default')],
            ['disabled', new OutputFormatterStyle('cyan', 'default')],
        ];
        foreach ($styles as $style) {
            $output->getFormatter()->setStyle($style[0], $style[1]);
        }
    }

    /**
     * Prints in the console all the existent products.
     *
     * @param OutputInterface $output
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    private function listProductAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('list-products')) {
            return false;
        }
        $products = $this->getProductRepository()->findBy([], ['name' => 'ASC']);
        foreach ($products as $product) {
            $lineCategories = [];
            /** @var Product $product */
            foreach ($product->getCategories() as $productCategory) {
                /** @var Category $productCategory */
                $lineCategories[] = sprintf(
                    "<disabled> - %s: %s</disabled>",
                    $productCategory->getId(),
                    $productCategory->getName()
                );
            }

            $output->writeln(
                sprintf(
                    '<comment>%-15s|%-10s|%-15s|%-10s|%s</comment>',
                    'Id',
                    'Enabled',
                    'Code',
                    'Price',
                    'Product'
                )
            );
            $output->writeln(
                sprintf(
                    '<info>%-15s|%-10s|%-15s|%-10s|%s</info>',
                    $product->getId(),
                    $product->getEnabled(),
                    $product->getCode(),
                    $product->getPrice(),
                    $product->getName()
                )
            );

            $output->writeln("\n<comment>Category</comment>");
            foreach ($lineCategories as $lineCategory) {
                $output->writeln($lineCategory);
            }
        }

        return true;
    }

    /**
     * Prints in the console all the existent categories.
     *
     * @param OutputInterface $output
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    private function listCategoryAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('list-categories')) {
            return false;
        }
        $categories = $this->getCategoryRepository()->findBy([], ['name' => 'ASC']);
        $output->writeln(
            sprintf('<comment>%-10s|%-10s|%s</comment>', 'Id', 'Enabled', 'Category')
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
     * Create a new product.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function createAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('create-with-name')) {
            return false;
        }
        $name = trim($input->getOption('create-with-name'));

        if (!$input->getOption('category-id') ||
        !$input->getOption('code') ||
        !$input->getOption('price')) {
            throw new InvalidOptionException(
                "I can't create a new product without a 'category', a 'code' and a 'price'. "
                .'At least one of the is missing.'
            );
        }

        $category = $this->getCategoryRepository()->findOneBy(['id' => $input->getOption('category-id')]);
        if (!$category) {
            throw new UnexistentCategoryException();
        }

        $price = $input->getOption('price');
        if (!is_numeric($price)) {
            throw new InvalidProductPriceException();
        }

        $code = strtoupper(trim($input->getOption('code')));
        if (!preg_match(self::REGEX_VALID_CODE, $code)) {
            throw new InvalidProductCodeException();
        }

        $enabled = true;
        if ($input->getOption('disable')) {
            $enabled = false;
        }

        $product = $this->getProductRepository()->findOneBy(['name' => $name]);
        if (!$product) {
            $product = new Product();
            $product
                ->addCategory($category)
                ->setCode($code)
                ->setEnabled($enabled)
                ->setName($name)
                ->setPrice($price);
            $this->getEntityManager()->persist($product);
            $this->getEntityManager()->flush();
            $output->writeln('<info>The product was successfully created.</info>');

            return true;
        }
    }

    /**
     * Disable a product.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws UnexistentProductException
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    private function disableAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('disable')) {
            return false;
        }
        if (!$input->getOption('product-id')) {
            $output->writeln("<error>Can't disable a product without its id.</error>");

            return false;
        }

        $id = $input->getOption('product-id');
        /** @var Product $product */
        $product = $this->getProductRepository()->findOneBy(['id' => $id]);

        if (!$product) {
            throw new UnexistentProductException();
        }
        $product->setEnabled(false);
        $this->getEntityManager()->flush();
        $output->writeln('<info>The product was disabled.</info>');

        return false;
    }

    /**
     * Enable a product.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws UnexistentProductException
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    private function enableAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('enable')) {
            return false;
        }
        if (!$input->getOption('product-id')) {
            $output->writeln("<error>Can't enable a product without its id.</error>");

            return false;
        }

        $id = $input->getOption('product-id');
        /** @var Product $product */
        $product = $this->getProductRepository()->findOneBy(['id' => $id]);

        if (!$product) {
            throw new UnexistentProductException();
        }
        $product->setEnabled(true);
        $this->getEntityManager()->flush();
        $output->writeln('<info>The product was enabled.</info>');

        return false;
    }

    /**
     * Change the name of a product.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws UnexistentProductException
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    private function changeNameAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('change-name-to')) {
            return false;
        }
        $name = trim($input->getOption('change-name-to'));

        if (!$input->getOption('product-id')) {
            $output->writeln("<error>Can't enable a product without its id.</error>");

            return false;
        }

        $id = $input->getOption('product-id');
        /** @var Product $product */
        $product = $this->getProductRepository()->findOneBy(['id' => $id]);

        if (!$product) {
            throw new UnexistentProductException();
        }
        $product->setName($name);
        $this->getEntityManager()->flush();
        $output->writeln('<info>The product name was changed.</info>');

        return false;
    }

    // change-price-to

    /**
     * Change the name of a product.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws UnexistentProductException
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    private function changePriceAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('change-price-to')) {
            return false;
        }
        $price = trim($input->getOption('change-price-to'));
        if (!is_numeric($price)) {
            throw new InvalidProductPriceException();
        }

        if (!$input->getOption('product-id')) {
            $output->writeln("<error>Can't enable a product without its id.</error>");

            return false;
        }

        $id = $input->getOption('product-id');
        /** @var Product $product */
        $product = $this->getProductRepository()->findOneBy(['id' => $id]);

        if (!$product) {
            throw new UnexistentProductException();
        }
        $product->setPrice($price);
        $this->getEntityManager()->flush();
        $output->writeln('<info>The product price was changed.</info>');

        return false;
    }

    /**
     * Add a category to a product
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws UnexistentCategoryException
     * @throws UnexistentProductException
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    private function addCategoryAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('add-category-to')) {
            return false;
        }
        $category = $this->getCategoryRepository()->findOneBy(['id' => $input->getOption('add-category-to')]);
        if (!$category) {
            throw new UnexistentCategoryException();
        }

        if (!$input->getOption('product-id')) {
            $output->writeln("<error>Can't add a category without a product id.</error>");

            return false;
        }

        $id = $input->getOption('product-id');
        /** @var Product $product */
        $product = $this->getProductRepository()->findOneBy(['id' => $id]);

        if (!$product) {
            throw new UnexistentProductException();
        }
        $product->addCategory($category);
        $this->getEntityManager()->flush();
        $output->writeln('<info>The category was added to the product.</info>');

        return false;
    }

    /**
     * Remove a category from a product
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws UnexistentCategoryException
     * @throws UnexistentProductException
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    private function removeCategoryAction(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('remove-category-from')) {
            return false;
        }
        $category = $this->getCategoryRepository()->findOneBy(['id' => $input->getOption('remove-category-from')]);
        if (!$category) {
            throw new UnexistentCategoryException();
        }

        if (!$input->getOption('product-id')) {
            $output->writeln("<error>Can't remove a category without a product id.</error>");

            return false;
        }

        $id = $input->getOption('product-id');
        /** @var Product $product */
        $product = $this->getProductRepository()->findOneBy(['id' => $id]);

        if (!$product) {
            throw new UnexistentProductException();
        }
        $product->removeCategory($category);
        $this->getEntityManager()->flush();
        $output->writeln('<info>The category was added to the product.</info>');

        return false;
    }
}
