Configuration Generator
-----------------------
Here is the help of the `yucca:generate-configuration` command:
```bash
php bin/console yucca:generate-configuration --help
Usage:
 yucca:generate-configuration path namespace dbname [user] [password] [host] [driver] [port]

Arguments:
 path                  Path into wich put config
 namespace             Model namespace
 dbname                Database name
 user                  Database user (default: "root")
 password              Database password (default: "")
 host                  Database Host (default: "localhost")
 driver                Database driver (default: "pdo_mysql")
 port                  Database port (default: "")

Options:
....
```

Example : `php bin/console yucca:generate-configuration app/config \Your\Project\Namespace your_db_name db_user db_pass localhost pdo_mysql 3306`

This will create the `app/config/yucca.yml` config file.
Import it in your current configuration:
```yaml
# app/config/config.yml
imports:
    - { resource: security.yml }
    - { resource: yucca.yml }
```

Check this file to ensure you have the correct type associated to the fields in the [**sources** section](https://github.com/rjanot/yucca/blob/master/README.md#sources):
For reminder, here are some types: `identifier`, `json`, `boolean`, `object`,`datetime`

When confident, you can use the second command: Model Generator

Model Generator
---------------
```bash
php bin/console yucca:generate-models --help
Usage:
 yucca:generate-models path

Arguments:
 path                  Path into wich put models

Options:
....
```

Example : `php bin/console yucca:generate-models src

This will use your configuration file, especially the mapping section, to create models.
If you have a `\Your\Project\Namespace\Entity\User` declaration, it will create the `src/Your/Project/Namespace/Model/User.php` file.

You can check them, and if not totally satisfied, you can make changes directly in models, or you can iterate by going back in the configuration file.

**WARNING FOR THE MOMENT, IT'S JUST A CREATION BEHAVIOR, NO UPDATE POSSIBLE**

These commands are subject to change, but it should not cause problem, as they are intended for being used only once.
