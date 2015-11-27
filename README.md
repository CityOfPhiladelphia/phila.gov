# phila.gov

[![Tested on BrowserStack](https://img.shields.io/badge/browserstack-tested-brightgreen.svg?logo=data%3Aimage%2Fpng%3Bbase64%2CiVBORw0KGgoAAAANSUhEUgAAAA4AAAAOCAYAAAAfSC3RAAACsUlEQVQokVWSTWwUZQCGn2%2B%2Bnd2d7XS7K1v6Q6WAaBoKklAMP0rcNBqWiMET0SskxEBAURLjBfZkPJAQWmOswkVjYnqiKiGALQ0NP0EIcjAg0gottNB26f7Odmfmm89TG31P7%2BF5bo%2FgP%2BvX%2FfKto31bmY3vV2O590VJIlKps5FS0Fv35roRkc06C6xYOLkvt3cyWTmh7wdve402411Jys0GNkWSUxXij%2Bw79S1N2brPTp9ZFJ9lM6%2FKfP5c%2BK7ZOvpGmsHMu9wImnjmgUWeDeavpNUQa25ZKpVq%2FST6wbc9QvccjExP3LhkPdJbxpdnOL%2FnEAMPKzy48gve%2BH2q7Wuo37Sdd2Kn2BW7wusPG%2F1k%2B4sZIzd9d1tYqS3lis2Tne8xWoHJn05g9h1jxa2LfLfKRI4McNPdyG03zMyyaqhWLB41dCT2EZ7Ci8bxUimCuVn0zUuIcBSzPs7fo2OI4Z%2BpuYrn8yEeWwGE1CaD%2BWLaB5Rw8P0SrzTGCSdSaOUz%2BWSSvq96cRNLWW1X0TrAV2CGRMQILWseMySYco78P0O8tDTB7sOf09KxFqvOZslrabr37aVD%2Fo4hBY1ljVCCkGyI9UgrciqWcGm6eoY%2F2iKs27iDzm9%2BoJzP4cZqTJcHmCg9oEv6rJyL4b0gnFD91uVn8zNTszhPU13FAoULP3K9awRpNwOK0swEQVBhveuTGXdJdLRRM4zLAsD5bd%2Bn8t7E8cJfTzF8eNwQ5l6zScmSWAhWFmq8XDVIrm9HJ%2BuqfhDtFgBa98va8OBJOZU74IxO4z93EF4AhsCImpitDcQ6W1ARs%2Br55odW99ffLyaH1sK99vEeUXOOaGe%2BQypFIDQh28IThhJCXvZ1%2BJiV7h35X6uL%2Fp9Z2y8WNiOj25BWp67NDaOMa18MHr%2BdzYpggfsXmkch023E8JUAAAAASUVORK5CYII%3D)](https://www.browserstack.com/)

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

Then set environment variables and generate `wp-config.php`:
```
$ export WP_SITEURL=http://localhost:1234
$ export WP_HOME=http://localhost:1234
$ export DB_NAME=wp
$ export DB_USER=root
$ export AWS_ACCESS_KEY_ID=<aws_access_key_id>
$ export AWS_SECRET_ACCESS_KEY=<aws_secret_access_key>
$ export SWIFTYPE_ENGINE=<swiftype_engine>
$ scripts/wp-config.sh
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


## Running a remote test instance

1. Install aws cli
2. Install fswatch
3. Create .env file with these vars:
  - AMI=`ID for the AMI to use for the instance`
  - SUBNET=`ID for the VPC subnet the instance should be in`
  - KEY_PAIR=`name of the key pair in AWS to use for the instance`
  - PHILA_TEST=true
  - PHILA_DB_BUCKET=`S3 bucket where we keep DB dumps`
  - PHILA_MEDIA_BUCKET=`S3 bucket where instance media is stored`
  - AWS_ID=`AWS access key ID for the instance to use`
  - AWS_SECRET=`AWS secret access key for the instance to use`
  - SWIFTYPE_ENGINE=`Swiftype engine ID`
  - COMPOSER_URL=`URL for our Satis repository`
4. Save private key as .ssh/$KEY_PAIR.pem in this repo
5. Run `instance/up`
