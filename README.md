[![Tested on BrowserStack](https://img.shields.io/badge/browserstack-tested-brightgreen.svg)](https://www.browserstack.com/)

# phila.gov
The phila.gov site is WordPress running behind Nginx on AWS. This repo contains vendored versions of WordPress core, plugins, and the City’s custom theme and plugin.


## Getting Started
Visit the [phila.gov-docker-dev](https://github.com/CityOfPhiladelphia/phila.gov-docker-dev) repo for instructions on how to create a local copy of phila.gov. 

## Deployment

- A pull request into `master` will kick off a [travis-ci](https://travis-ci.org/CityOfPhiladelphia/phila.gov) build.
- Merging into master will deploy the codebase to the production machines, though CodeDeploy in AWS. 
 

## License
This project is licensed under the MIT License - see the [LICENSE](https://github.com/CityOfPhiladelphia/phila.gov/blob/master/LICENSE) file for details
