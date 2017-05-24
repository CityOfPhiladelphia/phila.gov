[![Tested on BrowserStack](https://img.shields.io/badge/browserstack-tested-brightgreen.svg)](https://www.browserstack.com/)


# phila.gov

The phila.gov site is WordPress running behind Nginx on an AWS instance. The entire machine setup is kept in this repo. 


## Running a test instance

1. [Install joia](https://github.com/CityOfPhiladelphia/joia#install)
2. Copy `.env.test` from S3 to `.env` in this repo
4. Run `joia up`


## Launching a new production instance

Same as above for test instance but replace `.env` with `.env.prod`. Then, after machine is up:

1. Set branch and project tags in AWS for Travis deploy (unsetting tags on current production instance)
2. Restart most recent build in Travis
3. After deploy has succeeded, add instance to load balancer
4. Once instance status is InService, remove and terminate old instance
