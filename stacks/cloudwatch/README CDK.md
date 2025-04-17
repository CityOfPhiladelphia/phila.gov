# cloudwatch-stack
[![Coverage](https://img.shields.io/codecov/c/github/CityOfPhiladelphia/cloudwatch-stack)](https://codecov.io/gh/CityOfPhiladelphia/cloudwatch-stack)
[![AWS CDK](https://img.shields.io/badge/AWS-CDK-FF9900?logo=amazonaws&logoColor=white)](https://aws.amazon.com/cdk/)
[![Node.js](https://img.shields.io/badge/Node.js-18.x-339933?logo=nodedotjs&logoColor=white)](https://nodejs.org/)
[![TypeScript](https://img.shields.io/badge/TypeScript-Enabled-007ACC?logo=typescript&logoColor=white)](https://www.typescriptlang.org/)

This project contains the AWS CDK stack for setting up CloudWatch monitoring and logging for various City of Philadelphia applications.

## Table of Contents

- [Project Architecture](#project-architecture)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation and Usage](#installation-and-usage)
  - [Executing Program](#executing-program)
## Project Architecture

This project uses the [AWS Cloud Development Kit (CDK)](https://aws.amazon.com/cdk/) to define infrastructure as code. The stack provisions CloudWatch resources such as log groups, alarms, and dashboards.

For more details, refer to the [AWS CDK Documentation](https://docs.aws.amazon.com/cdk/latest/guide/home.html).

## Getting Started

Follow these instructions to set up the project locally for development and testing.

### Prerequisites

Ensure the following tools are installed:

- [Node.js 18.x](https://nodejs.org/)
- [AWS CLI](https://aws.amazon.com/cli/)
- [AWS CDK](https://docs.aws.amazon.com/cdk/latest/guide/cli.html)
- [Git](https://git-scm.com/downloads)

### Installation and Usage

1. Clone the repository:
   ```bash
   git clone https://github.com/CityOfPhiladelphia/cloudwatch-stack.git
   cd cloudwatch-stack
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Bootstrap your AWS environment (if not already done):
   ```bash
   cdk bootstrap aws://<ACCOUNT_ID>/<REGION>
   ```

### Executing Program

To deploy the stack, run:
```bash
cdk deploy
```

To destroy the stack, run:
```bash
cdk destroy
```