import * as cdk from 'aws-cdk-lib';
import { Construct } from 'constructs';
import * as sns from 'aws-cdk-lib/aws-sns';
import * as sns_subs from 'aws-cdk-lib/aws-sns-subscriptions';
import * as cw from 'aws-cdk-lib/aws-cloudwatch';
import * as cw_actions from 'aws-cdk-lib/aws-cloudwatch-actions';
import * as autoscaling from 'aws-cdk-lib/aws-autoscaling';
import * as dotenv from 'dotenv';
import { validate } from './utils/validate';

dotenv.config();

export class CloudwatchStack extends cdk.Stack {
  constructor(scope: Construct, id: string, props?: cdk.StackProps) {
    super(scope, id, {
      ...props,
      env: {
        account: process.env.CDK_DEFAULT_ACCOUNT,
        region: process.env.CDK_DEFAULT_REGION,
      },
    });

    // Validate and destructure environment variables
    const { ASG_NAME, ALERT_EMAILS } = validate.environment(['ASG_NAME', 'ALERT_EMAILS']);

    const NEW_SNS_TOPIC_NAME = process.env.NEW_SNS_TOPIC_NAME;
    const EXISTING_SNS_TOPIC_ARN = process.env.EXISTING_SNS_TOPIC_ARN;
    
    if ((NEW_SNS_TOPIC_NAME && EXISTING_SNS_TOPIC_ARN) || (!NEW_SNS_TOPIC_NAME && !EXISTING_SNS_TOPIC_ARN)) {
      throw new Error('You must specify either NEW_SNS_TOPIC_NAME or EXISTING_SNS_TOPIC_ARN, but not both.');
    }
    
    const alertTopic = EXISTING_SNS_TOPIC_ARN
      ? sns.Topic.fromTopicArn(this, 'ExistingAlertTopic', EXISTING_SNS_TOPIC_ARN)
      : new sns.Topic(this, 'AsgAlerts', {
          topicName: NEW_SNS_TOPIC_NAME,
          displayName: 'ASG Alerts',
        });

    // Add email subscriptions
    ALERT_EMAILS?.split(',').forEach(email => {
      alertTopic.addSubscription(new sns_subs.EmailSubscription(email.trim()));
    });
    
    const createAlarm = (
      id: string,
      metric: cw.Metric | cw.MathExpression,
      threshold: number,
      customName: string
    ) => {
      const alarm = new cw.Alarm(this, id, {
        alarmName: customName,
        metric,
        evaluationPeriods: 5,
        datapointsToAlarm: 3,
        threshold,
        comparisonOperator: cw.ComparisonOperator.GREATER_THAN_THRESHOLD,
        actionsEnabled: true,
      });
    
      alarm.addAlarmAction(new cw_actions.SnsAction(alertTopic));
    };

    const metrics = {
      CPUUtilization: new cw.Metric({
        namespace: 'AWS/EC2',
        metricName: 'CPUUtilization',
        dimensionsMap: {
          AutoScalingGroupName: ASG_NAME!,
        },
        statistic: 'Average',
        period: cdk.Duration.minutes(1),
      }),
    
      MemoryUtilization: new cw.MathExpression({
        expression: `SELECT AVG(mem_used_percent) FROM CWAgent WHERE AutoScalingGroupName = '${ASG_NAME}'`,
        label: 'Avg Memory Utilization',
        period: cdk.Duration.minutes(1),
      }),
    
      DiskUtilization: new cw.MathExpression({
        expression: `SELECT AVG(disk_used_percent) FROM CWAgent WHERE path = '/' AND AutoScalingGroupName = '${ASG_NAME}'`,
        label: 'Avg Disk Utilization',
        period: cdk.Duration.minutes(1),
      }),
    };

    createAlarm('CPUUtilizationAlarm', metrics.CPUUtilization, 70, `${ASG_NAME} - CPU Utilization Alarm`);
    createAlarm('MemoryUtilizationAlarm', metrics.MemoryUtilization, 80, `${ASG_NAME} - Memory Utilization Alarm`);
    createAlarm('DiskUtilizationAlarm', metrics.DiskUtilization, 75, `${ASG_NAME} - Disk Utilization Alarm`);
  }
}