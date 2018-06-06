<?php

namespace AppBundle\Command;

use AppBundle\Entity\OrderPromotion;
use AppBundle\Exception\Command\InvalidOptionException;
use AppBundle\Traits\CategoryRepositoryTrait;
use AppBundle\Traits\EntityManagerTrait;
use AppBundle\Traits\ManagersCommandServiceTrait;
use AppBundle\Traits\OrderPromotionTrait;
use AppBundle\Traits\ProductRepositoryTrait;
use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use TheSeer\Tokenizer\Exception;

class ManageOrderPromotionCommand extends GeneratorCommand
{
    const REGEX_PATTEN_CODE = '/^([A-Z0-9]+)$/';

    /**
     * @var InputInterface
     */
    private $input;
    /**
     * @var OutputInterface
     */
    private $output;

    use CategoryRepositoryTrait;
    use ProductRepositoryTrait;
    use ManagersCommandServiceTrait;
    use OrderPromotionTrait;
    use EntityManagerTrait;

    protected function createGenerator()
    {
        return false;
    }

    protected function configure()
    {
        $this
            ->setName('app:order-promotion')
            ->setDescription('Create and edit an order promotion.')
            ->addOption(
                'list-categories',
                null,
                InputOption::VALUE_NONE,
                'List all categories'
            )
            ->addOption(
                'list-products',
                null,
                InputOption::VALUE_NONE,
                'List all products'
            )
            ->addOption(
                'list-promotions',
                null,
                InputOption::VALUE_NONE,
                'List all order promotions'
            )
            ->addOption(
                'code',
                null,
                InputOption::VALUE_REQUIRED,
                'The promotion CODE (No spaces or special chars)'
            )
            ->addOption(
                'description',
                null,
                InputOption::VALUE_REQUIRED,
                'The promotion description'
            )
            ->addOption(
                'minimum-amount',
                null,
                InputOption::VALUE_REQUIRED,
                'The minimum amount to get a promotion.'
            )
            ->addOption(
                'discount',
                null,
                InputOption::VALUE_REQUIRED,
                'The promotion discount.'
            )
            ->addOption(
                'freebie',
                null,
                InputOption::VALUE_REQUIRED,
                'The code of the item to be offered.'
            )
            ->addOption(
                'freebie-quantity',
                null,
                InputOption::VALUE_REQUIRED,
                'The how many item to be offered.'
            )
            ->addOption(
                'create',
                null,
                InputOption::VALUE_NONE,
                'Create a new order promotion'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $this->input = $input;
        $this->output = $output;
        if ($input->getOption('list-categories')) {
            $questionHelper->writeSection($output, 'Categories');
            $lines = $this->getManagersCommandService()->listCategory();
            foreach ($lines as $line) {
                $output->writeln($line);
            }

            return;
        }

        if ($input->getOption('list-products')) {
            $questionHelper->writeSection($output, 'Products');
            $lines = $this->getManagersCommandService()->listProduct();
            foreach ($lines as $line) {
                $output->writeln($line);
            }

            return;
        }

        if ($input->getOption('list-promotions')) {
            $questionHelper->writeSection($output, 'Promotions');
            $lines = $this->getManagersCommandService()->listOrderPromotions();
            foreach ($lines as $line) {
                $output->writeln($line);
            }

            return;
        }

        $this->createAction();

        if (!$input->getOption('list-categories') &&
            !$input->getOption('list-products') &&
            !$input->getOption('list-promotions') &&
            !$input->getOption('create')) {
            $this->displayHelp();
        }
    }

    /**
     * Displays the help.
     */
    protected function displayHelp()
    {
        $help = new HelpCommand();
        $help->setCommand($this);

        return $help->run($this->input, $this->input);
    }

    /**
     * Reads and validate the code parameter.
     *
     * @return string
     */
    protected function getOptionCode()
    {
        $questionHelper = $this->getQuestionHelper();
        $code = trim(strtoupper($this->input->getOption('code')));

        while (!$code ||
                !preg_match(self::REGEX_PATTEN_CODE, $code)) {
            $question = new Question("<question>What is the promotion code (Only letters and numbers)?</question>\n");
            $code = trim(strtoupper($questionHelper->ask($this->input, $this->output, $question)));
            $this->input->setOption('code', $code);
        }

        return $code;
    }

    /**
     * Reads and validate the description parameter.
     *
     * @return string
     */
    protected function getOptionDescription()
    {
        $questionHelper = $this->getQuestionHelper();
        $description = trim($this->input->getOption('description'));

        while (!$description) {
            $question = new Question("<question>What is the promotion description?</question>\n");
            $description = trim($questionHelper->ask($this->input, $this->output, $question));
            $this->input->setOption('description', $description);
        }

        return $description;
    }

    /**
     * Reads and validate the minimum amount parameter.
     *
     * @return float
     */
    protected function getOptionMinimumAmount()
    {
        $questionHelper = $this->getQuestionHelper();
        $minimumAmount = trim($this->input->getOption('minimum-amount'));

        while (!$minimumAmount || !is_numeric($minimumAmount)) {
            $question = new Question("<question>What is the promotion minimum amount?</question>\n");
            $minimumAmount = trim($questionHelper->ask($this->input, $this->output, $question));
            $this->input->setOption('minimum-amount', $minimumAmount);
        }

        return $minimumAmount;
    }

    /**
     * Reads and validate the discount parameter.
     *
     * @return float
     */
    protected function getOptionDiscount()
    {
        $questionHelper = $this->getQuestionHelper();
        $discount = trim($this->input->getOption('discount'));

        if (!$discount) {
            $question = new Question(
                "<question>What is the promotion discount (Hit enter for none)?</question>\n"
            );
            $discount = trim($questionHelper->ask($this->input, $this->output, $question));
        }

        if (!$discount) {
            return 0;
        }

        while (!is_numeric($discount)) {
            $question = new Question(
                "<question>Your discount is invalid. What is the promotion discount?</question>\n"
            );
            $discount = trim($questionHelper->ask($this->input, $this->output, $question));

            $this->input->setOption('description', $discount);
        }

        return $discount;
    }

    /**
     * Reads and validate the freebie parameter.
     *
     * @return int|null
     */
    protected function getOptionFreebie()
    {
        $questionHelper = $this->getQuestionHelper();
        $freebie = trim($this->input->getOption('freebie'));

        if (!$freebie) {
            $question = new Question(
                "<question>What is the freebie product code (Hit enter for none)?</question>\n"
            );
            $freebie = trim($questionHelper->ask($this->input, $this->output, $question));
        }

        if (!$freebie) {
            return null;
        }

        while (!$this->getProductRepository()->findOneByCode($freebie)) {
            $question = new Question(
                '<question>Your freebie product code is invalid.'
                ."What is the correct freebie product code?</question>\n"
            );
            $freebie = trim($questionHelper->ask($this->input, $this->output, $question));

            $this->input->setOption('freebie', $freebie);
        }

        return $freebie;
    }

    /**
     * Reads and validate the freebie quantity parameter.
     *
     * @return int
     */
    protected function getOptionFreebieQuantity()
    {
        $questionHelper = $this->getQuestionHelper();
        $freebieQuantity = trim($this->input->getOption('freebie-quantity'));

        if (!$freebieQuantity) {
            $question = new Question(
                "<question>How many freebie products (Hit enter for none)?</question>\n"
            );
            $freebieQuantity = trim($questionHelper->ask($this->input, $this->output, $question));
        }

        if (!$freebieQuantity || !$this->getOptionFreebie()) {
            return 0;
        }

        while (!is_numeric($freebieQuantity)) {
            $question = new Question(
                "<question>Your freebie quantity is invalid. How many freebie products?</question>\n"
            );
            $freebieQuantity = trim($questionHelper->ask($this->input, $this->output, $question));

            $this->input->setOption('freebie-quantity', $freebieQuantity);
        }

        return $freebieQuantity;
    }

    /**
     * Create a new order promotion.
     *
     * @throws InvalidOptionException
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    protected function createAction()
    {
        if (!$this->input->getOption('create')) {
            return false;
        }
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($this->output, 'Create a new order offer');

        $code = $this->getOptionCode();
        $description = $this->getOptionDescription();
        $minimumAmount = $this->getOptionMinimumAmount();
        $discount = $this->getOptionDiscount();
        $freebie = $this->getOptionFreebie();
        $freebieQuantity = $this->getOptionFreebieQuantity();
        if ($discount && $freebie ||
            !$discount && !$freebie) {
            throw new InvalidOptionException(
                "You need to have exactly 'ONE' discount or 'ONE' free item in a promotion. You can't have both."
            );
        }

        $orderPromotion = new OrderPromotion();
        $orderPromotion
            ->setCode($code)
            ->setDescription($description)
            ->setMinimumAmount($minimumAmount)
            ->setEnabled(true);

        if ($discount) {
            $orderPromotion->setDiscount($discount);
        } else {
            $orderPromotion->setFreebieItem($freebie)
            ->setFreebieQuantity($freebieQuantity);
        }

        $this->getEntityManager()->persist($orderPromotion);
        $this->getEntityManager()->flush();
    }

    /**
     * Edit an order promotion.
     *
     * @throws \Exception
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    protected function editAction()
    {
        throw new \Exception('To be implemented.');
    }

    /**
     * Enable an order promotion.
     *
     * @throws \Exception
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    protected function enableAction()
    {
        throw new \Exception('To be implemented.');
    }

    /**
     * Disable an order promotion.
     *
     * @throws \Exception
     *
     * @return bool True if the command should stop processing, false if it should continue
     */
    protected function disableAction()
    {
        throw new \Exception('To be implemented.');
    }
}
