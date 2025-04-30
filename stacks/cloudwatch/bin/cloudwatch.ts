import * as cdk from 'aws-cdk-lib';
import { CloudwatchStack } from '../lib/cloudwatch-stack';
import { validate } from '../lib/utils/validate';

const app = new cdk.App();
const { ASG_NAME } = validate.environment(['ASG_NAME']);
new CloudwatchStack(app, `AutoscaleBaseEC2AlarmMetrics-${ASG_NAME}`, {});