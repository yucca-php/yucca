uh ?
====

http://www.wikiwand.com/en/Shard_(database_architecture)

How to configure sharded tables
===============================

Create your connections
-----------------------

```yaml
    yucca:
        connections:
            default:
                type: doctrine
                options:
                    driver:   %database_default_driver%
                    host:     %database_default_host%
                    port:     %database_default_port%
                    dbname:   %database_default_name%
                    user:     %database_default_user%
                    password: %database_default_password%
                    charset:  UTF8
            order1:
                type: doctrine
                options:
                    driver:   %database_order1_driver%
                    host:     %database_order1_host%
                    port:     %database_order1_port%
                    dbname:   %database_order1_name%
                    user:     %database_order1_user%
                    password: %database_order1_password%
                    charset:  UTF8
            order2:
                type: doctrine
                options:
                    driver:   %database_order2_driver%
                    host:     %database_order2_host%
                    port:     %database_order2_port%
                    dbname:   %database_order2_name%
                    user:     %database_order2_user%
                    password: %database_order2_password%
                    charset:  UTF8
```

Create your schema
------------------

```yaml
    yucca:
        schema:
            user:
                sharding_strategy: modulo
                shards:
                    - default

            order:
                sharding_strategy: modulo
                shards:
                    - order1
                    - order1
                    - order1
                    - order1
                    - order1
                    - order2
                    - order2
                    - order2
                    - order2
                    - order2
```

That's al for the config to change

How to use Models linked to sharded tables ?
============================================

When saving an entity, you have to set the sharding key in the entity:

```php
    public function setUser(\Acme\Bundle\YuccaDemoBundle\Entity\User $user) {
        $this->hydrate('user');
        $this->user = $user;
        $this->yuccaShardingKey = $user->getId();

        return $this;
    }
```

To retrieve an entity, the third argument is the sharding key:

```php
    $order = $this->container->get('yucca.entity_manager')->load('Acme\Bundle\YuccaDemoBundle\Entity\Order', $order_id, $user_id);
```
To loop on entity on sharded tables, you have to specify it in the selector criteria:

```php
    public function setUserCriteria(User $user) {
        $this->criterias['user_id'] = $user;
        $this->options[SelectorSourceInterface::SHARDING_KEY] = $user->getId();
    }
```

To delete an entity, nothing has changed, since the entity has been already loaded with the sharding Key:

```php
    $this->container->get('yucca.entity_manager')->remove($order);
```
