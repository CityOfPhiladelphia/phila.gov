import * as cdk from 'aws-cdk-lib';
import { Construct } from 'constructs';
import * as sns from 'aws-cdk-lib/aws-sns';
import * as sns_subs from 'aws-cdk-lib/aws-sns-subscriptions';
import * as cw from 'aws-cdk-lib/aws-cloudwatch';
import * as cw_actions from 'aws-cdk-lib/aws-cloudwatch-actions';
import * as autoscaling from 'aws-cdk-lib/aws-autoscaling';
import * as dotenv from 'dotenv';
import { create } from 'domain';
import { validate } from './utils/validate';
import { Environment } from 'aws-cdk-lib/aws-appconfig';

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

    const { ASG_NAME, SNS_TOPIC_NAME, ALERT_EMAILS, ENVIRONMENT } = validate.environment([
      'ASG_NAME',
      'SNS_TOPIC_NAME',
      'ALERT_EMAILS',
      'ENVIRONMENT',
    ]);
    
    // use the following line if the SNS topic already exists
    const alertTopic = 
      sns.Topic.fromTopicArn(
        this, 
        'ExistingAlertTopic', 
        `arn:aws:sns:${this.region}:${this.account}:${SNS_TOPIC_NAME}`
      );

    // Uncomment the following lines to create a new SNS topic
    // const alertTopic = new sns.Topic(this, 'AsgAlerts', {
    //   topicName: snsTopicName,
    //   displayName: 'ASG Alerts',
    // });

    // Add email subscriptions
    ALERT_EMAILS?.split(',').forEach(email => {
      alertTopic.addSubscription(new sns_subs.EmailSubscription(email.trim()));
    });

    const autoScalingGroup = autoscaling.AutoScalingGroup.fromAutoScalingGroupName(
      this,
      'ExistingAsg',
      ASG_NAME!
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
    
      NetworkIn: new cw.Metric({
        namespace: 'AWS/EC2',
        metricName: 'NetworkIn',
        dimensionsMap: {
          AutoScalingGroupName: ASG_NAME!,
        },
        statistic: 'Average',
        period: cdk.Duration.minutes(1),
      }),
    
      NetworkOut: new cw.Metric({
        namespace: 'AWS/EC2',
        metricName: 'NetworkOut',
        dimensionsMap: {
          AutoScalingGroupName: ASG_NAME!,
        },
        statistic: 'Average',
        period: cdk.Duration.minutes(1),
      }),
    };

    type Thresholds = { 
      [environment: string]: { // Could also be enum if feeling fancy
          network: { 
               in: number, 
               out: number
           }
         }
     };
     
     const thresholds: Thresholds = { 
       staging: { 
        network: {
           in: 1400 * 1024 * 1024, // 1.4 GB
           out: 130 * 1024 * 1024  // 130 MB
        }
       },
       production: { 
        network: {
          in: 150 * 1024 * 1024, // need to set this
          out: 150 * 1024 * 1024 // need to set this
        }
       }
     }

    createAlarm('CPUUtilizationAlarm', metrics.CPUUtilization, 70, `${ASG_NAME} - CPU Utilization Alarm`);
    createAlarm('MemoryUtilizationAlarm', metrics.MemoryUtilization, 80, `${ASG_NAME} - Memory Utilization Alarm`);
    createAlarm('DiskUtilizationAlarm', metrics.DiskUtilization, 75, `${ASG_NAME} - Disk Utilization Alarm`);
    createAlarm('NetworkInAlarm', metrics.NetworkIn, thresholds[ENVIRONMENT!].network.in, `${ASG_NAME} - Network In Alarm`);
    createAlarm('NetworkOutAlarm', metrics.NetworkOut, thresholds[ENVIRONMENT!].network.out, `${ASG_NAME} - Network Out Alarm`);
  }
}