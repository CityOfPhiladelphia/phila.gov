# phila.gov

Where the pieces are pulled together for phila.gov deployment.

## Local setup

- [Install Vagrant](https://docs.vagrantup.com/v2/installation/)

- Clone this repo.
```
git clone git@github.com:CityOfPhiladelphia/phila.gov.git
```

- Initialize the virtual environment.
```
vagrant up
```

- Log into your virtual environment.
```
vagrant ssh
```

- Go to the shared dirctory.
```
cd /vagrant
```

- Install your dependencies.
```
composer install
```

- TBD