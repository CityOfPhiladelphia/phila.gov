# cloudwatch-stack
[![Coverage](https://img.shields.io/codecov/c/github/CityOfPhiladelphia/cloudwatch-stack)](https://codecov.io/gh/CityOfPhiladelphia/cloudwatch-stack)
[![AWS CDK](https://img.shields.io/badge/AWS-CDK-FF9900?logo=amazonaws&logoColor=white)](https://aws.amazon.com/cdk/)
[![Node.js](https://img.shields.io/badge/Node.js-18.x-339933?logo=nodedotjs&logoColor=white)](https://nodejs.org/)
[![TypeScript](https://img.shields.io/badge/TypeScript-Enabled-007ACC?logo=typescript&logoColor=white)](https://www.typescriptlang.org/)

This project contains the AWS CDK stack for setting up CloudWatch monitoring and logging for various City of Philadelphia applications.

This stack creates EC2 alarms per autoscaling group in CloudWatch for:
- Average Memory Utilization (sourced from CloudWatch Agent)
- Average Disk Utilization (sourced from CloudWatch Agent)
- Average CPU Utilzation

**Note:** 
- Data for **Average Memory Utilization** and **Average Disk Utilization** will only be collected if the CloudWatch Agent is installed and running on the EC2 instances in the Auto Scaling Group.
- The installation of the CloudWatch Agent is automated via the user data script in the launch configuration of the relevant Auto Scaling Group. 
- An example of this setup exists in the staging environment. The user data script references the CloudWatch Agent configuration stored in AWS Systems Manager Parameter Store.

## Table of Contents

- [Project Architecture](#project-architecture)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation and Usage](#installation-and-usage)
## Project Architecture

This project uses the [AWS Cloud Development Kit (CDK)](https://aws.amazon.com/cdk/) to define infrastructure as code. The stack provisions CloudWatch resources such as log groups and alarms.

For more details, refer to the [AWS CDK Documentation](https://docs.aws.amazon.com/cdk/latest/guide/home.html).

## Getting Started

Follow these instructions to set up the project locally for development and testing.

### Prerequisites

Ensure the following tools are installed:

- [Node.js 18.x](https://nodejs.org/) - [Installation Guide](https://nodejs.org/en/download/)
- [AWS CLI](https://aws.amazon.com/cli/) - [Installation Guide](https://docs.aws.amazon.com/cli/latest/userguide/install-cliv2.html)
- [AWS CDK](https://docs.aws.amazon.com/cdk/latest/guide/cli.html) - [Installation Guide](https://docs.aws.amazon.com/cdk/v2/guide/getting-started.html)
- [Git](https://git-scm.com/downloads) - [Installation Guide](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)

### Installation and Usage

1. Navigate to the `cloudwatch` directory:
   ```bash
   cd ../stacks/cloudwatch
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Log in to your AWS account in the CLI:
   ```bash
   aws sso login --profile <YOUR_AWS_ACCOUNT_PROFILE_NAME>
   ```
   
4. Bootstrap your AWS environment (if not already done):
   ```bash
   cdk bootstrap --profile <YOUR_AWS_ACCOUNT_PROFILE_NAME>
   ```

5. Deploy the stack:
   ```bash
   cdk deploy --profile <YOUR_AWS_ACCOUNT_PROFILE_NAME>
   ```

### Notes

- Ensure that `<ACCOUNT_ID>` and `<REGION>` are replaced with your AWS account ID and region.
- Replace `<YOUR_AWS_ACCOUNT_PROFILE_NAME>` with the name of your AWS CLI profile.
- Confirm that your AWS environment is properly configured before deploying.
