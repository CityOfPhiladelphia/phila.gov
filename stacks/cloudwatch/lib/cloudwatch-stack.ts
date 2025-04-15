import * as cdk from 'aws-cdk-lib';
import { Construct } from 'constructs';
import * as sns from 'aws-cdk-lib/aws-sns';
import * as sns_subs from 'aws-cdk-lib/aws-sns-subscriptions';
import * as cw from 'aws-cdk-lib/aws-cloudwatch';
import * as cw_actions from 'aws-cdk-lib/aws-cloudwatch-actions';
import * as autoscaling from 'aws-cdk-lib/aws-autoscaling';
import * as dotenv from 'dotenv';

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

    const alertTopic = new sns.Topic(this, 'AsgAlerts', {
      topicName: snsTopicName,
      displayName: 'ASG Alerts',
    });

    // Add email subscriptions
    alertEmails.forEach(email => {
      alertTopic.addSubscription(new sns_subs.EmailSubscription(email.trim()));
    });

    const autoScalingGroup = autoscaling.AutoScalingGroup.fromAutoScalingGroupName(
      this,
      'ExistingAsg',
      asgName
    );
  
    

    const createAlarm = (id: string, metric: cw.IMetric, threshold: number) => {
      const alarm = new cw.Alarm(this, id, {
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
        namespace: 'AWS/AutoScaling',
        metricName: 'CPUUtilization',
        dimensionsMap: { AutoScalingGroupName: asgName },
        statistic: 'Average',
      }),
      MemoryUtilization: new cw.Metric({
        namespace: 'CWAgent',
        metricName: 'mem_used_percent',
        dimensionsMap: { AutoScalingGroupName: asgName },
        statistic: 'Average',
      }),
      DiskUtilization: new cw.Metric({
        namespace: 'CWAgent',
        metricName: 'disk_used_percent',
        dimensionsMap: { AutoScalingGroupName: asgName },
        statistic: 'Average',
      }),
      NetworkIn: new cw.Metric({
        namespace: 'AWS/AutoScaling',
        metricName: 'NetworkIn',
        dimensionsMap: { AutoScalingGroupName: asgName },
        statistic: 'Average',
      }),
      NetworkOut: new cw.Metric({
        namespace: 'AWS/AutoScaling',
        metricName: 'NetworkOut',
        dimensionsMap: { AutoScalingGroupName: asgName },
        statistic: 'Average',
      }),
    };

    createAlarm('CPUUtilizationAlarm', metrics.CPUUtilization, 70);
    createAlarm('MemoryUtilizationAlarm', metrics.MemoryUtilization, 80);
    createAlarm('DiskUtilizationAlarm', metrics.DiskUtilization, 75);
    createAlarm('NetworkInAlarm', metrics.NetworkIn, 10 * 1024 * 1024); // 10 MB
    createAlarm('NetworkOutAlarm', metrics.NetworkOut, 10 * 1024 * 1024); // 10 MB
  }
}
