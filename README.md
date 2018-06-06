# Team-Leader

A small (micro)service that calculates discounts for orders.

## Install

- Clone the repository
- Run a `composer install`
- Edit your `parameters.yml` file
- Make sure you addded a test database to your `parameters.yml` file if you want to execute the unity tests.

## Concepts

The allow easy configuration and quick implementation of new types of promotion, this webservice uses Processor services which implement the ProcessorInterface to calculate the promotions.

Each Processor is totally independent and can be found at the `AppBundle\Processor` namespace.

A processor will receive an order, process it with its own "promotion" logic and return an altered order.

At this moment 3 Processors were implemented, as requested:

- A customer who has already bought for over € 1000, gets a discount of 10% on the whole order.
- For every product of category "Switches" (id 2), when you buy five, you get a sixth for free.
- If you buy two or more products of category "Tools" (id 1), you get a 20% discount on the cheapest product.

If a new promotion is wanted, for example:

- 50% to your order during black friday

It would be just a matter of implement a new Processor checking the date and giving the correspondent amount. Since each Processor is independently, for configuration, that could mean adding a new Entity.

The webservice can be invoked in the `/process/order` route and will apply all the Processors on the same order, chaging it if it fits.

## Configuration

It's possible to execute `bin/console doctrine:fixtures:load` to load all the products, categories and promotions required by the execize.

But, commands were implemented to allow manual creation and edition of those items.

- app:category                            Create and edit a category.
- app:order-promotion                     Create and edit an order promotion.
- app:product                             Create, enable and disable a product.

**TODO**: Create commands to allow edition of the `CategoryFreebiePromotion` and of the `CheapestDiscountPromotion` processor items (saved in the entity).
