import * as cdk from 'aws-cdk-lib';
import { Construct } from 'constructs';
import * as sns from 'aws-cdk-lib/aws-sns';
import * as sns_subs from 'aws-cdk-lib/aws-sns-subscriptions';
import * as cw from 'aws-cdk-lib/aws-cloudwatch';
import * as cw_actions from 'aws-cdk-lib/aws-cloudwatch-actions';
import * as autoscaling from 'aws-cdk-lib/aws-autoscaling';
import * as dotenv from 'dotenv';
import { create } from 'domain';

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

    const asgName = process.env.ASG_NAME!;
    const snsTopicName = process.env.SNS_TOPIC_NAME!;
    const alertEmails = process.env.ALERT_EMAILS!.split(',');

    // use the following line if the SNS topic already exists
    const alertTopic = 
      sns.Topic.fromTopicArn(
        this, 
        'ExistingAlertTopic', 
        `arn:aws:sns:${this.region}:${this.account}:${snsTopicName}`
      );

    // Uncomment the following lines to create a new SNS topic
    // const alertTopic = new sns.Topic(this, 'AsgAlerts', {
    //   topicName: snsTopicName,
    //   displayName: 'ASG Alerts',
    // });

    // Add email subscriptions
    alertEmails.forEach(email => {
      alertTopic.addSubscription(new sns_subs.EmailSubscription(email.trim()));
    });

    const autoScalingGroup = autoscaling.AutoScalingGroup.fromAutoScalingGroupName(
      this,
      'ExistingAsg',
      asgName
    );
    
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
          AutoScalingGroupName: asgName,
        },
        statistic: 'Average',
        period: cdk.Duration.minutes(1),
      }),
    
      MemoryUtilization: new cw.MathExpression({
        expression: `SELECT AVG(mem_used_percent) FROM CWAgent WHERE AutoScalingGroupName = '${asgName}'`,
        label: 'Avg Memory Utilization',
        period: cdk.Duration.minutes(1),
      }),
    
      DiskUtilization: new cw.MathExpression({
        expression: `SELECT AVG(disk_used_percent) FROM CWAgent WHERE path = '/' AND AutoScalingGroupName = '${asgName}'`,
        label: 'Avg Disk Utilization',
        period: cdk.Duration.minutes(1),
      }),
    
      NetworkIn: new cw.Metric({
        namespace: 'AWS/EC2',
        metricName: 'NetworkIn',
        dimensionsMap: {
          AutoScalingGroupName: asgName,
        },
        statistic: 'Average',
        period: cdk.Duration.minutes(1),
      }),
    
      NetworkOut: new cw.Metric({
        namespace: 'AWS/EC2',
        metricName: 'NetworkOut',
        dimensionsMap: {
          AutoScalingGroupName: asgName,
        },
        statistic: 'Average',
        period: cdk.Duration.minutes(1),
      }),
    };

    const stagingNetworkInThreshold = 1400 * 1024 * 1024; // 1.4 GB
    const stagingNetworkOutThreshold = 130 * 1024 * 1024; // 130 MB

    const productionNetworkInThreshold = 150 * 1024 * 1024; // need to set this
    const productionNetworkOutThreshold = 150 * 1024 * 1024; // need to set this

    // Set thresholds based on the environment
    const isStaging = process.env.ENVIRONMENT === 'staging';
    const networkInThreshold = isStaging ? stagingNetworkInThreshold : productionNetworkInThreshold;
    const networkOutThreshold = isStaging ? stagingNetworkOutThreshold : productionNetworkOutThreshold;

    createAlarm('CPUUtilizationAlarm', metrics.CPUUtilization, 70, `${asgName} - CPU Utilization Alarm`);
    createAlarm('MemoryUtilizationAlarm', metrics.MemoryUtilization, 80, `${asgName} - Memory Utilization Alarm`);
    createAlarm('DiskUtilizationAlarm', metrics.DiskUtilization, 75, `${asgName} - Disk Utilization Alarm`);
    createAlarm('NetworkInAlarm', metrics.NetworkIn, networkInThreshold, `${asgName} - Network In Alarm`);
    createAlarm('NetworkOutAlarm', metrics.NetworkOut, networkOutThreshold, `${asgName} - Network Out Alarm`);
  }
}