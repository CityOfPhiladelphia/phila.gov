# phila.gov

Where the pieces are pulled together for phila.gov deployment.


## Local setup

- [Install Vagrant](https://docs.vagrantup.com/v2/installation/)

- Clone this repo.
```
git clone git@github.com:CityOfPhiladelphia/phila.gov.git
```

- Initialize the virtual environment and log in:
```
$ vagrant up
$ vagrant ssh
```

Composer relies on our private repo. Add it to the global config with this command:
```
$ composer config -g repositories.private composer <repo_url>
```

Then install php components with composer:
```
$ composer install
```

Import configuration and fixture data:
```
$ wp db import
$ wp import wp.xml --authors=skip
```

Finally, flush rewrite rules so permalinks work:
```
$ wp rewrite flush
```


## Details

The following happens when you `vagrant up`:

- Installs all server dependencies (PHP, MySQL, nginx)
- Installs tools (composer, wp-cli)


## Updating content data for import

1. Start with a clean database. You can do this by repeating the import steps above.
2. Make any changes in the Wordpress Admin. This would include adding, editing, or deleting pages or taxonomies.
3. From the Wordpress Admin select `Tools -> Export`. Select the `All Content` option, then download.
4. Update the contents of `wp.xml` with the new export file.
5. Run the import steps again to test your changes.
6. Commit the updated `wp.xml` to the repository


## How to deploy

1. Modify `composer.json` to point at new require versions.
2. Run `composer install`.
3. Check out the site locally.
4. If all looks good, commit and push to the `staging` branch.
5. That push will trigger a build at https://travis-ci.org/CityOfPhiladelphia/phila.gov, which will in turn trigger a staging deploy.
6. Check out the site at the staging server.
7. If all looks good, create a pull request from staging to master. Once the PR has been accepted and merged, a production deploy will be triggered.
