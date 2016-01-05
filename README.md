# phila.gov

Where the pieces are pulled together for phila.gov deployment.

[![Tested on BrowserStack](https://img.shields.io/badge/browserstack-tested-brightgreen.svg?logo=data%3Aimage%2Fpng%3Bbase64%2CiVBORw0KGgoAAAANSUhEUgAAAA4AAAAOCAYAAAAfSC3RAAACsUlEQVQokVWSTWwUZQCGn2%2B%2Bnd2d7XS7K1v6Q6WAaBoKklAMP0rcNBqWiMET0SskxEBAURLjBfZkPJAQWmOswkVjYnqiKiGALQ0NP0EIcjAg0gottNB26f7Odmfmm89TG31P7%2BF5bo%2FgP%2BvX%2FfKto31bmY3vV2O590VJIlKps5FS0Fv35roRkc06C6xYOLkvt3cyWTmh7wdve402411Jys0GNkWSUxXij%2Bw79S1N2brPTp9ZFJ9lM6%2FKfP5c%2BK7ZOvpGmsHMu9wImnjmgUWeDeavpNUQa25ZKpVq%2FST6wbc9QvccjExP3LhkPdJbxpdnOL%2FnEAMPKzy48gve%2BH2q7Wuo37Sdd2Kn2BW7wusPG%2F1k%2B4sZIzd9d1tYqS3lis2Tne8xWoHJn05g9h1jxa2LfLfKRI4McNPdyG03zMyyaqhWLB41dCT2EZ7Ci8bxUimCuVn0zUuIcBSzPs7fo2OI4Z%2BpuYrn8yEeWwGE1CaD%2BWLaB5Rw8P0SrzTGCSdSaOUz%2BWSSvq96cRNLWW1X0TrAV2CGRMQILWseMySYco78P0O8tDTB7sOf09KxFqvOZslrabr37aVD%2Fo4hBY1ljVCCkGyI9UgrciqWcGm6eoY%2F2iKs27iDzm9%2BoJzP4cZqTJcHmCg9oEv6rJyL4b0gnFD91uVn8zNTszhPU13FAoULP3K9awRpNwOK0swEQVBhveuTGXdJdLRRM4zLAsD5bd%2Bn8t7E8cJfTzF8eNwQ5l6zScmSWAhWFmq8XDVIrm9HJ%2BuqfhDtFgBa98va8OBJOZU74IxO4z93EF4AhsCImpitDcQ6W1ARs%2Br55odW99ffLyaH1sK99vEeUXOOaGe%2BQypFIDQh28IThhJCXvZ1%2BJiV7h35X6uL%2Fp9Z2y8WNiOj25BWp67NDaOMa18MHr%2BdzYpggfsXmkch023E8JUAAAAASUVORK5CYII%3D)](https://www.browserstack.com/)


## Running a remote test instance

1. [Install aws cli](http://docs.aws.amazon.com/cli/latest/userguide/installing.html#install-bundle-other-os)
2. [Install unison](http://www.cis.upenn.edu/~bcpierce/unison/download.html)
2. [Install joia](https://github.com/gsf/joia#install)
3. Create .env file with these vars:
  - AMI=`ID for the AMI to use for the instance`
  - SUBNET=`ID for the VPC subnet the instance should be in`
  - KEY_PAIR=`name of the key pair in AWS to use for the instance`
  - INSTANCE_NAME=`name to set for the instance`
  - PHILA_TEST=`"true" or any truthy value so setup knows this is a test instance`
  - PHILA_DB_BUCKET=`S3 bucket where we keep DB dumps`
  - PHILA_MEDIA_BUCKET=`S3 bucket where instance media is stored`
  - PHILA_PLUGIN_BUCKET=`S3 bucket for private plugins`
  - AWS_ID=`AWS access key ID for the instance to use`
  - AWS_SECRET=`AWS secret access key for the instance to use`
  - SWIFTYPE_ENGINE=`Swiftype engine ID`
4. Private key must be at ~/.ssh/$KEY_PAIR.pem
5. Run `joia up`


## Local setup (deprecated)

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


## How to deploy

1. Modify `composer.json` to point at new require versions.
2. Run `composer install`.
3. Check out the site locally.
4. If all looks good, commit and push to the `staging` branch.
5. That push will trigger a build at https://travis-ci.org/CityOfPhiladelphia/phila.gov, which will in turn trigger a staging deploy.
6. Check out the site at the staging server.
7. If all looks good, create a pull request from staging to master. Once the PR has been accepted and merged, a production deploy will be triggered.
